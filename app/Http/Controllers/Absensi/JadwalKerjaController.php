<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\JadwalKerja;
use App\Models\MasterShift;
use App\Models\User;
use Illuminate\Http\Request;
use Alert;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class JadwalKerjaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::where('Role', 'Pegawai')->get();
        $shift = MasterShift::get();
        $jadwal = JadwalKerja::get();

        $tukar = JadwalKerja::with('User', 'Shift')->where('status','T')->get();

        return view('absensi.jadwal', compact('user', 'shift', 'jadwal','tukar'));
    }
    public function getEventDetails($id){
        $jadwal = JadwalKerja::with(['User', 'Shift'])->find($id);

        return response()->json([
            'id' => $jadwal->jadwal_id,
            'start_date' => $jadwal->tanggal,
            'pegawai' => $jadwal->User->id,
            'keterangan' => $jadwal->keterangan,
            'shift' => $jadwal->shift_id
        ]);
    }

    public function getJadwalKerja()
    {
        $jadwal = JadwalKerja::with(['User', 'Shift'])->get();
        return response()->json($jadwal);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $start_date = Carbon::parse($request->start_date);
            $end_date = $request->end_date ? Carbon::parse($request->end_date) : null;

            // Check if end_date is null, save data once for start_date only
            if (!$end_date) {
                // Check if there's already an entry for the employee, shift, and date
                $item = JadwalKerja::where('id', $request->pegawai)
                    ->where('shift_id', $request->shift)
                    ->where('tanggal', $start_date->toDateString())
                    ->first();

                if ($item) {
                    Alert::warning('Warning', 'Pegawai dan Jadwal Telah Ada');
                    return redirect()->back();
                }

                DB::beginTransaction();

                $new = new JadwalKerja();
                $new->id = $request->pegawai;
                $new->shift_id = $request->shift;
                $new->tanggal = $start_date->toDateString();
                // $new->keterangan = $request->keterangan;
                $new->month = $start_date->format('m');
                $new->year = $start_date->format('Y');
                $new->status = 'X';
                $new->save();

                DB::commit();

                Alert::success('Success', 'Data Berhasil Ditambahkan');
                return redirect()->back();
            }

            // Loop through each date between start_date and end_date
            while ($start_date->lte($end_date)) {
                // Check if there's already an entry for the employee, shift, and date
                $item = JadwalKerja::where('id', $request->pegawai)
                    ->where('shift_id', $request->shift)
                    ->where('tanggal', $start_date->toDateString())
                    ->first();

                if ($item) {
                    Alert::warning('Warning', 'Pegawai dan Jadwal Telah Ada');
                    return redirect()->back();
                }

                DB::beginTransaction();

                $new = new JadwalKerja();
                $new->id = $request->pegawai;
                $new->shift_id = $request->shift;
                $new->tanggal = $start_date->toDateString();
                // $new->keterangan = $request->keterangan;
                $new->month = $start_date->format('m');
                $new->year = $start_date->format('Y');
                $new->status = 'X';
                $new->save();

                DB::commit();

                // Move to the next date
                $start_date->addDay();
            }

            Alert::success('Success', 'Data Berhasil Ditambahkan');
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            Alert::warning('Warning', 'Internal Server Error, Data Not Found');
            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            // $check = JadwalKerja::where('tanggal', $request->start_date)->where('shift_id', $request->shiftEdit)->first();
            // if($check){
            //     Alert::warning('Warning', 'Jadwal pada tanggal dan shift telah terisi');
            //     return redirect()->back();
            // }

            $data = JadwalKerja::where('jadwal_id', $request->jadwalIdEdit)->first();
            $data->id = $request->pegawai;
            $data->tanggal = $request->start_date;
            $data->shift_id = $request->shiftEdit;
            // $data->keterangan = $request->keterangan;
            $data->status = 'X';
            $data->update();

            DB::commit();
            Alert::success('Success', 'Data Berhasil Diupdate');
            return redirect()->back();
        } catch (\Throwable $th) {
            return $th;
            DB::rollBack();
            Alert::warning('Warning', 'Internal Server Error, Data Not Found');
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $jadwal = JadwalKerja::findOrFail($id);
            $jadwal->delete();
            return response()->json(['message' => 'Schedule deleted successfully']);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error']);

        }
    }
}
