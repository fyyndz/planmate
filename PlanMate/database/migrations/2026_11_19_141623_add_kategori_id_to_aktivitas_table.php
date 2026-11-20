<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('aktivitas', 'kategori_id')) {
            Schema::table('aktivitas', function (Blueprint $table) {
                $table->foreignId('kategori_id')
                      ->nullable()
                      ->constrained('kategoris')
                      ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('aktivitas', 'kategori_id')) {
            Schema::table('aktivitas', function (Blueprint $table) {
                $table->dropForeign(['kategori_id']);
                $table->dropColumn('kategori_id');
            });
        }
    }
};