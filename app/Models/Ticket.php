<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    public const TOPICS = ['Git', 'Laravel', 'Blade', 'Database', 'Setup'];

    public const STATUSES = ['waiting', 'helping', 'done'];

    protected $fillable = [
        'name',
        'topic',
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
}
