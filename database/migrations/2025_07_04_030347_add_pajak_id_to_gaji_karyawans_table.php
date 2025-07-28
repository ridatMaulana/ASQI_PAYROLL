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
            // Tambahkan kolom ini setelah kolom tunjangan (atau di mana saja)
            // `nullable` artinya boleh kosong jika tidak ada pajak yang diterapkan
            // `constrained` membuat relasi ke tabel `pajaks`
            $table->foreignId('pajak_id')->nullable()->constrained()->after('tunjangan');
        });
    }

    public function down(): void
    {
        Schema::table('gaji_karyawans', function (Blueprint $table) {
            $table->dropForeign(['pajak_id']); // Hapus foreign key dulu
            $table->dropColumn('pajak_id'); // Baru hapus kolomnya
        });
    }
};
