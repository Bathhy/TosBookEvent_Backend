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
        Schema::table('ticket', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn(['ticket_number', 'qr_code', 'is_used', 'issued_date']);

            // Add new columns
            $table->unsignedBigInteger('user_id')->after('ticket_id');
            $table->unsignedBigInteger('ticket_status_id')->after('booking_id');
            $table->string('column_name')->after('ticket_status_id');
            $table->string('qr_code_text')->after('column_name');

            // Foreign keys
            $table->foreign('user_id')->references('customer_id')->on('customer')->onDelete('cascade');
            $table->foreign('ticket_status_id')->references('id')->on('ticket_status')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('ticket', function (Blueprint $table) {
            // Drop new foreign keys
            $table->dropForeign(['user_id']);
            $table->dropForeign(['ticket_status_id']);

            // Drop new columns
            $table->dropColumn(['user_id', 'ticket_status_id', 'column_name', 'qr_code_text']);

            // Restore old columns
            $table->string('ticket_number');
            $table->string('qr_code')->nullable();
            $table->boolean('is_used')->default(false);
            $table->timestamp('issued_date');
        });
    }
};
