<?php

namespace App\Http\Controllers;

use App\Models\LogEdit;
use App\Models\LogEditDetail;
use Illuminate\Http\Request;

class LogEditController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $log = LogEdit::with([
            'detailLog',
        ]);        
        if($request->from){
            $log->where('tanggal_transaksi', '>=', $request->from);
        }
        if($request->to){
            $log->where('tanggal_transaksi', '<=', $request->to);
        }
        if($request->jenis){
            $log->where('jenis_log', '=', $request->jenis);
        }
        $log = $log->orderBy('updated_at','DESC')->get();
        return view('pages.log.index', compact('log'));
    }

    public function filterLog(Request $request)
    {
        $log = LogEdit::with('detailLog');
        if($request->from_date_export){
            $log->where('tanggal_transaksi', '>=', $request->from_date_export);
        }
        if($request->to_date_export){
            $log->where('tanggal_transaksi', '<=', $request->to_date_export);
        }
        if($request->jenis_log){
            $log->where('jenis_log', '=', $request->jenis_log);
        }
        $log = $log->get();

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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $log = LogEdit::find($id);
        $detail = LogEditDetail::where('id_log', $id)->get();
        return view('pages.log.detail', compact('log','detail'));
    }

    public function getdetail(Request $request, $id)
    {
        $log = LogEditDetail::
        join('new_log_edit','new_detail_log_edit.id_log','new_log_edit.id_log')
        ->leftjoin('tb_currency','new_detail_log_edit.currency_id','tb_currency.id_currency')
        ->where('new_detail_log_edit.id_log', $id)->get();
        return $log;
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
