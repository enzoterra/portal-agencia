<?php

namespace App\Support\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToClient
{
    public static function bootBelongsToClient(): void
    {
        static::addGlobalScope('client', function (Builder $builder) {
            if (auth()->check() && auth()->user()->isClient()) {
                $builder->where(
                    (new static)->getTable() . '.client_id',
                    auth()->user()->client_id
                );
            }
        });
    }
}