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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function aktivitas()
    {
        return $this->hasMany(Aktivitas::class, 'category_id');
    }

    public function scopeMilikUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}