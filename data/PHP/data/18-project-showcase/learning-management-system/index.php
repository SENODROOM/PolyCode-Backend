<?php
/**
 * Learning Management System - Main Entry Point
 * 
 * A comprehensive LMS with course management, student enrollment,
 * progress tracking, and assessment features.
 */

session_start();

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'LMS\\';
    $base_dir = __DIR__ . '/src/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

use LMS\Config\Database;
use LMS\Core\Router;
use LMS\Core\Request;
use LMS\Core\Response;
use LMS\Services\AuthService;
use LMS\Services\CourseService;
use LMS\Services\StudentService;
use LMS\Services\InstructorService;
use LMS\Services\LessonService;
use LMS\Services\QuizService;
use LMS\Services\ProgressService;
use LMS\Services\CertificateService;
use LMS\Middleware\AuthMiddleware;

// Initialize services
$db = new Database();
$router = new Router();
$request = new Request();
$response = new Response();

// Middleware
$router->addMiddleware(new AuthMiddleware());

// Services
$authService = new AuthService($db);
$courseService = new CourseService($db);
$studentService = new StudentService($db);
$instructorService = new InstructorService($db);
$lessonService = new LessonService($db);
$quizService = new QuizService($db);
$progressService = new ProgressService($db);
$certificateService = new CertificateService($db);

// Define routes
$router->get('/', function() use ($courseService) {
    if (!isset($_SESSION['user'])) {
        include __DIR__ . '/views/landing.php';
        return;
    }
    
    $userId = $_SESSION['user']['id'];
    $userRole = $_SESSION['user']['role'];
    
    if ($userRole === 'student') {
        $courses = $courseService->getEnrolledCourses($userId);
        include __DIR__ . '/views/student/dashboard.php';
    } elseif ($userRole === 'instructor') {
        $courses = $courseService->getInstructorCourses($userId);
        include __DIR__ . '/views/instructor/dashboard.php';
    } else {
        $courses = $courseService->getAllCourses();
        include __DIR__ . '/views/admin/dashboard.php';
    }
});

// Authentication routes
$router->get('/login', function() {
    if (isset($_SESSION['user'])) {
        header('Location: /');
        exit;
    }
    include __DIR__ . '/views/login.php';
});

$router->post('/login', function() use ($authService, $request) {
    $email = $request->post('email');
    $password = $request->post('password');
    
    $result = $authService->login($email, $password);
    
    if ($result['success']) {
        header('Location: /');
    } else {
        $_SESSION['error'] = $result['message'];
        header('Location: /login');
    }
});

$router->get('/register', function() {
    if (isset($_SESSION['user'])) {
        header('Location: /');
        exit;
    }
    include __DIR__ . '/views/register.php';
});

$router->post('/register', function() use ($authService, $request) {
    $userData = [
        'name' => $request->post('name'),
        'email' => $request->post('email'),
        'password' => $request->post('password'),
        'role' => $request->post('role', 'student'),
        'phone' => $request->post('phone', ''),
        'bio' => $request->post('bio', '')
    ];
    
    $result = $authService->register($userData);
    
    if ($result['success']) {
        header('Location: /login?registered=true');
    } else {
        $_SESSION['error'] = $result['message'];
        header('Location: /register');
    }
});

$router->get('/logout', function() use ($authService) {
    $authService->logout();
    header('Location: /');
});

// Course routes
$router->get('/courses', function() use ($courseService) {
    $page = $_GET['page'] ?? 1;
    $category = $_GET['category'] ?? '';
    $search = $_GET['search'] ?? '';
    $level = $_GET['level'] ?? '';
    
    $courses = $courseService->getCourses($page, 12, $category, $search, $level);
    $categories = $courseService->getCategories();
    
    include __DIR__ . '/views/courses.php';
});

$router->get('/courses/{id}', function($id) use ($courseService, $lessonService, $quizService) {
    $course = $courseService->getCourse($id);
    
    if (!$course) {
        http_response_code(404);
        include __DIR__ . '/views/404.php';
        return;
    }
    
    $lessons = $lessonService->getCourseLessons($id);
    $quizzes = $quizService->getCourseQuizzes($id);
    $isEnrolled = false;
    
    if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'student') {
        $isEnrolled = $courseService->isEnrolled($_SESSION['user']['id'], $id);
    }
    
    include __DIR__ . '/views/course.php';
});

