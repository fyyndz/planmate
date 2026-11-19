<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aktivitas', function (Blueprint $table) {
            if (!Schema::hasColumn('aktivitas', 'user_id')) {
                $table->foreignId('user_id')
                      ->nullable()
                      ->after('id')
                      ->constrained('users')
                      ->cascadeOnDelete();
            }

            if (!Schema::hasColumn('aktivitas', 'kategori_id')) {
                $table->foreignId('kategori_id')
                      ->nullable()
                      ->constrained('kategoris')
                      ->nullOnDelete();
            }

            if (!Schema::hasColumn('aktivitas', 'judul')) {
                $table->string('judul')->nullable();
            }

            if (!Schema::hasColumn('aktivitas', 'deadline')) {
                $table->dateTime('deadline')->nullable();
            }

            if (!Schema::hasColumn('aktivitas', 'prioritas')) {
                $table->string('prioritas', 20)->default('Sedang');
            }

            if (!Schema::hasColumn('aktivitas', 'status')) {
                $table->string('status', 30)->default('Belum Dimulai');
            }

            if (!Schema::hasColumn('aktivitas', 'deskripsi')) {
                $table->text('deskripsi')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('aktivitas', function (Blueprint $table) {
            if (Schema::hasColumn('aktivitas', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }

            if (Schema::hasColumn('aktivitas', 'kategori_id')) {
                $table->dropForeign(['kategori_id']);
                $table->dropColumn('kategori_id');
            }

            foreach (['judul', 'deadline', 'prioritas', 'status', 'deskripsi'] as $col) {
                if (Schema::hasColumn('aktivitas', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};