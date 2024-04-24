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
        Schema::create('program_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->double('capacity');
            $table->integer('min_age');
            $table->integer('max_age');
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained(
                table: 'program_levels'
            );
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_levels');
    }
};
