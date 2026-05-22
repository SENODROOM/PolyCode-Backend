<?php
/**
 * Social Media Application - Main Entry Point
 * 
 * A complete social networking platform with user profiles,
 * posts, messaging, and real-time features.
 */

session_start();

// Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'SocialMedia\\';
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

use SocialMedia\Config\Database;
use SocialMedia\Core\Router;
use SocialMedia\Core\Request;
use SocialMedia\Core\Response;
use SocialMedia\Services\AuthService;
use SocialMedia\Services\PostService;
use SocialMedia\Services\UserService;
use SocialMedia\Services\MessageService;
use SocialMedia\Services\NotificationService;
use SocialMedia\Middleware\AuthMiddleware;
use SocialMedia\Middleware\RateLimitMiddleware;

// Initialize services
$db = new Database();
$router = new Router();
$request = new Request();
$response = new Response();

// Middleware
$router->addMiddleware(new AuthMiddleware());
$router->addMiddleware(new RateLimitMiddleware());

// Services
$authService = new AuthService($db);
$postService = new PostService($db);
$userService = new UserService($db);
$messageService = new MessageService($db);
$notificationService = new NotificationService($db);

// Define routes
$router->get('/', function() use ($authService, $postService) {
    if (!isset($_SESSION['user'])) {
        include __DIR__ . '/views/landing.php';
        return;
    }
    
    $feed = $postService->getFeed($_SESSION['user']['id'], 20);
    $suggestions = $userService->getUserSuggestions($_SESSION['user']['id'], 5);
    
    include __DIR__ . '/views/home.php';
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
        'username' => $request->post('username'),
        'password' => $request->post('password'),
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

// Profile routes
$router->get('/profile/{username}', function($username) use ($userService, $postService) {
    $user = $userService->getUserByUsername($username);
    
    if (!$user) {
        http_response_code(404);
        include __DIR__ . '/views/404.php';
        return;
    }
    
    $posts = $postService->getUserPosts($user['id'], 20);
    $isFollowing = false;
    $followersCount = $userService->getFollowersCount($user['id']);
    $followingCount = $userService->getFollowingCount($user['id']);
    
    if (isset($_SESSION['user'])) {
        $isFollowing = $userService->isFollowing($_SESSION['user']['id'], $user['id']);
    }
    
    include __DIR__ . '/views/profile.php';
});

$router->get('/profile', function() use ($userService) {
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    
    $user = $userService->getUser($_SESSION['user']['id']);
    include __DIR__ . '/views/edit-profile.php';
});

$router->post('/profile', function() use ($userService, $request) {
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    
    $userData = [
        'name' => $request->post('name'),
        'bio' => $request->post('bio'),
        'location' => $request->post('location'),
        'website' => $request->post('website')
    ];
    
    if ($request->hasFile('avatar')) {
        $userData['avatar'] = $request->file('avatar');
    }
    
    $result = $userService->updateProfile($_SESSION['user']['id'], $userData);
    
    if ($result['success']) {
        $_SESSION['success'] = 'Profile updated successfully';
        header('Location: /profile/' . $_SESSION['user']['username']);
    } else {
        $_SESSION['error'] = $result['message'];
        header('Location: /profile');
    }
});

// Post routes
$router->post('/post', function() use ($postService, $request) {
    if (!isset($_SESSION['user'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        exit;
    }
    
    $content = $request->post('content');
    $image = $request->file('image') ?? null;
    
    $result = $postService->createPost($_SESSION['user']['id'], $content, $image);
    
    header('Content-Type: application/json');
    echo json_encode($result);
});

$router->post('/post/{id}/like', function($id) use ($postService) {
    if (!isset($_SESSION['user'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        exit;
    }
    
    $result = $postService->toggleLike($_SESSION['user']['id'], $id);
    
    header('Content-Type: application/json');
    echo json_encode($result);
});

$router->post('/post/{id}/comment', function($id) use ($postService, $request) {
    if (!isset($_SESSION['user'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        exit;
    }
    
    $content = $request->post('content');
    $result = $postService->addComment($_SESSION['user']['id'], $id, $content);
    
    header('Content-Type: application/json');
    echo json_encode($result);
});

$router->delete('/post/{id}', function($id) use ($postService) {
    if (!isset($_SESSION['user'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        exit;
    }
    
    $result = $postService->deletePost($_SESSION['user']['id'], $id);
    
    header('Content-Type: application/json');
    echo json_encode($result);
});

// Follow routes
$router->post('/follow/{userId}', function($userId) use ($userService) {
    if (!isset($_SESSION['user'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        exit;
    }
    
    $result = $userService->toggleFollow($_SESSION['user']['id'], $userId);
    
    header('Content-Type: application/json');
    echo json_encode($result);
});

// Search routes
$router->get('/search', function() use ($userService, $postService, $request) {
    $query = $request->get('q');
    $type = $request->get('type', 'all');
    
    $users = [];
    $posts = [];
    
    if ($type === 'all' || $type === 'users') {
        $users = $userService->searchUsers($query);
    }
    
    if ($type === 'all' || $type === 'posts') {
        $posts = $postService->searchPosts($query);
    }
    
    include __DIR__ . '/views/search.php';
});

// Message routes
$router->get('/messages', function() use ($messageService) {
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    
    $conversations = $messageService->getConversations($_SESSION['user']['id']);
    include __DIR__ . '/views/messages.php';
});

$router->get('/messages/{userId}', function($userId) use ($messageService, $userService) {
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    
    $user = $userService->getUser($userId);
    if (!$user) {
        http_response_code(404);
        include __DIR__ . '/views/404.php';
        return;
    }
    
    $messages = $messageService->getMessages($_SESSION['user']['id'], $userId);
    $messageService->markAsRead($_SESSION['user']['id'], $userId);
    
    include __DIR__ . '/views/conversation.php';
});

$router->post('/messages/{userId}', function($userId) use ($messageService, $request) {
    if (!isset($_SESSION['user'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        exit;
    }
    
    $content = $request->post('content');
    $result = $messageService->sendMessage($_SESSION['user']['id'], $userId, $content);
    
    header('Content-Type: application/json');
    echo json_encode($result);
});

// Notification routes
$router->get('/notifications', function() use ($notificationService) {
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    
    $notifications = $notificationService->getNotifications($_SESSION['user']['id']);
    include __DIR__ . '/views/notifications.php';
});

$router->post('/notifications/read/{id}', function($id) use ($notificationService) {
    if (!isset($_SESSION['user'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        exit;
    }
    
    $result = $notificationService->markAsRead($_SESSION['user']['id'], $id);
    
    header('Content-Type: application/json');
    echo json_encode($result);
});

// API routes for real-time features
$router->get('/api/feed', function() use ($postService, $request) {
    if (!isset($_SESSION['user'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        exit;
    }
    
    $page = $request->get('page', 1);
    $feed = $postService->getFeed($_SESSION['user']['id'], 20, $page);
    
    header('Content-Type: application/json');
    echo json_encode($feed);
});

$router->get('/api/notifications/count', function() use ($notificationService) {
    if (!isset($_SESSION['user'])) {
        header('Content-Type: application/json');
        echo json_encode(['count' => 0]);
        exit;
    }
    
    $count = $notificationService->getUnreadCount($_SESSION['user']['id']);
    
    header('Content-Type: application/json');
    echo json_encode(['count' => $count]);
});

$router->get('/api/messages/unread', function() use ($messageService) {
    if (!isset($_SESSION['user'])) {
        header('Content-Type: application/json');
        echo json_encode(['count' => 0]);
        exit;
    }
    
    $count = $messageService->getUnreadCount($_SESSION['user']['id']);
    
    header('Content-Type: application/json');
    echo json_encode(['count' => $count]);
});

// WebSocket simulation for real-time updates
$router->get('/ws/events', function() use ($notificationService, $messageService) {
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        exit;
    }
    
    // This would be handled by a proper WebSocket server
    // For demo purposes, we'll simulate with long polling
    set_time_limit(60);
    
    $userId = $_SESSION['user']['id'];
    $lastCheck = time();
    
    while (time() - $lastCheck < 30) {
        $notifications = $notificationService->getNewNotifications($userId);
        $messages = $messageService->getNewMessages($userId);
        
        if (!empty($notifications) || !empty($messages)) {
            header('Content-Type: application/json');
            echo json_encode([
                'notifications' => $notifications,
                'messages' => $messages
            ]);
            exit;
        }
        
        sleep(1);
    }
    
    header('Content-Type: application/json');
    echo json_encode(['notifications' => [], 'messages' => []]);
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
