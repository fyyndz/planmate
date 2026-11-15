<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoalTahapan extends Model
{
    use HasFactory;

    protected $fillable = [
        'goal_id',
        'text',
        'done',
    ];
}