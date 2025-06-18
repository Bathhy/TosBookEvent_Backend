<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('report', function (Blueprint $table) {
            // Drop old columns if they exist
            if (Schema::hasColumn('report', 'report_name')) {
                $table->dropColumn('report_name');
            }
            if (Schema::hasColumn('report', 'report_data')) {
                $table->dropColumn('report_data');
            }
            if (Schema::hasColumn('report', 'generated_date')) {
                $table->dropColumn('generated_date');
            }
            if (Schema::hasColumn('report', 'created_at')) {
                $table->dropColumn('created_at');
            }
            if (Schema::hasColumn('report', 'updated_at')) {
                $table->dropColumn('updated_at');
            }

            // Add new columns
            $table->date('date_from')->after('report_id');
            $table->date('date_to')->after('date_from');
            $table->integer('total_booking')->default(0)->after('date_to');
            $table->decimal('total_income', 10, 2)->default(0)->after('total_booking');
            $table->decimal('total_refunds', 10, 2)->default(0)->after('total_income');
            $table->decimal('net_income', 10, 2)->default(0)->after('total_refunds');
            $table->timestamp('report_create')->useCurrent()->after('net_income');
        });
    }

    public function down()
    {
        Schema::table('report', function (Blueprint $table) {
            // Drop new columns
            $table->dropColumn([
                'date_from',
                'date_to',
                'total_booking',
                'total_income',
                'total_refunds',
                'net_income',
                'report_create',
            ]);

            // Add old columns back
            $table->string('report_name', 255);
            $table->text('report_data');
            $table->timestamp('generated_date');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
};
