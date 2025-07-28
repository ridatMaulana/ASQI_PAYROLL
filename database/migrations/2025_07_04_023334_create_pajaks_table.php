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
        Schema::create('pajaks', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pajak'); // Contoh: "PPh 21 Golongan I"
            // Gunakan decimal untuk persentase agar lebih presisi
            $table->decimal('persentase', 5, 2); // 5 digit total, 2 di belakang koma (misal 5.00 atau 15.50)
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pajaks');
    }
};
