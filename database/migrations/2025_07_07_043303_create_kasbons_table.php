<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('kasbons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('jenis_kasbon');
            $table->decimal('total_kasbon', 12, 2);
            $table->date('periode_mulai')->nullable();
            $table->date('periode_selesai')->nullable();
            $table->decimal('bayar_perbulan', 12, 2)->nullable();
            $table->string('status');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kasbons');
    }
};