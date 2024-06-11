<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveIdMemberFromCheckoutInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('checkout_information', function (Blueprint $table) {
            // Hapus kolom
            $table->dropColumn('id_member');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('checkout_information', function (Blueprint $table) {
            // Tambahkan kembali kolom
            $table->integer('id_member')->nullable()->after('member_id');
        });
    }
}
