<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kategoris', function (Blueprint $table) {
            if (!Schema::hasColumn('kategoris', 'user_id')) {
                $table->foreignId('user_id')
                      ->nullable()
                      ->constrained('users')
                      ->cascadeOnDelete();
            }

            if (!Schema::hasColumn('kategoris', 'name')) {
                $table->string('name')->nullable();
            }

            if (!Schema::hasColumn('kategoris', 'color')) {
                $table->string('color', 20)->default('#4f46e5');
            }

            if (!Schema::hasColumn('kategoris', 'description')) {
                $table->text('description')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('kategoris', function (Blueprint $table) {
            if (Schema::hasColumn('kategoris', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }

            foreach (['name', 'color', 'description'] as $col) {
                if (Schema::hasColumn('kategoris', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};