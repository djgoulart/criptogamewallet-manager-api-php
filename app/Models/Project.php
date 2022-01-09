<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Uuid;

class Project extends Model
{
    use HasFactory, SoftDeletes, Uuid;

    protected $fillable = [
        'name',
        'url',
        'network',
        'is_active',
    ];

    public $incrementing = false;

    protected $keyType = 'uuid';

    protected $casts = ['id' => 'string', 'is_active' => 'boolean'];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
}
