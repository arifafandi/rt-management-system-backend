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
            $table->foreignId('house_resident_id')->constrained()->onDelete('cascade');
            $table->enum('payment_type', ['security', 'cleaning']);
            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->enum('payment_period', ['monthly', 'yearly']);
            $table->date('period_start');
            $table->date('period_end');
            $table->boolean('is_paid')->default(false);
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
