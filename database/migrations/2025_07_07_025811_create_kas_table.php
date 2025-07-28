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
        Schema::create('kas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pengguna');
            $table->string('email');    
            $table->enum('peran', ['bank']);
            $table->decimal('saldo', 15, 2)->default(0); // Gunakan decimal untuk uang
            $table->timestamps();
            $table->softDeletes(); // <-- PENTING! Karena Anda menggunakan fitur restore/trash
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kas');
    }
};
