<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    public const STATUSES = ['waiting', 'helping', 'done'];

    protected $fillable = [
        'name',
        'topic_id',
        'description',
    ];

    public function scopeWaiting(Builder $query): void
    {
        $query->where('status', 'waiting')->orderBy('id');
    }

    public function scopeHelping(Builder $query): void
    {
        $query->where('status', 'helping');
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }
}
