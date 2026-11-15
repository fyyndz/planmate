<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'aktivitas_id', 'judul', 'deskripsi', 'deadline', 'progres'])]
class Goal extends Model
{
    protected function casts(): array
    {
        return [
            'deadline' => 'datetime',
            'progres'  => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function aktivitas()
    {
        return $this->belongsTo(Aktivitas::class);
    }

    public function tahapan()
    {
        return $this->hasMany(GoalTahapan::class)->orderBy('id');
    }

    /**
     * Bentuk data yang dipakai halaman goals (sama seperti format lama di store.js).
     */
    public function toFrontend(): array
    {
        return [
            'id'          => $this->id,
            'userId'      => $this->user_id,
            'aktivitasId' => $this->aktivitas_id,
            'judul'       => $this->judul,
            'deskripsi'   => $this->deskripsi ?? '',
            'deadline'    => $this->deadline?->format('Y-m-d\TH:i') ?? '',
            'progres'     => (int) $this->progres,
            'targetKecil' => $this->tahapan->map(fn ($t) => [
                'id'   => $t->id,
                'text' => $t->text,
                'done' => (bool) $t->done,
            ])->values()->all(),
            'createdAt'   => $this->created_at?->toIso8601String(),
        ];
    }

    /**
     * Semua goals milik satu user, dalam format halaman.
     */
    public static function forFrontend(int $userId): array
    {
        return static::with('tahapan')
            ->where('user_id', $userId)
            ->orderBy('id')
            ->get()
            ->map(fn (Goal $g) => $g->toFrontend())
            ->all();
    }
}
