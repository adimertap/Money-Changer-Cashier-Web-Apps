<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSupportingDocumentsToTbTransaksi extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('tb_transaksi')) {
            return;
        }

        Schema::table('tb_transaksi', function (Blueprint $table) {
            if (!Schema::hasColumn('tb_transaksi', 'supporting_document_type')) {
                $table->string('supporting_document_type', 100)->nullable();
            }
            if (!Schema::hasColumn('tb_transaksi', 'supporting_document_number')) {
                $table->string('supporting_document_number', 100)->nullable();
            }
            if (!Schema::hasColumn('tb_transaksi', 'supporting_document_date')) {
                $table->date('supporting_document_date')->nullable();
            }
            if (!Schema::hasColumn('tb_transaksi', 'supporting_document_note')) {
                $table->text('supporting_document_note')->nullable();
            }
            if (!Schema::hasColumn('tb_transaksi', 'supporting_document_file')) {
                $table->string('supporting_document_file', 255)->nullable();
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('tb_transaksi')) {
            return;
        }

        Schema::table('tb_transaksi', function (Blueprint $table) {
            $columns = [
                'supporting_document_type',
                'supporting_document_number',
                'supporting_document_date',
                'supporting_document_note',
                'supporting_document_file',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('tb_transaksi', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
