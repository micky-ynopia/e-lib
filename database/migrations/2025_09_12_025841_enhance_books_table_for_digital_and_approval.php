<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->enum('book_type', ['physical', 'digital', 'both'])->default('physical');
            $table->string('file_path')->nullable(); // Path to digital book file
            $table->string('file_name')->nullable(); // Original filename
            $table->string('file_size')->nullable(); // File size in bytes
            $table->string('cover_image')->nullable(); // Book cover image
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->integer('download_count')->default(0);
            $table->integer('view_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn([
                'book_type', 'file_path', 'file_name', 'file_size', 'cover_image',
                'description', 'status', 'approved_by', 'approved_at', 'rejection_reason',
                'is_featured', 'download_count', 'view_count'
            ]);
        });
    }
};
