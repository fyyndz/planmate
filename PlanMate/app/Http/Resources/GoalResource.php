<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GoalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'judul'       => $this->judul,
            'deskripsi'   => $this->deskripsi,
            'deadline'    => $this->deadline?->toISOString(),
            'progres'     => (int) $this->progres,
            'aktivitasId' => $this->aktivitas_id,
            'aktivitas'   => $this->whenLoaded('aktivitas', fn () => [
                'id'       => $this->aktivitas->id,
                'judul'    => $this->aktivitas->judul,
                'status'   => $this->aktivitas->status ?? null,
                'deadline' => $this->aktivitas->deadline ?? null,
            ]),
            'targetKecil' => $this->whenLoaded('tahapan', fn () =>
                $this->tahapan->map(fn ($t) => [
                    'id'   => $t->id,
                    'text' => $t->text,
                    'done' => (bool) $t->done,
                ])->values()->all()
            ),
        ];
    }
}