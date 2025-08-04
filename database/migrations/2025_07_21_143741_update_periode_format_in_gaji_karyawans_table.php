<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('gaji_karyawans')
            ->whereRaw("LENGTH(periode::text) = 7")
            ->update([
                'periode' => DB::raw("CONCAT(periode::text, '-01')::date")
            ]);
    }

    /**
     * Reverse the migrations.
     */
     public function down(): void
    {
        DB::table('gaji_karyawans')
            ->update([
                'periode' => DB::raw("SUBSTRING(periode, 1, 7)")
            ]);
    }
};
