<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('refund', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_id');
            $table->unsignedBigInteger('user_id');

            // Optional: add foreign key constraints
            $table->foreign('payment_id')->references('payment_id')->on('payment')->onDelete('cascade');
            $table->foreign('user_id')->references('customer_id')->on('customer')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('refund', function (Blueprint $table) {
            // Drop foreign keys first (if they exist)
            $table->dropForeign(['payment_id']);
            $table->dropForeign(['user_id']);

            // Then drop columns
            $table->dropColumn(['payment_id', 'user_id']);
        });
    }
};
