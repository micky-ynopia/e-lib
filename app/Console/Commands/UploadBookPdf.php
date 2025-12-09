<?php

namespace App\Console\Commands;

use App\Models\Book;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class UploadBookPdf extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'book:upload-pdf 
                            {title : The title of the book}
                            {file : Path to the PDF file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload a PDF file to a book';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $title = $this->argument('title');
        $filePath = $this->argument('file');

        // Find the book
        $book = Book::where('title', 'like', "%{$title}%")->first();

        if (!$book) {
            $this->error("Book not found: {$title}");
            $this->info("Available books:");
            Book::select('title')->get()->each(function ($b) {
                $this->line("  - {$b->title}");
            });
            return Command::FAILURE;
        }

        // Check if file exists
        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return Command::FAILURE;
        }

        // Check file type
        $mimeType = mime_content_type($filePath);
        if ($mimeType !== 'application/pdf') {
            $this->error("File is not a PDF. MIME type: {$mimeType}");
            return Command::FAILURE;
        }

        // Check file size (10MB limit)
        $fileSize = filesize($filePath);
        if ($fileSize > 10 * 1024 * 1024) {
            $this->error("File is too large. Maximum size: 10MB");
            return Command::FAILURE;
        }

        // Upload file
        try {
            $filename = time() . '_' . basename($filePath);
            $storedPath = Storage::disk('public')->putFileAs('books', $filePath, $filename);

            // Update book
            $book->update([
                'file_path' => $storedPath,
                'file_name' => basename($filePath),
                'file_size' => $fileSize,
                'book_type' => 'digital', // Ensure it's digital
            ]);

            $this->info("✅ PDF uploaded successfully!");
            $this->info("   Book: {$book->title}");
            $this->info("   File: {$book->file_name}");
            $this->info("   Size: " . $this->formatBytes($fileSize));
            $this->info("   Path: storage/app/public/{$storedPath}");
            $this->info("");
            $this->info("📖 Book is now available for reading and downloading!");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error uploading file: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}

