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

    public function scopeMilikUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /** Ganti seluruh tahapan dengan daftar yang dikirim. */
    public function gantiTahapan(array $rows): void
    {
        $this->tahapan()->delete();
        $this->tahapan()->createMany(
            collect($rows)->map(fn ($r) => [
                'text' => $r['text'],
                'done' => (bool) ($r['done'] ?? false),
            ])->all()
        );
    }

    /** Ada tahapan: progres = persen selesai. Tidak ada: pakai nilai manual. */
    public function sinkronProgres(?int $manual = null): void
    {
        $this->load('tahapan');
        $total = $this->tahapan->count();

        $progres = $total > 0
            ? (int) round($this->tahapan->where('done', true)->count() / $total * 100)
            : ($manual ?? (int) $this->progres);

        $this->update(['progres' => max(0, min(100, $progres))]);
    }

    /** Slider update cepat. */
    public function setProgres(int $p): void
    {
        $this->update(['progres' => $p]);

        if (in_array($p, [0, 100], true) && $this->tahapan()->exists()) {
            $this->tahapan()->update(['done' => $p === 100]);
        }
    }

    // Di dalam app/Models/Goal.php
    public static function forFrontend(?int $userId = null): array
    {
        $userId ??= auth()->id();

        if (! $userId) {
            return [];
        }

        return static::milikUser($userId)
            ->with(['aktivitas', 'tahapan'])
            ->orderBy('deadline')
            ->get()
            ->map(fn (self $goal) => [
                'id'           => $goal->id,
                'judul'        => $goal->judul,
                'deskripsi'    => $goal->deskripsi,
                'deadline'     => $goal->deadline?->toISOString(),
                'progres'      => $goal->progres,
                'aktivitas_id' => $goal->aktivitas_id,
                'tahapan'      => $goal->tahapan->map(fn ($t) => [
                    'id'   => $t->id,
                    'text' => $t->text,
                    'done' => (bool) $t->done,
                ])->values()->all(),
            ])
            ->all();
    }
}

