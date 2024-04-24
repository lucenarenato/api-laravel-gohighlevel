<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('progress_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('progress_step_id');
            $table->string('name');
            $table->integer('next_task')->nullable();
            $table->integer('prev_task')->nullable();
            $table->integer('orders');
            $table->text('content');
            $table->string('video_id')->nullable();
            $table->string('video_platform')->nullable();
            $table->string('cta_btn')->nullable();
            $table->string('cta_url')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('progress_step_id')->references('id')->on('progress_steps');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_tasks');
    }
};
