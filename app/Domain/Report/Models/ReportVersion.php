<?php

namespace App\Domain\Report\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportVersion extends Model
{
    public $timestamps = false;
    const CREATED_AT = 'created_at';

    protected $fillable = [
        'report_id', 'version', 'data_snapshot',
        'changed_by', 'change_reason',
    ];

    protected function casts(): array
    {
        return [
            'data_snapshot' => 'array',
            'created_at'    => 'datetime',
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }
}