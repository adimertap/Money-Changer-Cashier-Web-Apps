<?php

namespace App\Http\Controllers\Absensi;

use Alert;
use App\Http\Controllers\Controller;
use App\Models\JadwalKerja;
use App\Models\MasterCabang;
use App\Models\MasterShift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class JadwalKerjaController extends Controller
{
    private function cabangIds()
    {
        if (auth()->user()->role === 'Owner') {
            return MasterCabang::where('is_active', 1)->pluck('cabang_id')->all();
        }

        return array_map('intval', array_column(session('cabangs', []), 'cabang_id'));
    }

    private function cabangs()
    {
        return MasterCabang::where('is_active', 1)
            ->whereIn('cabang_id', $this->cabangIds())
            ->orderBy('cabang_name')
            ->get();
    }

    private function selectedCabang(Request $request)
    {
        return (int) ($request->input('cabang_id') ?: session('cabang_aktif'));
    }

    private function employeeQuery($cabangId)
    {
        return User::where('role', '!=', 'Owner')
            ->whereHas('cabangs', function ($query) use ($cabangId) {
                $query->where('tb_master_cabang.cabang_id', $cabangId);
            })
            ->orderBy('name');
    }

    private function validateAssignment(Request $request, $shiftField, $employeeField = 'pegawai')
    {
        $cabangId = $this->selectedCabang($request);
        $allowed = $this->cabangIds();

        $request->validate([
            'cabang_id' => ['required', 'integer', Rule::in($allowed)],
            $employeeField => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) use ($cabangId) {
                    $query->where('role', '!=', 'Owner')
                        ->whereExists(function ($pivot) use ($cabangId) {
                            $pivot->select(DB::raw(1))
                                ->from('tb_master_cabang_user')
                                ->whereColumn('tb_master_cabang_user.user_id', 'users.id')
                                ->where('tb_master_cabang_user.cabang_id', $cabangId);
                        });
                }),
            ],
            $shiftField => [
                'required',
                'integer',
                Rule::exists('tb_master_shift', 'shift_id')->where(function ($query) use ($cabangId) {
                    $query->where('cabang_id', $cabangId);
                }),
            ],
        ]);

        return $cabangId;
    }

    public function index()
    {
        $cabangs = $this->cabangs();
        $activeCabang = session('cabang_aktif') ?: optional($cabangs->first())->cabang_id;
        $user = $activeCabang ? $this->employeeQuery($activeCabang)->get() : collect();
        $shift = MasterShift::withoutGlobalScope('cabang')
            ->whereIn('cabang_id', $this->cabangIds())
            ->orderBy('cabang_id')
            ->orderBy('shift_name')
            ->get();
        $jadwal = JadwalKerja::get();
        $tukar = JadwalKerja::with('User', 'Shift')->where('status', 'T')->get();

        return view('absensi.jadwal', compact('user', 'shift', 'jadwal', 'tukar', 'cabangs', 'activeCabang'));
    }

    public function employeesByCabang($cabangId)
    {
        abort_unless(in_array((int) $cabangId, $this->cabangIds(), true), 403);

        return response()->json($this->employeeQuery((int) $cabangId)->get(['id', 'name']));
    }

    public function getEventDetails($id)
    {
        $jadwal = JadwalKerja::with(['User', 'Shift', 'Cabang'])->findOrFail($id);

        return response()->json([
            'id' => $jadwal->jadwal_id,
            'start_date' => $jadwal->tanggal,
            'pegawai' => $jadwal->User->id,
            'keterangan' => $jadwal->keterangan,
            'shift' => $jadwal->shift_id,
            'cabang_id' => $jadwal->cabang_id,
        ]);
    }

    public function getJadwalKerja()
    {
        return response()->json(JadwalKerja::with(['User', 'Shift'])->get());
    }

    public function jadwalUploadExcel(Request $request)
    {
        try {
            $file = $request->file('file');
            $data = Excel::toArray([], $file)[0];
            $rows = array_slice($data, 1);
            $cabangId = (int) (session('cabang_aktif') ?: optional($this->cabangs()->first())->cabang_id);

            DB::transaction(function () use ($rows, $cabangId) {
                foreach ($rows as $row) {
                    $shiftId = $row[0];
                    $pegawaiId = $row[1];
                    $tanggal = $row[3];
                    $keterangan = $row[4];
                    $month = $row[5];
                    $year = $row[6];

                    $employee = $this->employeeQuery($cabangId)->whereKey($pegawaiId)->firstOrFail();
                    $shift = MasterShift::where('cabang_id', $cabangId)->findOrFail($shiftId);
                    $exists = JadwalKerja::where('id', $employee->id)->where('tanggal', $tanggal)->exists();

                    if ($exists) {
                        JadwalKerja::where('id', $employee->id)->where('tanggal', $tanggal)->update([
                            'shift_id' => $shift->shift_id,
                            'month' => $month,
                            'year' => $year,
                            'cabang_id' => $cabangId,
                        ]);
                        continue;
                    }

                    JadwalKerja::create([
                        'shift_id' => $shift->shift_id,
                        'id' => $employee->id,
                        'tanggal' => $tanggal,
                        'month' => $month,
                        'year' => $year,
                        'keterangan' => $keterangan,
                        'status' => 'X',
                        'cabang_id' => $cabangId,
                    ]);
                }
            });

            Alert::success('Success', 'Data Berhasil Diimport');
        } catch (\Throwable $th) {
            Alert::warning('Warning', 'Data Excel tidak valid atau tidak sesuai cabang');
        }

        return redirect()->back();
    }

    public function jadwalDownloadFormat(Request $request)
    {
        try {
            $pegawaiId = $request->pegawai;
            $startDate = $request->start_date;
            $endDate = $request->end_date ?? $startDate;
            $pegawaiIds = empty($pegawaiId)
                ? ($this->selectedCabang($request) ? $this->employeeQuery($this->selectedCabang($request))->pluck('id')->toArray() : [])
                : [$pegawaiId];

            return Excel::download(
                new \App\Exports\JadwalKerjaFormatExport($pegawaiIds, $startDate, $endDate, $request->shift_id),
                'jadwal_kerja_format.xlsx'
            );
        } catch (\Throwable $th) {
            Alert::warning('Warning', 'Internal Server Error, Data Not Found');
            return redirect()->back();
        }
    }

    public function create()
    {
        // Form is rendered by index().
    }

    public function store(Request $request)
    {
        try {
            $cabangId = $this->validateAssignment($request, 'shift');
            $startDate = Carbon::parse($request->start_date);
            $endDate = $request->end_date ? Carbon::parse($request->end_date) : $startDate->copy();

            DB::transaction(function () use ($request, $cabangId, $startDate, $endDate) {
                while ($startDate->lte($endDate)) {
                    $exists = JadwalKerja::where('id', $request->pegawai)
                        ->where('shift_id', $request->shift)
                        ->where('tanggal', $startDate->toDateString())
                        ->exists();

                    if ($exists) {
                        throw new \RuntimeException('Pegawai dan Jadwal Telah Ada');
                    }

                    JadwalKerja::create([
                        'id' => $request->pegawai,
                        'shift_id' => $request->shift,
                        'tanggal' => $startDate->toDateString(),
                        'month' => $startDate->format('m'),
                        'year' => $startDate->format('Y'),
                        'status' => 'X',
                        'cabang_id' => $cabangId,
                    ]);
                    $startDate->addDay();
                }
            });

            Alert::success('Success', 'Data Berhasil Ditambahkan');
        } catch (\Throwable $th) {
            Alert::warning('Warning', $th->getMessage() ?: 'Internal Server Error, Data Not Found');
        }

        return redirect()->back();
    }

    public function show($id) {}

    public function edit($id) {}

    public function update(Request $request, $id)
    {
        try {
            $cabangId = $this->validateAssignment($request, 'shiftEdit');
            $data = JadwalKerja::findOrFail($request->jadwalIdEdit ?: $id);
            $data->id = $request->pegawai;
            $data->tanggal = $request->start_date;
            $data->shift_id = $request->shiftEdit;
            $data->cabang_id = $cabangId;
            $data->status = 'X';
            $data->save();

            Alert::success('Success', 'Data Berhasil Diupdate');
        } catch (\Throwable $th) {
            Alert::warning('Warning', $th->getMessage() ?: 'Internal Server Error, Data Not Found');
        }

        return redirect()->back();
    }

    public function destroy($id)
    {
        try {
            JadwalKerja::findOrFail($id)->delete();
            return response()->json(['message' => 'Schedule deleted successfully']);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error'], 500);
        }
    }
}