$router->post('/courses/{id}/enroll', function($id) use ($courseService) {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Students only can enroll in courses']);
        exit;
    }
    
    $result = $courseService->enrollStudent($_SESSION['user']['id'], $id);
    
    header('Content-Type: application/json');
    echo json_encode($result);
});

// Instructor course management
$router->get('/instructor/courses', function() use ($courseService) {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'instructor') {
        header('Location: /login');
        exit;
    }
    
    $courses = $courseService->getInstructorCourses($_SESSION['user']['id']);
    include __DIR__ . '/views/instructor/courses.php';
});

$router->get('/instructor/courses/new', function() {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'instructor') {
        header('Location: /login');
        exit;
    }
    
    include __DIR__ . '/views/instructor/new-course.php';
});

$router->post('/instructor/courses', function() use ($courseService, $request) {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'instructor') {
        header('Location: /login');
        exit;
    }
    
    $courseData = [
        'title' => $request->post('title'),
        'description' => $request->post('description'),
        'category' => $request->post('category'),
        'level' => $request->post('level'),
        'price' => $request->post('price'),
        'duration' => $request->post('duration'),
        'instructor_id' => $_SESSION['user']['id']
    ];
    
    if ($request->hasFile('thumbnail')) {
        $courseData['thumbnail'] = $request->file('thumbnail');
    }
    
    $result = $courseService->createCourse($courseData);
    
    if ($result['success']) {
        header('Location: /instructor/courses/' . $result['course_id']);
    } else {
        $_SESSION['error'] = $result['message'];
        header('Location: /instructor/courses/new');
    }
});

// Lesson routes
$router->get('/courses/{courseId}/lessons/{lessonId}', function($courseId, $lessonId) use ($courseService, $lessonService, $progressService) {
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    
    $course = $courseService->getCourse($courseId);
    $lesson = $lessonService->getLesson($lessonId);
    
    if (!$course || !$lesson || $lesson['course_id'] != $courseId) {
        http_response_code(404);
        include __DIR__ . '/views/404.php';
        return;
    }
    
    // Check access
    $hasAccess = false;
    if ($_SESSION['user']['role'] === 'instructor' && $course['instructor_id'] == $_SESSION['user']['id']) {
        $hasAccess = true;
    } elseif ($_SESSION['user']['role'] === 'student') {
        $hasAccess = $courseService->isEnrolled($_SESSION['user']['id'], $courseId);
    }
    
    if (!$hasAccess) {
        header('Location: /courses/' . $courseId);
        exit;
    }
    
    // Mark lesson as viewed
    if ($_SESSION['user']['role'] === 'student') {
        $progressService->markLessonViewed($_SESSION['user']['id'], $lessonId);
    }
    
    $nextLesson = $lessonService->getNextLesson($courseId, $lessonId);
    $previousLesson = $lessonService->getPreviousLesson($courseId, $lessonId);
    $progress = $progressService->getLessonProgress($_SESSION['user']['id'], $lessonId);
    
    include __DIR__ . '/views/lesson.php';
});

// Quiz routes
$router->get('/courses/{courseId}/quizzes/{quizId}', function($courseId, $quizId) use ($courseService, $quizService) {
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    
    $course = $courseService->getCourse($courseId);
    $quiz = $quizService->getQuiz($quizId);
    
    if (!$course || !$quiz || $quiz['course_id'] != $courseId) {
        http_response_code(404);
        include __DIR__ . '/views/404.php';
        return;
    }
    
    // Check access
    $hasAccess = false;
    if ($_SESSION['user']['role'] === 'instructor' && $course['instructor_id'] == $_SESSION['user']['id']) {
        $hasAccess = true;
    } elseif ($_SESSION['user']['role'] === 'student') {
        $hasAccess = $courseService->isEnrolled($_SESSION['user']['id'], $courseId);
    }
    
    if (!$hasAccess) {
        header('Location: /courses/' . $courseId);
        exit;
    }
    
    $questions = $quizService->getQuizQuestions($quizId);
    $attempt = $quizService->getLatestAttempt($_SESSION['user']['id'], $quizId);
    
    include __DIR__ . '/views/quiz.php';
});

$router->post('/courses/{courseId}/quizzes/{quizId}/submit', function($courseId, $quizId) use ($quizService, $request) {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        exit;
    }
    
    $answers = $request->post('answers', []);
    $result = $quizService->submitQuiz($_SESSION['user']['id'], $quizId, $answers);
    
    header('Content-Type: application/json');
    echo json_encode($result);
});

