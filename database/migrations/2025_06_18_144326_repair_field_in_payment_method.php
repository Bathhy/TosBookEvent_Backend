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
        Schema::table('payment_method', function (Blueprint $table) {
            // Make existing columns nullable
            $table->text('description')->nullable()->change();
            $table->boolean('is_active')->nullable()->change();

            // Add new image column
            $table->string('image', 255)->nullable()->after('isActive');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_method', function (Blueprint $table) {
            // Revert columns to NOT nullable (adjust defaults if needed)
            $table->text('description')->nullable(false)->change();
            $table->boolean('is_active')->nullable(false)->change();

            // Drop image column
            $table->dropColumn('image');
        });
    }
};
