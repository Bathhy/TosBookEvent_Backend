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
        Schema::create('ticket_status', function (Blueprint $table) {
            $table->id(); // default auto-increment PK named 'id'
            $table->string('name');
            $table->timestamps();
        });

        // Seed default data
        DB::table('ticket_status')->insert([
            ['name' => 'Valid',     'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Used',      'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cancelled', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Expired',   'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_ticket_status');
    }
};
