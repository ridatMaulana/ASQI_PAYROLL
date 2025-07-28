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
        Schema::table('evaluasis', function (Blueprint $table) {
            $table->dateTime('tanggal_evaluasi')->nullable()->after('total_rata_rata');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluasis', function (Blueprint $table) {
            //
        });
    }
};
