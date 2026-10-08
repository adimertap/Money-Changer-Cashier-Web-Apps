<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDocumentCddFieldsToTbTransaksiAndTbMasterCustomer extends Migration
{
    private $documentColumns = [
        'npwp' => 50,
        'domicile' => 150,
        'income' => 100,
        'job' => 100,
        'company' => 150,
        'company_form' => 150,
        'position' => 100,
        'business_sector' => 100,
        'transaction_purpose' => 150,
        'relationship' => 100,
        'source_of_funds' => 100,
    ];

    public function up()
    {
        // 1. Tambahkan ke tb_master_customer
        if (Schema::hasTable('tb_master_customer')) {
            Schema::table('tb_master_customer', function (Blueprint $table) {
                foreach ($this->documentColumns as $col => $len) {
                    if (!Schema::hasColumn('tb_master_customer', $col)) {
                        $table->string($col, $len)->nullable();
                    }
                }
            });
        }

        // 2. Tambahkan ke tb_transaksi
        if (Schema::hasTable('tb_transaksi')) {
            Schema::table('tb_transaksi', function (Blueprint $table) {
                foreach ($this->documentColumns as $col => $len) {
                    if (!Schema::hasColumn('tb_transaksi', $col)) {
                        $table->string($col, $len)->nullable();
                    }
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('tb_master_customer')) {
            Schema::table('tb_master_customer', function (Blueprint $table) {
                foreach (array_keys($this->documentColumns) as $col) {
                    if (Schema::hasColumn('tb_master_customer', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('tb_transaksi')) {
            Schema::table('tb_transaksi', function (Blueprint $table) {
                foreach (array_keys($this->documentColumns) as $col) {
                    if (Schema::hasColumn('tb_transaksi', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
}
