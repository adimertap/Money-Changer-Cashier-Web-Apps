<?php

namespace App\Http\Controllers;

use App\Exports\ExcelHarian;
use App\Exports\ExcelHarianOwner;
use App\Exports\ExcelHarianView;
use App\Models\DetailTransaksi;
use App\Models\Jurnal;
use App\Models\LogEdit;
use App\Models\LogEditDetail;
use App\Models\MasterCurrency;
use App\Models\ModalTransaksi;
use App\Models\Transaksi;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // if(Auth::user()->role == 'Pegawai'){
        //     $transaksi = Transaksi::where('id_pegawai', Auth::user()->id)->where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->orderBy('updated_at','DESC')->get();
        //     $count = Transaksi::where('id_pegawai', Auth::user()->id)->where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->count();
        //     $today =  Carbon::now()->format('Y-m-d');
        //     $total_transaksi = Transaksi::where('id_pegawai', Auth::user()->id)->where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->sum('total');
        //     $currency = MasterCurrency::orderBy('jenis_kurs','ASC')->get();

        //     // REPORT PEGAWAI CEPAT
        //     $report = Jurnal::join('tb_currency','tb_jurnal.id_currency','tb_currency.id_currency')
        //     ->where('tanggal_jurnal', $today)->where('id_pegawai', Auth::user()->id)
        //     ->selectRaw('nama_currency as nama_kurs, SUM(jumlah_tukar) as jumlah_tukar, kurs as nilai_kurs')
        //     ->groupBy('nama_currency','kurs')->get();

        //     $valas = Jurnal::join('tb_currency','tb_jurnal.id_currency','tb_currency.id_currency')
        //     ->where('tanggal_jurnal', $today)->where('id_pegawai', Auth::user()->id)
        //     ->selectRaw('nama_currency as nama_kurs, SUM(jumlah_tukar) as jumlah, kurs as nilai, jenis_kurs as jenis')
        //     ->groupBy('nama_currency','jenis_kurs')
        //     ->get();

        //     return view('pages.transaksi.index', compact('valas','transaksi','count','today','total_transaksi','currency','report'));
        // }else{
        //     $transaksi = Transaksi::with('Pegawai')->where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->orderBy('updated_at', 'DESC')->get();
        //     $count = Transaksi::where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->count();
        //     $today =  Carbon::now()->format('Y-m-d');
        //     $total_transaksi = Transaksi::where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->sum('total');
        //     $currency = MasterCurrency::orderBy('jenis_kurs','ASC')->get();
        //     $pegawai = User::where('role','!=','Owner')->get();

        //     $report = Jurnal::join('tb_currency','tb_jurnal.id_currency','tb_currency.id_currency')
        //     ->where('tanggal_jurnal', $today)->selectRaw('nama_currency as nama_kurs, SUM(jumlah_tukar) as jumlah_tukar, kurs as nilai_kurs, jenis_kurs as jenis')
        //     ->groupBy('nama_currency','kurs','jenis_kurs')->get();

        //     $valas = Jurnal::join('tb_currency','tb_jurnal.id_currency','tb_currency.id_currency')
        //     ->where('tanggal_jurnal', $today)
        //     ->selectRaw('nama_currency as nama_kurs, SUM(jumlah_tukar) as jumlah, kurs as nilai, jenis_kurs as jenis, SUM(total_tukar) as total')
        //     ->groupBy('nama_currency')
        //     ->get();

        //     if($request->filterData){
        //         $report = Jurnal::join('tb_currency','tb_jurnal.id_currency','tb_currency.id_currency')
        //             ->where('tanggal_jurnal', $today)->where('id_pegawai', $request->filterData)->selectRaw('nama_currency as nama_kurs, SUM(jumlah_tukar) as jumlah_tukar, kurs as nilai_kurs, jenis_kurs as jenis, id_pegawai as user')->groupBy('nama_currency','kurs','jenis_kurs','id_pegawai')->get();
        //             return view('pages.transaksi.owner', compact('transaksi', 'count','today','total_transaksi','currency','pegawai','report'));

        //     }

        //     return view('pages.transaksi.owner', compact('valas','transaksi', 'count','today','total_transaksi','currency','pegawai','report'));


        // }
        try {
            $today = Carbon::now()->format('Y-m-d');
            $user = Auth::user();
            $isPegawai = $user->role == 'Pegawai';

            $transaksiQuery = Transaksi::where('tanggal_transaksi', $today)
                ->where('jenis_transaksi', 'Beli')
                ->orderBy('updated_at', 'DESC');
            $jurnalQuery = Jurnal::join('tb_currency', 'tb_jurnal.id_currency', 'tb_currency.id_currency')
                ->where('tanggal_jurnal', $today);
            $jurnalQuery2 = Jurnal::join('tb_currency', 'tb_jurnal.id_currency', 'tb_currency.id_currency')
                ->where('tanggal_jurnal', $today);

            if ($isPegawai) {
                $transaksiQuery->where('id_pegawai', $user->id);
                $jurnalQuery->where('id_pegawai', $user->id);
                $jurnalQuery2->where('id_pegawai', $user->id);

            }


            $transaksi = $transaksiQuery->get();
            $count = $transaksiQuery->count();
            $total_transaksi = $transaksiQuery->sum('total');
            $currency = MasterCurrency::orderBy('jenis_kurs', 'ASC')->get();

            $report = $jurnalQuery->selectRaw('nama_currency as nama_kurs, SUM(jumlah_tukar) as jumlah_tukar, kurs as nilai_kurs, jenis_kurs as jenis')
                ->where('jenis_jurnal', 'Debit')
                ->groupBy('nama_currency','kurs','jenis_kurs')
                ->get();

            $valas = $jurnalQuery2->selectRaw('nama_currency as nama_kurs, SUM(jumlah_tukar) as jumlah, kurs as nilai, jenis_kurs as jenis, SUM(total_tukar) as total')
                ->where('jenis_jurnal', 'Debit')
                ->groupBy('nama_currency', 'jenis_kurs')
                ->get();


            if (!$isPegawai) {
                $pegawai = User::where('role', '!=', 'Owner')->get();

                if ($request->filterData) {
                    $report = $jurnalQuery->where('id_pegawai', $request->filterData)
                        ->selectRaw('nama_currency as nama_kurs, SUM(jumlah_tukar) as jumlah_tukar, kurs as nilai_kurs, jenis_kurs as jenis, id_pegawai as user')
                        ->where('jenis_jurnal', 'Debit')
                        ->groupBy('nama_currency',  'jenis_kurs','kurs', 'id_pegawai')
                        ->get();
                    $valas = $jurnalQuery2->where('id_pegawai', $request->filterData)
                        ->selectRaw('nama_currency as nama_kurs, SUM(jumlah_tukar) as jumlah, kurs as nilai, jenis_kurs as jenis, SUM(total_tukar) as total, id_pegawai as user')
                        ->where('jenis_jurnal', 'Debit')
                        ->groupBy('nama_currency', 'jenis_kurs','id_pegawai')
                        ->get();

                    return view('pages.transaksi.owner', compact('valas','transaksi', 'count', 'today', 'total_transaksi', 'currency', 'pegawai', 'report'));
                }

                return view('pages.transaksi.owner', compact('valas', 'transaksi', 'count', 'today', 'total_transaksi', 'currency', 'pegawai', 'report'));
            }
            return view('pages.transaksi.index', compact('valas', 'transaksi', 'count', 'today', 'total_transaksi', 'currency', 'report'));
        } catch (\Throwable $th) {
            dd($th);
            Alert::warning('Error', 'Internal Server Error, Try Refreshing The Page');
            return redirect()->back();
        }
    }

    public function getkurs($id_currency)
    {
        try {
            $kurs = MasterCurrency::where('id_currency', '=', $id_currency)->pluck('nilai_kurs');
            return json_encode($kurs);
        } catch (\Throwable $th) {
            Alert::warning('Error', 'Internal Server Error, Try Refreshing The Page');
            return redirect()->back();
        }
    }

    public function getkursedit($id_currency)
    {
        try {
            $kurs = MasterCurrency::where('id_currency', '=', $id_currency)->pluck('nilai_kurs');
            return json_encode($kurs);
        } catch (\Throwable $th) {
            Alert::warning('Error', 'Internal Server Error, Try Refreshing The Page');
            return redirect()->back();
        }

    }

    public function Export_dokumen(Request $request)
    {
        try {
            if (Auth::user()->role == 'Pegawai') {
                $transaksi = Transaksi::with('Pegawai')->join('tb_detail_transaksi', 'tb_transaksi.id_transaksi', 'tb_detail_transaksi.id_transaksi')
                    ->join('tb_currency', 'tb_detail_transaksi.currency_id', 'tb_currency.id_currency')->where('id_pegawai', Auth::user()->id);
                if ($request->id_currency) {
                    $transaksi->where('currency_id', $request->id_currency);
                }
                $transaksi = $transaksi->where('jenis_transaksi', 'Beli')->where('tanggal_transaksi', Carbon::today())->get();
                $total = $transaksi->sum('total');
                $jumlah = $transaksi->count();
                $today = Carbon::now()->format('d-M-Y');
                // return $transaksi;

                if (count($transaksi) == 0) {
                    Alert::warning('Tidak Ditemukan Data', 'Data yang Anda Cari Tidak Ditemukan');
                    return redirect()->back();
                } else {
                    if ($request->radio_input == 'pdf') {
                        $pdf = Pdf::loadview('export.pdf-harian', ['transaksi' => $transaksi, 'total' => $total, 'jumlah' => $jumlah, 'today' => $today]);
                        return $pdf->download('report-harian ' . $today . ' ' . Auth::user()->name . ' .pdf');
                        Alert::success('Berhasil', 'Data Transaksi Berhasil Didownload');
                    } else {
                        return new ExcelHarian($transaksi);
                    }
                }
            } else {
                $transaksi = Transaksi::with('Pegawai')->join('tb_detail_transaksi', 'tb_transaksi.id_transaksi', 'tb_detail_transaksi.id_transaksi')
                    ->join('tb_currency', 'tb_detail_transaksi.currency_id', 'tb_currency.id_currency')
                    ->where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->OrderBy('tb_transaksi.updated_at');
                if ($request->id_currency) {
                    $transaksi->where('currency_id', $request->id_currency);
                }
                if ($request->id_pegawai) {
                    $transaksi->where('id_pegawai', $request->id_pegawai);
                }
                $transaksi = $transaksi->where('jenis_transaksi', 'Beli')->get();
                $total = $transaksi->sum('total');
                $jumlah = $transaksi->count();
                $today = Carbon::now()->format('d-M-Y');

                if (count($transaksi) == 0) {
                    Alert::warning('Tidak Ditemukan Data', 'Data yang Anda Cari Tidak Ditemukan');
                    return redirect()->back();
                } else {
                    if ($request->radio_input == 'pdf') {
                        $pdf = Pdf::loadview('export.pdf-harian-owner', ['transaksi' => $transaksi, 'total' => $total, 'jumlah' => $jumlah, 'today' => $today]);
                        if ($request->id_pegawai) {
                            return $pdf->download('report-harian ' . $today . ' ' . $transaksi[0]->Pegawai->name . ' .pdf');
                        }
                        return $pdf->download('report-harian ' . $today . ' .pdf');
                        Alert::success('Berhasil', 'Data Transaksi Berhasil Didownload');
                    } else {
                        return new ExcelHarianOwner($transaksi);
                    }
                }
            }
        } catch (\Throwable $th) {
            Alert::warning('Error', 'Internal Server Error, Try Refreshing The Page');
            return redirect()->back();
        }
    }

    public function Export_dokumen_jual(Request $request)
    {
        try {
            if (Auth::user()->role == 'Pegawai') {
                $transaksi = Transaksi::with('Pegawai')->join('tb_detail_transaksi', 'tb_transaksi.id_transaksi', 'tb_detail_transaksi.id_transaksi')
                    ->join('tb_currency', 'tb_detail_transaksi.currency_id', 'tb_currency.id_currency')->where('id_pegawai', Auth::user()->id);
                if ($request->id_currency) {
                    $transaksi->where('currency_id', $request->id_currency);
                }
                $transaksi = $transaksi->where('jenis_transaksi', 'Jual')->where('tanggal_transaksi', Carbon::today())->get();
                $total = $transaksi->sum('total');
                $jumlah = $transaksi->count();
                $today = Carbon::now()->format('d-M-Y');
                // return $transaksi;

                if (count($transaksi) == 0) {
                    Alert::warning('Tidak Ditemukan Data', 'Data yang Anda Cari Tidak Ditemukan');
                    return redirect()->back();
                } else {
                    if ($request->radio_input == 'pdf') {
                        $pdf = Pdf::loadview('export.pdf-harian', ['transaksi' => $transaksi, 'total' => $total, 'jumlah' => $jumlah, 'today' => $today]);
                        return $pdf->download('report-harian ' . $today . ' ' . Auth::user()->name . ' .pdf');
                        Alert::success('Berhasil', 'Data Transaksi Berhasil Didownload');
                    } else {
                        return new ExcelHarian($transaksi);
                    }
                }
            } else {
                $transaksi = Transaksi::with('Pegawai')->join('tb_detail_transaksi', 'tb_transaksi.id_transaksi', 'tb_detail_transaksi.id_transaksi')
                    ->join('tb_currency', 'tb_detail_transaksi.currency_id', 'tb_currency.id_currency')
                    ->where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->OrderBy('tb_transaksi.updated_at');
                if ($request->id_currency) {
                    $transaksi->where('currency_id', $request->id_currency);
                }
                if ($request->id_pegawai) {
                    $transaksi->where('id_pegawai', $request->id_pegawai);
                }
                $transaksi = $transaksi->where('jenis_transaksi', 'Jual')->get();
                $total = $transaksi->sum('total');
                $jumlah = $transaksi->count();
                $today = Carbon::now()->format('d-M-Y');

                if (count($transaksi) == 0) {
                    Alert::warning('Tidak Ditemukan Data', 'Data yang Anda Cari Tidak Ditemukan');
                    return redirect()->back();
                } else {
                    if ($request->radio_input == 'pdf') {
                        $pdf = Pdf::loadview('export.pdf-harian-owner', ['transaksi' => $transaksi, 'total' => $total, 'jumlah' => $jumlah, 'today' => $today]);
                        if ($request->id_pegawai) {
                            return $pdf->download('report-harian ' . $today . ' ' . $transaksi[0]->Pegawai->name . ' .pdf');
                        }
                        return $pdf->download('report-harian ' . $today . ' .pdf');
                        Alert::success('Berhasil', 'Data Transaksi Berhasil Didownload');
                    } else {
                        return new ExcelHarianOwner($transaksi);
                    }
                }
            }
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
        $currency = MasterCurrency::orderBy('jenis_kurs', 'ASC')->get();
        $tes = ModalTransaksi::where('tanggal_modal', Carbon::now()->format('Y-m-d'))->first();
        if (empty($tes)) {
            Alert::warning('Belum Mengajukan Modal', 'Anda Belum Mengajukan Modal');
            return redirect()->route('modal.index');
        } else {
            if ($tes->status_modal == 'Pending') {
                Alert::warning('Modal Diproses, Mohon Menunggu', 'Pengajuan Modal Anda Hari Ini Belum Diproses oleh Owner');
                return redirect()->route('modal.index');
            } elseif ($tes->status_modal == 'Tolak') {
                Alert::warning('Modal Ditolak', 'Pengajuan Modal Anda Hari Ini Ditolak oleh Owner, Edit Data Modal');
                return redirect()->route('modal.index');
            } else {
                $modal = ModalTransaksi::where('tanggal_modal', Carbon::now()->format('Y-m-d'))->where('status_modal', 'Terima')->first();
            }
        }
        $today = Carbon::now()->format('d M Y H:i:s');
        $today_format = Carbon::now()->format('Y-m-d');
        $jumlah_transaksi = Transaksi::where('id_pegawai', Auth::user()->id)->where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->count();
        $total_transaksi = Transaksi::where('id_pegawai', Auth::user()->id)->where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->sum('total');
        // $id = Transaksi::getId();
        $id = Transaksi::getIdBeli();
        foreach ($id as $value);
        $idlama = $value->id_transaksi;
        $idbaru = $idlama + 1;
        $blt = date('ymd');
        $id = Auth::user()->id;
        $kode_transaksi = 'RV' . $blt . '-' . $idbaru;

        return view('pages.transaksi.create', compact('currency', 'modal', 'today', 'kode_transaksi', 'today_format', 'idbaru', 'jumlah_transaksi', 'total_transaksi'));
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
            $transaksi = new Transaksi();
            $transaksi->kode_transaksi = $request->kode_transaksi;
            $transaksi->tanggal_transaksi = $request->tanggal_transaksi;
            $transaksi->id_modal = $request->id_modal;
            $transaksi->total = $request->total;
            $transaksi->id_pegawai = Auth::user()->id;
            $transaksi->nama_customer = $request->nama_customer;
            $transaksi->nomor_passport = $request->nomor_passport;
            $transaksi->negara_asal = $request->asal_negara;
            $transaksi->jenis_transaksi = 'Beli';
            $transaksi->save();

            // $transaksi->detailTransaksi()->insert($request->detail);
            foreach ($request->detail as $key) {
                $det = new DetailTransaksi;
                $det->currency_id = $key['currency_id'];
                $det->jumlah_currency = $key['jumlah_currency'];
                $det->jumlah_tukar = $key['jumlah_tukar'];
                $det->total_tukar = $key['total_tukar'];
                $det->id_transaksi = $transaksi->id_transaksi;
                $det->save();

                $jurnal = new Jurnal();
                $jurnal->id_transaksi = $transaksi->id_transaksi;
                $jurnal->tanggal_jurnal = $transaksi->tanggal_transaksi;
                $jurnal->id_currency = $key['currency_id'];
                $jurnal->kurs = $key['jumlah_currency'];
                $jurnal->jumlah_tukar = $key['jumlah_tukar'];
                $jurnal->total_tukar = $key['total_tukar'];
                $jurnal->jenis_jurnal = 'Debit';
                $jurnal->id_pegawai = Auth::user()->id;
                $jurnal->save();

                $cry = MasterCurrency::where('id_currency', $key['currency_id'])->first();
                if ($cry) {
                    $cry->jumlah_valas += $key['jumlah_tukar'];
                    $cry->update();
                }
            }

            $modal = ModalTransaksi::find($request->id_modal);
            $perhitungan = $modal->riwayat_modal - $request->total;
            $modal->riwayat_modal = $perhitungan;
            $modal->save();
            DB::commit();
            Alert::success('Berhasil', 'Data Transaksi Berhasil Ditambahkan');
            return $transaksi;
        } catch (\Throwable $th) {
            dd($th);
            DB::rollBack();
            Alert::warning('Error', 'Internal Server Error, Try Refreshing The Page');
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
        $transaksi = Transaksi::with('Pegawai','detailTransaksi.Currency')->find($id);
        $detail = DetailTransaksi::where('id_transaksi', $id)->get();
        return view('pages.transaksi.detail', compact('transaksi','detail'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $transaksi = Transaksi::with('detailTransaksi.Currency')->find($id);
        $currency = MasterCurrency::orderBy('jenis_kurs','ASC')->get();
        $modal = ModalTransaksi::where('tanggal_modal', Carbon::now()->format('Y-m-d'))->where('status_modal','Terima')->first();
        $today = Carbon::now()->format('d M Y H:i:s');
        $today_format = Carbon::now()->format('Y-m-d');
        $jumlah_transaksi = Transaksi::where('id_pegawai', Auth::user()->id)->where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->count();
        $total_transaksi = Transaksi::where('id_pegawai', Auth::user()->id)->where('tanggal_transaksi', Carbon::now()->format('Y-m-d'))->sum('total');

        return view('pages.transaksi.edit', compact('transaksi','currency','modal','today','today_format','jumlah_transaksi','total_transaksi'));
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
            $transaksi = Transaksi::find($id);
            $new_log = new LogEdit();
            $new_log->id_pegawai = Auth::user()->id;
            $new_log->id_modal = $transaksi->id_modal;
            $new_log->tanggal_transaksi = $transaksi->tanggal_transaksi;
            $new_log->kode_transaksi = $transaksi->kode_transaksi;
            $new_log->total = $transaksi->total;
            $new_log->jenis_log = 'Edit';
            $new_log->keterangan_log = $request->keterangan_log;
            $new_log->save();

            $getDetail = DetailTransaksi::where('id_transaksi', $transaksi->id_transaksi)->get();
            foreach ($getDetail as $item) {
                $new_det_log = new LogEditDetail();
                $new_det_log->id_log = $new_log->id_log;
                $new_det_log->currency_id = $item->currency_id;
                $new_det_log->jumlah_currency = $item->jumlah_currency;
                $new_det_log->jumlah_tukar = $item->jumlah_tukar;
                $new_det_log->total_tukar = $item->total_tukar;
                $new_det_log->save();

                $cry = MasterCurrency::where('id_currency', $item['currency_id'])->first();
                if ($cry) {
                    // Decrement jumlah_valas
                    $cry->jumlah_valas += $item['jumlah_tukar'];
                    $cry->update();
                }
            }

            $transaksi->total = $request->total;
            $transaksi->nama_customer = $request->nama_customer;
            $transaksi->nomor_passport = $request->nomor_passport;
            $transaksi->negara_asal = $request->asal_negara;
            $transaksi->id_pegawai = Auth::user()->id;
            $transaksi->save();

            $transaksi->detailTransaksi()->delete();
            $transaksi->detailTransaksi()->insert($request->detail);

            foreach ($request->detail as $key) {
                $jurnal = Jurnal::where('id_transaksi', $transaksi->id_transaksi)->where('id_currency', $key['currency_id'])->first();
                if (empty($jurnal)) {
                    $jurnal = new Jurnal();
                    $jurnal->id_transaksi = $transaksi->id_transaksi;
                    $jurnal->tanggal_jurnal = $transaksi->tanggal_transaksi;
                    $jurnal->id_currency = $key['currency_id'];
                    $jurnal->kurs = $key['jumlah_currency'];
                    $jurnal->jumlah_tukar = $key['jumlah_tukar'];
                    $jurnal->total_tukar = $key['total_tukar'];
                    $jurnal->jenis_jurnal = 'Debit';
                    $jurnal->id_pegawai = Auth::user()->id;
                    $jurnal->save();
                } else {
                    $jurnal->id_transaksi = $transaksi->id_transaksi;
                    $jurnal->tanggal_jurnal = $transaksi->tanggal_transaksi;
                    $jurnal->id_currency = $key['currency_id'];
                    $jurnal->kurs = $key['jumlah_currency'];
                    $jurnal->jumlah_tukar = $key['jumlah_tukar'];
                    $jurnal->total_tukar = $key['total_tukar'];
                    $jurnal->jenis_jurnal = 'Debit';
                    $jurnal->id_pegawai = Auth::user()->id;
                    $jurnal->save();
                }
                $cry = MasterCurrency::where('id_currency', $key['currency_id'])->first();
                if ($cry) {
                    $cry->jumlah_valas += $key['jumlah_tukar'];
                    $cry->update();
                }
            }
            $modal = ModalTransaksi::find($request->id_modal);
            $modal->riwayat_modal = $request->jumlah_modal;
            $modal->save();
            DB::commit();

            Alert::success('Berhasil', 'Data Transaksi Berhasil Diedit');
            return $request;
        } catch (\Throwable $th) {
            DB::rollBack();
            Alert::warning('Error', 'Internal Server Error, Try Refreshing The Page');
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
        //
    }

    public function hapus(Request $request)
    {
        try {
            DB::beginTransaction();
            $transaksi = Transaksi::find($request->transaksi_id);
            $log = new LogEdit;
            $log->id_pegawai = $transaksi->id_pegawai;
            $log->id_modal = $transaksi->id_modal;
            $log->jenis_log = 'Delete';
            $log->keterangan_log = $request->keterangan_log;
            $log->tanggal_transaksi = $transaksi->tanggal_transaksi;
            $log->kode_transaksi = $transaksi->kode_transaksi;
            $log->total = $transaksi->total;
            $log->save();

            $getDetail = DetailTransaksi::where('id_transaksi', $request->transaksi_id)->get();
            foreach ($getDetail as $item) {
                $new_det_log = new LogEditDetail();
                $new_det_log->id_log = $log->id_log;
                $new_det_log->currency_id = $item->currency_id;
                $new_det_log->jumlah_currency = $item->jumlah_currency;
                $new_det_log->jumlah_tukar = $item->jumlah_tukar;
                $new_det_log->total_tukar = $item->total_tukar;
                $new_det_log->save();
                $cry = MasterCurrency::where('id_currency', $item['currency_id'])->first();
                if ($cry) {
                    if ($transaksi->jenis_transaksi  == 'Jual') {
                        $cry->jumlah_valas += $item['jumlah_tukar'];
                    } else {
                        $cry->jumlah_valas -= $item['jumlah_tukar'];
                    }
                    $cry->update();
                }
            }

            $jurnal = Jurnal::where('id_transaksi', $transaksi->id_transaksi)->get();
            foreach ($jurnal as $tes) {
                $tes->delete();
            }
            $detail = DetailTransaksi::where('id_transaksi', $transaksi->id_transaksi)->get();
            foreach ($detail as $s) {
                $s->delete();
            }
            $modal = ModalTransaksi::where('id_modal', $transaksi->id_modal)->first();
            $perhitungan = $modal->riwayat_modal + $transaksi->total;
            $modal->riwayat_modal = $perhitungan;
            $modal->save();
            $transaksi->delete();
            DB::commit();
            Alert::success('Berhasil', 'Data Transaksi Berhasil Terhapus');
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            Alert::warning('Error', 'Internal Server Error, Try Refreshing The Page');
            return redirect()->back();
        }
    }
}
