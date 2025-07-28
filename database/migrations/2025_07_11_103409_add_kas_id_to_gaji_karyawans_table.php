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
        Schema::table('gaji_karyawans', function (Blueprint $table) {
            // Kode ini menambahkan kolom baru ke tabel 'gaji_karyawans'
            $table->foreignId('kas_id')      // 1. Buat kolom bernama 'kas_id'
                  ->nullable()               // 2. Kolom ini boleh kosong (nullable)
                  ->after('user_id')         // 3. Posisikan setelah kolom 'user_id' (opsional, untuk kerapian)
                  ->constrained('kas')       // 4. Jadikan sebagai foreign key yang terhubung ke tabel 'kas'
                  ->onDelete('set null');    // 5. Jika data di tabel 'kas' terhapus, isi kolom ini dengan NULL
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gaji_karyawans', function (Blueprint $table) {
            // Urutan harus dibalik: hapus foreign key dulu, baru hapus kolomnya.
            $table->dropForeign(['kas_id']);
            $table->dropColumn('kas_id');
        });
    }
};
