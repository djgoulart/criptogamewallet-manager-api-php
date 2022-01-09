<?php

namespace App\Traits;

use Ramsey\Uuid\Uuid as RamseyUuid;

trait Uuid
{
    protected static function booted()
    {
        parent::booted();

        static::creating(function ($model) {
            $model->id = (string) RamseyUuid::uuid4();
        });
    }
}
