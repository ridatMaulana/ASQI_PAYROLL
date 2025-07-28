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
        // Perintah untuk mengubah tabel 'kas'
        Schema::table('kas', function (Blueprint $table) {
            // Mengubah tipe kolom 'peran' menjadi string
            // Ini akan mengubah ENUM('admin', 'karyawan') menjadi VARCHAR
            $table->string('peran')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kas', function (Blueprint $table) {
            // Perintah untuk mengembalikan jika di-rollback
            $table->enum('peran', ['admin', 'karyawan'])->change();
        });
    }
};