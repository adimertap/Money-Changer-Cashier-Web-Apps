<?php

namespace App\Http\Controllers\Absensi;

use Alert;
use App\Http\Controllers\Controller;
use App\Models\MasterCabang;
use App\Models\MasterShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MasterShiftController extends Controller
{
    private function cabangIds()
    {
        if (Auth::user()->role === 'Owner') {
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

    private function rules()
    {
        return [
            'shift_name' => 'required|string|max:100',
            'shift_in' => 'required|date_format:H:i',
            'shift_out' => 'required|date_format:H:i',
            'cabang_id' => ['required', 'integer', Rule::in($this->cabangIds())],
        ];
    }

    public function index()
    {
        $shift = MasterShift::with('Cabang')->get();
        $cabangs = $this->cabangs();

        return view('absensi.shift', compact('shift', 'cabangs'));
    }

    public function create()
    {
        // Form is rendered by index().
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        DB::transaction(function () use ($data) {
            MasterShift::create($data);
        });

        Alert::success('Success', 'Data Berhasil Ditambahkan');
        return redirect()->back();
    }

    public function show($id)
    {
        $item = MasterShift::with('Cabang')->find($id);
        if (!$item) {
            return response()->json(404, 404);
        }

        return response()->json($item);
    }

    public function edit($id)
    {
        // Form is rendered by index().
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate($this->rules());
        $item = MasterShift::findOrFail($id);
        $item->update($data);

        Alert::success('Success', 'Data Berhasil DiUpdate');
        return redirect()->back();
    }

    public function destroy($id)
    {
        MasterShift::findOrFail($id)->delete();

        Alert::success('Success', 'Data berhasil dihapus');
        return redirect()->back();
    }
}
