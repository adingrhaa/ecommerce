<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToCheckoutHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('checkout_histories', function (Blueprint $table) {
            $table->string('provinsi')->after('delivery');
            $table->string('kota_kabupaten')->after('provinsi');
            $table->string('kecamatan')->after('kota_kabupaten');
            $table->string('kode_pos')->after('kecamatan');
            $table->string('detail')->nullable()->after('kode_pos');
            $table->string('detail_lainnya')->nullable()->after('detail');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('checkout_histories', function (Blueprint $table) {
            $table->dropColumn('provinsi');
            $table->dropColumn('kota_kabupaten');
            $table->dropColumn('kecamatan');
            $table->dropColumn('kode_pos');
            $table->dropColumn('detail');
            $table->dropColumn('detail_lainnya');
        });
    }
}
