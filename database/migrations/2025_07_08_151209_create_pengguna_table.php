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
        Schema::create('pengguna', function (Blueprint $table) {
            $table->id();

            // Relasi ke user (One-to-One)
            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->onDelete('cascade');

            // Data dasar
            $table->string('nama');
            $table->string('nis')->unique();

            // Relasi ke jabatan (tanpa divisi)
            $table->foreignId('jabatan_id')
                ->constrained('jabatans')
                ->onDelete('cascade');

            // TAMBAHAN KOLOM BARU YANG ANDA INGINKAN
            $table->string('whatsapp', 15)->nullable()->comment('Format: 628...');
            $table->string('email')->nullable()->unique()->comment('Email aktif pengguna');
            $table->text('alamat')->nullable()->comment('Alamat lengkap pengguna');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengguna');
    }
};