// Student progress
$router->get('/my-courses', function() use ($courseService, $progressService) {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
        header('Location: /login');
        exit;
    }
    
    $userId = $_SESSION['user']['id'];
    $courses = $courseService->getEnrolledCourses($userId);
    
    foreach ($courses as &$course) {
        $course['progress'] = $progressService->getCourseProgress($userId, $course['id']);
    }
    
    include __DIR__ . '/views/student/my-courses.php';
});

$router->get('/my-progress/{courseId}', function($courseId) use ($courseService, $progressService, $lessonService, $quizService) {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
        header('Location: /login');
        exit;
    }
    
    $course = $courseService->getCourse($courseId);
    if (!$course || !$courseService->isEnrolled($_SESSION['user']['id'], $courseId)) {
        header('Location: /my-courses');
        exit;
    }
    
    $userId = $_SESSION['user']['id'];
    $courseProgress = $progressService->getCourseProgress($userId, $courseId);
    $lessons = $lessonService->getCourseLessons($courseId);
    $quizzes = $quizService->getCourseQuizzes($courseId);
    
    foreach ($lessons as &$lesson) {
        $lesson['progress'] = $progressService->getLessonProgress($userId, $lesson['id']);
    }
    
    foreach ($quizzes as &$quiz) {
        $quiz['attempt'] = $quizService->getLatestAttempt($userId, $quiz['id']);
    }
    
    include __DIR__ . '/views/student/progress.php';
});

// Certificate routes
$router->get('/my-certificates', function() use ($certificateService) {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
        header('Location: /login');
        exit;
    }
    
    $certificates = $certificateService->getUserCertificates($_SESSION['user']['id']);
    include __DIR__ . '/views/student/certificates.php';
});

$router->get('/certificates/{id}', function($id) use ($certificateService) {
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    
    $certificate = $certificateService->getCertificate($id);
    
    if (!$certificate || 
        ($certificate['user_id'] != $_SESSION['user']['id'] && $_SESSION['user']['role'] !== 'admin')) {
        http_response_code(404);
        include __DIR__ . '/views/404.php';
        return;
    }
    
    include __DIR__ . '/views/certificate.php';
});

// Search routes
$router->get('/search', function() use ($courseService, $request) {
    $query = $request->get('q');
    $type = $request->get('type', 'courses');
    
    $results = [];
    if ($type === 'courses') {
        $results = $courseService->searchCourses($query);
    }
    
    include __DIR__ . '/views/search.php';
});

// API routes
$router->get('/api/courses/{id}/progress', function($id) use ($progressService) {
    if (!isset($_SESSION['user'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        exit;
    }
    
    $userId = $_SESSION['user']['id'];
    $progress = $progressService->getCourseProgress($userId, $id);
    
    header('Content-Type: application/json');
    echo json_encode($progress);
});

$router->post('/api/lessons/{id}/complete', function($id) use ($progressService) {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        exit;
    }
    
    $result = $progressService->markLessonCompleted($_SESSION['user']['id'], $id);
    
    header('Content-Type: application/json');
    echo json_encode($result);
});

// Video streaming
$router->get('/video/{id}', function($id) use ($lessonService) {
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    
    $lesson = $lessonService->getLesson($id);
    if (!$lesson || empty($lesson['video_url'])) {
        http_response_code(404);
        exit;
    }
    
    // Check access
    $hasAccess = false;
    if ($_SESSION['user']['role'] === 'instructor') {
        // Instructors can access their own course videos
        $course = $lessonService->getLessonCourse($id);
        $hasAccess = $course['instructor_id'] == $_SESSION['user']['id'];
    } elseif ($_SESSION['user']['role'] === 'student') {
        // Students must be enrolled
        $course = $lessonService->getLessonCourse($id);
        $hasAccess = $courseService->isEnrolled($_SESSION['user']['id'], $course['id']);
    }
    
    if (!$hasAccess) {
        http_response_code(403);
        exit;
    }
    
    // Stream video (simplified - in production, use proper streaming)
    header('Content-Type: video/mp4');
    readfile($lesson['video_url']);
});

// Handle the request
try {
    $router->dispatch($request, $response);
} catch (Exception $e) {
    error_log($e->getMessage());
    
    http_response_code(500);
    include __DIR__ . '/views/500.php';
}
?>
