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
        Schema::create('theses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('abstract');
            $table->string('author_name');
            $table->string('author_student_id')->nullable();
            $table->string('course');
            $table->string('year_level');
            $table->string('academic_year');
            $table->string('adviser_name');
            $table->string('adviser_email')->nullable();
            $table->string('keywords')->nullable();
            $table->string('file_path')->nullable(); // Path to uploaded thesis file
            $table->string('file_name')->nullable(); // Original filename
            $table->string('file_size')->nullable(); // File size in bytes
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->boolean('is_public')->default(true);
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('theses');
    }
};
