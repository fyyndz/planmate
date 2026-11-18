<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lampirans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('aktivitas_id')->nullable()->index();
            $table->string('judul_lampiran');
            $table->string('nama_file_asli');
            $table->string('path_file');
            $table->string('ekstensi', 20);
            $table->unsignedBigInteger('ukuran_file'); // dalam bytes
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lampirans');
    }
};