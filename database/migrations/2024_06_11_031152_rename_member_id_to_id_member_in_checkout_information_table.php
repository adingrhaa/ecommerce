<?php 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RenameMemberIdToIdMemberInCheckoutInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('checkout_information', function (Blueprint $table) {
            // Tambahkan kolom baru
            $table->integer('id_member')->nullable()->after('member_id');
        });

        // Menyalin data dari kolom lama ke kolom baru
        DB::statement('UPDATE checkout_information SET id_member = member_id');

        Schema::table('checkout_information', function (Blueprint $table) {
            // Hapus kolom lama
            $table->dropColumn('member_id');
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
            // Tambahkan kembali kolom lama
            $table->integer('member_id')->nullable()->after('id_member');
        });

        // Menyalin data kembali dari kolom baru ke kolom lama
        DB::statement('UPDATE checkout_information SET member_id = id_member');

        Schema::table('checkout_information', function (Blueprint $table) {
            // Hapus kolom baru
            $table->dropColumn('id_member');
        });
    }
}
