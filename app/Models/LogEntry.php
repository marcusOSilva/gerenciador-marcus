<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class LogEntry extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'logs';

    protected $fillable = [
        'user_id',
        'user_email',
        'ip',
        'route',
        'method',
        'action',
        'payload',
        'created_at',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
