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
        Schema::create('vendor_progress', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('progress_step_id');
            $table->unsignedBigInteger('vendor_id');
            $table->boolean('active')->default(false);
            $table->boolean('completed')->default(false);
            $table->json('tasks_completed')->nullable();
            $table->timestamps();
        
            $table->foreign('progress_step_id')->references('id')->on('progress_steps');
            $table->foreign('vendor_id')->references('id')->on('vendors');
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_progress');
    }
};
