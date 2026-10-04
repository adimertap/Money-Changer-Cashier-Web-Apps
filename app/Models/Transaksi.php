<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Transaksi extends Model
{
    use SoftDeletes, Concerns\BelongsToCabang;

    protected $table = "tb_transaksi";

    protected $primaryKey = 'id_transaksi';

    protected $fillable = [
        'id_pegawai',
        'id_modal',
        'tanggal_transaksi',
        'kode_transaksi',
        'total',
        'nama_customer',
        'nomor_passport',
        'negara_asal',
        'jenis_transaksi',
        'cabang_id',
        'supporting_document_type',
        'supporting_document_number',
        'supporting_document_date',
        'supporting_document_note',
        'supporting_document_file'
    ];

    protected $hidden = [
        'updated_at',
        'created_at',
        'deleted_at'
    ];

    public $timestamps = true;

    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class, 'id_transaksi','id_transaksi');
    }

    public function Pegawai()
    {
        return $this->belongsTo(User::class, 'id_pegawai', 'id')->withTrashed();
    }

    public function Modal()
    {
        return $this->belongsTo(ModalTransaksi::class, 'id_modal', 'id_modal');
    }

    public static function getId()
    {
        $getId = DB::table('tb_transaksi')->orderBy('id_transaksi', 'DESC')->take(1)->get();
        if (count($getId) > 0) return $getId;
        return (object)[
            (object)[
                'id_transaksi' => 0
            ]
        ];
    }
    public static function getIdBeli()
    {
        $getId = DB::table('tb_transaksi')->where('jenis_transaksi','Beli')->orderBy('id_transaksi', 'DESC')->take(1)->get();
        if (count($getId) > 0) return $getId;
        return (object)[
            (object)[
                'id_transaksi' => 0
            ]
        ];
    }

    public static function getIdJual()
    {
        $getId = DB::table('tb_transaksi')->where('jenis_transaksi','Jual')->orderBy('id_transaksi', 'DESC')->take(1)->get();
        if (count($getId) > 0) return $getId;
        return (object)[
            (object)[
                'id_transaksi' => 0
            ]
        ];
    }

    /**
     * Memastikan data customer (nama, passport, negara) tidak kosong jika ada referensi di database.
     */
    public function healCustomerData()
    {
        $dirty = false;

        // 1. Jika nama_customer kosong
        if (empty($this->nama_customer)) {
            $matchedCustomer = null;

            // Cek berdasarkan nomor passport
            if (!empty($this->nomor_passport)) {
                $passport = trim($this->nomor_passport);
                $cleanPassport = preg_replace('/[^a-zA-Z0-9]/', '', $passport);
                $matchedCustomer = MasterCustomer::where(function ($q) use ($passport, $cleanPassport) {
                    $q->whereRaw('LOWER(TRIM(passport)) = ?', [mb_strtolower($passport)])
                        ->orWhereRaw("REPLACE(REPLACE(LOWER(passport), ' ', ''), '-', '') = ?", [mb_strtolower($cleanPassport)])
                        ->orWhereRaw('LOWER(TRIM(nik)) = ?', [mb_strtolower($passport)]);
                })->first();
            }

            // Fallback 1: cari transaksi pada tanggal/pegawai yang sama yang memiliki data customer
            if (!$matchedCustomer) {
                $prevWithCustomer = self::where('id_pegawai', $this->id_pegawai)
                    ->where('tanggal_transaksi', $this->tanggal_transaksi)
                    ->whereNotNull('nama_customer')
                    ->where('nama_customer', '!=', '')
                    ->where('id_transaksi', '!=', $this->id_transaksi)
                    ->orderBy('id_transaksi', 'desc')
                    ->first();

                if ($prevWithCustomer) {
                    $this->nama_customer = $prevWithCustomer->nama_customer;
                    if (empty($this->nomor_passport)) {
                        $this->nomor_passport = $prevWithCustomer->nomor_passport;
                    }
                    if (empty($this->negara_asal)) {
                        $this->negara_asal = $prevWithCustomer->negara_asal;
                    }
                    $dirty = true;
                } else {
                    // Fallback 2: ambil customer terakhir dari master data
                    $latestCustomer = MasterCustomer::orderBy('customer_id', 'desc')->first();
                    if ($latestCustomer) {
                        $this->nama_customer = $latestCustomer->name;
                        if (empty($this->nomor_passport)) {
                            $this->nomor_passport = $latestCustomer->passport;
                        }
                        if (empty($this->negara_asal)) {
                            $this->negara_asal = $latestCustomer->country;
                        }
                        $dirty = true;
                    }
                }
            } else {
                $this->nama_customer = $matchedCustomer->name;
                if (empty($this->negara_asal)) {
                    $this->negara_asal = $matchedCustomer->country;
                }
                $dirty = true;
            }
        }

        // 2. Jika nomor passport kosong tetapi nama customer ada
        if (empty($this->nomor_passport) && !empty($this->nama_customer)) {
            $cust = MasterCustomer::whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower(trim($this->nama_customer))])->first();
            if ($cust && !empty($cust->passport)) {
                $this->nomor_passport = $cust->passport;
                if (empty($this->negara_asal)) {
                    $this->negara_asal = $cust->country;
                }
                $dirty = true;
            }
        }

        // 3. Jika negara asal kosong
        if (empty($this->negara_asal)) {
            $cust = null;
            if (!empty($this->nomor_passport)) {
                $cust = MasterCustomer::whereRaw('LOWER(TRIM(passport)) = ?', [mb_strtolower(trim($this->nomor_passport))])->first();
            }
            if (!$cust && !empty($this->nama_customer)) {
                $cust = MasterCustomer::whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower(trim($this->nama_customer))])->first();
            }

            if ($cust && !empty($cust->country)) {
                $this->negara_asal = $cust->country;
                $dirty = true;
            }
        }

        if ($dirty) {
            $this->saveQuietly();
        }

        return $this;
    }
}

