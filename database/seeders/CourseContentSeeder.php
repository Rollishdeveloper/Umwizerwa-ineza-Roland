<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Assignment;
use App\Models\Instructor;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CourseContentSeeder extends Seeder
{
    private array $instructors = [];

    private array $categories = [];

    public function run(): void
    {
        $this->command->info('Creating instructors...');
        $this->createInstructors();

        $this->command->info('Loading categories...');
        $this->categories = Category::pluck('category_id', 'category_name')->toArray();

        $this->command->info('Creating courses with full content...');
        $this->createCourses();

        $this->command->info('Course content seeding complete!');
    }

    private function createInstructors(): void
    {
        $instructorData = [
            [
                'name' => 'Dr. John Smith',
                'email' => 'john.smith@elearning.com',
                'specialization' => 'Computer Science & Software Engineering',
                'biography' => 'PhD in Computer Science with 15+ years of experience in software development and teaching. Expert in web technologies, programming languages, and system design.',
            ],
            [
                'name' => 'Prof. Jane Doe',
                'email' => 'jane.doe@elearning.com',
                'specialization' => 'Business Administration & Entrepreneurship',
                'biography' => 'MBA graduate and serial entrepreneur. Has taught business management and entrepreneurship at top universities for over a decade.',
            ],
            [
                'name' => 'Prof. Alice Williams',
                'email' => 'alice.williams@elearning.com',
                'specialization' => 'Education & Instructional Design',
                'biography' => 'Expert in modern teaching methodologies and educational technology. Passionate about transforming traditional education through technology.',
            ],
            [
                'name' => 'Dr. James Mugabo',
                'email' => 'james.mugabo@elearning.com',
                'specialization' => 'Linguistics & Communication',
                'biography' => 'PhD in Applied Linguistics with expertise in multilingual education. Fluent in English, French, Kinyarwanda, and Swahili.',
            ],
            [
                'name' => 'Eng. Peter Habimana',
                'email' => 'peter.habimana@elearning.com',
                'specialization' => 'Engineering & Applied Sciences',
                'biography' => 'Professional engineer with extensive experience in civil, electrical, and mechanical engineering projects across East Africa.',
            ],
            [
                'name' => 'Dr. Marie Uwimana',
                'email' => 'marie.uwimana@elearning.com',
                'specialization' => 'Public Health & Health Informatics',
                'biography' => 'Medical doctor turned public health specialist. Over 12 years of experience in health systems strengthening and health information management.',
            ],
            [
                'name' => 'David Niyonzima',
                'email' => 'david.niyonzima@elearning.com',
                'specialization' => 'Full-Stack Web Development',
                'biography' => 'Senior software engineer and lead instructor with expertise in Laravel, Vue.js, React, and modern web technologies. Built multiple production-scale applications.',
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson.prof@elearning.com',
                'specialization' => 'Data Science & Artificial Intelligence',
                'biography' => 'Data scientist with experience in machine learning, deep learning, and statistical analysis. Previously worked at top tech companies building AI solutions.',
            ],
        ];

        foreach ($instructorData as $data) {
            $user = User::create([
                'name' => $data['name'],
                'username' => Str::slug($data['name']),
                'email' => $data['email'],
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'status' => 'active',
            ]);

            $instructor = Instructor::create([
                'user_id' => $user->id,
                'name' => $data['name'],
                'email' => $data['email'],
                'specialization' => $data['specialization'],
                'biography' => $data['biography'],
            ]);

            $this->instructors[] = $instructor;
        }
    }

    private function createCourses(): void
    {
        $courses = $this->getCourseDefinitions();

        foreach ($courses as $index => $courseDef) {
            $instructor = $this->instructors[$index % count($this->instructors)];
            $categoryId = $this->categories[$courseDef['category']] ?? null;

            if (!$categoryId) {
                $this->command->warn("Category '{$courseDef['category']}' not found, skipping course '{$courseDef['title']}'");
                continue;
            }

            $levels = ['beginner', 'intermediate', 'advanced'];
            $level = $courseDef['level'] ?? $levels[array_rand($levels)];

            $objectives = $this->generateLearningObjectives($courseDef['title']);
            $prereqs = $this->generatePrerequisites($level);

            // Generate a thumbnail URL (placeholder image service)
            $thumbnailUrl = "https://picsum.photos/seed/" . Str::slug($courseDef['title']) . "/800/450";

            $course = Course::create([
                'instructor_id' => $instructor->instructor_id,
                'title' => $courseDef['title'],
                'slug' => Str::slug($courseDef['title']) . '-' . Str::random(6),
                'description' => $courseDef['description'],
                'learning_objectives' => $objectives,
                'prerequisites' => $prereqs,
                'thumbnail' => $thumbnailUrl,
                'category_id' => $categoryId,
                'duration' => rand(20, 80),
                'level' => $level,
                'price' => $courseDef['free'] ? 0 : rand(29, 199),
                'status' => 'published',
            ]);

            $this->command->info("  Creating: {$course->title}");

            // Create modules
            $numModules = rand(5, 12);
            for ($m = 1; $m <= $numModules; $m++) {
                $moduleTitle = $courseDef['modules'][($m - 1) % count($courseDef['modules'])];

                $module = Module::create([
                    'course_id' => $course->course_id,
                    'title' => "{$moduleTitle}",
                    'description' => "Comprehensive coverage of {$moduleTitle} concepts, theories, and practical applications.",
                    'position' => $m,
                ]);

                // Create lessons for this module
                $numLessons = rand(4, 8);
                for ($l = 1; $l <= $numLessons; $l++) {
                    $lesson = Lesson::create([
                        'module_id' => $module->module_id,
                        'title' => $this->generateLessonTitle($moduleTitle, $l, $numLessons),
                        'content' => $this->generateLessonContent($moduleTitle, $l),
                        'video_url' => $l % 3 === 0 ? 'https://www.youtube.com/watch?v=dQw4w9WgXcQ' : null,
                        'document_path' => $l % 4 === 0 ? "documents/{$courseDef['slug']}/module-{$m}-lesson-{$l}.pdf" : null,
                        'duration' => rand(10, 45),
                        'position' => $l,
                    ]);

                    // Create learning materials for this lesson
                    $this->createLearningMaterialsForLesson($lesson, $courseDef['title'], $moduleTitle, $m, $l);
                }

                // Create a quiz for this module
                $numQuestions = rand(4, 8);
                $totalMarks = $numQuestions * 10;

                $quiz = Quiz::create([
                    'course_id' => $course->course_id,
                    'title' => "{$moduleTitle} - Module Quiz",
                    'description' => "Test your understanding of {$moduleTitle} concepts covered in this module.",
                    'total_marks' => $totalMarks,
                    'passing_marks' => ceil($totalMarks * 0.6),
                    'duration_minutes' => rand(15, 30),
                ]);

                // Create questions for the quiz
                for ($q = 1; $q <= $numQuestions; $q++) {
                    Question::create([
                        'quiz_id' => $quiz->quiz_id,
                        'question_text' => $this->generateQuestionText($moduleTitle, $q),
                        'option_a' => 'Option A: ' . $this->randomAnswer(),
                        'option_b' => 'Option B: ' . $this->randomAnswer(),
                        'option_c' => 'Option C: ' . $this->randomAnswer(),
                        'option_d' => 'Option D: ' . $this->randomAnswer(),
                        'correct_answer' => ['a', 'b', 'c', 'd'][array_rand(['a', 'b', 'c', 'd'])],
                    ]);
                }
            }

            // Create final exam for the course
            $this->createFinalExam($course, $courseDef['title']);

            // Create assignments for the course
            $numAssignments = rand(3, 5);
            for ($a = 1; $a <= $numAssignments; $a++) {
                Assignment::create([
                    'course_id' => $course->course_id,
                    'title' => "Assignment {$a}: {$courseDef['assignments'][($a - 1) % count($courseDef['assignments'])]}",
                    'description' => $this->generateAssignmentDesc($courseDef['assignments'][($a - 1) % count($courseDef['assignments'])]),
                    'due_date' => now()->addDays(rand(7, 60)),
                    'total_marks' => rand(50, 100),
                ]);
            }
        }
    }

    private function getCourseDefinitions(): array
    {
        return [
            // ===== INFORMATION TECHNOLOGY (18 courses) =====
            [
                'category' => 'Information Technology',
                'title' => 'Introduction to Computer Science',
                'description' => 'A comprehensive introduction to the fundamental concepts of computer science, including algorithms, data structures, computational thinking, and the history of computing. Perfect for beginners eager to understand how computers work.',
                'level' => 'beginner',
                'free' => true,
                'slug' => 'intro-to-cs',
                'modules' => ['What is Computer Science?', 'Binary & Data Representation', 'Computer Architecture', 'Operating Systems', 'Algorithms & Flowcharts', 'Introduction to Programming', 'Data Structures Basics', 'Computer Networks', 'Databases & Storage', 'Software Engineering Principles', 'Web Technologies', 'The Future of Computing'],
                'assignments' => ['Binary Calculator', 'Algorithm Design Challenge', 'Simple Database Design', 'Research Paper on Computing History'],
            ],
            [
                'category' => 'Information Technology',
                'title' => 'Programming Fundamentals',
                'description' => 'Master the core concepts of programming including variables, control structures, functions, arrays, and object-oriented programming. Uses Python as the teaching language but concepts apply to any language.',
                'level' => 'beginner',
                'free' => true,
                'slug' => 'programming-fundamentals',
                'modules' => ['Getting Started with Programming', 'Variables & Data Types', 'Control Flow & Conditionals', 'Loops & Iteration', 'Functions & Methods', 'Arrays & Collections', 'Strings & Text Processing', 'Object-Oriented Programming', 'File I/O & Exception Handling', 'Basic Debugging Techniques'],
                'assignments' => ['Build a Calculator', 'Student Grade Manager', 'File Processor Tool', 'Simple Game Development'],
            ],
            [
                'category' => 'Information Technology',
                'title' => 'Web Development',
                'description' => 'Learn to build modern, responsive websites from scratch. Covers HTML5, CSS3, JavaScript, responsive design, and frontend frameworks. Hands-on projects build a complete portfolio.',
                'level' => 'beginner',
                'free' => false,
                'slug' => 'web-development',
                'modules' => ['HTML5 Fundamentals', 'CSS3 Styling & Layout', 'Responsive Design', 'JavaScript Essentials', 'DOM Manipulation', 'Forms & Validation', 'CSS Frameworks (Bootstrap)', 'Introduction to APIs', 'Web Performance', 'Deployment & Hosting'],
                'assignments' => ['Build a Personal Portfolio', 'Responsive Landing Page', 'Interactive Quiz App', 'E-commerce Product Page', 'Blog Template Design'],
            ],
            [
                'category' => 'Information Technology',
                'title' => 'Laravel Framework Development',
                'description' => 'Deep dive into Laravel, the most popular PHP framework. Learn MVC architecture, routing, Eloquent ORM, Blade templating, authentication, and building RESTful APIs.',
                'level' => 'intermediate',
                'free' => false,
                'slug' => 'laravel-development',
                'modules' => ['Introduction to Laravel', 'MVC Architecture', 'Routing & Controllers', 'Blade Templating Engine', 'Eloquent ORM', 'Database Migrations & Seeding', 'Forms & Validation', 'Authentication & Authorization', 'RESTful API Development', 'Testing with PHPUnit', 'Queues & Jobs', 'Deployment Best Practices'],
                'assignments' => ['Build a Blog System', 'REST API for E-commerce', 'Task Management App', 'Multi-role Auth System'],
            ],
            [
                'category' => 'Information Technology',
                'title' => 'PHP Programming',
                'description' => 'Master PHP programming from basics to advanced concepts. Learn syntax, functions, OOP, PDO, error handling, and modern PHP practices used in professional development.',
                'level' => 'beginner',
                'free' => false,
                'slug' => 'php-programming',
                'modules' => ['PHP Basics & Syntax', 'Variables & Data Types', 'Control Structures', 'Functions & Scope', 'Working with Arrays', 'String Manipulation', 'File System Operations', 'Object-Oriented PHP', 'PDO & Database Access', 'Error & Exception Handling', 'Sessions & Cookies', 'Security Best Practices'],
                'assignments' => ['Contact Form Processor', 'User Authentication System', 'File Upload Manager', 'Simple CMS Builder'],
            ],
            [
                'category' => 'Information Technology',
                'title' => 'Java Programming',
                'description' => 'Comprehensive Java course covering core concepts, OOP principles, collections framework, multithreading, and GUI development. Build enterprise-ready applications.',
                'level' => 'intermediate',
                'free' => false,
                'slug' => 'java-programming',
                'modules' => ['Java Fundamentals', 'Object-Oriented Java', 'Inheritance & Polymorphism', 'Interfaces & Abstract Classes', 'Collections Framework', 'Generics & Enums', 'Exception Handling', 'File I/O & Serialization', 'Multithreading', 'Lambda Expressions & Streams', 'JDBC & Database Integration', 'JavaFX GUI Development'],
                'assignments' => ['Bank Account System', 'Library Management App', 'Multi-threaded Chat Server', 'GUI Calculator Application'],
            ],
            [
                'category' => 'Information Technology',
                'title' => 'Python Programming',
                'description' => 'Learn Python from scratch — one of the most versatile programming languages. Covers syntax, data structures, OOP, file handling, libraries, and real-world projects.',
                'level' => 'beginner',
                'free' => true,
                'slug' => 'python-programming',
                'modules' => ['Python Basics', 'Data Types & Variables', 'Lists, Tuples & Dictionaries', 'Control Flow & Loops', 'Functions & Modules', 'File Handling', 'Object-Oriented Python', 'Error Handling', 'Working with Libraries', 'Web Scraping Basics', 'Introduction to Flask', 'Data Analysis with Pandas'],
                'assignments' => ['Weather Data Analyzer', 'Task List Manager', 'Web Scraper Tool', 'Data Visualization Dashboard'],
            ],
            [
                'category' => 'Information Technology',
                'title' => 'Mobile App Development with React Native',
                'description' => 'Build cross-platform mobile applications using React Native. Learn component architecture, navigation, state management, and publish to both iOS and Android stores.',
                'level' => 'intermediate',
                'free' => false,
                'slug' => 'react-native-development',
                'modules' => ['React Native Fundamentals', 'Components & Props', 'State & Lifecycle', 'Navigation & Routing', 'Working with APIs', 'Local Storage & AsyncStorage', 'User Authentication', 'Camera & Device Features', 'Push Notifications', 'App Store Deployment'],
                'assignments' => ['To-Do Mobile App', 'Weather Forecast App', 'Social Media Feed', 'Fitness Tracker App'],
            ],
            [
                'category' => 'Information Technology',
                'title' => 'Database Management Systems',
                'description' => 'Comprehensive coverage of database design, SQL, normalization, indexing, transactions, and both relational and NoSQL database systems.',
                'level' => 'intermediate',
                'free' => false,
                'slug' => 'database-management',
                'modules' => ['Introduction to Databases', 'Relational Model', 'SQL Fundamentals', 'Joins & Subqueries', 'Database Normalization', 'Indexing & Performance', 'Transactions & ACID', 'Stored Procedures & Triggers', 'Backup & Recovery', 'NoSQL Databases (MongoDB)', 'Database Security', 'Data Warehousing Basics'],
                'assignments' => ['Library Database Design', 'E-commerce Database Schema', 'SQL Query Optimization', 'Database Migration Script'],
            ],
            [
                'category' => 'Information Technology',
                'title' => 'Networking Fundamentals',
                'description' => 'Understand computer networking from the ground up. Learn OSI model, TCP/IP, routing, switching, subnetting, and network security essentials.',
                'level' => 'beginner',
                'free' => false,
                'slug' => 'networking-fundamentals',
                'modules' => ['Network Basics', 'OSI & TCP/IP Models', 'IP Addressing & Subnetting', 'Ethernet & Switching', 'Routing Protocols', 'DNS & DHCP', 'Network Security Basics', 'Firewalls & VPNs', 'Wireless Networking', 'Network Troubleshooting', 'SDN & Network Automation'],
                'assignments' => ['Subnet Calculator Tool', 'Network Topology Design', 'Packet Analysis Lab', 'Firewall Configuration Guide'],
            ],
            [
                'category' => 'Information Technology',
                'title' => 'Cybersecurity Fundamentals',
                'description' => 'Learn to protect systems, networks, and data from cyber threats. Covers encryption, threat modeling, penetration testing, and security best practices.',
                'level' => 'intermediate',
                'free' => false,
                'slug' => 'cybersecurity-basics',
                'modules' => ['Cybersecurity Overview', 'Threats & Attack Vectors', 'Cryptography Basics', 'Network Security', 'Web Application Security', 'Malware Analysis', 'Social Engineering', 'Incident Response', 'Penetration Testing', 'Security Policies & Compliance', 'Cloud Security', 'Digital Forensics'],
                'assignments' => ['Security Audit Report', 'Web Vulnerability Assessment', 'Encryption Implementation', 'Incident Response Plan'],
            ],
            [
                'category' => 'Information Technology',
                'title' => 'Cloud Computing with AWS',
                'description' => 'Master cloud computing concepts and AWS services. Learn EC2, S3, Lambda, RDS, and design scalable, fault-tolerant cloud architectures.',
                'level' => 'advanced',
                'free' => false,
                'slug' => 'cloud-computing-aws',
                'modules' => ['Cloud Computing Concepts', 'AWS Global Infrastructure', 'EC2 & Compute Services', 'S3 & Storage Solutions', 'VPC & Networking', 'RDS & Database Services', 'Lambda & Serverless', 'CloudFormation & IaC', 'Monitoring & CloudWatch', 'Security & IAM', 'Cost Optimization', 'Architecting for Scale'],
                'assignments' => ['Deploy a Web App on AWS', 'Serverless API Design', 'Cloud Architecture Blueprint', 'Cost Analysis Report'],
            ],
            [
                'category' => 'Information Technology',
                'title' => 'Artificial Intelligence',
                'description' => 'Explore the fundamentals of AI including search algorithms, knowledge representation, machine learning, neural networks, and natural language processing.',
                'level' => 'advanced',
                'free' => false,
                'slug' => 'artificial-intelligence',
                'modules' => ['What is AI?', 'Problem Solving & Search', 'Knowledge Representation', 'Logic & Reasoning', 'Probability & Uncertainty', 'Machine Learning Basics', 'Neural Networks', 'Natural Language Processing', 'Computer Vision', 'Reinforcement Learning', 'Ethics in AI', 'AI in Practice'],
                'assignments' => ['Search Algorithm Implementation', 'Expert System Prototype', 'NLP Text Classifier', 'AI Ethics Case Study'],
            ],
            [
                'category' => 'Information Technology',
                'title' => 'Machine Learning',
                'description' => 'Hands-on machine learning course covering supervised and unsupervised learning, regression, classification, clustering, and deep learning with real datasets.',
                'level' => 'advanced',
                'free' => false,
                'slug' => 'machine-learning',
                'modules' => ['ML Fundamentals', 'Data Preprocessing', 'Linear & Logistic Regression', 'Decision Trees & Random Forests', 'Support Vector Machines', 'K-Nearest Neighbors', 'K-Means Clustering', 'Dimensionality Reduction', 'Neural Networks & Deep Learning', 'Model Evaluation & Tuning', 'Feature Engineering', 'ML Pipeline Deployment'],
                'assignments' => ['House Price Prediction', 'Image Classification Model', 'Customer Segmentation', 'Sentiment Analysis Engine'],
            ],
            [
                'category' => 'Information Technology',
                'title' => 'Data Structures & Algorithms',
                'description' => 'Master essential data structures and algorithms for coding interviews and building efficient software. Covers arrays, linked lists, trees, graphs, sorting, and searching.',
                'level' => 'intermediate',
                'free' => false,
                'slug' => 'data-structures-algorithms',
                'modules' => ['Algorithm Analysis', 'Arrays & Strings', 'Linked Lists', 'Stacks & Queues', 'Trees & Binary Search Trees', 'Heaps & Priority Queues', 'Hash Tables', 'Graphs & Graph Algorithms', 'Sorting Algorithms', 'Searching Algorithms', 'Dynamic Programming', 'Greedy Algorithms'],
                'assignments' => ['Sorting Visualizer', 'Pathfinding Algorithm', 'Data Structure Library', 'Algorithm Complexity Analysis'],
            ],
            [
                'category' => 'Information Technology',
                'title' => 'Blockchain Development',
                'description' => 'Understand blockchain technology, cryptocurrencies, smart contracts, and build decentralized applications (dApps) using Ethereum and Solidity.',
                'level' => 'advanced',
                'free' => false,
                'slug' => 'blockchain-development',
                'modules' => ['Blockchain Fundamentals', 'Cryptography in Blockchain', 'Bitcoin & Cryptocurrency', 'Ethereum & Smart Contracts', 'Solidity Programming', 'dApp Development', 'Web3 & Frontend Integration', 'DeFi Concepts', 'NFT Development', 'Blockchain Security', 'Layer 2 Scaling', 'Enterprise Blockchain'],
                'assignments' => ['Build a Simple Cryptocurrency', 'Smart Contract Development', 'NFT Marketplace', 'DeFi Lending Protocol'],
            ],
            [
                'category' => 'Information Technology',
                'title' => 'DevOps & CI/CD',
                'description' => 'Learn DevOps practices, continuous integration, continuous delivery, containerization with Docker, and orchestration with Kubernetes.',
                'level' => 'advanced',
                'free' => false,
                'slug' => 'devops-cicd',
                'modules' => ['DevOps Culture & Practices', 'Version Control with Git', 'Continuous Integration', 'Docker Containers', 'Docker Compose', 'Kubernetes Basics', 'CI/CD Pipelines', 'Infrastructure as Code', 'Configuration Management', 'Monitoring & Logging', 'Cloud Deployment', 'DevSecOps'],
                'assignments' => ['Dockerize an Application', 'Build a CI/CD Pipeline', 'Kubernetes Deployment', 'Infrastructure Automation Script'],
            ],
            [
                'category' => 'Information Technology',
                'title' => 'Internet of Things (IoT)',
                'description' => 'Explore IoT architecture, sensors, microcontrollers, communication protocols, and build connected devices with cloud integration.',
                'level' => 'intermediate',
                'free' => false,
                'slug' => 'internet-of-things',
                'modules' => ['IoT Fundamentals', 'Sensors & Actuators', 'Microcontrollers (Arduino)', 'Raspberry Pi Programming', 'IoT Communication Protocols', 'MQTT & Data Streaming', 'Cloud IoT Platforms', 'IoT Data Analytics', 'Edge Computing', 'IoT Security', 'Building IoT Solutions', 'Case Studies'],
                'assignments' => ['Smart Home Sensor System', 'Weather Station Project', 'IoT Data Dashboard', 'Security System Prototype'],
            ],
            // ===== BUSINESS & ENTREPRENEURSHIP (12 courses) =====
            [
                'category' => 'Business & Entrepreneurship',
                'title' => 'Business Management',
                'description' => 'Learn the core principles of managing a business — planning, organizing, leading, and controlling. Covers organizational behavior, decision-making, and strategic management.',
                'level' => 'beginner',
                'free' => true,
                'slug' => 'business-management',
                'modules' => ['Introduction to Management', 'Planning & Strategy', 'Organizational Structure', 'Leadership & Motivation', 'Communication Management', 'Decision Making', 'Human Resource Management', 'Operations Management', 'Financial Management', 'Change Management', 'Business Ethics', 'Global Management'],
                'assignments' => ['Business Plan Creation', 'Organizational Analysis', 'Strategic Plan Report', 'Management Case Study'],
            ],
            [
                'category' => 'Business & Entrepreneurship',
                'title' => 'Entrepreneurship',
                'description' => 'Turn your business idea into reality. Learn ideation, business modeling, funding, marketing, and scaling strategies from successful entrepreneurs.',
                'level' => 'beginner',
                'free' => true,
                'slug' => 'entrepreneurship',
                'modules' => ['The Entrepreneurial Mindset', 'Identifying Opportunities', 'Business Model Canvas', 'Market Research', 'Lean Startup Methodology', 'Business Plan Writing', 'Funding & Investment', 'Legal Structures', 'Branding & Marketing', 'Sales Strategies', 'Scaling Your Business', 'Social Entrepreneurship'],
                'assignments' => ['Business Model Canvas', 'Market Research Report', 'Pitch Deck Creation', 'Minimum Viable Product Plan'],
            ],
            [
                'category' => 'Business & Entrepreneurship',
                'title' => 'Marketing Fundamentals',
                'description' => 'Master the 4 Ps of marketing — Product, Price, Place, Promotion. Learn market segmentation, targeting, positioning, and consumer behavior.',
                'level' => 'beginner',
                'free' => false,
                'slug' => 'marketing-fundamentals',
                'modules' => ['Marketing Principles', 'Market Research', 'Consumer Behavior', 'Segmentation & Targeting', 'Branding Strategy', 'Product Management', 'Pricing Strategies', 'Distribution Channels', 'Integrated Marketing Communications', 'Digital Marketing Overview', 'Marketing Analytics', 'Marketing Ethics'],
                'assignments' => ['Marketing Plan Development', 'Brand Audit Report', 'Consumer Survey Analysis', 'Advertising Campaign Proposal'],
            ],
            [
                'category' => 'Business & Entrepreneurship',
                'title' => 'Digital Marketing',
                'description' => 'Learn to market products and services online. Covers SEO, SEM, social media marketing, email marketing, content strategy, and analytics.',
                'level' => 'intermediate',
                'free' => false,
                'slug' => 'digital-marketing',
                'modules' => ['Digital Marketing Landscape', 'Search Engine Optimization', 'Search Engine Marketing', 'Social Media Marketing', 'Content Marketing', 'Email Marketing', 'Affiliate Marketing', 'Influencer Marketing', 'Google Analytics', 'Conversion Optimization', 'Marketing Automation', 'Digital Marketing Strategy'],
                'assignments' => ['SEO Audit & Recommendations', 'Social Media Campaign', 'Email Marketing Sequence', 'Digital Marketing Strategy Plan'],
            ],
            [
                'category' => 'Business & Entrepreneurship',
                'title' => 'Accounting Principles',
                'description' => 'Understand the fundamentals of accounting — double-entry bookkeeping, financial statements, ledgers, and basic financial analysis for business decision-making.',
                'level' => 'beginner',
                'free' => false,
                'slug' => 'accounting-principles',
                'modules' => ['Accounting Basics', 'The Accounting Equation', 'Double-Entry System', 'Journal Entries', 'Ledgers & Trial Balance', 'Adjusting Entries', 'Financial Statements', 'Cash Flow Statement', 'Accounts Receivable & Payable', 'Inventory Accounting', 'Depreciation & Amortization', 'Financial Ratios'],
                'assignments' => ['General Ledger Preparation', 'Financial Statement Analysis', 'Budget Preparation', 'Accounting System Design'],
            ],
            [
                'category' => 'Business & Entrepreneurship',
                'title' => 'Financial Management',
                'description' => 'Learn to manage business finances effectively. Covers budgeting, capital structure, investment decisions, risk management, and financial planning.',
                'level' => 'intermediate',
                'free' => false,
                'slug' => 'financial-management',
                'modules' => ['Financial Management Overview', 'Time Value of Money', 'Financial Statement Analysis', 'Working Capital Management', 'Capital Budgeting', 'Cost of Capital', 'Leverage & Capital Structure', 'Dividend Policy', 'Risk & Return', 'Portfolio Management', 'International Finance', 'Financial Modeling'],
                'assignments' => ['Financial Ratio Analysis', 'Investment Appraisal Report', 'Budgeting & Forecasting Model', 'Financial Risk Assessment'],
            ],
            [
                'category' => 'Business & Entrepreneurship',
                'title' => 'Project Management',
                'description' => 'Master project management methodologies including Agile, Scrum, and Waterfall. Learn to plan, execute, monitor, and close projects successfully.',
                'level' => 'intermediate',
                'free' => false,
                'slug' => 'project-management',
                'modules' => ['Project Management Basics', 'Project Lifecycle', 'Project Planning', 'Work Breakdown Structure', 'Scheduling & Gantt Charts', 'Resource Management', 'Risk Management', 'Quality Management', 'Agile & Scrum', 'Project Monitoring & Control', 'Project Communication', 'Project Closure'],
                'assignments' => ['Project Charter Development', 'WBS & Schedule Creation', 'Risk Register Document', 'Project Closure Report'],
            ],
            [
                'category' => 'Business & Entrepreneurship',
                'title' => 'Human Resource Management',
                'description' => 'Learn to manage an organization\'s most valuable asset — its people. Covers recruitment, training, performance management, compensation, and labor relations.',
                'level' => 'beginner',
                'free' => false,
                'slug' => 'human-resource-management',
                'modules' => ['HRM Overview', 'Job Analysis & Design', 'Recruitment & Selection', 'Training & Development', 'Performance Management', 'Compensation & Benefits', 'Employee Relations', 'Labor Laws & Compliance', 'Organizational Culture', 'Diversity & Inclusion', 'HR Analytics', 'Strategic HRM'],
                'assignments' => ['Job Description Creation', 'Recruitment Plan', 'Performance Appraisal System', 'HR Policy Manual'],
            ],
            [
                'category' => 'Business & Entrepreneurship',
                'title' => 'E-commerce Strategies',
                'description' => 'Learn to build and grow an online business. Covers platform selection, product sourcing, payment gateways, logistics, and customer acquisition.',
                'level' => 'intermediate',
                'free' => false,
                'slug' => 'ecommerce-strategies',
                'modules' => ['E-commerce Overview', 'Business Models', 'Platform Selection', 'Store Setup & Design', 'Product Management', 'Payment Processing', 'Shipping & Fulfillment', 'Customer Acquisition', 'Conversion Optimization', 'Customer Retention', 'E-commerce Analytics', 'International E-commerce'],
                'assignments' => ['E-commerce Business Plan', 'Store Design Mockup', 'Digital Marketing Funnel', 'Customer Journey Map'],
            ],
            [
                'category' => 'Business & Entrepreneurship',
                'title' => 'Business Analytics',
                'description' => 'Use data to drive business decisions. Learn data collection, visualization, statistical analysis, and tools like Excel, Tableau, and Power BI.',
                'level' => 'intermediate',
                'free' => false,
                'slug' => 'business-analytics',
                'modules' => ['Analytics Fundamentals', 'Data Collection & Sources', 'Data Cleaning & Preparation', 'Exploratory Data Analysis', 'Statistical Analysis', 'Data Visualization', 'Excel for Analytics', 'SQL for Business Analysis', 'Tableau Fundamentals', 'Power BI Basics', 'Predictive Analytics', 'Data-Driven Decision Making'],
                'assignments' => ['Sales Data Analysis', 'Dashboard Creation', 'Customer Segmentation Analysis', 'Forecasting Model'],
            ],
            [
                'category' => 'Business & Entrepreneurship',
                'title' => 'Supply Chain Management',
                'description' => 'Understand the end-to-end supply chain — procurement, inventory, logistics, warehousing, and global supply chain strategies.',
                'level' => 'intermediate',
                'free' => false,
                'slug' => 'supply-chain-management',
                'modules' => ['SCM Overview', 'Procurement & Sourcing', 'Inventory Management', 'Warehousing Operations', 'Transportation & Logistics', 'Demand Forecasting', 'Supply Chain Analytics', 'Lean & Six Sigma', 'Global Supply Chain', 'Supplier Relationship Management', 'Technology in SCM', 'Sustainability in SCM'],
                'assignments' => ['Supply Chain Process Map', 'Inventory Optimization Plan', 'Logistics Strategy Report', 'Supplier Evaluation Framework'],
            ],
            [
                'category' => 'Business & Entrepreneurship',
                'title' => 'Negotiation Skills',
                'description' => 'Develop effective negotiation strategies for business deals, salary discussions, partnership agreements, and conflict resolution.',
                'level' => 'beginner',
                'free' => false,
                'slug' => 'negotiation-skills',
                'modules' => ['Negotiation Fundamentals', 'Preparation & Planning', 'BATNA & ZOPA', 'Communication Strategies', 'Persuasion Techniques', 'Dealing with Difficult Negotiators', 'Cross-Cultural Negotiation', 'Salary & Contract Negotiation', 'Partnership Negotiations', 'Conflict Resolution', 'Ethics in Negotiation', 'Advanced Negotiation Tactics'],
                'assignments' => ['Negotiation Simulation', 'Preparation Worksheet', 'Case Study Analysis', 'Role-Play Reflection'],
            ],
            // ===== EDUCATION (6 courses) =====
            [
                'category' => 'Education',
                'title' => 'Teaching Methodologies',
                'description' => 'Explore modern teaching approaches including student-centered learning, project-based learning, flipped classrooms, and differentiated instruction.',
                'level' => 'beginner',
                'free' => true,
                'slug' => 'teaching-methodologies',
                'modules' => ['Introduction to Teaching', 'Learning Theories', 'Student-Centered Learning', 'Project-Based Learning', 'Flipped Classroom', 'Differentiated Instruction', 'Cooperative Learning', 'Inquiry-Based Learning', 'Assessment Strategies', 'Classroom Technology', 'Lesson Planning', 'Reflective Teaching'],
                'assignments' => ['Lesson Plan Design', 'Teaching Philosophy Statement', 'Curriculum Mapping', 'Assessment Rubric Creation'],
            ],
            [
                'category' => 'Education',
                'title' => 'Educational Technology',
                'description' => 'Learn to integrate technology effectively in education. Covers LMS platforms, digital tools, multimedia resources, and online teaching strategies.',
                'level' => 'intermediate',
                'free' => false,
                'slug' => 'educational-technology',
                'modules' => ['EdTech Overview', 'Learning Management Systems', 'Digital Content Creation', 'Multimedia in Education', 'Online Assessment Tools', 'Gamification in Education', 'Virtual Classrooms', 'Mobile Learning', 'Accessibility & Universal Design', 'Data Analytics in Education', 'Emerging Technologies', 'EdTech Implementation'],
                'assignments' => ['LMS Course Design', 'Digital Lesson Plan', 'EdTech Tool Evaluation', 'Online Course Blueprint'],
            ],
            [
                'category' => 'Education',
                'title' => 'Classroom Management',
                'description' => 'Develop effective classroom management strategies — behavior management, student engagement, classroom routines, and creating a positive learning environment.',
                'level' => 'beginner',
                'free' => false,
                'slug' => 'classroom-management',
                'modules' => ['Classroom Environment', 'Setting Expectations', 'Building Relationships', 'Engagement Strategies', 'Behavior Management', 'Positive Reinforcement', 'Dealing with Disruptions', 'Classroom Routines', 'Group Work Management', 'Time Management', 'Communication with Parents', 'Self-Care for Teachers'],
                'assignments' => ['Classroom Management Plan', 'Behavior Intervention Strategy', 'Parent Communication Template', 'Classroom Setup Design'],
            ],
            [
                'category' => 'Education',
                'title' => 'Curriculum Development',
                'description' => 'Learn to design effective curricula — needs analysis, learning objectives, content selection, instructional strategies, and evaluation methods.',
                'level' => 'intermediate',
                'free' => false,
                'slug' => 'curriculum-development',
                'modules' => ['Curriculum Foundations', 'Needs Assessment', 'Learning Objectives (Bloom\'s)', 'Content Selection', 'Instructional Strategies', 'Learning Activities Design', 'Assessment Design', 'Curriculum Mapping', 'Standards Alignment', 'Curriculum Evaluation', 'Revision & Improvement', 'Digital Curriculum'],
                'assignments' => ['Course Curriculum Design', 'Needs Assessment Report', 'Assessment Blueprint', 'Curriculum Evaluation Plan'],
            ],
            [
                'category' => 'Education',
                'title' => 'E-Learning Design',
                'description' => 'Master the art of creating effective online learning experiences. Covers instructional design models, multimedia principles, and learner engagement.',
                'level' => 'intermediate',
                'free' => false,
                'slug' => 'elearning-design',
                'modules' => ['E-Learning Foundations', 'Instructional Design Models (ADDIE)', 'Learning Theories Applied', 'Content Chunking', 'Multimedia Design Principles', 'Interactive Elements', 'Video & Audio Production', 'Scenario-Based Learning', 'Gamification & Badges', 'Mobile Learning Design', 'Accessibility Standards', 'Quality Assurance'],
                'assignments' => ['E-Learning Module Design', 'Storyboard Creation', 'Interactive Activity Design', 'Course Quality Evaluation'],
            ],
            [
                'category' => 'Education',
                'title' => 'Educational Psychology',
                'description' => 'Understand how students learn and develop. Covers cognitive development, motivation, learning styles, and the psychological foundations of education.',
                'level' => 'intermediate',
                'free' => false,
                'slug' => 'educational-psychology',
                'modules' => ['Introduction to Ed Psychology', 'Cognitive Development (Piaget)', 'Social Development (Vygotsky)', 'Learning Styles & Multiple Intelligences', 'Motivation Theories', 'Memory & Information Processing', 'Metacognition', 'Classroom Assessment', 'Exceptional Learners', 'Cultural & Social Factors', 'Positive Psychology in Education', 'Research Methods'],
                'assignments' => ['Child Development Case Study', 'Motivation Strategy Plan', 'Learning Style Analysis', 'Research Paper on Learning Theory'],
            ],
            // ===== LANGUAGES (6 courses) =====
            [
                'category' => 'Languages',
                'title' => 'English Language',
                'description' => 'Comprehensive English language course covering grammar, vocabulary, reading comprehension, writing skills, and conversational English for all levels.',
                'level' => 'beginner',
                'free' => true,
                'slug' => 'english-language',
                'modules' => ['English Basics', 'Grammar Fundamentals', 'Vocabulary Building', 'Sentence Structure', 'Reading Comprehension', 'Writing Skills', 'Conversational English', 'Business English', 'Academic English', 'English for Exams', 'Listening Skills', 'Pronunciation & Accent'],
                'assignments' => ['Essay Writing', 'Book Review', 'Business Email Composition', 'Presentation Script'],
            ],
            [
                'category' => 'Languages',
                'title' => 'French Language',
                'description' => 'Learn French from beginner to advanced. Covers grammar, vocabulary, conversation, reading, writing, and French culture.',
                'level' => 'beginner',
                'free' => false,
                'slug' => 'french-language',
                'modules' => ['French Basics', 'Pronunciation & Accents', 'Nouns & Articles', 'Adjectives & Adverbs', 'Present Tense Verbs', 'Past Tenses', 'Future Tense', 'Subjunctive Mood', 'Vocabulary Themes', 'French Conversation', 'French Writing', 'French Culture & Civilization'],
                'assignments' => ['French Composition', 'Dialogue Creation', 'Cultural Presentation', 'Translation Exercise'],
            ],
            [
                'category' => 'Languages',
                'title' => 'Kinyarwanda Language',
                'description' => 'Learn Kinyarwanda, the national language of Rwanda. Covers basic grammar, common phrases, vocabulary, and conversational skills for everyday communication.',
                'level' => 'beginner',
                'free' => true,
                'slug' => 'kinyarwanda-language',
                'modules' => ['Introduction to Kinyarwanda', 'Alphabet & Pronunciation', 'Basic Greetings', 'Nouns & Classes', 'Verb Conjugation', 'Sentence Structure', 'Numbers & Counting', 'Days, Months & Time', 'Family & Relationships', 'Food & Daily Life', 'Rwandan Culture', 'Advanced Conversation'],
                'assignments' => ['Greetings Dialogue', 'Family Description Essay', 'Cultural Story Translation', 'Conversation Practice'],
            ],
            [
                'category' => 'Languages',
                'title' => 'Communication Skills',
                'description' => 'Master effective communication in personal and professional settings. Covers verbal, non-verbal, written, and digital communication strategies.',
                'level' => 'beginner',
                'free' => false,
                'slug' => 'communication-skills',
                'modules' => ['Communication Basics', 'Active Listening', 'Non-Verbal Communication', 'Public Speaking', 'Written Communication', 'Business Communication', 'Interpersonal Skills', 'Conflict Resolution', 'Presentation Skills', 'Digital Communication', 'Persuasive Communication', 'Cross-Cultural Communication'],
                'assignments' => ['Persuasive Speech Script', 'Business Report Writing', 'Communication Audit', 'Presentation Video Recording'],
            ],
            [
                'category' => 'Languages',
                'title' => 'Academic Writing',
                'description' => 'Develop advanced academic writing skills — essays, research papers, thesis writing, citations, and academic publishing.',
                'level' => 'advanced',
                'free' => false,
                'slug' => 'academic-writing',
                'modules' => ['Academic Writing Overview', 'Essay Structure', 'Research & Sources', 'Critical Analysis', 'Argumentation', 'Citation & Referencing (APA/MLA)', 'Literature Review', 'Research Paper Writing', 'Thesis & Dissertation Writing', 'Abstracts & Proposals', 'Academic Style & Tone', 'Publishing & Peer Review'],
                'assignments' => ['Critical Essay', 'Literature Review', 'Research Proposal', 'Annotated Bibliography'],
            ],
            [
                'category' => 'Languages',
                'title' => 'Public Speaking',
                'description' => 'Overcome fear and become a confident public speaker. Learn speech writing, delivery techniques, audience engagement, and presentation skills.',
                'level' => 'beginner',
                'free' => false,
                'slug' => 'public-speaking',
                'modules' => ['Overcoming Fear', 'Speech Structure', 'Storytelling Techniques', 'Vocal Delivery', 'Body Language', 'Visual Aids', 'Audience Engagement', 'Impromptu Speaking', 'Persuasive Speaking', 'Informative Speaking', 'Special Occasion Speeches', 'Speech Evaluation'],
                'assignments' => ['Informative Speech Script', 'Persuasive Speech Outline', 'TED-Style Talk Preparation', 'Speech Self-Evaluation'],
            ],
            // ===== ENGINEERING (5 courses) =====
            [
                'category' => 'Engineering',
                'title' => 'Electrical Engineering Basics',
                'description' => 'Fundamentals of electrical engineering — circuits, voltage, current, resistance, power, and introduction to electronics.',
                'level' => 'beginner',
                'free' => false,
                'slug' => 'electrical-engineering-basics',
                'modules' => ['Electrical Fundamentals', 'Ohm\'s Law & Circuits', 'Series & Parallel Circuits', 'Kirchhoff\'s Laws', 'Capacitors & Inductors', 'AC & DC Circuits', 'Transformers', 'Diodes & Rectifiers', 'Transistors & Amplifiers', 'Digital Logic Basics', 'Microcontrollers', 'Power Systems'],
                'assignments' => ['Circuit Analysis Problems', 'Breadboard Circuit Build', 'Logic Gate Design', 'Power Supply Design'],
            ],
            [
                'category' => 'Engineering',
                'title' => 'Civil Engineering Basics',
                'description' => 'Introduction to civil engineering — structural analysis, materials, surveying, construction methods, and infrastructure design.',
                'level' => 'beginner',
                'free' => false,
                'slug' => 'civil-engineering-basics',
                'modules' => ['Civil Engineering Overview', 'Engineering Materials', 'Structural Analysis', 'Load Calculations', 'Beams & Columns', 'Foundation Design', 'Surveying Basics', 'Construction Methods', 'Fluid Mechanics', 'Transportation Engineering', 'Environmental Engineering', 'Project Management'],
                'assignments' => ['Structural Load Calculation', 'Bridge Design Sketch', 'Surveying Field Report', 'Construction Project Plan'],
            ],
            [
                'category' => 'Engineering',
                'title' => 'Mechanical Engineering Basics',
                'description' => 'Core concepts of mechanical engineering — mechanics, thermodynamics, fluid dynamics, materials science, and machine design.',
                'level' => 'beginner',
                'free' => false,
                'slug' => 'mechanical-engineering-basics',
                'modules' => ['Engineering Mechanics', 'Statics & Dynamics', 'Thermodynamics', 'Fluid Mechanics', 'Heat Transfer', 'Materials Science', 'Machine Design', 'Manufacturing Processes', 'CAD & Drafting', 'Kinematics', 'Vibrations & Control', 'Renewable Energy'],
                'assignments' => ['Stress Analysis Report', 'CAD Model Design', 'Thermodynamic Cycle Analysis', 'Machine Component Design'],
            ],
            [
                'category' => 'Engineering',
                'title' => 'Robotics Fundamentals',
                'description' => 'Learn robotics from the ground up — kinematics, sensors, actuators, control systems, and programming robot behavior.',
                'level' => 'intermediate',
                'free' => false,
                'slug' => 'robotics-fundamentals',
                'modules' => ['Robotics Overview', 'Robot Kinematics', 'Sensors & Perception', 'Actuators & Motors', 'Control Systems (PID)', 'Robot Programming', 'Embedded Systems', 'Computer Vision', 'Path Planning', 'Robot Operating System (ROS)', 'Human-Robot Interaction', 'Robotics Projects'],
                'assignments' => ['Robot Arm Kinematics', 'Sensor Integration Project', 'PID Controller Tuning', 'Autonomous Navigation Algorithm'],
            ],
            [
                'category' => 'Engineering',
                'title' => 'Structural Engineering',
                'description' => 'Advanced study of structural analysis and design — steel structures, reinforced concrete, seismic design, and structural health monitoring.',
                'level' => 'advanced',
                'free' => false,
                'slug' => 'structural-engineering',
                'modules' => ['Structural Engineering Principles', 'Steel Structure Design', 'Reinforced Concrete Design', 'Seismic Analysis', 'Wind Load Analysis', 'Foundation Engineering', 'Bridge Engineering', 'Finite Element Analysis', 'Structural Dynamics', 'Prestressed Concrete', 'Structural Health Monitoring', 'Building Codes & Standards'],
                'assignments' => ['Steel Frame Design', 'Concrete Beam Analysis', 'Seismic Retrofit Plan', 'Structural Audit Report'],
            ],
            // ===== HEALTH SCIENCES (5 courses) =====
            [
                'category' => 'Health Sciences',
                'title' => 'Public Health',
                'description' => 'Comprehensive introduction to public health — epidemiology, biostatistics, health policy, environmental health, and community health interventions.',
                'level' => 'beginner',
                'free' => true,
                'slug' => 'public-health',
                'modules' => ['Public Health Overview', 'Epidemiology Fundamentals', 'Biostatistics Basics', 'Health Policy & Management', 'Environmental Health', 'Community Health', 'Disease Prevention', 'Health Promotion', 'Global Health', 'Maternal & Child Health', 'Infectious Disease Control', 'Health Systems Strengthening'],
                'assignments' => ['Epidemiological Study Design', 'Community Health Assessment', 'Policy Brief Writing', 'Health Program Evaluation'],
            ],
            [
                'category' => 'Health Sciences',
                'title' => 'First Aid Training',
                'description' => 'Learn life-saving first aid skills — CPR, wound care, fracture management, burns, choking, and emergency response procedures.',
                'level' => 'beginner',
                'free' => false,
                'slug' => 'first-aid-training',
                'modules' => ['First Aid Principles', 'Scene Safety & Assessment', 'CPR & AED', 'Choking Response', 'Wound Care & Bleeding', 'Fractures & Splinting', 'Burn Management', 'Allergic Reactions', 'Poisoning & Bites', 'Heat & Cold Emergencies', 'Medical Emergencies', 'Emergency Preparedness'],
                'assignments' => ['Emergency Action Plan', 'CPR Skills Checklist', 'First Aid Kit Inventory', 'Scenario Response Guide'],
            ],
            [
                'category' => 'Health Sciences',
                'title' => 'Health Information Systems',
                'description' => 'Learn the design, implementation, and management of health information systems including EHRs, HMIS, and health data analytics.',
                'level' => 'intermediate',
                'free' => false,
                'slug' => 'health-information-systems',
                'modules' => ['HIS Overview', 'Electronic Health Records', 'Health Data Standards', 'Database Design for Health', 'Health Data Analytics', 'System Implementation', 'Data Quality & Integrity', 'Health Information Exchange', 'Telemedicine Systems', 'HIS Security & Privacy', 'Mobile Health (mHealth)', 'HIS Evaluation'],
                'assignments' => ['EHR System Design', 'Health Data Analysis Report', 'HIS Implementation Plan', 'Data Quality Assessment'],
            ],
            [
                'category' => 'Health Sciences',
                'title' => 'Epidemiology',
                'description' => 'Advanced study of disease patterns, outbreak investigation, study designs, and epidemiological methods for public health research.',
                'level' => 'advanced',
                'free' => false,
                'slug' => 'epidemiology',
                'modules' => ['Epidemiological Concepts', 'Disease Transmission Dynamics', 'Study Designs (Cohort, Case-Control)', 'Measures of Association', 'Bias & Confounding', 'Screening & Diagnosis', 'Outbreak Investigation', 'Infectious Disease Epidemiology', 'Chronic Disease Epidemiology', 'Genetic Epidemiology', 'Social Epidemiology', 'Advanced Statistical Methods'],
                'assignments' => ['Outbreak Investigation Report', 'Cohort Study Design', 'Systematic Review', 'Epidemiological Data Analysis'],
            ],
            [
                'category' => 'Health Sciences',
                'title' => 'Nutrition & Wellness',
                'description' => 'Understand the science of nutrition, dietary planning, wellness strategies, and the relationship between diet and chronic disease prevention.',
                'level' => 'beginner',
                'free' => false,
                'slug' => 'nutrition-wellness',
                'modules' => ['Nutrition Fundamentals', 'Macronutrients', 'Micronutrients', 'Digestion & Metabolism', 'Dietary Guidelines', 'Meal Planning', 'Sports Nutrition', 'Weight Management', 'Nutrition & Chronic Disease', 'Food Safety', 'Wellness & Lifestyle', 'Global Nutrition Issues'],
                'assignments' => ['Personal Nutrition Plan', 'Meal Prep Schedule', 'Nutrition Label Analysis', 'Community Nutrition Program Proposal'],
            ],
        ];
    }

    private function generateLessonTitle(string $moduleTitle, int $lessonNum, int $totalLessons): string
    {
        $templates = [
            "Introduction to {$moduleTitle}",
            "Understanding {$moduleTitle}",
            "Key Concepts in {$moduleTitle}",
            "{$moduleTitle} — Core Principles",
            "Exploring {$moduleTitle}",
            "{$moduleTitle} in Practice",
            "Advanced {$moduleTitle} Concepts",
            "{$moduleTitle} — Case Studies",
            "Applying {$moduleTitle}",
            "{$moduleTitle} — Best Practices",
            "{$moduleTitle} Techniques & Tools",
            "Mastering {$moduleTitle}",
            "{$moduleTitle} — Real-World Applications",
            "{$moduleTitle} — Common Challenges",
            "{$moduleTitle} — Future Trends",
        ];

        $index = ($lessonNum - 1) % count($templates);
        return $templates[$index];
    }

    private function generateLessonContent(string $moduleTitle, int $lessonNum): string
    {
        $paragraphs = [
            "In this lesson, we will explore the foundational concepts of <strong>{$moduleTitle}</strong>. Understanding these principles is essential for building a strong knowledge base in this area.",
            "Let's dive deeper into the key theories and frameworks that underpin {$moduleTitle}. We'll examine how these concepts have evolved over time and their relevance in today's context.",
            "Practical application is crucial when learning {$moduleTitle}. In this section, we'll walk through step-by-step examples that demonstrate how to apply these concepts in real-world scenarios.",
            "One of the most important aspects of {$moduleTitle} is understanding common pitfalls and how to avoid them. We'll discuss best practices and proven strategies used by industry professionals.",
            "To reinforce your understanding, we'll analyze several case studies that illustrate successful implementations of {$moduleTitle} principles. Pay close attention to the decision-making processes involved.",
            "This lesson includes a hands-on exercise where you'll apply what you've learned about {$moduleTitle}. Follow the instructions carefully and try to complete the exercise before moving to the next lesson.",
            "Let's review the key takeaways from this lesson on {$moduleTitle}. These core concepts will serve as building blocks for more advanced topics covered later in the course.",
            "As we conclude this lesson, take a moment to reflect on how {$moduleTitle} connects to the broader subject area. Consider discussing these ideas with fellow learners to deepen your understanding.",
        ];

        $index = ($lessonNum - 1) % count($paragraphs);
        $content = "<h2>{$moduleTitle} — Lesson Overview</h2>\n\n";
        $content .= "<p>{$paragraphs[$index]}</p>\n\n";

        // Add some random extra paragraphs
        $extraCount = rand(1, 3);
        for ($i = 0; $i < $extraCount; $i++) {
            $pIndex = ($index + $i + 1) % count($paragraphs);
            $content .= "<p>{$paragraphs[$pIndex]}</p>\n\n";
        }

        // Add a code block or list for variety
        if ($lessonNum % 2 === 0) {
            $content .= "<h3>Key Points to Remember</h3>\n<ul>\n";
            $points = [
                "Always start with the fundamentals before moving to advanced topics.",
                "Practice regularly to reinforce your understanding of {$moduleTitle}.",
                "Use available resources and tools to enhance your learning experience.",
                "Collaborate with peers to gain different perspectives on the subject.",
                "Apply theoretical knowledge to practical scenarios for better retention.",
            ];
            foreach ($points as $point) {
                $content .= "    <li>{$point}</li>\n";
            }
            $content .= "</ul>\n\n";
        } else {
            $content .= "<h3>Learning Objectives</h3>\n<ol>\n";
            $objectives = [
                "Understand the core concepts and terminology related to {$moduleTitle}.",
                "Identify key principles and how they apply in different contexts.",
                "Analyze real-world examples and extract actionable insights.",
                "Evaluate different approaches and determine the most effective strategies.",
                "Create your own solutions using the techniques learned in this lesson.",
            ];
            foreach ($objectives as $objective) {
                $content .= "    <li>{$objective}</li>\n";
            }
            $content .= "</ol>\n\n";
        }

        $content .= "<p><em>Continue to the next lesson to build on what you've learned about {$moduleTitle}.</em></p>";

        return $content;
    }

    private function generateQuestionText(string $moduleTitle, int $questionNum): string
    {
        $templates = [
            "What is the primary concept behind {$moduleTitle}?",
            "Which of the following best describes {$moduleTitle}?",
            "In the context of {$moduleTitle}, which statement is TRUE?",
            "Which technique is most commonly associated with {$moduleTitle}?",
            "What is the main advantage of using {$moduleTitle} approaches?",
            "Which of the following is NOT a key principle of {$moduleTitle}?",
            "What role does {$moduleTitle} play in modern practice?",
            "Which tool or methodology is essential for implementing {$moduleTitle}?",
            "How does {$moduleTitle} differ from alternative approaches?",
            "What is the first step when applying {$moduleTitle} principles?",
            "Which factor is most critical for success in {$moduleTitle}?",
            "What common mistake do beginners make with {$moduleTitle}?",
            "Which industry standard relates to {$moduleTitle}?",
            "What is the relationship between {$moduleTitle} and related concepts?",
            "In evaluating {$moduleTitle} outcomes, which metric is most important?",
        ];

        $index = ($questionNum - 1) % count($templates);
        return $templates[$index];
    }

    private function randomAnswer(): string
    {
        $answers = [
            'The standard approach used by most professionals in the field.',
            'A theoretical framework that guides practical implementation.',
            'An essential component of modern best practices.',
            'A proven methodology with documented success cases.',
            'The result of extensive research and development in the area.',
            'A specialized technique requiring specific expertise.',
            'A fundamental principle that underpins the entire discipline.',
            'An emerging trend that is gaining widespread adoption.',
            'A classic approach that remains relevant today.',
            'A controversial method with both advocates and critics.',
        ];

        return $answers[array_rand($answers)];
    }

    private function generateLearningObjectives(string $courseTitle): string
    {
        $objectives = [
            "Understand the fundamental concepts and principles of {$courseTitle}.",
            "Apply practical techniques and best practices in {$courseTitle} to real-world scenarios.",
            "Analyze and evaluate different approaches within {$courseTitle} to make informed decisions.",
            "Develop practical skills through hands-on exercises and projects related to {$courseTitle}.",
            "Build a comprehensive understanding of {$courseTitle} that prepares you for advanced study.",
        ];

        return implode("\n", $objectives);
    }

    private function generatePrerequisites(string $level): string
    {
        if ($level === 'beginner') {
            return "No prior experience required. Just a willingness to learn and a computer with internet access.";
        } elseif ($level === 'intermediate') {
            return "Basic understanding of core concepts in the field. Familiarity with fundamental terminology and tools is recommended.";
        } else {
            return "Solid foundation in the subject area. Completion of beginner and intermediate level courses or equivalent practical experience is required.";
        }
    }

    private function createLearningMaterialsForLesson($lesson, string $courseTitle, string $moduleTitle, int $moduleNum, int $lessonNum): void
    {
        $materials = [
            [
                'title' => "{$moduleTitle} - Lesson {$lessonNum} Study Notes",
                'type' => 'pdf',
                'description' => 'Detailed study notes for this lesson covering key concepts and definitions.',
                'url' => null,
            ],
            [
                'title' => "{$moduleTitle} - Lesson {$lessonNum} Video",
                'type' => 'video',
                'description' => 'Recorded video lecture for this lesson explaining concepts step by step.',
                'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            ],
            [
                'title' => "{$moduleTitle} - Lesson {$lessonNum} Slides",
                'type' => 'powerpoint',
                'description' => 'PowerPoint slides used in the video lecture for this lesson.',
                'url' => null,
            ],
            [
                'title' => "{$moduleTitle} - Lesson {$lessonNum} Audio Recording",
                'type' => 'audio',
                'description' => 'Audio-only version of the lesson for learning on the go.',
                'url' => null,
            ],
            [
                'title' => "{$moduleTitle} - Lesson {$lessonNum} Practice",
                'type' => 'external_link',
                'description' => 'Online practice exercises and additional resources for this lesson.',
                'url' => 'https://developer.mozilla.org/en-US/',
            ],
        ];

        foreach ($materials as $index => $material) {
            \App\Models\LearningMaterial::create([
                'materialable_id' => $lesson->lesson_id,
                'materialable_type' => \App\Models\Lesson::class,
                'title' => $material['title'],
                'type' => $material['type'],
                'description' => $material['description'],
                'url' => $material['url'],
                'file_path' => "materials/{$courseTitle}/module-{$moduleNum}/lesson-{$lessonNum}-{$material['type']}." . ($material['type'] === 'pdf' ? 'pdf' : ($material['type'] === 'powerpoint' ? 'pptx' : ($material['type'] === 'audio' ? 'mp3' : ''))),
                'position' => $index + 1,
                'is_free' => $index < 2,
            ]);
        }
    }

    private function createFinalExam($course, string $courseTitle): void
    {
        $exam = \App\Models\FinalExam::create([
            'course_id' => $course->course_id,
            'title' => "{$courseTitle} - Final Examination",
            'description' => "This comprehensive final exam covers all modules of {$courseTitle}. You must achieve the minimum passing score to earn your certificate.",
            'total_marks' => 100,
            'passing_marks' => 60,
            'duration_minutes' => rand(60, 120),
            'num_questions' => 10,
            'auto_grade' => true,
            'attempts_allowed' => 2,
        ]);

        $topics = ['Fundamentals', 'Core Concepts', 'Practical Applications', 'Advanced Topics', 'Best Practices',
                    'Theory', 'Case Studies', 'Tools & Techniques', 'Industry Standards', 'Future Trends'];

        for ($q = 1; $q <= 10; $q++) {
            $topic = $topics[($q - 1) % count($topics)];
            \App\Models\FinalExamQuestion::create([
                'exam_id' => $exam->exam_id,
                'question_text' => "In the context of {$courseTitle}, which statement best describes {$topic}?",
                'option_a' => 'Option A: The standard approach used in professional practice for ' . strtolower($topic),
                'option_b' => 'Option B: A theoretical framework that guides practical implementation of ' . strtolower($topic),
                'option_c' => 'Option C: An emerging trend in ' . strtolower($topic) . ' that is gaining adoption',
                'option_d' => 'Option D: A foundational principle that underpins ' . strtolower($topic) . ' in the field',
                'correct_answer' => ['a', 'b', 'c', 'd'][array_rand(['a', 'b', 'c', 'd'])],
            ]);
        }
    }

    private function generateAssignmentDesc(string $assignmentTitle): string
    {
        $descriptions = [
            "In this assignment, you will demonstrate your understanding of {$assignmentTitle} by completing a comprehensive analysis and producing a detailed report. Apply the concepts and techniques learned throughout the course to solve practical problems.",
            "This practical assignment requires you to apply the principles of {$assignmentTitle} in a real-world context. You will need to research, plan, and execute your approach, documenting your methodology and results thoroughly.",
            "For this assignment, you will create a complete project related to {$assignmentTitle}. Your submission should include documentation, code (if applicable), and a reflection on the challenges encountered and how you overcame them.",
            "This case study assignment asks you to analyze a scenario involving {$assignmentTitle}. Identify key issues, propose solutions, and justify your recommendations using concepts from the course material and external research.",
        ];

        return $descriptions[array_rand($descriptions)];
    }
}
