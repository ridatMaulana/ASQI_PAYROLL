<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel users
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            
            $table->string('nama');
            $table->string('nis')->unique();

            // Kolom baru sesuai permintaan Anda
            $table->string('pendidikan'); // Cth: SMK Telkom, Universitas Gadjah Mada
            $table->text('alasan');      // Cth: Magang, PKL, Penelitian
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};