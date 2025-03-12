<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    const STATUSES = [
        'draft' => 'Draft',
        'published' => 'Published',
    ];

    protected $fillable = [
        'name',
        'start_date_time',
        'description',
        'duration',
        'location',
        'status',
        'capacity',
        'waitlist_capacity',
    ];

    protected $casts = [
        'start_date_time' => 'datetime',
    ];

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUSES['published']);
    }
}
