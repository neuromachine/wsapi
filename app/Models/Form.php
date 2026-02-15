<?php

namespace App\Models;
use App\Enums\FormStatus;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    protected $fillable = [
        'form_key',
        'data',
        'meta',
        'status',
    ];

    protected $casts = [
        'data' => 'array',
        'meta' => 'array',
        'status' => FormStatus::class,
    ];
}
