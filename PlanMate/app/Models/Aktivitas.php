<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'kategori_id', 'judul', 'deskripsi', 'deadline', 'prioritas', 'status'])]
class Aktivitas extends Model
{
    protected $table = 'aktivitas';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }
}