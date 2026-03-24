<?php
/**
 * Project Management Tool - Main Entry Point
 * 
 * A comprehensive project management system with task tracking,
 * team collaboration, time tracking, and reporting.
 */

session_start();

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'ProjectManagement\\';
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

use ProjectManagement\Config\Database;
use ProjectManagement\Core\Router;
use ProjectManagement\Core\Request;
use ProjectManagement\Core\Response;
use ProjectManagement\Services\AuthService;
use ProjectManagement\Services\ProjectService;
use ProjectManagement\Services\TaskService;
use ProjectManagement\Services\TeamService;
use ProjectManagement\Services\TimeTrackingService;
use ProjectManagement\Services\ReportService;
use ProjectManagement\Middleware\AuthMiddleware;

// Initialize services
$db = new Database();
$router = new Router();
$request = new Request();
$response = new Response();

// Middleware
$router->addMiddleware(new AuthMiddleware());

// Services
$authService = new AuthService($db);
$projectService = new ProjectService($db);
$taskService = new TaskService($db);
$teamService = new TeamService($db);
$timeTrackingService = new TimeTrackingService($db);
$reportService = new ReportService($db);

// Define routes
$router->get('/', function() use ($projectService, $taskService) {
    if (!isset($_SESSION['user'])) {
        include __DIR__ . '/views/landing.php';
        return;
    }
    
    $userId = $_SESSION['user']['id'];
    $projects = $projectService->getUserProjects($userId);
    $recentTasks = $taskService->getRecentTasks($userId, 5);
    $upcomingDeadlines = $taskService->getUpcomingDeadlines($userId, 7);
    
    include __DIR__ . '/views/dashboard.php';
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
        'company' => $request->post('company', '')
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

// Project routes
$router->get('/projects', function() use ($projectService) {
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    
    $userId = $_SESSION['user']['id'];
    $projects = $projectService->getUserProjects($userId);
    
    include __DIR__ . '/views/projects.php';
});

$router->get('/projects/new', function() use ($teamService) {
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    
    $teams = $teamService->getUserTeams($_SESSION['user']['id']);
    include __DIR__ . '/views/new-project.php';
});

$router->post('/projects', function() use ($projectService, $request) {
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    
    $projectData = [
        'name' => $request->post('name'),
        'description' => $request->post('description'),
        'start_date' => $request->post('start_date'),
        'end_date' => $request->post('end_date'),
        'team_id' => $request->post('team_id'),
        'created_by' => $_SESSION['user']['id']
    ];
    
    $result = $projectService->createProject($projectData);
    
    if ($result['success']) {
        header('Location: /projects/' . $result['project_id']);
    } else {
        $_SESSION['error'] = $result['message'];
        header('Location: /projects/new');
    }
});

$router->get('/projects/{id}', function($id) use ($projectService, $taskService, $teamService) {
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    
    $project = $projectService->getProject($id);
    if (!$project || !$projectService->hasAccess($_SESSION['user']['id'], $id)) {
        http_response_code(404);
        include __DIR__ . '/views/404.php';
        return;
    }
    
    $tasks = $taskService->getProjectTasks($id);
    $team = $teamService->getTeam($project['team_id']);
    $progress = $projectService->getProjectProgress($id);
    
    include __DIR__ . '/views/project.php';
});

$router->post('/projects/{id}', function($id) use ($projectService, $request) {
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    
    if (!$projectService->hasAccess($_SESSION['user']['id'], $id)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        exit;
    }
    
    $projectData = [
        'name' => $request->post('name'),
        'description' => $request->post('description'),
        'start_date' => $request->post('start_date'),
        'end_date' => $request->post('end_date'),
        'status' => $request->post('status')
    ];
    
    $result = $projectService->updateProject($id, $projectData);
    
    header('Content-Type: application/json');
    echo json_encode($result);
});

// Task routes
$router->get('/tasks', function() use ($taskService) {
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    
    $userId = $_SESSION['user']['id'];
    $status = $_GET['status'] ?? 'all';
    $priority = $_GET['priority'] ?? 'all';
    
    $tasks = $taskService->getUserTasks($userId, $status, $priority);
    
    include __DIR__ . '/views/tasks.php';
});

$router->get('/projects/{projectId}/tasks/new', function($projectId) use ($projectService, $teamService) {
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    
    if (!$projectService->hasAccess($_SESSION['user']['id'], $projectId)) {
        header('Location: /projects');
        exit;
    }
    
    $project = $projectService->getProject($projectId);
    $teamMembers = $teamService->getTeamMembers($project['team_id']);
    
    include __DIR__ . '/views/new-task.php';
});

$router->post('/projects/{projectId}/tasks', function($projectId) use ($taskService, $request) {
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    
    if (!$projectService->hasAccess($_SESSION['user']['id'], $projectId)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        exit;
    }
    
    $taskData = [
        'project_id' => $projectId,
        'title' => $request->post('title'),
        'description' => $request->post('description'),
        'priority' => $request->post('priority'),
        'due_date' => $request->post('due_date'),
        'assigned_to' => $request->post('assigned_to'),
        'created_by' => $_SESSION['user']['id']
    ];
    
    $result = $taskService->createTask($taskData);
    
    if ($result['success']) {
        header('Location: /tasks/' . $result['task_id']);
    } else {
        $_SESSION['error'] = $result['message'];
        header('Location: /projects/' . $projectId . '/tasks/new');
    }
});

$router->get('/tasks/{id}', function($id) use ($taskService, $timeTrackingService) {
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    
    $task = $taskService->getTask($id);
    if (!$task || !$taskService->hasAccess($_SESSION['user']['id'], $id)) {
        http_response_code(404);
        include __DIR__ . '/views/404.php';
        return;
    }
    
    $timeEntries = $timeTrackingService->getTaskTimeEntries($id);
    $totalTime = $timeTrackingService->getTaskTotalTime($id);
    
    include __DIR__ . '/views/task.php';
});

$router->post('/tasks/{id}', function($id) use ($taskService, $request) {
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    
    if (!$taskService->hasAccess($_SESSION['user']['id'], $id)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        exit;
    }
    
    $taskData = [
        'title' => $request->post('title'),
        'description' => $request->post('description'),
        'priority' => $request->post('priority'),
        'due_date' => $request->post('due_date'),
        'status' => $request->post('status'),
        'assigned_to' => $request->post('assigned_to')
    ];
    
    $result = $taskService->updateTask($id, $taskData);
    
    header('Content-Type: application/json');
    echo json_encode($result);
});

$router->post('/tasks/{id}/comments', function($id) use ($taskService, $request) {
    if (!isset($_SESSION['user'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        exit;
    }
    
    if (!$taskService->hasAccess($_SESSION['user']['id'], $id)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        exit;
    }
    
    $commentData = [
        'content' => $request->post('content'),
        'user_id' => $_SESSION['user']['id']
    ];
    
    $result = $taskService->addComment($id, $commentData);
    
    header('Content-Type: application/json');
    echo json_encode($result);
});

// Time tracking routes
$router->post('/tasks/{id}/time', function($id) use ($timeTrackingService, $request) {
    if (!isset($_SESSION['user'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        exit;
    }
    
    if (!$taskService->hasAccess($_SESSION['user']['id'], $id)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        exit;
    }
    
    $timeData = [
        'user_id' => $_SESSION['user']['id'],
        'hours' => $request->post('hours'),
        'description' => $request->post('description'),
        'date' => $request->post('date', date('Y-m-d'))
    ];
    
    $result = $timeTrackingService->addTimeEntry($id, $timeData);
    
    header('Content-Type: application/json');
    echo json_encode($result);
});

// Team routes
$router->get('/teams', function() use ($teamService) {
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    
    $userId = $_SESSION['user']['id'];
    $teams = $teamService->getUserTeams($userId);
    
    include __DIR__ . '/views/teams.php';
});

$router->get('/teams/new', function() {
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    
    include __DIR__ . '/views/new-team.php';
});

$router->post('/teams', function() use ($teamService, $request) {
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    
    $teamData = [
        'name' => $request->post('name'),
        'description' => $request->post('description'),
        'created_by' => $_SESSION['user']['id']
    ];
    
    $result = $teamService->createTeam($teamData);
    
    if ($result['success']) {
        header('Location: /teams/' . $result['team_id']);
    } else {
        $_SESSION['error'] = $result['message'];
        header('Location: /teams/new');
    }
});

$router->get('/teams/{id}', function($id) use ($teamService) {
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    
    $team = $teamService->getTeam($id);
    if (!$team || !$teamService->isMember($_SESSION['user']['id'], $id)) {
        http_response_code(404);
        include __DIR__ . '/views/404.php';
        return;
    }
    
    $members = $teamService->getTeamMembers($id);
    $projects = $teamService->getTeamProjects($id);
    
    include __DIR__ . '/views/team.php';
});

// Kanban board
$router->get('/projects/{id}/kanban', function($id) use ($projectService, $taskService) {
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    
    if (!$projectService->hasAccess($_SESSION['user']['id'], $id)) {
        header('Location: /projects');
        exit;
    }
    
    $project = $projectService->getProject($id);
    $tasks = $taskService->getProjectTasksByStatus($id);
    
    include __DIR__ . '/views/kanban.php';
});

// Reports
$router->get('/reports', function() use ($reportService) {
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    
    $userId = $_SESSION['user']['id'];
    $projectStats = $reportService->getUserProjectStats($userId);
    $taskStats = $reportService->getUserTaskStats($userId);
    $timeStats = $reportService->getUserTimeStats($userId);
    
    include __DIR__ . '/views/reports.php';
});

$router->get('/reports/projects/{id}', function($id) use ($projectService, $reportService) {
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    
    if (!$projectService->hasAccess($_SESSION['user']['id'], $id)) {
        header('Location: /projects');
        exit;
    }
    
    $project = $projectService->getProject($id);
    $projectStats = $reportService->getProjectStats($id);
    $taskStats = $reportService->getProjectTaskStats($id);
    $timeStats = $reportService->getProjectTimeStats($id);
    
    include __DIR__ . '/views/project-reports.php';
});

// API routes
$router->get('/api/tasks/{id}/status', function($id) use ($taskService) {
    if (!isset($_SESSION['user'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        exit;
    }
    
    if (!$taskService->hasAccess($_SESSION['user']['id'], $id)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        exit;
    }
    
    $result = $taskService->updateTaskStatus($id, $_GET['status']);
    
    header('Content-Type: application/json');
    echo json_encode($result);
});

$router->get('/api/projects/{id}/gantt', function($id) use ($projectService, $taskService) {
    if (!isset($_SESSION['user'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        exit;
    }
    
    if (!$projectService->hasAccess($_SESSION['user']['id'], $id)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        exit;
    }
    
    $project = $projectService->getProject($id);
    $tasks = $taskService->getProjectTasks($id);
    
    $ganttData = [
        'project' => $project,
        'tasks' => array_map(function($task) {
            return [
                'id' => $task['id'],
                'name' => $task['title'],
                'start' => $task['start_date'],
                'end' => $task['due_date'],
                'progress' => $task['progress'],
                'dependencies' => $task['dependencies'] ?? []
            ];
        }, $tasks)
    ];
    
    header('Content-Type: application/json');
    echo json_encode($ganttData);
});

// Calendar view
$router->get('/calendar', function() use ($taskService) {
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    
    $userId = $_SESSION['user']['id'];
    $month = $_GET['month'] ?? date('Y-m');
    $tasks = $taskService->getTasksByMonth($userId, $month);
    
    include __DIR__ . '/views/calendar.php';
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
