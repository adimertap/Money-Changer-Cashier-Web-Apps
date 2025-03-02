<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DetailTransaksi;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class JurnalBulananController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     try {
    //         $transaksi = Transaksi::selectRaw(
    //             '
    //             SUM(CASE WHEN jenis_transaksi = "Beli" THEN total ELSE 0 END) as grand_total,
    //             SUM(CASE WHEN jenis_transaksi = "Jual" THEN total ELSE 0 END) as jual_total,
    //             DATE_FORMAT(tanggal_transaksi, "%m") as month,
    //             YEAR(tanggal_transaksi) as year,
    //             SUM(CASE WHEN jenis_transaksi = "Beli" THEN 1 ELSE 0 END) as jumlah_transaksi,
    //             SUM(CASE WHEN jenis_transaksi = "Jual" THEN 1 ELSE 0 END) as jual_transaksi'
    //         )
    //             ->groupBy('year', 'month')
    //             ->orderBy('year', 'DESC')
    //             ->orderBy('month', 'DESC')
    //             ->get();
    //         return $transaksi;

    //         return view('pages.jurnal.bulan.index', compact('transaksi'));
    //     } catch (\Throwable $th) {
    //         Alert::warning('Error', 'Internal Server Error, Try Refreshing The Page');
    //         return redirect()->back();
    //     }
    // }
    public function index()
    {
        try {

            $transaksi = Transaksi::selectRaw(
                'SUM(CASE WHEN jenis_transaksi = "Beli" THEN total ELSE 0 END) as grand_total,
                SUM(CASE WHEN jenis_transaksi = "Jual" THEN total ELSE 0 END) as jual_total,
                DATE_FORMAT(tanggal_transaksi, "%m") as month,
                YEAR(tanggal_transaksi) as year'
            )
            ->groupBy('year', 'month')
            ->orderByRaw("FIELD(month, '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12')")
            ->orderBy('year', 'DESC')
            ->get();
            $months = [
                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
            ];
            $years = $transaksi->pluck('year')->unique()->sort()->values()->all();
            $data = [];
            foreach ($months as $num => $name) {
                $data[$num] = [
                    'month_name' => $name,
                    'totals' => array_fill_keys($years, 0)
                ];
            }

            // Populate transaction totals
            foreach ($transaksi as $item) {
                $data[$item->month]['totals'][$item->year] = $item->grand_total;
            }

            return view('pages.jurnal.bulan.index', compact('data', 'years'));
        } catch (\Throwable $th) {
            Alert::warning('Error', 'Internal Server Error, Try Refreshing The Page');
            return redirect()->back();
        }
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

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
    public function show(Request $request,$month)
    {
        try {
            $transaksi = Transaksi::whereMonth('tanggal_transaksi', '=', $month)
            ->selectRaw('DATE_FORMAT(tanggal_transaksi, "%M") as month, SUM(total) as grand_total, tanggal_transaksi, COUNT(id_transaksi) as jumlah_transaksi, jenis_transaksi as jenis',)
            ->groupBy('tanggal_transaksi')
            ->orderBy('tanggal_transaksi', 'DESC')
            ->get();

        $transaksi_seluruh = Transaksi::with('Pegawai')->whereMonth('tanggal_transaksi', '=', $month);
        if ($request->from) {
            $transaksi_seluruh->where('tanggal_transaksi', '>=', $request->from);
        }
        if ($request->to) {
            $transaksi_seluruh->where('tanggal_transaksi', '<=', $request->to);
        }
        $transaksi_seluruh = $transaksi_seluruh->orderBy('tanggal_transaksi', 'DESC')->get();
        $bulan = $month;

        return view('pages.jurnal.bulan.detail', compact('transaksi', 'transaksi_seluruh', 'bulan'));

        } catch (\Throwable $th) {
            Alert::warning('Error', 'Internal Server Error, Try Refreshing The Page');
            return redirect()->back();
        }



    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($tanggal_transaksi)
    {
        $transaksi = Transaksi::where('tanggal_transaksi', $tanggal_transaksi)->get();
        return view('pages.jurnal.bulan.detailtanggal', compact('transaksi'));
    }

    public function DetailTransaksi($id)
    {
        $transaksi = Transaksi::with('Pegawai','detailTransaksi.Currency')->find($id);
        $detail = DetailTransaksi::where('id_transaksi', $id)->get();
        return view('pages.jurnal.bulan.detailtransaksi', compact('transaksi','detail'));
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
