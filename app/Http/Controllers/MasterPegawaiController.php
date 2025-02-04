<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class MasterPegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pegawai = User::get();
        $jumlah = User::where('role','Pegawai')->count();

        return view('pages.masterpegawai.index', compact('pegawai','jumlah'));
    }

    public function reset_password($id)
    {
        $pegawai = User::where('id', $id)->first();
        if(!$pegawai){
            Alert::warning('Warning', 'Pegawai not found');
            return redirect()->back();
        }

        return view('auth.passwords.resetv2', compact('pegawai'));
    }

    public function reset_password_post(Request $request, $id){
        try {
            $pegawai = User::where('id', $id)->first();
            if(!$pegawai){
                Alert::warning('Warning', 'Pegawai not found');
                return redirect()->back();
            }

            if($request->password !== $request->confirm_password){
                Alert::warning('Warning', 'Pegawai not found');
                return redirect()->back();
            }
            $pegawai->password = bcrypt($request->password);
            $pegawai->update();

            Alert::success('Success', 'Pasword ' . $pegawai->name . ' Updated');
            return redirect()->route('master-pegawai.show', $id);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.masterpegawai.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $tes = User::where('email', $request->email)->first();
        if(empty($tes)){
            $pegawai = new User;
            $pegawai->name = $request->name;
            $pegawai->nama_panggilan = $request->nama_panggilan;
            $pegawai->jenis_kelamin = $request->jenis_kelamin;
            $pegawai->phone_number = $request->phone_number;
            $pegawai->alamat = $request->alamat;
            $pegawai->role = $request->role;
            $pegawai->email = $request->email;
            $pegawai->password = bcrypt($request->password);
            $pegawai->email_verified_at = Carbon::now();
            $pegawai->save();
            event(new Registered($pegawai));

            Alert::success('Success Title', 'Data Pegawai Berhasil Ditambahkan');
            return redirect()->route('master-pegawai.index');
        }else{
            Alert::warning('Warning', 'Email Telah Ada! Gunakan Email Lainnya');
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
        $item = User::find($id);
        return view('pages.masterpegawai.detail',compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = User::find($id);
        return view('pages.masterpegawai.edit', compact('item'));
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
        $pegawai = User::find($id);
        $pegawai->name = $request->name;
        $pegawai->nama_panggilan = $request->nama_panggilan;
        $pegawai->jenis_kelamin = $request->jenis_kelamin;
        $pegawai->phone_number = $request->phone_number;
        $pegawai->alamat = $request->alamat;
        $pegawai->role = $request->role;
        $pegawai->email = $request->email;
        $pegawai->update();
        Alert::success('Success Title', 'Data Pegawai Berhasil Diedit');
        return redirect()->route('master-pegawai.index');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pegawai = User::find($id);
        $pegawai->delete();
        Alert::success('Success Title', 'Data Pegawai Berhasil Terhapus');
        return redirect()->back();
    }

    public function hapus(Request $request)
    {
        $pegawai = User::find($request->pegawai_delete_id);
        $pegawai->delete();
        Alert::success('Success Title', 'Data Pegawai Berhasil Terhapus');
        return redirect()->route('master-pegawai.index');
    }
}
