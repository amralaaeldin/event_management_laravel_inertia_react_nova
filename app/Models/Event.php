<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

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

    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_user_attendance')->withTimestamps();
    }

    public function wishlistUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_user_wishlist')->withTimestamps();
    }
}
