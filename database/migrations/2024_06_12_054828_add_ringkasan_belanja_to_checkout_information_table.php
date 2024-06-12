<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRingkasanBelanjaToCheckoutInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('checkout_information', function (Blueprint $table) {
            $table->json('ringkasan_belanja')->nullable()->after('delivery');
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
            $table->dropColumn('ringkasan_belanja');
        });
    }
}
