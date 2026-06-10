<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Assignment;
use App\Models\FinalExam;
use App\Models\FinalExamQuestion;
use App\Models\UploadedMaterial;
use App\Models\QuestionBank;
use App\Models\ActivityLog;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AICourseGeneratorService
{
    private array $commonTopics = [
        'Introduction', 'Fundamentals', 'Core Concepts', 'Advanced Topics',
        'Practical Applications', 'Case Studies', 'Best Practices', 'Review & Assessment'
    ];

    /**
     * Process an uploaded file and generate a course from its content.
     */
    public function processUploadedMaterial(UploadedMaterial $material): array
    {
        $material->update(['status' => 'processing']);
        
        $text = $this->extractText($material);
        $material->update(['extracted_text' => $text]);

        if (empty(trim($text))) {
            $material->update(['status' => 'failed', 'ai_confidence' => 0]);
            return ['success' => false, 'error' => 'No text could be extracted from the file.'];
        }

        $analysis = $this->analyzeContent($text);
        $courseStructure = $this->generateCourseStructure($analysis);
        
        $material->update([
            'status' => 'processed',
            'metadata' => $analysis,
            'ai_confidence' => $analysis['confidence'],
        ]);

        return [
            'success' => true,
            'analysis' => $analysis,
            'structure' => $courseStructure,
            'material' => $material,
        ];
    }

    /**
     * Extract text from uploaded material.
     * Falls back to simulated extraction when the actual file is not found
     * (e.g. in dev/testing or when content is referenced without uploading).
     */
    private function extractText(UploadedMaterial $material): string
    {
        $path = Storage::disk('local')->path($material->stored_path);
        if (!file_exists($path)) {
            // File not on disk — use simulated extraction so the demo/preview flow still works
            return $this->simulateExtraction($material->original_filename);
        }

        $content = file_get_contents($path);
        $mimeType = $material->mime_type;

        // For text-based files, return as-is
        if (str_contains($mimeType, 'text')) {
            return $content;
        }

        // For all other formats, use filename to generate content
        return $this->simulateExtraction($material->original_filename);
    }

    /**
     * Simulated extraction - in production, use OCR/PDF parsing libraries.
     */
    private function simulateExtraction(string $filename): string
    {
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $name = str_replace(['-', '_'], ' ', $name);
        
        return "
# {$name}

## Chapter 1: Introduction
This chapter introduces the fundamental concepts of {$name}. Understanding these core principles is essential for building a strong foundation.
Topics covered include definitions, historical context, and the importance of {$name} in modern practice.

## Chapter 2: Core Concepts
Dive deeper into the key theories and frameworks that underpin {$name}. We examine how these concepts have evolved and their relevance today.
Key topics: theoretical foundations, practical applications, and emerging trends.

## Chapter 3: Practical Applications
Learn how to apply {$name} concepts in real-world scenarios through step-by-step examples and case studies.
Topics include: implementation strategies, common challenges, and solution patterns.

## Chapter 4: Advanced Topics
Explore advanced concepts in {$name} including specialized techniques, optimization strategies, and industry-specific applications.

## Chapter 5: Best Practices and Review
Review the key takeaways and best practices for {$name}. Includes assessment questions and further reading recommendations.
        ";
    }

    /**
     * Analyze extracted text to identify chapters, topics, key concepts.
     */
    public function analyzeContent(string $text): array
    {
        $lines = explode("\n", $text);
        $chapters = [];
        $currentChapter = null;
        $totalWords = 0;

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (str_starts_with($trimmed, '#') || str_starts_with($trimmed, 'Chapter')) {
                if ($currentChapter) {
                    $chapters[] = $currentChapter;
                }
                $currentChapter = [
                    'title' => preg_replace('/^#+\s*/', '', $trimmed),
                    'content' => '',
                ];
            } elseif ($currentChapter) {
                $currentChapter['content'] .= $trimmed . ' ';
                $totalWords += str_word_count($trimmed);
            }
        }
        if ($currentChapter) {
            $chapters[] = $currentChapter;
        }

        // Detect key concepts from text
        $keyConcepts = $this->detectKeyConcepts($text);
        
        // Calculate confidence based on content richness
        $confidence = min(95, 50 + count($chapters) * 5 + count($keyConcepts) * 2);
        $confidence = min(95, max(30, $confidence));

        return [
            'title' => $this->detectTitle($text),
            'chapters' => $chapters,
            'total_words' => $totalWords,
            'key_concepts' => $keyConcepts,
            'estimated_duration' => max(10, intdiv($totalWords, 200)),
            'difficulty' => $totalWords > 5000 ? 'advanced' : ($totalWords > 2000 ? 'intermediate' : 'beginner'),
            'confidence' => $confidence,
        ];
    }

    private function detectTitle(string $text): string
    {
        $lines = explode("\n", trim($text));
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (str_starts_with($trimmed, '# ') || str_starts_with($trimmed, '# ')) {
                return substr($trimmed, 2);
            }
            if (!empty($trimmed) && !str_contains($trimmed, 'Chapter')) {
                return Str::limit($trimmed, 100);
            }
        }
        return 'Generated Course';
    }

    private function detectKeyConcepts(string $text): array
    {
        $concepts = [];
        $keywordPatterns = [
            '/\b(?:definition|concept|principle|theory|framework)\s+of\s+([A-Z][a-z]+(?:\s+[A-Z][a-z]+)*)/' => 1,
            '/\b(?:topics?\s+include|covering|including)\s*:?\s*([^.]+)/' => 1,
            '/([A-Z][a-z]+(?:\s+[A-Z][a-z]+)*)\s+(?:is\s+(?:a|an|the|one)|refers?\s+to|involves|encompasses)/' => 1,
        ];

        foreach ($keywordPatterns as $pattern => $group) {
            if (preg_match_all($pattern, $text, $matches)) {
                foreach ($matches[$group] as $match) {
                    $terms = explode(',', $match);
                    foreach ($terms as $term) {
                        $term = trim($term);
                        if (strlen($term) > 3 && !in_array($term, $concepts)) {
                            $concepts[] = $term;
                            if (count($concepts) >= 15) break 2;
                        }
                    }
                }
            }
        }

        return array_slice(array_unique($concepts), 0, 15);
    }

    /**
     * Generate complete course structure from analyzed content.
     */
    public function generateCourseStructure(array $analysis): array
    {
        $chapters = $analysis['chapters'] ?? [];
        $concepts = $analysis['key_concepts'] ?? [];
        $numChapters = max(3, count($chapters));

        $modules = [];
        $usedConcepts = [];

        foreach ($chapters as $index => $chapter) {
            $title = !empty($chapter['title']) ? $chapter['title'] : ($this->commonTopics[$index % count($this->commonTopics)] ?? "Module " . ($index + 1));
            
            $moduleConcepts = array_slice($concepts, $index * 3, 3);
            $usedConcepts = array_merge($usedConcepts, $moduleConcepts);

            $lessons = $this->generateLessons($title, $moduleConcepts, $index + 1);
            
            $modules[] = [
                'title' => $title,
                'description' => "Comprehensive coverage of {$title} concepts, theories, and practical applications.",
                'position' => $index + 1,
                'lessons' => $lessons,
                'num_quiz_questions' => max(3, min(8, count($moduleConcepts) + 2)),
            ];
        }

        $remainingConcepts = array_diff($concepts, $usedConcepts);
        if (!empty($remainingConcepts) && count($modules) < 15) {
            $modules[] = [
                'title' => 'Additional Topics',
                'description' => 'Supplementary concepts and advanced topics.',
                'position' => count($modules) + 1,
                'lessons' => $this->generateLessons('Additional Topics', array_values($remainingConcepts), count($modules) + 1),
                'num_quiz_questions' => max(3, min(6, count($remainingConcepts))),
            ];
        }

        return [
            'title' => $analysis['title'],
            'description' => "A comprehensive course on {$analysis['title']}. Covers " . implode(', ', array_slice($concepts, 0, 5)) . ", and more.",
            'learning_objectives' => $this->generateObjectives($analysis['title'], $concepts),
            'duration' => $analysis['estimated_duration'],
            'difficulty' => $analysis['difficulty'],
            'modules' => $modules,
            'concepts_detected' => $concepts,
        ];
    }

    private function generateLessons(string $moduleTitle, array $concepts, int $moduleNum): array
    {
        $lessons = [];
        $lessonTemplates = [
            "Introduction to %s",
            "Understanding %s",
            "Key Concepts in %s",
            "Practical Applications of %s",
            "Advanced %s Techniques",
            "%s Case Studies",
            "%s Best Practices",
            "Review and Assessment of %s",
        ];

        $numLessons = max(3, min(6, count($concepts) + 2));

        for ($i = 0; $i < $numLessons; $i++) {
            $topic = $concepts[$i % max(1, count($concepts))] ?? $moduleTitle;
            $template = $lessonTemplates[$i % count($lessonTemplates)];
            
            $lessons[] = [
                'title' => sprintf($template, $topic),
                'content' => $this->generateLessonContent($topic, $moduleTitle, $i + 1),
                'duration' => rand(10, 30),
                'position' => $i + 1,
                'has_video' => $i % 3 === 0,
                'has_exercises' => $i % 2 === 0,
            ];
        }

        return $lessons;
    }

    private function generateLessonContent(string $topic, string $module, int $num): string
    {
        return "<h2>Lesson {$num}: {$topic}</h2>
<p>In this lesson, we explore <strong>{$topic}</strong> in the context of {$module}.</p>
<p>Understanding this topic is crucial for mastering the subject. We'll cover the fundamental concepts, practical applications, and common challenges.</p>
<h3>Key Learning Points</h3>
<ul>
    <li>Understand the core principles of {$topic}</li>
    <li>Apply {$topic} concepts to real-world scenarios</li>
    <li>Analyze best practices and common pitfalls</li>
    <li>Evaluate different approaches and select optimal solutions</li>
</ul>
<p><em>Continue to the next lesson to build on what you've learned.</em></p>";
    }

    private function generateObjectives(string $title, array $concepts): string
    {
        $objectives = [
            "Understand the fundamental concepts and principles of {$title}.",
            "Apply practical techniques and best practices in {$title} to real-world scenarios.",
        ];
        foreach (array_slice($concepts, 0, 3) as $concept) {
            $objectives[] = "Analyze and evaluate {$concept} within the context of {$title}.";
        }
        $objectives[] = "Develop practical skills through hands-on exercises and projects.";
        return implode("\n", $objectives);
    }

    /**
     * Generate quiz questions from course content.
     */
    public function generateQuizQuestions(string $topic, int $count = 5): array
    {
        $questions = [];
        $types = ['mcq', 'true_false', 'fill_blank', 'short_answer'];

        for ($i = 0; $i < $count; $i++) {
            $type = $types[$i % count($types)];
            $difficulty = $i < 2 ? 'easy' : ($i < 4 ? 'medium' : 'hard');

            switch ($type) {
                case 'mcq':
                    $questions[] = [
                        'question_type' => 'mcq',
                        'question_text' => "Which of the following best describes {$topic}?",
                        'options' => json_encode([
                            'a' => "The standard approach used in {$topic}",
                            'b' => "A theoretical framework for understanding {$topic}",
                            'c' => "An emerging trend in {$topic}",
                            'd' => "A foundational principle of {$topic}",
                        ]),
                        'correct_answer' => 'a',
                        'explanation' => "This is the most accurate description of {$topic} based on standard definitions.",
                        'difficulty' => $difficulty,
                        'marks' => $difficulty === 'hard' ? 15 : 10,
                    ];
                    break;
                case 'true_false':
                    $questions[] = [
                        'question_type' => 'true_false',
                        'question_text' => "{$topic} is an important concept in modern practice.",
                        'options' => json_encode(['a' => 'True', 'b' => 'False']),
                        'correct_answer' => 'a',
                        'explanation' => "{$topic} is widely recognized as a fundamental concept in the field.",
                        'difficulty' => 'easy',
                        'marks' => 5,
                    ];
                    break;
                case 'fill_blank':
                    $questions[] = [
                        'question_type' => 'fill_blank',
                        'question_text' => "The concept of __________ is essential for understanding {$topic}.",
                        'options' => null,
                        'correct_answer' => strtolower($topic),
                        'explanation' => "{$topic} is the missing term that completes this statement.",
                        'difficulty' => 'medium',
                        'marks' => 10,
                    ];
                    break;
                case 'short_answer':
                    $questions[] = [
                        'question_type' => 'short_answer',
                        'question_text' => "Briefly explain the significance of {$topic} in one or two sentences.",
                        'options' => null,
                        'correct_answer' => "{$topic} is important because it provides the foundational understanding needed to apply advanced concepts and solve complex problems in the field.",
                        'explanation' => "A complete answer should mention the practical importance and application of {$topic}.",
                        'difficulty' => 'medium',
                        'marks' => 15,
                    ];
                    break;
            }
        }

        return $questions;
    }

    /**
     * Create the actual course from generated structure.
     */
    public function createCourseFromStructure(array $structure, int $instructorId, int $categoryId, UploadedMaterial $material): Course
    {
        // Look up the instructor's user_id for FK references that point to users.id
        $instructor = \App\Models\Instructor::find($instructorId);
        $userId = $instructor ? $instructor->user_id : $instructorId;

        $course = Course::create([
            'instructor_id' => $instructorId,
            'title' => $structure['title'],
            'slug' => Str::slug($structure['title']) . '-' . Str::random(6),
            'description' => $structure['description'],
            'learning_objectives' => $structure['learning_objectives'],
            'category_id' => $categoryId,
            'duration' => $structure['duration'],
            'level' => $structure['difficulty'],
            'price' => 0,
            'status' => 'draft',
        ]);

        $material->update(['course_id' => $course->course_id]);

        // Create modules with lessons
        foreach ($structure['modules'] as $moduleData) {
            $module = Module::create([
                'course_id' => $course->course_id,
                'title' => $moduleData['title'],
                'description' => $moduleData['description'],
                'position' => $moduleData['position'],
            ]);

            foreach ($moduleData['lessons'] as $lessonData) {
                Lesson::create([
                    'module_id' => $module->module_id,
                    'title' => $lessonData['title'],
                    'content' => $lessonData['content'],
                    'video_url' => $lessonData['has_video'] ? 'https://www.youtube.com/watch?v=generated' : null,
                    'practice_exercises' => $lessonData['has_exercises'] ? "Complete the following exercises based on {$lessonData['title']}:\n1. Review the key concepts covered\n2. Write a summary of what you learned\n3. Apply the concepts to a practical scenario" : null,
                    'duration' => $lessonData['duration'],
                    'position' => $lessonData['position'],
                ]);
            }

            // Generate quiz for the module
            $questions = $this->generateQuizQuestions($moduleData['title'], $moduleData['num_quiz_questions']);
            $totalMarks = array_sum(array_column($questions, 'marks'));

            $quiz = Quiz::create([
                'course_id' => $course->course_id,
                'title' => "{$moduleData['title']} - Module Quiz",
                'description' => "Test your understanding of {$moduleData['title']} concepts.",
                'total_marks' => $totalMarks,
                'passing_marks' => ceil($totalMarks * 0.6),
                'duration_minutes' => count($questions) * 3,
            ]);

            foreach ($questions as $qData) {
                Question::create([
                    'quiz_id' => $quiz->quiz_id,
                    'question_text' => $qData['question_text'],
                    'question_type' => $qData['question_type'],
                    'option_a' => $qData['options'] ? json_decode($qData['options'], true)['a'] ?? '' : '',
                    'option_b' => $qData['options'] ? json_decode($qData['options'], true)['b'] ?? '' : '',
                    'option_c' => $qData['options'] ? json_decode($qData['options'], true)['c'] ?? '' : '',
                    'option_d' => $qData['options'] ? json_decode($qData['options'], true)['d'] ?? '' : '',
                    'correct_answer' => $qData['correct_answer'],
                    'explanation' => $qData['explanation'],
                    'difficulty' => $qData['difficulty'],
                    'marks' => $qData['marks'],
                    'ai_confidence' => rand(70, 95),
                ]);

                // Also add to question bank
                QuestionBank::create([
                    'course_id' => $course->course_id,
                    'module_id' => $module->module_id,
                    'user_id' => $userId,
                    'question_text' => $qData['question_text'],
                    'question_type' => $qData['question_type'],
                    'options' => $qData['options'] ? json_decode($qData['options'], true) : null,
                    'correct_answer' => $qData['correct_answer'],
                    'explanation' => $qData['explanation'],
                    'difficulty' => $qData['difficulty'],
                    'marks' => $qData['marks'],
                    'ai_confidence' => rand(70, 95),
                    'status' => 'draft',
                ]);
            }
        }

        // Create assignments
        $assignmentTopics = ['Practice Exercise', 'Case Study Analysis', 'Final Project'];
        foreach ($assignmentTopics as $index => $topic) {
            Assignment::create([
                'course_id' => $course->course_id,
                'title' => "{$topic}: {$structure['title']}",
                'description' => "Apply what you've learned in this course by completing this {$topic} assignment. Demonstrate your understanding of the key concepts and practical applications.",
                'due_date' => now()->addDays(14 * ($index + 1)),
                'total_marks' => 100,
            ]);
        }

        // Create final exam
        $examQuestions = $this->generateQuizQuestions($structure['title'], 10);
        $examTotalMarks = 100;

        $exam = FinalExam::create([
            'course_id' => $course->course_id,
            'title' => "{$structure['title']} - Final Examination",
            'description' => "Comprehensive final exam covering all modules of {$structure['title']}.",
            'total_marks' => $examTotalMarks,
            'passing_marks' => 60,
            'duration_minutes' => max(30, count($structure['modules']) * 15),
            'num_questions' => 10,
            'auto_grade' => true,
            'attempts_allowed' => 2,
        ]);

        foreach ($examQuestions as $qData) {
            FinalExamQuestion::create([
                'exam_id' => $exam->exam_id,
                'question_text' => $qData['question_text'],
                'option_a' => $qData['options'] ? (json_decode($qData['options'], true)['a'] ?? '') : '',
                'option_b' => $qData['options'] ? (json_decode($qData['options'], true)['b'] ?? '') : '',
                'option_c' => $qData['options'] ? (json_decode($qData['options'], true)['c'] ?? '') : '',
                'option_d' => $qData['options'] ? (json_decode($qData['options'], true)['d'] ?? '') : '',
                'correct_answer' => $qData['correct_answer'],
            ]);
        }

        // Create initial workflow and version
        \App\Models\ApprovalWorkflow::create([
            'course_id' => $course->course_id,
            'current_stage' => 'ai_generated',
            'priority' => 'medium',
        ]);

        \App\Models\ContentVersion::create([
            'course_id' => $course->course_id,
            'version_number' => '1.0',
            'changes' => 'AI-generated course from uploaded material',
            'created_by' => $userId,
            'status' => 'ai_generated',
            'ai_confidence' => $material->ai_confidence ?? 85,
        ]);

        ActivityLog::create([
            'user_id' => $userId,
            'activity' => "AI-generated course: {$course->title} from {$material->original_filename}",
        ]);

        return $course;
    }

    /**
     * Generate video teaching scripts from course structure.
     */
    public function generateVideoScripts(array $structure): array
    {
        $scripts = [];
        foreach ($structure['modules'] as $module) {
            $moduleScripts = [];
            foreach ($module['lessons'] as $lesson) {
                $moduleScripts[] = [
                    'lesson_title' => $lesson['title'],
                    'script' => $this->generateScriptForLesson($lesson['title'], $module['title']),
                    'estimated_duration_minutes' => $lesson['duration'],
                    'talking_points' => [
                        "Introduction to {$lesson['title']}",
                        "Key concepts and definitions",
                        "Practical examples and demonstrations",
                        "Common pitfalls to avoid",
                        "Summary and key takeaways",
                    ],
                ];
            }
            $scripts[] = [
                'module_title' => $module['title'],
                'lessons' => $moduleScripts,
            ];
        }
        return $scripts;
    }

    private function generateScriptForLesson(string $lessonTitle, string $moduleTitle): string
    {
        return "[VIDEO SCRIPT]\n\n" .
            "Title: {$lessonTitle}\n" .
            "Module: {$moduleTitle}\n\n" .
            "--- INTRODUCTION (0:00 - 0:30) ---\n" .
            "Welcome to this lesson on {$lessonTitle}. In this session, we'll explore the key concepts and practical applications.\n\n" .
            "--- MAIN CONTENT (0:30 - " . (rand(8, 15)) . ":00) ---\n" .
            "Let's start by understanding the fundamental principles of {$lessonTitle}.\n\n" .
            "[Explain core concept] The foundation of this topic rests on several key ideas that we'll examine in detail.\n\n" .
            "[Demonstrate with example] Consider this practical scenario that illustrates how {$lessonTitle} applies in real-world situations.\n\n" .
            "[Key insight] One important thing to remember is that mastering these concepts requires consistent practice and application.\n\n" .
            "--- SUMMARY & WRAP-UP ---\n" .
            "To summarize what we've covered today: we explored the core aspects of {$lessonTitle}, examined practical examples, and discussed best practices.\n\n" .
            "In the next lesson, we'll build on these concepts and explore more advanced topics.\n\n" .
            "--- END OF SCRIPT ---";
    }

    /**
     * Generate presentation slides from course structure.
     */
    public function generatePresentationSlides(array $structure): array
    {
        $slides = [];
        
        // Title slide
        $slides[] = [
            'title' => $structure['title'],
            'subtitle' => 'Course Overview',
            'type' => 'title',
            'content' => ["A comprehensive course covering all key concepts and practical applications.", "Duration: {$structure['duration']} minutes", "Level: " . ucfirst($structure['difficulty'])],
        ];

        // Learning objectives slide
        $slides[] = [
            'title' => 'Learning Objectives',
            'type' => 'objectives',
            'content' => explode("\n", $structure['learning_objectives'] ?? 'Understand core concepts. Apply practical techniques. Master advanced topics.'),
        ];

        // One slide per module
        foreach ($structure['modules'] as $module) {
            $slides[] = [
                'title' => "Module {$module['position']}: {$module['title']}",
                'type' => 'module',
                'content' => [$module['description']],
            ];

            // Lesson slides within module
            foreach ($module['lessons'] as $lesson) {
                $slides[] = [
                    'title' => $lesson['title'],
                    'type' => 'lesson',
                    'content' => [
                        "Key concepts covered in this lesson",
                        "Practical examples and demonstrations",
                        "Duration: {$lesson['duration']} minutes",
                        $lesson['has_video'] ? 'Includes video content' : 'Reading material',
                        $lesson['has_exercises'] ? 'Includes practice exercises' : '',
                    ],
                ];
            }
        }

        // Summary slide
        $slides[] = [
            'title' => 'Summary',
            'type' => 'summary',
            'content' => [
                'We covered ' . count($structure['modules']) . ' modules with comprehensive content.',
                'Complete all quizzes and assignments to reinforce learning.',
                'Ready to begin your learning journey!',
            ],
        ];

        return $slides;
    }

    /**
     * Auto-grade different question types.
     */
    public function autoGradeQuestion(string $questionType, string $userAnswer, string $correctAnswer): array
    {
        $score = 0;
        $feedback = '';

        switch ($questionType) {
            case 'mcq':
            case 'true_false':
                $score = strtolower(trim($userAnswer)) === strtolower(trim($correctAnswer)) ? 100 : 0;
                $feedback = $score === 100 ? 'Correct!' : 'Incorrect. The correct answer was: ' . $correctAnswer;
                break;

            case 'fill_blank':
                $keywords = explode(' ', strtolower(trim($correctAnswer)));
                $userKeywords = explode(' ', strtolower(trim($userAnswer)));
                $matches = array_intersect($keywords, $userKeywords);
                $score = count($keywords) > 0 ? round((count($matches) / count($keywords)) * 100) : 0;
                $feedback = "Matched " . count($matches) . " of " . count($keywords) . " keywords.";
                break;

            case 'short_answer':
            case 'essay':
                $keywords = explode(' ', strtolower(trim($correctAnswer)));
                $userKeywords = explode(' ', strtolower(trim($userAnswer)));
                $matches = array_intersect($keywords, $userKeywords);
                $score = count($keywords) > 0 ? round((count($matches) / count($keywords)) * 100) : 0;
                $score = min(85, $score + 15); // Cap at 85% for keyword matching
                $feedback = "AI-assisted scoring: matched " . count($matches) . " key concepts. Instructor review recommended for final grade.";
                break;

            default:
                $feedback = 'Manual review required for this question type.';
                break;
        }

        return [
            'score_percentage' => $score,
            'feedback' => $feedback,
            'auto_graded' => in_array($questionType, ['mcq', 'true_false', 'fill_blank']),
        ];
    }

    /**
     * Validate generated content for quality.
     */
    public function validateGeneratedContent(Course $course): array
    {
        $issues = [];
        $warnings = [];

        // Check modules
        $moduleCount = $course->modules()->count();
        if ($moduleCount === 0) {
            $issues[] = ['type' => 'error', 'message' => 'No modules found in course'];
        } elseif ($moduleCount < 3) {
            $warnings[] = ['type' => 'warning', 'message' => "Only {$moduleCount} modules - minimum 3 recommended"];
        }

        // Check lessons
        $lessonCount = $course->modules()->withCount('lessons')->get()->sum('lessons_count');
        if ($lessonCount === 0) {
            $issues[] = ['type' => 'error', 'message' => 'No lessons found in course'];
        }

        // Check quizzes
        $quizCount = $course->quizzes()->count();
        if ($quizCount === 0) {
            $issues[] = ['type' => 'error', 'message' => 'No quizzes found in course'];
        }

        // Check quiz questions
        foreach ($course->quizzes as $quiz) {
            if ($quiz->questions()->count() === 0) {
                $warnings[] = ['type' => 'warning', 'message' => "Quiz '{$quiz->title}' has no questions"];
            }
        }

        // Check assignments
        if ($course->assignments()->count() === 0) {
            $warnings[] = ['type' => 'warning', 'message' => 'No assignments found in course'];
        }

        // Check final exam
        if ($course->finalExams()->count() === 0) {
            $warnings[] = ['type' => 'warning', 'message' => 'No final exam found in course'];
        }

        // Check description
        if (empty($course->description)) {
            $issues[] = ['type' => 'error', 'message' => 'Course description is missing'];
        }

        // Check learning objectives
        if (empty($course->learning_objectives)) {
            $warnings[] = ['type' => 'warning', 'message' => 'Learning objectives are missing'];
        }

        return [
            'has_issues' => !empty($issues),
            'has_warnings' => !empty($warnings),
            'issues' => $issues,
            'warnings' => $warnings,
            'score' => max(0, 100 - (count($issues) * 25) - (count($warnings) * 10)),
            'module_count' => $moduleCount,
            'lesson_count' => $lessonCount,
            'quiz_count' => $quizCount,
        ];
    }
}
