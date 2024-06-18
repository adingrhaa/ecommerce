<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeedbackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->integer('id_member');  // Foreign key for member
            $table->json('id_product');               // Array of product IDs
            $table->tinyInteger('rating');            // Rating
            $table->text('comment')->nullable();                  // Comment
            $table->string('gambar')->nullable();      // Nullable image
            $table->timestamps();

            // Foreign key constraint (assuming you have a members table)
            // $table->foreign('id_member')->references('id')->on('members')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('feedback');
    }
}
