<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkedHour extends Model
{
    protected $fillable = [
        'task',
        'hours',
        'minutes',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
