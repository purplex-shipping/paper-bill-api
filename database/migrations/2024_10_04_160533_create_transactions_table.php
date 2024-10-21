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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['deposit', 'withdraw', 'transfer'])->default('deposit');
            $table->decimal('amount', 15, 2);
            $table->string('description')->nullable();
            $table->string('request_id')->nullable();
            $table->string('transaction_id');
            $table->enum('payment_gateway', ['coin', 'online','wallet']);
            $table->string('transaction_type')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
