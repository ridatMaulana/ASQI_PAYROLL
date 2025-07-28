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
        Schema::table('transaksi_kas', function (Blueprint $table) {
            // Mengubah tipe kolom 'tanggal' menjadi DATETIME
            $table->dateTime('tanggal')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi_kas', function (Blueprint $table) {
            // Mengembalikan tipe kolom menjadi DATE jika di-rollback
            $table->date('tanggal')->change();
        });
    }
};
