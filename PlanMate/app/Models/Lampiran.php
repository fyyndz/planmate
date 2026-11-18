<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'user_id',
    'aktivitas_id',
    'judul_lampiran',
    'nama_file_asli',
    'path_file',
    'ekstensi',
    'ukuran_file',
    'keterangan',
])]
class Lampiran extends Model
{
    protected $table = 'lampirans';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function toFrontend(): array
    {
        return [
            'id'           => $this->id,
            'aktivitasId'  => $this->aktivitas_id,
            'judul'        => $this->judul_lampiran,
            'namaFileAsli' => $this->nama_file_asli,
            'fileUrl'      => Storage::url($this->path_file),
            'ekstensi'     => strtoupper($this->ekstensi),
            'ukuranFile'   => $this->ukuran_file,
            'keterangan'   => $this->keterangan,
            'createdAt'    => $this->created_at?->toIso8601String(),
        ];
    }

    public static function forFrontend(int $userId): array
    {
        return static::where('user_id', $userId)
            ->latest()
            ->get()
            ->map(fn (self $item) => $item->toFrontend())
            ->all();
    }
}