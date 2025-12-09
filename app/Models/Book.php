<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Book extends Model
{
    protected $fillable = [
        'title',
        'author_id',
        'category_id',
        'isbn',
        'published_year',
        'total_copies',
        'available_copies',
        'book_type',
        'file_path',
        'file_name',
        'file_size',
        'cover_image',
        'description',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'is_featured',
        'download_count',
        'view_count',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function borrows(): HasMany
    {
        return $this->hasMany(Borrow::class);
    }

    public function bookRequests(): HasMany
    {
        return $this->hasMany(BookRequest::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function reserves(): HasMany
    {
        return $this->hasMany(Reserve::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get average rating for the book
     */
    public function getAverageRatingAttribute(): float
    {
        return Cache::remember("book_{$this->id}_rating", 3600, function () {
            return $this->reviews()
                ->approved()
                ->avg('rating') ?? 0;
        });
    }

    /**
     * Get total reviews count
     */
    public function getReviewsCountAttribute(): int
    {
        return Cache::remember("book_{$this->id}_reviews_count", 3600, function () {
            return $this->reviews()->approved()->count();
        });
    }

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
            'is_featured' => 'boolean',
            'download_count' => 'integer',
            'view_count' => 'integer',
        ];
    }

    /**
     * Check if book is digital
     */
    public function isDigital(): bool
    {
        return in_array($this->book_type, ['digital', 'both']);
    }

    /**
     * Check if book is physical
     */
    public function isPhysical(): bool
    {
        return in_array($this->book_type, ['physical', 'both']);
    }

    /**
     * Check if book is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) return 'N/A';
        
        $bytes = (int) $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
