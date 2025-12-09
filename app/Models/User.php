<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'student_id',
        'course',
        'year_level',
        'phone',
        'is_approved',
        'approved_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_approved' => 'boolean',
            'approved_at' => 'datetime',
        ];
    }

    /**
     * Check if user is a librarian/admin
     */
    public function isLibrarian(): bool
    {
        return $this->role === 'librarian';
    }

    /**
     * Check if user is library staff/encoder
     */
    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    /**
     * Check if user is a student
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Check if user has admin privileges (librarian or staff)
     */
    public function isAdmin(): bool
    {
        return $this->isLibrarian() || $this->isStaff();
    }

    /**
     * Get user's full name
     */
    public function getFullNameAttribute(): string
    {
        return $this->name;
    }

    /**
     * Get user's role display name
     */
    public function getRoleDisplayAttribute(): string
    {
        return match($this->role) {
            'librarian' => 'Librarian',
            'staff' => 'Library Staff',
            'student' => 'Student',
            default => 'Unknown'
        };
    }

    /**
     * Get user's book requests
     */
    public function bookRequests(): HasMany
    {
        return $this->hasMany(BookRequest::class);
    }

    /**
     * Get user's borrows
     */
    public function borrows(): HasMany
    {
        return $this->hasMany(Borrow::class);
    }

    /**
     * Get announcements created by user
     */
    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class, 'created_by');
    }

    /**
     * Get theses approved by user
     */
    public function approvedTheses(): HasMany
    {
        return $this->hasMany(Thesis::class, 'approved_by');
    }

    /**
     * Get books approved by user
     */
    public function approvedBooks(): HasMany
    {
        return $this->hasMany(Book::class, 'approved_by');
    }

    /**
     * Get user's favorite books
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Check if user has favorited a book
     */
    public function isFavorited(Book $book): bool
    {
        return $this->favorites()->where('book_id', $book->id)->exists();
    }

    /**
     * Get user's book reservations
     */
    public function reserves(): HasMany
    {
        return $this->hasMany(Reserve::class);
    }

    /**
     * Get user's reviews
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
