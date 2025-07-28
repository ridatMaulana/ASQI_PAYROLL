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
            // Kolom ini akan menjadi "jembatan" ke tabel 'gaji_karyawans'
            $table->foreignId('gaji_karyawan_id')
                  ->nullable()               // Boleh kosong
                  ->after('kas_id')          // Posisikan setelah kolom 'kas_id'
                  ->constrained('gaji_karyawans') // Terhubung ke tabel 'gaji_karyawans'
                  ->onDelete('set null');    // Jika data gaji dihapus, kolom ini menjadi NULL
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi_kas', function (Blueprint $table) {
            // Urutan harus dibalik: hapus foreign key dulu, baru hapus kolomnya.
            $table->dropForeign(['gaji_karyawan_id']);
            $table->dropColumn('gaji_karyawan_id');
        });
    }
};
