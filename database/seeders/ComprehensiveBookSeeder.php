<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Category;
use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ComprehensiveBookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding comprehensive book collection...');

        // Get or create admin user for approval
        $admin = User::where('role', 'librarian')->first() 
            ?? User::where('role', 'staff')->first()
            ?? User::first();

        // Create Categories
        $categories = $this->createCategories();
        $this->command->info('✅ Categories created');

        // Create Authors
        $authors = $this->createAuthors();
        $this->command->info('✅ Authors created');

        // Create Books
        $this->createBooks($categories, $authors, $admin);
        $this->command->info('✅ Books created');

        $this->command->info('🎉 Comprehensive book collection seeded successfully!');
        $this->command->info('📚 Total Categories: ' . Category::count());
        $this->command->info('✍️  Total Authors: ' . Author::count());
        $this->command->info('📖 Total Books: ' . Book::count());
    }

    private function createCategories(): array
    {
        $categoriesData = [
            ['name' => 'Computer Science', 'description' => 'Programming, algorithms, software engineering'],
            ['name' => 'Mathematics', 'description' => 'Algebra, calculus, statistics, discrete mathematics'],
            ['name' => 'Science', 'description' => 'Physics, chemistry, biology, natural sciences'],
            ['name' => 'Engineering', 'description' => 'Civil, electrical, mechanical, and other engineering fields'],
            ['name' => 'Business', 'description' => 'Management, finance, marketing, entrepreneurship'],
            ['name' => 'Literature', 'description' => 'Fiction, poetry, classic and contemporary literature'],
            ['name' => 'History', 'description' => 'World history, Philippine history, historical studies'],
            ['name' => 'Philosophy', 'description' => 'Ethics, logic, metaphysics, philosophical texts'],
            ['name' => 'Education', 'description' => 'Teaching methods, pedagogy, educational psychology'],
            ['name' => 'Psychology', 'description' => 'Cognitive psychology, developmental psychology, clinical psychology'],
            ['name' => 'Social Sciences', 'description' => 'Sociology, anthropology, political science'],
            ['name' => 'Health & Medicine', 'description' => 'Medical texts, public health, nursing'],
            ['name' => 'Arts & Design', 'description' => 'Visual arts, graphic design, multimedia'],
            ['name' => 'Language', 'description' => 'English, Filipino, foreign languages, linguistics'],
            ['name' => 'Reference', 'description' => 'Dictionaries, encyclopedias, research guides'],
        ];

        $categories = [];
        foreach ($categoriesData as $data) {
            $categories[$data['name']] = Category::firstOrCreate(
                ['name' => $data['name']],
                $data
            );
        }

        return $categories;
    }

    private function createAuthors(): array
    {
        $authorsData = [
            // Computer Science
            ['first_name' => 'Robert', 'last_name' => 'Sedgewick', 'bio' => 'Computer science professor and author'],
            ['first_name' => 'Thomas', 'last_name' => 'Cormen', 'bio' => 'Co-author of Introduction to Algorithms'],
            ['first_name' => 'Charles', 'last_name' => 'Leiserson', 'bio' => 'Computer scientist and MIT professor'],
            ['first_name' => 'Ronald', 'last_name' => 'Rivest', 'bio' => 'Cryptographer and computer scientist'],
            ['first_name' => 'Clifford', 'last_name' => 'Stein', 'bio' => 'Computer science professor'],
            ['first_name' => 'Martin', 'last_name' => 'Fowler', 'bio' => 'Software engineer and author'],
            ['first_name' => 'Eric', 'last_name' => 'Freeman', 'bio' => 'Software engineer and author'],
            ['first_name' => 'Elisabeth', 'last_name' => 'Robson', 'bio' => 'Software engineer and author'],
            
            // Mathematics
            ['first_name' => 'James', 'last_name' => 'Stewart', 'bio' => 'Mathematics professor and author'],
            ['first_name' => 'Ron', 'last_name' => 'Larson', 'bio' => 'Mathematics professor and author'],
            ['first_name' => 'Gilbert', 'last_name' => 'Strang', 'bio' => 'MIT mathematics professor'],
            ['first_name' => 'Michael', 'last_name' => 'Spivak', 'bio' => 'Mathematics professor and author'],
            
            // Science
            ['first_name' => 'Raymond', 'last_name' => 'Chang', 'bio' => 'Chemistry professor and author'],
            ['first_name' => 'Neil', 'last_name' => 'Campbell', 'bio' => 'Biology professor and author'],
            ['first_name' => 'Jane', 'last_name' => 'Reece', 'bio' => 'Biology professor and author'],
            ['first_name' => 'David', 'last_name' => 'Halliday', 'bio' => 'Physics professor and author'],
            ['first_name' => 'Robert', 'last_name' => 'Resnick', 'bio' => 'Physics professor and author'],
            
            // Literature
            ['first_name' => 'Jose', 'last_name' => 'Rizal', 'bio' => 'National hero and author'],
            ['first_name' => 'Nick', 'last_name' => 'Joaquin', 'bio' => 'Filipino writer and journalist'],
            ['first_name' => 'F. Sionil', 'last_name' => 'José', 'bio' => 'Filipino novelist'],
            ['first_name' => 'J.K.', 'last_name' => 'Rowling', 'bio' => 'British author'],
            ['first_name' => 'George', 'last_name' => 'Orwell', 'bio' => 'English novelist and essayist'],
            ['first_name' => 'Jane', 'last_name' => 'Austen', 'bio' => 'English novelist'],
            
            // Business
            ['first_name' => 'Philip', 'last_name' => 'Kotler', 'bio' => 'Marketing professor and author'],
            ['first_name' => 'Gary', 'last_name' => 'Armstrong', 'bio' => 'Marketing professor'],
            ['first_name' => 'Stephen', 'last_name' => 'Robbins', 'bio' => 'Management professor and author'],
            ['first_name' => 'Mary', 'last_name' => 'Coulter', 'bio' => 'Management professor'],
            
            // History
            ['first_name' => 'Teodoro', 'last_name' => 'Agoncillo', 'bio' => 'Filipino historian'],
            ['first_name' => 'Renato', 'last_name' => 'Constantino', 'bio' => 'Filipino historian'],
            ['first_name' => 'William', 'last_name' => 'Manchester', 'bio' => 'American historian'],
            
            // Philosophy
            ['first_name' => 'Bertrand', 'last_name' => 'Russell', 'bio' => 'British philosopher'],
            ['first_name' => 'Immanuel', 'last_name' => 'Kant', 'bio' => 'German philosopher'],
            ['first_name' => 'Plato', 'last_name' => '', 'bio' => 'Ancient Greek philosopher'],
        ];

        $authors = [];
        foreach ($authorsData as $data) {
            $key = $data['first_name'] . ' ' . $data['last_name'];
            $authors[$key] = Author::firstOrCreate(
                [
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name']
                ],
                $data
            );
        }

        return $authors;
    }

    private function createBooks(array $categories, array $authors, $admin): void
    {
        $booksData = [
            // Computer Science Books
            [
                'title' => 'Introduction to Algorithms',
                'author' => 'Thomas Cormen',
                'category' => 'Computer Science',
                'isbn' => '9780262046305',
                'published_year' => 2022,
                'book_type' => 'digital',
                'description' => 'The leading textbook on algorithms, covering a broad range of algorithms in depth. Essential reading for computer science students.',
                'is_featured' => true,
            ],
            [
                'title' => 'Clean Code: A Handbook of Agile Software Craftsmanship',
                'author' => 'Robert C. Martin',
                'category' => 'Computer Science',
                'isbn' => '9780132350884',
                'published_year' => 2008,
                'book_type' => 'digital',
                'description' => 'A guide to writing clean, maintainable code. Learn how to write code that is easy to read and understand.',
                'is_featured' => true,
            ],
            [
                'title' => 'Head First Design Patterns',
                'author' => 'Eric Freeman',
                'category' => 'Computer Science',
                'isbn' => '9780596007126',
                'published_year' => 2004,
                'book_type' => 'digital',
                'description' => 'A brain-friendly guide to design patterns. Learn design patterns in a fun and engaging way.',
            ],
            [
                'title' => 'The Pragmatic Programmer',
                'author' => 'Andrew Hunt',
                'category' => 'Computer Science',
                'isbn' => '9780201616224',
                'published_year' => 1999,
                'book_type' => 'digital',
                'description' => 'A guide to becoming a better programmer. Practical advice for software developers.',
            ],
            [
                'title' => 'Structure and Interpretation of Computer Programs',
                'author' => 'Harold Abelson',
                'category' => 'Computer Science',
                'isbn' => '9780262510875',
                'published_year' => 1996,
                'book_type' => 'digital',
                'description' => 'The classic MIT textbook on computer science. Covers programming concepts and techniques.',
            ],
            [
                'title' => 'You Don\'t Know JS: Up & Going',
                'author' => 'Kyle Simpson',
                'category' => 'Computer Science',
                'isbn' => '9781491924464',
                'published_year' => 2015,
                'book_type' => 'digital',
                'description' => 'A deep dive into JavaScript. Learn the language from the ground up.',
            ],

            // Mathematics Books
            [
                'title' => 'Calculus: Early Transcendentals',
                'author' => 'James Stewart',
                'category' => 'Mathematics',
                'isbn' => '9781337613927',
                'published_year' => 2020,
                'book_type' => 'digital',
                'description' => 'Comprehensive calculus textbook covering limits, derivatives, integrals, and series.',
                'is_featured' => true,
            ],
            [
                'title' => 'Linear Algebra and Its Applications',
                'author' => 'David C. Lay',
                'category' => 'Mathematics',
                'isbn' => '9780321982384',
                'published_year' => 2015,
                'book_type' => 'digital',
                'description' => 'Introduction to linear algebra with applications. Essential for engineering and science students.',
            ],
            [
                'title' => 'Discrete Mathematics and Its Applications',
                'author' => 'Kenneth H. Rosen',
                'category' => 'Mathematics',
                'isbn' => '9780073383095',
                'published_year' => 2018,
                'book_type' => 'digital',
                'description' => 'Comprehensive introduction to discrete mathematics. Covers logic, sets, graphs, and algorithms.',
            ],
            [
                'title' => 'Introduction to Probability',
                'author' => 'Joseph K. Blitzstein',
                'category' => 'Mathematics',
                'isbn' => '9781466575578',
                'published_year' => 2014,
                'book_type' => 'digital',
                'description' => 'Introduction to probability theory with applications. Clear explanations and examples.',
            ],

            // Science Books
            [
                'title' => 'Biology',
                'author' => 'Neil Campbell',
                'category' => 'Science',
                'isbn' => '9780134093413',
                'published_year' => 2017,
                'book_type' => 'digital',
                'description' => 'Comprehensive biology textbook covering all major topics in the life sciences.',
                'is_featured' => true,
            ],
            [
                'title' => 'Chemistry: The Central Science',
                'author' => 'Raymond Chang',
                'category' => 'Science',
                'isbn' => '9780134414232',
                'published_year' => 2017,
                'book_type' => 'digital',
                'description' => 'Introduction to chemistry covering atomic structure, bonding, and chemical reactions.',
            ],
            [
                'title' => 'Fundamentals of Physics',
                'author' => 'David Halliday',
                'category' => 'Science',
                'isbn' => '9781118230718',
                'published_year' => 2013,
                'book_type' => 'digital',
                'description' => 'Comprehensive physics textbook covering mechanics, thermodynamics, and electromagnetism.',
            ],

            // Literature Books
            [
                'title' => 'Noli Me Tangere',
                'author' => 'Jose Rizal',
                'category' => 'Literature',
                'isbn' => '9789712714198',
                'published_year' => 1887,
                'book_type' => 'digital',
                'description' => 'The first novel written by Filipino national hero Jose Rizal. A powerful critique of Spanish colonial rule.',
                'is_featured' => true,
            ],
            [
                'title' => 'El Filibusterismo',
                'author' => 'Jose Rizal',
                'category' => 'Literature',
                'isbn' => '9789712714204',
                'published_year' => 1891,
                'book_type' => 'digital',
                'description' => 'The sequel to Noli Me Tangere. Continues the story of the struggle against Spanish oppression.',
            ],
            [
                'title' => 'The Woman Who Had Two Navels',
                'author' => 'Nick Joaquin',
                'category' => 'Literature',
                'isbn' => '9789712714211',
                'published_year' => 1961,
                'book_type' => 'digital',
                'description' => 'A collection of short stories by National Artist for Literature Nick Joaquin.',
            ],
            [
                'title' => 'Dusk',
                'author' => 'F. Sionil José',
                'category' => 'Literature',
                'isbn' => '9789712714228',
                'published_year' => 1998,
                'book_type' => 'digital',
                'description' => 'A novel exploring Filipino identity and social issues by renowned Filipino author F. Sionil José.',
            ],
            [
                'title' => '1984',
                'author' => 'George Orwell',
                'category' => 'Literature',
                'isbn' => '9780451524935',
                'published_year' => 1949,
                'book_type' => 'digital',
                'description' => 'A dystopian novel about totalitarian surveillance and thought control.',
            ],
            [
                'title' => 'Pride and Prejudice',
                'author' => 'Jane Austen',
                'category' => 'Literature',
                'isbn' => '9780141439518',
                'published_year' => 1813,
                'book_type' => 'digital',
                'description' => 'A classic romance novel about Elizabeth Bennet and Mr. Darcy.',
            ],

            // Business Books
            [
                'title' => 'Principles of Marketing',
                'author' => 'Philip Kotler',
                'category' => 'Business',
                'isbn' => '9780134492513',
                'published_year' => 2020,
                'book_type' => 'digital',
                'description' => 'Comprehensive introduction to marketing principles and strategies.',
                'is_featured' => true,
            ],
            [
                'title' => 'Management',
                'author' => 'Stephen Robbins',
                'category' => 'Business',
                'isbn' => '9780134527604',
                'published_year' => 2017,
                'book_type' => 'digital',
                'description' => 'Introduction to management principles and practices for modern organizations.',
            ],

            // History Books
            [
                'title' => 'History of the Filipino People',
                'author' => 'Teodoro Agoncillo',
                'category' => 'History',
                'isbn' => '9789712714235',
                'published_year' => 1990,
                'book_type' => 'digital',
                'description' => 'Comprehensive history of the Philippines from pre-colonial times to the present.',
                'is_featured' => true,
            ],
            [
                'title' => 'The Philippines: A Past Revisited',
                'author' => 'Renato Constantino',
                'category' => 'History',
                'isbn' => '9789712714242',
                'published_year' => 1975,
                'book_type' => 'digital',
                'description' => 'A critical examination of Philippine history from a Filipino perspective.',
            ],

            // Philosophy Books
            [
                'title' => 'The Problems of Philosophy',
                'author' => 'Bertrand Russell',
                'category' => 'Philosophy',
                'isbn' => '9780192854230',
                'published_year' => 1912,
                'book_type' => 'digital',
                'description' => 'An accessible introduction to philosophy by one of the 20th century\'s greatest philosophers.',
            ],
            [
                'title' => 'The Republic',
                'author' => 'Plato',
                'category' => 'Philosophy',
                'isbn' => '9780140449143',
                'published_year' => -380,
                'book_type' => 'digital',
                'description' => 'Plato\'s most famous work, exploring justice, the ideal state, and the nature of reality.',
            ],

            // Education Books
            [
                'title' => 'Educational Psychology',
                'author' => 'Anita Woolfolk',
                'category' => 'Education',
                'isbn' => '9780134895109',
                'published_year' => 2019,
                'book_type' => 'digital',
                'description' => 'Introduction to educational psychology and its applications in teaching and learning.',
            ],

            // Psychology Books
            [
                'title' => 'Introduction to Psychology',
                'author' => 'James W. Kalat',
                'category' => 'Psychology',
                'isbn' => '9781337568745',
                'published_year' => 2019,
                'book_type' => 'digital',
                'description' => 'Comprehensive introduction to psychology covering all major areas of the field.',
            ],

            // Reference Books
            [
                'title' => 'The Elements of Style',
                'author' => 'William Strunk Jr.',
                'category' => 'Reference',
                'isbn' => '9780205309023',
                'published_year' => 1918,
                'book_type' => 'digital',
                'description' => 'A classic guide to English writing style and usage.',
            ],

            // Additional Computer Science Books
            [
                'title' => 'JavaScript: The Definitive Guide',
                'author' => 'David Flanagan',
                'category' => 'Computer Science',
                'isbn' => '9781491952026',
                'published_year' => 2020,
                'book_type' => 'digital',
                'description' => 'Comprehensive guide to JavaScript programming language.',
            ],
            [
                'title' => 'Python Crash Course',
                'author' => 'Eric Matthes',
                'category' => 'Computer Science',
                'isbn' => '9781593279288',
                'published_year' => 2019,
                'book_type' => 'digital',
                'description' => 'A hands-on, project-based introduction to programming.',
            ],
            [
                'title' => 'Database System Concepts',
                'author' => 'Abraham Silberschatz',
                'category' => 'Computer Science',
                'isbn' => '9780078022159',
                'published_year' => 2019,
                'book_type' => 'digital',
                'description' => 'Introduction to database systems and concepts.',
            ],
            [
                'title' => 'Operating System Concepts',
                'author' => 'Abraham Silberschatz',
                'category' => 'Computer Science',
                'isbn' => '9781119800361',
                'published_year' => 2021,
                'book_type' => 'digital',
                'description' => 'Comprehensive introduction to operating systems.',
            ],

            // Additional Mathematics Books
            [
                'title' => 'Statistics and Probability',
                'author' => 'David M. Lane',
                'category' => 'Mathematics',
                'isbn' => '9781498755098',
                'published_year' => 2015,
                'book_type' => 'digital',
                'description' => 'Introduction to statistics and probability theory.',
            ],
            [
                'title' => 'Differential Equations',
                'author' => 'William E. Boyce',
                'category' => 'Mathematics',
                'isbn' => '9781119320630',
                'published_year' => 2017,
                'book_type' => 'digital',
                'description' => 'Introduction to ordinary and partial differential equations.',
            ],

            // Additional Science Books
            [
                'title' => 'General Chemistry',
                'author' => 'Donald A. McQuarrie',
                'category' => 'Science',
                'isbn' => '9781891389603',
                'published_year' => 2011,
                'book_type' => 'digital',
                'description' => 'Comprehensive general chemistry textbook.',
            ],
            [
                'title' => 'Molecular Biology of the Cell',
                'author' => 'Bruce Alberts',
                'category' => 'Science',
                'isbn' => '9780815344322',
                'published_year' => 2014,
                'book_type' => 'digital',
                'description' => 'Comprehensive textbook on molecular and cell biology.',
            ],

            // Additional Literature Books
            [
                'title' => 'To Kill a Mockingbird',
                'author' => 'Harper Lee',
                'category' => 'Literature',
                'isbn' => '9780061120084',
                'published_year' => 1960,
                'book_type' => 'digital',
                'description' => 'A classic American novel about racial injustice.',
            ],
            [
                'title' => 'The Great Gatsby',
                'author' => 'F. Scott Fitzgerald',
                'category' => 'Literature',
                'isbn' => '9780743273565',
                'published_year' => 1925,
                'book_type' => 'digital',
                'description' => 'A classic American novel about the Jazz Age.',
            ],
            [
                'title' => 'The Catcher in the Rye',
                'author' => 'J.D. Salinger',
                'category' => 'Literature',
                'isbn' => '9780316769174',
                'published_year' => 1951,
                'book_type' => 'digital',
                'description' => 'A coming-of-age novel about teenage rebellion.',
            ],
            [
                'title' => 'The Little Prince',
                'author' => 'Antoine de Saint-Exupéry',
                'category' => 'Literature',
                'isbn' => '9780156012195',
                'published_year' => 1943,
                'book_type' => 'digital',
                'description' => 'A philosophical tale about a young prince.',
            ],

            // Additional Business Books
            [
                'title' => 'Financial Accounting',
                'author' => 'Jerry J. Weygandt',
                'category' => 'Business',
                'isbn' => '9781119577651',
                'published_year' => 2018,
                'book_type' => 'digital',
                'description' => 'Introduction to financial accounting principles.',
            ],
            [
                'title' => 'Organizational Behavior',
                'author' => 'Stephen P. Robbins',
                'category' => 'Business',
                'isbn' => '9780134729329',
                'published_year' => 2018,
                'book_type' => 'digital',
                'description' => 'Understanding human behavior in organizations.',
            ],

            // Additional History Books
            [
                'title' => 'World History',
                'author' => 'William J. Duiker',
                'category' => 'History',
                'isbn' => '9781305091976',
                'published_year' => 2015,
                'book_type' => 'digital',
                'description' => 'Comprehensive world history from ancient to modern times.',
            ],
            [
                'title' => 'The Story of Civilization',
                'author' => 'Will Durant',
                'category' => 'History',
                'isbn' => '9781567310238',
                'published_year' => 1993,
                'book_type' => 'digital',
                'description' => 'A comprehensive history of human civilization.',
            ],

            // Additional Philosophy Books
            [
                'title' => 'Meditations',
                'author' => 'Marcus Aurelius',
                'category' => 'Philosophy',
                'isbn' => '9780486298238',
                'published_year' => 180,
                'book_type' => 'digital',
                'description' => 'Personal reflections of the Roman emperor and Stoic philosopher.',
            ],
            [
                'title' => 'The Art of War',
                'author' => 'Sun Tzu',
                'category' => 'Philosophy',
                'isbn' => '9780486425573',
                'published_year' => -500,
                'book_type' => 'digital',
                'description' => 'Ancient Chinese military treatise on strategy and tactics.',
            ],

            // Additional Education Books
            [
                'title' => 'Teaching Strategies',
                'author' => 'Donald C. Orlich',
                'category' => 'Education',
                'isbn' => '9781305960787',
                'published_year' => 2017,
                'book_type' => 'digital',
                'description' => 'Effective teaching strategies for educators.',
            ],
            [
                'title' => 'Curriculum Development',
                'author' => 'Allan C. Ornstein',
                'category' => 'Education',
                'isbn' => '9781305500990',
                'published_year' => 2017,
                'book_type' => 'digital',
                'description' => 'Principles and practices of curriculum development.',
            ],

            // Additional Psychology Books
            [
                'title' => 'Abnormal Psychology',
                'author' => 'Ronald J. Comer',
                'category' => 'Psychology',
                'isbn' => '9781319066949',
                'published_year' => 2018,
                'book_type' => 'digital',
                'description' => 'Introduction to abnormal psychology and mental disorders.',
            ],
            [
                'title' => 'Social Psychology',
                'author' => 'David G. Myers',
                'category' => 'Psychology',
                'isbn' => '9781259060642',
                'published_year' => 2016,
                'book_type' => 'digital',
                'description' => 'Understanding how people think, feel, and behave in social contexts.',
            ],

            // Additional Engineering Books
            [
                'title' => 'Mechanical Engineering Principles',
                'author' => 'John Bird',
                'category' => 'Engineering',
                'isbn' => '9780415662844',
                'published_year' => 2012,
                'book_type' => 'digital',
                'description' => 'Fundamental principles of mechanical engineering.',
            ],
            [
                'title' => 'Electrical Engineering Fundamentals',
                'author' => 'Vincent Del Toro',
                'category' => 'Engineering',
                'isbn' => '9780132320032',
                'published_year' => 2004,
                'book_type' => 'digital',
                'description' => 'Introduction to electrical engineering concepts.',
            ],

            // Additional Health & Medicine Books
            [
                'title' => 'Anatomy and Physiology',
                'author' => 'Elaine N. Marieb',
                'category' => 'Health & Medicine',
                'isbn' => '9780134156415',
                'published_year' => 2018,
                'book_type' => 'digital',
                'description' => 'Comprehensive textbook on human anatomy and physiology.',
            ],
            [
                'title' => 'Medical Terminology',
                'author' => 'Barbara A. Gylys',
                'category' => 'Health & Medicine',
                'isbn' => '9780803658677',
                'published_year' => 2016,
                'book_type' => 'digital',
                'description' => 'Introduction to medical terminology for healthcare professionals.',
            ],

            // Additional Language Books
            [
                'title' => 'English Grammar in Use',
                'author' => 'Raymond Murphy',
                'category' => 'Language',
                'isbn' => '9780521532891',
                'published_year' => 2004,
                'book_type' => 'digital',
                'description' => 'Self-study reference and practice book for English grammar.',
            ],
            [
                'title' => 'Filipino Language and Culture',
                'author' => 'Terestita Ramos',
                'category' => 'Language',
                'isbn' => '9780824813806',
                'published_year' => 1990,
                'book_type' => 'digital',
                'description' => 'Introduction to Filipino language and cultural context.',
            ],
        ];

        // Add some authors that might not be in the main list
        $additionalAuthors = [
            'Robert C. Martin' => ['first_name' => 'Robert', 'last_name' => 'Martin', 'bio' => 'Software engineer and author'],
            'Andrew Hunt' => ['first_name' => 'Andrew', 'last_name' => 'Hunt', 'bio' => 'Software engineer and author'],
            'Harold Abelson' => ['first_name' => 'Harold', 'last_name' => 'Abelson', 'bio' => 'Computer science professor'],
            'Kyle Simpson' => ['first_name' => 'Kyle', 'last_name' => 'Simpson', 'bio' => 'JavaScript expert and author'],
            'David C. Lay' => ['first_name' => 'David', 'last_name' => 'Lay', 'bio' => 'Mathematics professor'],
            'Kenneth H. Rosen' => ['first_name' => 'Kenneth', 'last_name' => 'Rosen', 'bio' => 'Mathematics professor'],
            'Joseph K. Blitzstein' => ['first_name' => 'Joseph', 'last_name' => 'Blitzstein', 'bio' => 'Statistics professor'],
            'Anita Woolfolk' => ['first_name' => 'Anita', 'last_name' => 'Woolfolk', 'bio' => 'Educational psychology professor'],
            'James W. Kalat' => ['first_name' => 'James', 'last_name' => 'Kalat', 'bio' => 'Psychology professor'],
            'William Strunk Jr.' => ['first_name' => 'William', 'last_name' => 'Strunk', 'bio' => 'English professor'],
            'David Flanagan' => ['first_name' => 'David', 'last_name' => 'Flanagan', 'bio' => 'JavaScript expert and author'],
            'Eric Matthes' => ['first_name' => 'Eric', 'last_name' => 'Matthes', 'bio' => 'Python educator and author'],
            'Abraham Silberschatz' => ['first_name' => 'Abraham', 'last_name' => 'Silberschatz', 'bio' => 'Computer science professor'],
            'David M. Lane' => ['first_name' => 'David', 'last_name' => 'Lane', 'bio' => 'Statistics professor'],
            'William E. Boyce' => ['first_name' => 'William', 'last_name' => 'Boyce', 'bio' => 'Mathematics professor'],
            'Donald A. McQuarrie' => ['first_name' => 'Donald', 'last_name' => 'McQuarrie', 'bio' => 'Chemistry professor'],
            'Bruce Alberts' => ['first_name' => 'Bruce', 'last_name' => 'Alberts', 'bio' => 'Biologist and author'],
            'Harper Lee' => ['first_name' => 'Harper', 'last_name' => 'Lee', 'bio' => 'American novelist'],
            'F. Scott Fitzgerald' => ['first_name' => 'F. Scott', 'last_name' => 'Fitzgerald', 'bio' => 'American novelist'],
            'J.D. Salinger' => ['first_name' => 'J.D.', 'last_name' => 'Salinger', 'bio' => 'American writer'],
            'Antoine de Saint-Exupéry' => ['first_name' => 'Antoine', 'last_name' => 'de Saint-Exupéry', 'bio' => 'French writer and aviator'],
            'Jerry J. Weygandt' => ['first_name' => 'Jerry', 'last_name' => 'Weygandt', 'bio' => 'Accounting professor'],
            'William J. Duiker' => ['first_name' => 'William', 'last_name' => 'Duiker', 'bio' => 'Historian'],
            'Will Durant' => ['first_name' => 'Will', 'last_name' => 'Durant', 'bio' => 'Historian and philosopher'],
            'Marcus Aurelius' => ['first_name' => 'Marcus', 'last_name' => 'Aurelius', 'bio' => 'Roman emperor and philosopher'],
            'Sun Tzu' => ['first_name' => 'Sun', 'last_name' => 'Tzu', 'bio' => 'Ancient Chinese military strategist'],
            'Donald C. Orlich' => ['first_name' => 'Donald', 'last_name' => 'Orlich', 'bio' => 'Education professor'],
            'Allan C. Ornstein' => ['first_name' => 'Allan', 'last_name' => 'Ornstein', 'bio' => 'Education professor'],
            'Ronald J. Comer' => ['first_name' => 'Ronald', 'last_name' => 'Comer', 'bio' => 'Psychology professor'],
            'David G. Myers' => ['first_name' => 'David', 'last_name' => 'Myers', 'bio' => 'Psychology professor'],
            'John Bird' => ['first_name' => 'John', 'last_name' => 'Bird', 'bio' => 'Engineering author'],
            'Vincent Del Toro' => ['first_name' => 'Vincent', 'last_name' => 'Del Toro', 'bio' => 'Electrical engineering professor'],
            'Elaine N. Marieb' => ['first_name' => 'Elaine', 'last_name' => 'Marieb', 'bio' => 'Anatomy and physiology professor'],
            'Barbara A. Gylys' => ['first_name' => 'Barbara', 'last_name' => 'Gylys', 'bio' => 'Medical terminology expert'],
            'Raymond Murphy' => ['first_name' => 'Raymond', 'last_name' => 'Murphy', 'bio' => 'English grammar author'],
            'Terestita Ramos' => ['first_name' => 'Terestita', 'last_name' => 'Ramos', 'bio' => 'Filipino language professor'],
        ];

        foreach ($additionalAuthors as $name => $data) {
            if (!isset($authors[$name])) {
                $authors[$name] = Author::firstOrCreate(
                    ['first_name' => $data['first_name'], 'last_name' => $data['last_name']],
                    $data
                );
            }
        }

        foreach ($booksData as $bookData) {
            $authorName = $bookData['author'];
            $categoryName = $bookData['category'];

            if (!isset($authors[$authorName])) {
                $nameParts = explode(' ', $authorName, 2);
                $authors[$authorName] = Author::firstOrCreate(
                    [
                        'first_name' => $nameParts[0] ?? '',
                        'last_name' => $nameParts[1] ?? $nameParts[0] ?? 'Unknown'
                    ],
                    ['bio' => 'Author']
                );
            }

            // Handle negative years (BC dates) - convert to null since unsignedInteger doesn't support negatives
            $publishedYear = $bookData['published_year'];
            if ($publishedYear < 0) {
                $publishedYear = null;
            }

            $book = Book::firstOrCreate(
                ['isbn' => $bookData['isbn']],
                [
                    'title' => $bookData['title'],
                    'author_id' => $authors[$authorName]->id,
                    'category_id' => $categories[$categoryName]->id,
                    'isbn' => $bookData['isbn'],
                    'published_year' => $publishedYear,
                    'total_copies' => 1,
                    'available_copies' => 1,
                    'book_type' => $bookData['book_type'],
                    'description' => $bookData['description'] ?? null,
                    'status' => 'approved',
                    'is_featured' => $bookData['is_featured'] ?? false,
                    'approved_by' => $admin->id ?? null,
                    'approved_at' => now(),
                    'view_count' => rand(0, 100),
                    'download_count' => rand(0, 50),
                ]
            );
        }
    }
}

