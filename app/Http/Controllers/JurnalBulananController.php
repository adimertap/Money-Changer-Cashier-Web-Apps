<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DetailTransaksi;
use App\Models\MasterCabang;
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
    public function index(Request $request)
    {
        try {
            $request->validate([
                'cabang_id' => 'nullable|integer|exists:tb_master_cabang,cabang_id',
            ]);
            $cabang = MasterCabang::where('is_active', 1)
                ->orderBy('cabang_name')
                ->get(['cabang_id', 'cabang_name']);

            $transaksiQuery = Transaksi::with('Cabang')
                ->selectRaw(
                    'SUM(total) as grand_total,
                    DATE_FORMAT(tanggal_transaksi, "%m") as month,
                    YEAR(tanggal_transaksi) as year,
                    cabang_id'
                )
                ->when($request->filled('cabang_id'), function ($query) use ($request) {
                    $query->where('cabang_id', $request->cabang_id);
                })
                ->groupBy('year', 'month', 'cabang_id')
                ->orderBy('year', 'DESC')
                ->orderByRaw("FIELD(month, '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12')");
            $transaksi = $transaksiQuery->get();
            $months = [
                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
            ];
            $years = $transaksi->pluck('year')->unique()->sort()->values()->all();
            $data = [];
            foreach ($transaksi as $item) {
                $key = $item->year . '-' . $item->month . '-' . ($item->cabang_id ?: 'all');
                $data[$key] = [
                    'month_name' => $months[$item->month],
                    'year' => $item->year,
                    'cabang_id' => $item->cabang_id,
                    'cabang_name' => optional($item->Cabang)->cabang_name ?: 'Semua Cabang',
                    'totals' => array_fill_keys($years, 0),
                ];
                $data[$key]['totals'][$item->year] = $item->grand_total;
            }

            return view('pages.jurnal.bulan.index', compact('data', 'years', 'cabang'));
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
    public function show(Request $request, $month)
    {
        try {
            $request->validate([
                'cabang_id' => 'nullable|integer|exists:tb_master_cabang,cabang_id',
            ]);
            $cabang = MasterCabang::where('is_active', 1)
                ->orderBy('cabang_name')
                ->get(['cabang_id', 'cabang_name']);

            $transaksiQuery = Transaksi::with('Cabang')->whereMonth('tanggal_transaksi', '=', $month)
                ->when($request->filled('cabang_id'), function ($query) use ($request) {
                    $query->where('cabang_id', $request->cabang_id);
                });
            $transaksi = $transaksiQuery
                ->selectRaw('DATE_FORMAT(tanggal_transaksi, "%M") as month, SUM(total) as grand_total, tanggal_transaksi, COUNT(id_transaksi) as jumlah_transaksi, jenis_transaksi as jenis, cabang_id')
                ->groupBy('tanggal_transaksi', 'jenis_transaksi', 'cabang_id')
                ->orderBy('tanggal_transaksi', 'DESC')
                ->get();

            $transaksi_seluruh = Transaksi::with(['Pegawai', 'Cabang'])
                ->whereMonth('tanggal_transaksi', '=', $month)
                ->when($request->filled('cabang_id'), function ($query) use ($request) {
                    $query->where('cabang_id', $request->cabang_id);
                });
            if ($request->from) {
                $transaksi_seluruh->where('tanggal_transaksi', '>=', $request->from);
            }
            if ($request->to) {
                $transaksi_seluruh->where('tanggal_transaksi', '<=', $request->to);
            }
            $transaksi_seluruh = $transaksi_seluruh->orderBy('tanggal_transaksi', 'DESC')->get();
            $bulan = $month;

            return view('pages.jurnal.bulan.detail', compact('transaksi', 'transaksi_seluruh', 'bulan', 'cabang'));

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
    public function edit(Request $request, $tanggal_transaksi)
    {
        $request->validate([
            'cabang_id' => 'nullable|integer|exists:tb_master_cabang,cabang_id',
        ]);
        $transaksi = Transaksi::with('Cabang')
            ->where('tanggal_transaksi', $tanggal_transaksi)
            ->when($request->filled('cabang_id'), function ($query) use ($request) {
                $query->where('cabang_id', $request->cabang_id);
            })
            ->get();
        return view('pages.jurnal.bulan.detailtanggal', compact('transaksi'));
    }

    public function DetailTransaksi(Request $request, $id)
    {
        $request->validate([
            'cabang_id' => 'nullable|integer|exists:tb_master_cabang,cabang_id',
        ]);
        $transaksi = Transaksi::with('Pegawai','Cabang','detailTransaksi.Currency')
            ->where('id_transaksi', $id)
            ->when($request->filled('cabang_id'), function ($query) use ($request) {
                $query->where('cabang_id', $request->cabang_id);
            })
            ->firstOrFail();

        $transaksi->healCustomerData();

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
