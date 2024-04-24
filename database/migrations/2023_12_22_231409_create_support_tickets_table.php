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
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->text('subject');
            $table->text('issue');
            $table->text('video')->nullable();
            $table->text('account_id');
            $table->text('description')->nullable();
            $table->enum('status', ['Open', 'Updated', 'Closed'])->default('Open');
            $table->unsignedBigInteger('location_id');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('location_id')->references('id')->on('locations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
