<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'kategoris';

    protected $fillable = [
        'user_id',
        'name',
        'color',
        'description',
    ];

    public function aktivitas()
    {
        return $this->hasMany(Aktivitas::class, 'kategori_id');
    }
}