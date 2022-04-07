<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->tinyInteger('is_flagged')->default(0);
            $table->tinyInteger('is_approved')->default(0);
            $table->string('video_title')->nullable();
            $table->string('video_description')->nullable();
            $table->string('hashtags')->nullable();
            $table->string('video_category')->nullable();
            $table->tinyInteger('investment_req')->default(0);
            $table->tinyInteger('allow_comment')->default(0);
            $table->tinyInteger('is_active')->default(1);
            $table->string('video_name')->nullable();
            $table->string('privacy')->nullable();
            $table->string('location')->nullable();
            $table->string('total_comments')->nullable();
            $table->string('total_shares')->nullable();
            $table->string('total_likes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('videos');
    }
};
