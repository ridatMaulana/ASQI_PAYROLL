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
        Schema::create('gaji_karyawans', function (Blueprint $table) {
            $table->id();
            $table->date('periode'); // format Y-m
            $table->integer('gaji_pokok');
            $table->integer('tunjangan')->default(0);
            $table->integer('pajak')->default(0);
            $table->integer('bpjs')->default(0);
            $table->integer('kasbon')->default(0);
            $table->integer('potongan_absen')->default(0);
            $table->integer('potongan_lainnya')->default(0);
            $table->integer('total_gaji_bersih')->default(0);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gaji_karyawans', function (Blueprint $table) {
            $table->dropForeign('total_gaji_bersih');
        });
    }
};