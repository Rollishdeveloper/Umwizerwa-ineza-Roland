# 🎓 Learning Management System (LMS)

A comprehensive, feature-rich Learning Management System built with **Laravel 12**, featuring AI-powered course generation, multi-level approval workflows, gamification, and an extensive pre-seeded course catalog with **52 courses**, **2,712 lessons**, and **1,000 enrollments**.

---

## 📋 Table of Contents

- [Quick Start](#-quick-start)
- [Login Credentials](#-login-credentials)
- [System Architecture](#-system-architecture)
- [Core Features](#-core-features)
- [Database Schema](#-database-schema)
- [Route Map](#-route-map)
- [Seeded Demo Data](#-seeded-demo-data)
- [Tech Stack](#-tech-stack)
- [User Roles & Permissions](#-user-roles--permissions)
- [Gamification System](#-gamification-system)
- [AI Course Generator](#-ai-course-generator)
- [Approval Workflow](#-approval-workflow)
- [Question Bank](#-question-bank)

---

## 🚀 Quick Start

### Prerequisites

- PHP 8.2+
- Composer
- SQLite (default) or MySQL
- Node.js & NPM (for frontend assets)

### Installation

```bash
# 1. Clone the repository
cd your-project-directory

# 2. Install PHP dependencies
composer install

# 3. Install & build frontend assets
npm install
npm run build

# 4. Environment configuration
copy .env.example .env
# Edit .env if using MySQL (default is SQLite)

# 5. Generate application key
php artisan key:generate

# 6. Run migrations and seed the database (creates ~22,000 records)
php artisan migrate:fresh --seed

# 7. Start the development server
php artisan serve

# 8. Visit http://localhost:8000 in your browser
```

---

## 🔑 Login Credentials

### Administrator
| Email | Password | Role |
|-------|----------|------|
| `admin@elearning.com` | `password` | **Admin** — Full system access |

### Instructors
| Email | Password | Role |
|-------|----------|------|
| `john.smith@elearning.com` | `password` | Instructor |
| `jane.doe@elearning.com` | `password` | Instructor |
| `sarah.johnson.prof@elearning.com` | `password` | Instructor |

### Students
| Email | Password | Role |
|-------|----------|------|
| `student@elearning.com` | `password` | Student |
| `student2@elearning.com` | `password` | Student |

All **52 students** use the password `password`.

---

## 🏗 System Architecture

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          # Admin dashboard, user/student/instructor mgmt, categories
│   │   ├── Auth/           # Authentication (Laravel Breeze)
│   │   ├── Instructor/     # Instructor dashboard
│   │   ├── Student/        # Student dashboard
│   │   ├── CourseController.php
│   │   ├── QuizController.php
│   │   ├── AssignmentController.php
│   │   ├── CertificateController.php
│   │   ├── EnrollmentController.php
│   │   ├── FinalExamController.php
│   │   ├── GamificationController.php
│   │   ├── LessonController.php
│   │   ├── ModuleController.php
│   │   ├── NotificationController.php
│   │   ├── ReportController.php
│   │   ├── AICourseGeneratorController.php
│   │   ├── ApprovalController.php
│   │   └── QuestionBankController.php
│   ├── Middleware/
│   │   └── RoleMiddleware.php
│   └── Requests/
├── Models/                 # 18 Eloquent models
├── Services/
│   ├── GamificationService.php
│   ├── AICourseGeneratorService.php
│   └── ApprovalWorkflowService.php
├── Exports/                # CSV/Excel exports
└── View/Components/
database/
├── migrations/             # 33 migration files
└── seeders/
    ├── DatabaseSeeder.php
    ├── UserSeeder.php          # 62 users (52 students, 8 instructors, 1 admin)
    ├── CourseContentSeeder.php # 52 courses with full content
    ├── CategorySeeder.php      # 6 categories
    ├── EnrollmentSeeder.php    # 1,000 enrollments
    └── GamificationSeeder.php  # 12 badges, 7 achievements
resources/views/            # 50+ Blade views
routes/
├── web.php                 # 127 routes
└── auth.php                # Auth routes (Breeze)
```

---

## ✨ Core Features

### 📚 Course Management
- 52 pre-seeded courses across 6 categories
- Learning objectives & prerequisites
- Course thumbnails (external URLs)
- Modules with ordered lessons
- Duration, difficulty level, pricing
- Enhanced search with filters (category, level, duration, price)
- Sorting (latest, popular, completion rate, price)
- Related course recommendations

### 📖 Lesson System
- Video, reading materials, downloadable PDFs
- Audio support with `audio_url` field
- Practice exercises
- Learning materials (PDF, Word, PowerPoint, video, audio, images, external links)
- Polymorphic material attachments

### 📝 Quizzes & Assessments
- Module-level quizzes with timed exams
- True/False and Multiple Choice question types
- Auto-grading with instant results
- Passing marks configuration
- Question-level marks and difficulty
- Explanation for each answer

### 📄 Assignments
- Per-course assignments with due dates
- Grading criteria and rubrics
- Student submissions with file uploads
- Instructor grading with marks

### 🎓 Final Examination
- Course-level final exams
- Timed exams with countdown timer
- Auto-grading with passing score config
- Attempt limits
- Detailed results

### 🏆 Gamification
- Points system (enroll, complete lessons, pass quizzes, earn certificates)
- 12 badges with types: points, courses, quizzes, assignments, certificates
- 7 achievements with milestones
- Level system (Level 1–10)
- Leaderboard with rankings
- XP progress bar

### 🤖 AI Course Generator
- Upload educational materials (PDF, DOCX, PPTX, TXT)
- Automatic content extraction and analysis
- AI-powered course structure generation
- Auto-generated quizzes with multiple question types
- Assignment and final exam generation
- AI confidence scoring
- Content validation checks

### ✅ Approval Workflow
- 3-level approval (Instructor → Coordinator → Admin)
- Content validation checks
- AI confidence scoring per item
- Version control with snapshots
- Review queue and dashboards
- Revision tracking with comments
- Approval analytics

### 📊 Reports & Analytics
- Student reports with enrollment, quiz, certificate data
- Course reports with enrollment stats
- Instructor reports with course performance
- System-wide statistics
- PDF, CSV, Excel export

### 📜 Certificates
- Course completion certificates
- Unique certificate numbers
- QR code verification
- PDF download

### 🔔 Notifications
- Achievement and badge notifications
- Approval workflow notifications
- Mark as read / mark all as read

---

## 🗄 Database Schema

### Core Tables (33 migrations)

| Table | Records | Description |
|-------|---------|-------------|
| `users` | 62 | All users with role (admin/instructor/student) |
| `students` | 52 | Student profiles |
| `instructors` | 8 | Instructor profiles |
| `categories` | 6 | Course categories |
| `courses` | 52 | Course details with objectives/prerequisites |
| `modules` | 451 | Course modules |
| `lessons` | 2,712 | Individual lessons with audio/exercises |
| `learning_materials` | 13,560 | Polymorphic materials (lessons) |
| `enrollments` | 1,000 | Student enrollments with progress |
| `quizzes` | 451 | Module quizzes |
| `questions` | 2,683 | Quiz questions (MCQ & True/False) |
| `quiz_results` | — | Quiz attempt results |
| `final_exams` | 52 | Course final exams |
| `final_exam_questions` | — | Final exam questions |
| `final_exam_results` | — | Final exam results |
| `assignments` | 207 | Course assignments |
| `assignment_submissions` | — | Student submissions |
| `certificates` | — | Course completion certificates |
| `badges` | 12 | Gamification badges |
| `achievements` | 7 | Gamification achievements |
| `student_badges` | — | Pivot: students ↔ badges |
| `student_achievements` | — | Pivot: students ↔ achievements |
| `notifications` | — | User notifications |
| `activity_logs` | — | System activity audit trail |
| `course_reviews` | — | Multi-level review tracking |
| `content_versions` | — | Version control snapshots |
| `approval_workflows` | — | Stage-based approval pipeline |
| `review_comments` | — | Detailed feedback per content |
| `question_banks` | — | Centralized reusable questions |
| `uploaded_materials` | — | AI generator upload tracking |

---

## 🗺 Route Map

### Public Routes
| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/` | `home` | Welcome page |
| GET | `/courses/{course}` | `courses.show` | Course detail page |
| GET | `/login` | `login` | Login form |
| GET | `/register` | `register` | Registration form |

### Authenticated Routes (All Roles)
| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/dashboard` | `dashboard` | Role-based redirect |
| GET | `/courses` | `courses.index` | Course catalog |
| GET | `/courses/create` | `courses.create` | Create course |
| POST | `/courses` | `courses.store` | Store course |
| GET/PUT/DELETE | `/courses/{course}` | `courses.*` | Course CRUD |
| GET | `/courses/{course}/quizzes` | `quizzes.index` | Quiz list |
| GET | `/courses/{course}/quizzes/{quiz}/take` | `quizzes.take` | Take quiz |
| POST | `/courses/{course}/quizzes/{quiz}/submit` | `quizzes.submit` | Submit quiz |
| GET | `/courses/{course}/quizzes/{quiz}/result/{result}` | `quizzes.result` | Quiz result |
| GET | `/courses/{course}/final-exam` | `final-exams.show` | Final exam info |
| GET | `/courses/{course}/final-exam/take/{exam}` | `final-exams.take` | Take final exam |
| POST | `/courses/{course}/final-exam/submit/{exam}` | `final-exams.submit` | Submit exam |
| GET | `/courses/{course}/assignments` | `assignments.index` | Assignments |
| POST | `/courses/{course}/enroll` | `enrollments.store` | Enroll in course |
| GET | `/enrollments` | `enrollments.index` | My enrollments |
| GET | `/certificates` | `certificates.index` | My certificates |
| GET | `/notifications` | `notifications.index` | Notifications |
| GET | `/gamification` | `gamification.index` | Gamification hub |
| GET | `/gamification/leaderboard` | `gamification.leaderboard` | Leaderboard |
| GET | `/gamification/badges` | `gamification.badges` | All badges |
| GET | `/gamification/achievements` | `gamification.achievements` | All achievements |

### Admin Routes (`/admin/*`)
| URI | Name | Description |
|-----|------|-------------|
| `/admin/dashboard` | `admin.dashboard` | Admin dashboard with charts |
| `/admin/users` | `admin.users` | User management |
| `/admin/students/*` | `admin.students.*` | Student CRUD |
| `/admin/instructors/*` | `admin.instructors.*` | Instructor CRUD |
| `/admin/categories/*` | `admin.categories.*` | Category management |
| `/admin/settings` | `admin.settings` | System settings |

### Instructor Routes (`/instructor/*`)
| URI | Name | Description |
|-----|------|-------------|
| `/instructor/dashboard` | `instructor.dashboard` | Instructor dashboard |

### Student Routes (`/student/*`)
| URI | Name | Description |
|-----|------|-------------|
| `/student/dashboard` | `student.dashboard` | Student dashboard |

### AI Generator Routes (`/ai-generator/*`)
| URI | Name | Description |
|-----|------|-------------|
| `/ai-generator` | `ai-generator.index` | AI generator homepage |
| `/ai-generator/upload` | `ai-generator.upload` | Upload material |
| `/ai-generator/preview/{material}` | `ai-generator.preview` | Preview generated content |
| `/ai-generator/generate/{material}` | `ai-generator.generate` | Generate course from material |
| `/ai-generator/history` | `ai-generator.history` | Generation history |

### Approval Routes (`/approval/*`)
| URI | Name | Description |
|-----|------|-------------|
| `/approval` | `approval.index` | Approval hub |
| `/approval/dashboard` | `approval.dashboard` | Review dashboard |
| `/approval/queue` | `approval.queue` | Review queue |
| `/approval/review/{course}` | `approval.review` | Review course content |
| `/approval/review/{course}/submit` | `approval.submit-review` | Submit review decision |
| `/approval/versions/{course}` | `approval.versions` | Version history |
| `/approval/analytics` | `approval.analytics` | Approval analytics |

### Question Bank Routes (`/question-bank/*`)
| URI | Name | Description |
|-----|------|-------------|
| `/question-bank` | `question-bank.index` | Question bank |
| `/question-bank/create` | `question-bank.create` | Create question |
| `/question-bank/generate/{course}` | `question-bank.generate` | Generate questions from bank |
| `/question-bank/{question}/approve` | `question-bank.approve` | Approve question |

### Report Routes (`/reports/*`)
| URI | Name | Description |
|-----|------|-------------|
| `/reports` | `reports.index` | Report dashboard |
| `/reports/students` | `reports.students` | Student report |
| `/reports/courses` | `reports.courses` | Course report |
| `/reports/instructors` | `reports.instructors` | Instructor report |
| `/reports/system` | `reports.system` | System report |
| `/reports/export-pdf/{type}` | `reports.export-pdf` | PDF export |
| `/reports/export-csv/{type}` | `reports.export-csv` | CSV export |

---

## 📊 Seeded Demo Data

| Item | Count | Description |
|------|-------|-------------|
| **Categories** | 6 | Computer Science, Business, Languages, Arts, Engineering, Health |
| **Courses** | 52 | Full course catalog across all categories |
| **Modules** | 451 | 5–15 modules per course |
| **Lessons** | 2,712 | 3–10 lessons per module with video, reading, PDFs |
| **Learning Materials** | 13,560 | 5 materials per lesson (PDF, video, slides, audio, links) |
| **Quizzes** | 451 | One per module with 5–15 questions each |
| **Questions** | 2,683 | MCQ and True/False types with difficulty & marks |
| **Assignments** | 207 | 3–5 assignments per course with grading criteria |
| **Final Exams** | 52 | One per course with auto-grading |
| **Enrollments** | 1,000 | Students enrolled across courses |
| **Students** | 52 | Diverse student profiles |
| **Instructors** | 8 | Specialized instructor profiles |
| **Badges** | 12 | Points, courses, quizzes, assignments, certificate types |
| **Achievements** | 7 | Milestone-based achievements |
| **Users** | 62 | 1 admin + 8 instructors + 52 students + 1 extra |

---

## 🛠 Tech Stack

| Technology | Version/Purpose |
|------------|-----------------|
| **Laravel** | 12 — PHP Framework |
| **PHP** | 8.2+ |
| **Database** | SQLite (default) / MySQL |
| **Frontend** | Blade + Bootstrap 5 + Tailwind CSS |
| **CSS** | app.css (custom styles) |
| **JavaScript** | Alpine.js + Vanilla JS |
| **Icons** | Bootstrap Icons |
| **Authentication** | Laravel Breeze |
| **API Tokens** | Laravel Sanctum |
| **File Storage** | Laravel Storage (local) |
| **PDF Export** | Barryvdh/DomPDF |
| **Queue** | Laravel Queues |

---

## 👥 User Roles & Permissions

### Administrator
- Full system access
- User management (students, instructors)
- Course management (all courses)
- Category management
- System reports and analytics
- Approval workflow oversight
- Settings management

### Instructor
- Create and manage own courses
- Create modules, lessons, quizzes, assignments
- Grade student submissions
- View course analytics
- AI course generation
- Review and approve generated content

### Student
- Browse and search course catalog
- Enroll in courses
- View lessons and learning materials
- Take quizzes and final exams
- Submit assignments
- Earn certificates, badges, achievements
- View leaderboard and progress tracking
- Gamification rewards

---

## 🏆 Gamification System

### Points Breakdown
| Action | Points |
|--------|--------|
| Enroll in a course | 10 |
| Complete a lesson | 5 |
| Pass a quiz | 25 |
| Perfect quiz score | 50 |
| Submit an assignment | 15 |
| Complete a course | 100 |
| Earn a certificate | 75 |
| Login streak | 5 |

### Level System
| Level | Points Required |
|-------|-----------------|
| 1 | 0 |
| 2 | 200 |
| 3 | 500 |
| 4 | 900 |
| 5 | 1,400 |
| 6 | 1,900 |
| 7 | 2,500 |
| 8 | 3,200 |
| 9 | 4,000 |
| 10 | 5,000+ |

### Badges (12 total)
- Points badges (various thresholds)
- Course completion badges
- Quiz performance badges
- Assignment submission badges
- Certificate badges

### Achievements (7 total)
- First Course Completed
- Quiz Star
- Assignment Pro
- Course Master
- Points Milestone
- Certificate Collector
- Streak achievements

---

## 🤖 AI Course Generator

The AI Course Generator allows instructors to upload educational materials and automatically create complete courses.

### Supported Upload Formats
- PDF Books & Documents
- Word Documents (.docx)
- PowerPoint Presentations (.pptx)
- Text Files (.txt)
- E-books & Lecture Notes
- Research Papers & Study Guides

### Automatic Generation
- **Course Structure**: Title, description, learning outcomes, duration, difficulty
- **Modules & Lessons**: Auto-generated from content analysis
- **Quizzes**: Multiple choice, True/False, fill-in-blank, short answer
- **Assignments**: Practice, project, and group assignments
- **Final Exam**: Auto-generated with answer key
- **Answer Key**: Correct answers, explanations, source references

### Auto-Grading
- MCQ & True/False: Instant grading
- Fill-in-blank: Keyword matching
- Short Answer: AI-assisted scoring
- Essay: Rubric-based evaluation

---

## ✅ Approval Workflow

### 3-Level Approval Process
1. **Instructor Review** — Verify accuracy, completeness, educational relevance
2. **Academic Coordinator** — Verify curriculum alignment, learning outcomes
3. **Administrator** — Compliance, system standards, publishing

### Workflow Statuses
- Uploaded → AI Generated → Pending Review → Instructor Approved/Rejected
- → Coordinator Approved/Rejected → Published/Rejected/Archived

### Features
- Content validation checks
- AI confidence scoring (95%+ auto-approve, 70%+ review, <50% mandatory)
- Version control with snapshots
- Revision requests with priority levels
- Review comments with severity
- Notification system for all stakeholders

---

## ❓ Question Bank

Centralized question repository with:
- Multiple question types (MCQ, True/False, Fill-blank, Short Answer, Essay)
- Difficulty levels (Easy, Medium, Hard)
- Source references (chapter, page number)
- AI confidence scores
- Approval system
- Randomized exam generation
- Question reuse across courses

---

## 🔧 Maintenance Commands

```bash
# Reset and reseed database
php artisan migrate:fresh --seed

# Clear application cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Cache views
php artisan view:cache

# List all routes
php artisan route:list --except-vendor

# Run tests
php artisan test
```

---


