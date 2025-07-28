<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            // Tambahkan kolom status setelah tanggal_selesai
            // 'aktif' akan menjadi nilai default saat data dibuat
            $table->boolean('aktif')->default(true)->after('tanggal_selesai');
        });
    }

    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn('aktif');
        });
    }
};