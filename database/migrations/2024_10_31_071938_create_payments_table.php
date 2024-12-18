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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_id');
            $table->string('payment_code')->unique();
            $table->integer('quantity');
            $table->decimal('tax', 8, 2);
            $table->decimal('total', 10, 2);
            $table->enum('status', ['Pending', 'Notified', 'Completed', 'Incomplete']);
            $table->date('date_of_payment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
