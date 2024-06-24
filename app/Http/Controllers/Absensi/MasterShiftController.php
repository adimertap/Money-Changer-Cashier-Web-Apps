<?php

namespace App\Http\Controllers\Absensi;

use App\Http\Controllers\Controller;
use App\Models\MasterShift;
use Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MasterShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $shift = MasterShift::get();
            return view('absensi.shift', compact('shift'));
        } catch (\Throwable $th) {
            return $th;
        }
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
            DB::beginTransaction();
            $item = new MasterShift();
            $item->shift_name = $request->shift_name;
            $item->shift_in = $request->shift_in;
            $item->shift_out = $request->shift_out;
            $item->save();
            DB::commit();
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
        try {
            $item = MasterShift::find($id);
            if (!$item) {
                return 404;
            }
            return response()->json($item);
        } catch (\Throwable $th) {
            Alert::warning('Warning', 'Internal Server Error, Data Not Found');
            return redirect()->back();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       
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
            $item = MasterShift::find($id);
            $item->shift_name = $request->shift_name;
            $item->shift_in = $request->shift_in;
            $item->shift_out = $request->shift_out;
            $item->save();
            DB::commit();
            Alert::success('Success', 'Data Berhasil DiUpdate');
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            Alert::warning('Warning', 'Internal Server Error');
            return redirect()->back()->withErrors($request->errors())->withInput();
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
            DB::beginTransaction();
            $item = MasterShift::find($id);
            $item->delete();
            DB::commit();
            Alert::success('Success', 'Data berhasil dihapus');
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            Alert::warning('Warning', 'Internal Server Error');
            return redirect()->back();
        }
    }
}
