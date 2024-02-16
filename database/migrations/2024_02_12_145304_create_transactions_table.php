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
            $table->integer('wallet_id'); //to get sender
            $table->decimal('tx_amount', 15, 7); //amount transferred
            $table->string('tx_symbol'); //to get symbol of sent currency
            $table->string('rx_symbol'); //to get symbol of recieved currency
            $table->decimal('rx_amount', 15, 7); //amount recieved 
            $table->string('rx_email'); //to get recipient by email
            $table->enum('tx_type', ['deposit', 'transfer']);
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
