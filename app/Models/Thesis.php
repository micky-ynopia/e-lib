<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Thesis extends Model
{
    protected $fillable = [
        'title',
        'abstract',
        'author_name',
        'author_student_id',
        'course',
        'year_level',
        'academic_year',
        'adviser_name',
        'adviser_email',
        'keywords',
        'file_path',
        'file_name',
        'file_size',
        'status',
        'is_public',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'submitted_by',
    ];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
            'approved_at' => 'datetime',
        ];
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Scope for approved theses
     */
    public function scopeApproved(Builder $query): void
    {
        $query->where('status', 'approved');
    }

    /**
     * Scope for public theses
     */
    public function scopePublic(Builder $query): void
    {
        $query->where('is_public', true);
    }

    /**
     * Scope for search by keywords
     */
    public function scopeSearch(Builder $query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('abstract', 'like', "%{$search}%")
              ->orWhere('author_name', 'like', "%{$search}%")
              ->orWhere('adviser_name', 'like', "%{$search}%")
              ->orWhere('keywords', 'like', "%{$search}%");
        });
    }

    /**
     * Check if thesis is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if thesis is public
     */
    public function isPublic(): bool
    {
        return $this->is_public;
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

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-warning',
            'approved' => 'bg-success',
            'rejected' => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    /**
     * Generate citation in APA format
     */
    public function getApaCitationAttribute(): string
    {
        $year = date('Y', strtotime($this->academic_year));
        return "{$this->author_name} ({$year}). {$this->title}. Unpublished {$this->course} thesis, Northeastern Mindanao State University - Cantilan Campus.";
    }

    /**
     * Generate citation in MLA format
     */
    public function getMlaCitationAttribute(): string
    {
        $year = date('Y', strtotime($this->academic_year));
        return "{$this->author_name}. \"{$this->title}.\" Unpublished {$this->course} thesis, Northeastern Mindanao State University - Cantilan Campus, {$year}.";
    }
}
