<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkedHour extends Model
{
    protected $table = 'worked_hours';
    protected $primaryKey = 'id';
    public $timestamps = false;
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
