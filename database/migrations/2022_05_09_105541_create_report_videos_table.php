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
        Schema::create('report_videos', function (Blueprint $table) {
            $table->id();
            $table->string('reason')->nullable();
            $table->text('detail')->nullable();
            $table->foreignId('video_id')->constrained('videos')->onDelete('cascade')->onUpdate('cascade');
            $table->string('status')->nullable();
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
        Schema::dropIfExists('report_videos');
    }
};
