<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin account (highest privileges use librarian role)
        $admin = User::firstOrNew(['email' => 'admin@nemsu.edu.ph']);
        $admin->name = 'System Admin';
        $admin->password = 'password123'; // Will be hashed automatically by the 'hashed' cast
        $admin->role = 'librarian';
        $admin->is_approved = true;
        $admin->approved_at = now();
        $admin->save();

        // Create librarian account
        $librarian = User::firstOrNew(['email' => 'librarian@nemsu.edu.ph']);
        $librarian->name = 'Library Librarian';
        $librarian->password = 'password123'; // Will be hashed automatically by the 'hashed' cast
        $librarian->role = 'librarian';
        $librarian->is_approved = true;
        $librarian->approved_at = now();
        $librarian->save();

        // Create sample student account
        $student = User::firstOrNew(['email' => 'student@nemsu.edu.ph']);
        $student->name = 'John Doe';
        $student->password = 'password123'; // Will be hashed automatically by the 'hashed' cast
        $student->role = 'student';
        $student->student_id = '2024-0001';
        $student->course = 'BSIT';
        $student->year_level = '3rd Year';
        $student->phone = '09123456789';
        $student->is_approved = true;
        $student->approved_at = now();
        $student->save();

        $this->command->info('Admin users created successfully!');
        $this->command->info('Admin: admin@nemsu.edu.ph / password123');
        $this->command->info('Librarian: librarian@nemsu.edu.ph / password123');
        $this->command->info('Student: student@nemsu.edu.ph / password123');
    }
}