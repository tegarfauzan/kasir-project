<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('store_name')->default('Warung Kopi');
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('receipt_footer')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('bank_name')->default('BRI');
            $table->string('bank_account_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_settings');
    }
};
