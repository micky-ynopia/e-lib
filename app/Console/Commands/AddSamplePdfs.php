<?php

namespace App\Console\Commands;

use App\Models\Book;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AddSamplePdfs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'books:add-sample-pdfs 
                            {--force : Force update even if PDF exists}
                            {--book= : Specific book title to add PDF to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add sample PDFs to books (creates placeholder PDFs for testing)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('📚 Adding sample PDFs to books...');
        $this->info('');

        // List of books that commonly have free PDFs available
        $booksWithPdfs = [
            'Pride and Prejudice' => 'https://www.gutenberg.org/files/1342/1342-pdf.pdf',
            '1984' => 'https://www.gutenberg.org/files/3280/3280-pdf.pdf',
            // Add more as needed
        ];

        $query = Book::where('book_type', 'digital')
            ->where('status', 'approved');

        if ($this->option('book')) {
            $query->where('title', 'like', '%' . $this->option('book') . '%');
        }

        $books = $query->get();

        if ($books->isEmpty()) {
            $this->warn('No digital books found.');
            return Command::FAILURE;
        }

        $this->info("Found {$books->count()} digital book(s)");
        $this->info('');

        $added = 0;
        $skipped = 0;

        foreach ($books as $book) {
            // Skip if PDF already exists and not forcing
            if ($book->file_path && !$this->option('force')) {
                $this->line("⏭️  Skipping: {$book->title} (PDF already exists)");
                $skipped++;
                continue;
            }

            // Create a simple placeholder PDF
            $pdfContent = $this->createPlaceholderPdf($book);

            try {
                $filename = time() . '_' . Str::slug($book->title) . '.pdf';
                // Store file using put (for string content) instead of putFileAs (for file paths)
                $storedPath = 'books/' . $filename;
                Storage::disk('public')->put($storedPath, $pdfContent);

                $book->update([
                    'file_path' => $storedPath,
                    'file_name' => $filename,
                    'file_size' => strlen($pdfContent),
                ]);

                $this->info("✅ Added PDF to: {$book->title}");
                $added++;
            } catch (\Exception $e) {
                $this->error("❌ Failed to add PDF to {$book->title}: " . $e->getMessage());
            }
        }

        $this->info('');
        $this->info("✅ Successfully added {$added} PDF(s)");
        if ($skipped > 0) {
            $this->info("⏭️  Skipped {$skipped} book(s) (PDFs already exist)");
        }
        $this->info('');
        $this->info('📖 Books are now ready for reading and downloading!');

        return Command::SUCCESS;
    }

    /**
     * Create a simple placeholder PDF content
     * Creates a valid PDF with book information
     */
    private function createPlaceholderPdf(Book $book): string
    {
        $title = $book->title;
        $author = $book->author ? "{$book->author->first_name} {$book->author->last_name}" : 'Unknown Author';
        $category = $book->category ? $book->category->name : 'Uncategorized';
        $description = $book->description ?: 'This is a digital book in the e-library collection.';
        $isbn = $book->isbn ?? 'N/A';
        $year = $book->published_year ?? 'N/A';

        // Escape special characters for PDF
        $escapePdf = function($text) {
            return str_replace(['(', ')', '\\'], ['\\(', '\\)', '\\\\'], $text);
        };

        $titleEscaped = $escapePdf($title);
        $authorEscaped = $escapePdf($author);
        $categoryEscaped = $escapePdf($category);
        $descEscaped = $escapePdf($description);

        // Create a proper PDF structure
        $pdf = "%PDF-1.4\n";
        
        // Catalog
        $pdf .= "1 0 obj\n";
        $pdf .= "<< /Type /Catalog /Pages 2 0 R >>\n";
        $pdf .= "endobj\n";
        
        // Pages
        $pdf .= "2 0 obj\n";
        $pdf .= "<< /Type /Pages /Kids [3 0 R] /Count 1 >>\n";
        $pdf .= "endobj\n";
        
        // Page
        $pdf .= "3 0 obj\n";
        $pdf .= "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 << /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >> /F2 << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> >> >> >>\n";
        $pdf .= "endobj\n";
        
        // Content stream
        $content = "BT\n";
        $content .= "/F1 28 Tf\n";
        $content .= "72 720 Td\n";
        $content .= "({$titleEscaped}) Tj\n";
        $content .= "0 -40 Td\n";
        $content .= "/F2 16 Tf\n";
        $content .= "({$authorEscaped}) Tj\n";
        $content .= "0 -30 Td\n";
        $content .= "/F2 12 Tf\n";
        $content .= "(Category: {$categoryEscaped}) Tj\n";
        $content .= "0 -20 Td\n";
        $content .= "(ISBN: {$isbn}) Tj\n";
        $content .= "0 -20 Td\n";
        $content .= "(Published: {$year}) Tj\n";
        $content .= "0 -30 Td\n";
        $content .= "({$descEscaped}) Tj\n";
        $content .= "0 -40 Td\n";
        $content .= "/F1 14 Tf\n";
        $content .= "(E-Library Management System) Tj\n";
        $content .= "0 -20 Td\n";
        $content .= "/F2 10 Tf\n";
        $content .= "(This is a placeholder PDF. Replace with actual book content.) Tj\n";
        $content .= "ET\n";
        
        $contentLength = strlen($content);
        $pdf .= "4 0 obj\n";
        $pdf .= "<< /Length {$contentLength} >>\n";
        $pdf .= "stream\n";
        $pdf .= $content;
        $pdf .= "endstream\n";
        $pdf .= "endobj\n";
        
        // Xref table
        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n";
        $pdf .= "0 5\n";
        $pdf .= "0000000000 65535 f \n";
        $pdf .= "0000000009 00000 n \n";
        $pdf .= "0000000058 00000 n \n";
        $pdf .= "0000000115 00000 n \n";
        $pdf .= sprintf("%010d 00000 n \n", $xrefOffset - 200);
        
        // Trailer
        $pdf .= "trailer\n";
        $pdf .= "<< /Size 5 /Root 1 0 R >>\n";
        $pdf .= "startxref\n";
        $pdf .= strlen($pdf) . "\n";
        $pdf .= "%%EOF";

        return $pdf;
    }
}

