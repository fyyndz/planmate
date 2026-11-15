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
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // Belum pakai foreign key: aktivitas masih di localStorage. FK ditambahkan nanti setelah aktivitas pindah ke database.
            $table->unsignedBigInteger('aktivitas_id')->nullable()->index();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->dateTime('deadline')->nullable();
            $table->unsignedTinyInteger('progres')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goals');
    }
};
