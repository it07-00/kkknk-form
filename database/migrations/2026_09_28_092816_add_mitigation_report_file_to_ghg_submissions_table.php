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
        Schema::table('ghg_submissions', function (Blueprint $table) {
            $table->string('mitigation_report_path')->nullable()->after('data');
            $table->string('mitigation_report_original_name')->nullable()->after('mitigation_report_path');
            $table->string('mitigation_report_mime_type')->nullable()->after('mitigation_report_original_name');
            $table->unsignedBigInteger('mitigation_report_size')->nullable()->after('mitigation_report_mime_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ghg_submissions', function (Blueprint $table) {
            $table->dropColumn([
                'mitigation_report_path',
                'mitigation_report_original_name',
                'mitigation_report_mime_type',
                'mitigation_report_size',
            ]);
        });
    }
};
