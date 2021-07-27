<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("comments", function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("comment", 400);
            $table->string("author");
            $table->date("date_posted");
            $table->unsignedBigInteger("video_id");
            $table
                ->foreign("video_id")
                ->references("id")
                ->on("videos");
            $table->unsignedBigInteger("user_id");
            $table
                ->foreign("user_id")
                ->references("id")
                ->on("users")
                ->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("sessions", function (Blueprint $table) {
            $table->dropForeign("user_id");
            $table->dropForeign("video_id");
        });
        Schema::dropIfExists("comments");
    }
}
