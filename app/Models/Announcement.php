<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'content',
        'type',
        'priority',
        'is_published',
        'published_at',
        'expires_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope for published announcements
     */
    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true)
              ->where(function ($q) {
                  $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
              });
    }

    /**
     * Scope for active announcements (not expired)
     */
    public function scopeActive(Builder $query): void
    {
        $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Check if announcement is published
     */
    public function isPublished(): bool
    {
        return $this->is_published && $this->isActive();
    }

    /**
     * Check if announcement is active (not expired)
     */
    public function isActive(): bool
    {
        return !$this->expires_at || $this->expires_at > now();
    }

    /**
     * Get priority badge class
     */
    public function getPriorityBadgeClassAttribute(): string
    {
        return match($this->priority) {
            'low' => 'bg-secondary',
            'medium' => 'bg-info',
            'high' => 'bg-warning',
            'urgent' => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    /**
     * Get type badge class
     */
    public function getTypeBadgeClassAttribute(): string
    {
        return match($this->type) {
            'announcement' => 'bg-primary',
            'event' => 'bg-success',
            'notice' => 'bg-warning',
            default => 'bg-secondary'
        };
    }
}
