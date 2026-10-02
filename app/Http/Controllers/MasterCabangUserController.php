<?php

namespace App\Http\Controllers;

use App\Models\MasterCabangUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class MasterCabangUserController extends Controller
{
    private function rules()
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'cabang_id' => 'required|integer|exists:tb_master_cabang,cabang_id',
        ];
    }

    public function index()
    {
        $cabang_user = MasterCabangUser::orderByDesc('cabang_user_id')->get();
        return view('pages.mastercabanguser.index', compact('cabang_user'));
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['created_by'] = Auth::id();
        MasterCabangUser::create($data);
        Alert::success('Berhasil', 'Data Cabang User Berhasil Ditambahkan');
        return redirect()->back();
    }

    public function show($id)
    {
        return response()->json(MasterCabangUser::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate($this->rules());
        $data['updated_by'] = Auth::id();
        MasterCabangUser::findOrFail($id)->update($data);
        Alert::success('Berhasil', 'Data Cabang User Berhasil Diedit');
        return redirect()->back();
    }

    public function destroy($id)
    {
        MasterCabangUser::findOrFail($id)->delete();
        Alert::success('Berhasil', 'Data Cabang User Berhasil Dihapus');
        return redirect()->back();
    }
}
