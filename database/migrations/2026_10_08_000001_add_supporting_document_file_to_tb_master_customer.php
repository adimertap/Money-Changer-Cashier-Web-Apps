<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSupportingDocumentFileToTbMasterCustomer extends Migration
{
    public function up()
    {
        if (Schema::hasTable('tb_master_customer')) {
            Schema::table('tb_master_customer', function (Blueprint $table) {
                if (!Schema::hasColumn('tb_master_customer', 'supporting_document_file')) {
                    $table->string('supporting_document_file', 255)->nullable();
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('tb_master_customer')) {
            Schema::table('tb_master_customer', function (Blueprint $table) {
                if (Schema::hasColumn('tb_master_customer', 'supporting_document_file')) {
                    $table->dropColumn('supporting_document_file');
                }
            });
        }
    }
}
