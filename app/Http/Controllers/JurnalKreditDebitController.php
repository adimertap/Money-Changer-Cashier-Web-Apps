<?php

namespace App\Http\Controllers;

use App\Exports\ExcelDebitKredit;
use App\Models\Jurnal;
use App\Models\MasterCurrency;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;

class JurnalKreditDebitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::user()->role == 'Owner'){
            $jurnal = Jurnal::orderBy('updated_at','DESC')->take(200)->get();
        }else{
            $jurnal = Jurnal::where('id_pegawai', Auth::user()->id)->orderBy('updated_at','DESC')->take(300)->get();
        }
       
        $currency = MasterCurrency::get();

        return view('pages.jurnal.kredit&debit.index', compact('jurnal','currency'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
       
        $jurnal = Jurnal::with('Currency')->OrderBy('updated_at');
        if($request->from_date_export){
            $jurnal->where('tanggal_jurnal', '>=', $request->from_date_export);
        }
        if($request->to_date_export){
            $jurnal->where('tanggal_jurnal', '<=', $request->to_date_export);
        }
        if($request->filter_jenis){
            $jurnal->where('jenis_jurnal', $request->filter_jenis);
        }
        if(Auth::user()->role =='Owner'){
            $jurnal = $jurnal->get();
        }else{
            $jurnal = $jurnal->where('id_pegawai', Auth::user()->id)->get();
        }
       

        $currency = Jurnal::join('tb_currency','tb_jurnal.id_currency','tb_currency.id_currency')
        ->selectRaw('nama_currency as nama, SUM(jumlah_tukar) as total, kurs as jumlah_kurs')
        ->groupBy('nama_currency','kurs');
        if($request->from_date_export){
            $currency->where('tanggal_jurnal', '>=', $request->from_date_export);
        }
        if($request->to_date_export){
            $currency->where('tanggal_jurnal', '<=', $request->to_date_export);
        }
        if($request->filter_jenis){
            $currency->where('jenis_jurnal', $request->filter_jenis);
        }
        if(Auth::user()->role =='Owner'){
            $currency = $currency->get();
        }else{
            $currency = $currency->where('id_pegawai', Auth::user()->id)->get();
        }
       
        
        if(count($jurnal) == 0){
            Alert::warning('Tidak Ditemukan Data', 'Data yang Anda Cari Tidak Ditemukan');
            return redirect()->back();
        }else{
            if($request->radio_input == 'pdf'){
                $pdf = Pdf::loadview('pages.jurnal.kredit&debit.pdf',['jurnal'=>$jurnal, 'kurs'=> $currency]);
                    if($request->from_date_export && $request->to_date_export){
                        return $pdf->download('report-jurnal '.$request->from_date_export.' '.$request->to_date_export.' .pdf');
                    }else{
                        return $pdf->download('report-jurnal.pdf');
                    }
            }else{
                if($request->from_date_export && $request->to_date_export){
                    return Excel::download(new ExcelDebitKredit($jurnal, $currency), 'report-jurnal'.$request->from_date_export.' '.$request->to_date_export.' .xlsx');
                }else{
                    return Excel::download(new ExcelDebitKredit($jurnal, $currency), 'report-jurnal.xlsx');
                }
            }
            Alert::success('Berhasil', 'Data Transaksi Berhasil Didownload');
        }
        
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
        return $id;
    }

    public function hapus(Request $request)
    {
        $jurnal = Jurnal::find($request->jurnal_id);
        $jurnal->delete();

        Alert::success('Berhasil', 'Data Jurnal Berhasil Terhapus');
        return redirect()->back();
    }
}
