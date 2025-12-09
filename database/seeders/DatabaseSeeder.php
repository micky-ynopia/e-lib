<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Author;
use App\Models\Category;
use App\Models\Book;
use App\Models\Borrow;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Librarian',
            'email' => 'librarian@example.com',
        ]);

        $authors = collect([
            ['first_name' => 'George', 'last_name' => 'Orwell'],
            ['first_name' => 'Jane', 'last_name' => 'Austen'],
            ['first_name' => 'Chinua', 'last_name' => 'Achebe'],
        ])->map(fn ($data) => Author::create($data));

        $categories = collect([
            ['name' => 'Fiction'],
            ['name' => 'Classics'],
            ['name' => 'African Literature'],
        ])->map(fn ($data) => Category::create($data));

        $books = collect([
            [
                'title' => '1984',
                'author_id' => $authors[0]->id,
                'category_id' => $categories[0]->id,
                'isbn' => '9780451524935',
                'published_year' => 1949,
                'total_copies' => 5,
                'available_copies' => 5,
            ],
            [
                'title' => 'Pride and Prejudice',
                'author_id' => $authors[1]->id,
                'category_id' => $categories[1]->id,
                'isbn' => '9780141439518',
                'published_year' => 1813,
                'total_copies' => 3,
                'available_copies' => 3,
            ],
            [
                'title' => 'Things Fall Apart',
                'author_id' => $authors[2]->id,
                'category_id' => $categories[2]->id,
                'isbn' => '9780385474542',
                'published_year' => 1958,
                'total_copies' => 4,
                'available_copies' => 4,
            ],
        ])->map(fn ($data) => Book::create($data));

        Borrow::create([
            'user_id' => $user->id,
            'book_id' => $books[0]->id,
            'borrowed_at' => now()->subDays(2),
            'due_at' => now()->addDays(12),
            'status' => 'borrowed',
        ]);
    }
}
