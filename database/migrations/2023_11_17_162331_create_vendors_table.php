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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('go_high_level_user_id')->nullable()->unique();
            $table->text('stripe_customer_id')->nullable();
            $table->string('company_name')->nullable();
            $table->string('company_email');
            $table->longText('company_website')->nullable();
            $table->longText('company_address')->nullable();
            $table->string('logo')->nullable();
            $table->foreignId('plan_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('phone_number')->nullable();
            $table->boolean('is_new')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
