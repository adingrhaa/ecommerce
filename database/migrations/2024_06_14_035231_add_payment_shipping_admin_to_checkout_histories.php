<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentShippingAdminToCheckoutHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('checkout_histories', function (Blueprint $table) {
            $table->string('payment_method')->after('status');
            $table->integer('biaya_pengiriman')->after('payment_method')->default(0);
            $table->integer('biaya_admin')->after('biaya_pengiriman')->default(0);
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
            $table->dropColumn('payment_method');
            $table->dropColumn('biaya_pengiriman');
            $table->dropColumn('biaya_admin');
        });
    }
}
