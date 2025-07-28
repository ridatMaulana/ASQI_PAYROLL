<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bpjs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('no_bpjs')->unique();
            $table->enum('jenis', ['Kesehatan', 'Ketenagakerjaan']);
            $table->enum('status', ['Aktif', 'Non-Aktif']);
            $table->decimal('persentase_potongan', 5, 2)->default(1.0); // Langsung dimasukkan di sini
            $table->decimal('iuran_perusahaan', 12, 2)->default(0);
            $table->decimal('iuran_karyawan', 12, 2)->default(0);
            $table->date('tanggal_aktif')->nullable();
            $table->date('tanggal_nonaktif')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'jenis', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('bpjs');
    }
};
