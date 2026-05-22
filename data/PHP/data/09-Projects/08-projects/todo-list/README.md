# Project 1: Todo List Manager 📝

A simple but complete todo list application that demonstrates CRUD operations, form handling, and session management.

## 🎯 Learning Objectives

After completing this project, you will:
- Master CRUD operations (Create, Read, Update, Delete)
- Understand form processing and validation
- Implement session-based data persistence
- Practice array manipulation and data filtering
- Build a clean, responsive user interface

## 🛠️ Features

- ✅ Add new todos
- ✅ Mark todos as complete/incomplete
- ✅ Edit existing todos
- ✅ Delete todos
- ✅ Filter todos by status (All, Active, Completed)
- ✅ Count active and completed todos
- ✅ Clean, modern UI with responsive design
- ✅ Session-based data persistence

## 📁 Project Structure

```
todo-list/
├── README.md           # This file
├── index.php          # Main application
├── config.php         # Configuration
├── functions.php      # Helper functions
├── style.css          # Styles
└── assets/
    └── images/        # Images and icons
```

## 🚀 Getting Started

### Prerequisites
- PHP 7.0 or higher
- Web server (Apache, Nginx, or PHP built-in server)
- Modern web browser

### Setup Instructions

1. **Navigate to the project directory**
   ```bash
   cd php-learning-guide/08-projects/todo-list
   ```

2. **Start PHP development server**
   ```bash
   php -S localhost:8000
   ```

3. **Open your browser**
   Visit `http://localhost:8000`

4. **Start using the todo list!**

## 📖 Implementation Guide

### Step 1: Configuration (config.php)
```php
<?php
// Start session
session_start();

// Application configuration
define('APP_NAME', 'Todo List Manager');
define('APP_VERSION', '1.0.0');

// Session key for todos
define('TODOS_SESSION_KEY', 'todos');

// Initialize todos array if not exists
if (!isset($_SESSION[TODOS_SESSION_KEY])) {
    $_SESSION[TODOS_SESSION_KEY] = [];
}
?>
```

### Step 2: Core Functions (functions.php)
```php
<?php
// Get all todos
function getTodos() {
    return $_SESSION[TODOS_SESSION_KEY] ?? [];
}

// Add new todo
function addTodo($text) {
    $todos = getTodos();
    $newTodo = [
        'id' => uniqid(),
        'text' => htmlspecialchars(trim($text)),
        'completed' => false,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    $_SESSION[TODOS_SESSION_KEY][] = $newTodo;
    return $newTodo;
}

// Update todo
function updateTodo($id, $text, $completed = null) {
    $todos = getTodos();
    
    foreach ($todos as &$todo) {
        if ($todo['id'] === $id) {
            if ($text !== null) {
                $todo['text'] = htmlspecialchars(trim($text));
            }
            if ($completed !== null) {
                $todo['completed'] = (bool)$completed;
            }
            $todo['updated_at'] = date('Y-m-d H:i:s');
            break;
        }
    }
    
    $_SESSION[TODOS_SESSION_KEY] = $todos;
    return true;
}

// Delete todo
function deleteTodo($id) {
    $todos = getTodos();
    $filteredTodos = array_filter($todos, function($todo) use ($id) {
        return $todo['id'] !== $id;
    });
    
    $_SESSION[TODOS_SESSION_KEY] = array_values($filteredTodos);
    return true;
}

// Get todo statistics
function getTodoStats() {
    $todos = getTodos();
    $total = count($todos);
    $completed = count(array_filter($todos, function($todo) {
        return $todo['completed'];
    }));
    $active = $total - $completed;
    
    return [
        'total' => $total,
        'completed' => $completed,
        'active' => $active
    ];
}

// Filter todos by status
function filterTodos($status = 'all') {
    $todos = getTodos();
    
    switch ($status) {
        case 'active':
            return array_filter($todos, function($todo) {
                return !$todo['completed'];
            });
        case 'completed':
            return array_filter($todos, function($todo) {
                return $todo['completed'];
            });
        default:
            return $todos;
    }
}
?>
```

### Step 3: Main Application (index.php)
```php
<?php
require_once 'config.php';
require_once 'functions.php';

// Handle form submissions
$message = '';
$error = '';

// Add new todo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'add':
            $text = $_POST['todo_text'] ?? '';
            if (!empty($text)) {
                addTodo($text);
                $message = 'Todo added successfully!';
            } else {
                $error = 'Please enter a todo item.';
            }
            break;
            
        case 'update':
            $id = $_POST['todo_id'] ?? '';
            $text = $_POST['todo_text'] ?? '';
            $completed = isset($_POST['completed']) ? 1 : 0;
            
            if (!empty($id) && !empty($text)) {
                updateTodo($id, $text, $completed);
                $message = 'Todo updated successfully!';
            } else {
                $error = 'Invalid todo data.';
            }
            break;
            
        case 'delete':
            $id = $_POST['todo_id'] ?? '';
            if (!empty($id)) {
                deleteTodo($id);
                $message = 'Todo deleted successfully!';
            }
            break;
            
        case 'toggle':
            $id = $_POST['todo_id'] ?? '';
            $completed = $_POST['completed'] ?? 0;
            
            if (!empty($id)) {
                updateTodo($id, null, $completed);
                $message = 'Todo status updated!';
            }
            break;
    }
}

// Get current filter
$filter = $_GET['filter'] ?? 'all';
$filteredTodos = filterTodos($filter);
$stats = getTodoStats();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><?= APP_NAME ?></h1>
            <p class="subtitle">Manage your tasks efficiently</p>
        </header>

        <!-- Statistics -->
        <div class="stats">
            <div class="stat-item">
                <span class="stat-number"><?= $stats['total'] ?></span>
                <span class="stat-label">Total</span>
            </div>
            <div class="stat-item">
                <span class="stat-number"><?= $stats['active'] ?></span>
                <span class="stat-label">Active</span>
            </div>
            <div class="stat-item">
                <span class="stat-number"><?= $stats['completed'] ?></span>
                <span class="stat-label">Completed</span>
            </div>
        </div>

        <!-- Messages -->
        <?php if ($message): ?>
            <div class="message success"><?= $message ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="message error"><?= $error ?></div>
        <?php endif; ?>

        <!-- Add Todo Form -->
        <div class="add-todo">
            <form method="post" class="todo-form">
                <input type="hidden" name="action" value="add">
                <input type="text" name="todo_text" placeholder="What needs to be done?" required>
                <button type="submit" class="btn btn-primary">Add Todo</button>
            </form>
        </div>

        <!-- Filter Tabs -->
        <div class="filters">
            <a href="?filter=all" class="filter-tab <?= $filter === 'all' ? 'active' : '' ?>">
                All (<?= $stats['total'] ?>)
            </a>
            <a href="?filter=active" class="filter-tab <?= $filter === 'active' ? 'active' : '' ?>">
                Active (<?= $stats['active'] ?>)
            </a>
            <a href="?filter=completed" class="filter-tab <?= $filter === 'completed' ? 'active' : '' ?>">
                Completed (<?= $stats['completed'] ?>)
            </a>
        </div>

        <!-- Todo List -->
        <div class="todo-list">
            <?php if (empty($filteredTodos)): ?>
                <div class="empty-state">
                    <p>No todos found. Add one to get started!</p>
                </div>
            <?php else: ?>
                <?php foreach ($filteredTodos as $todo): ?>
                    <div class="todo-item <?= $todo['completed'] ? 'completed' : '' ?>">
                        <form method="post" class="todo-item-form">
                            <input type="hidden" name="action" value="toggle">
                            <input type="hidden" name="todo_id" value="<?= $todo['id'] ?>">
                            <input type="hidden" name="completed" value="<?= $todo['completed'] ? '0' : '1' ?>">
                            
                            <button type="submit" class="checkbox">
                                <span class="checkmark"><?= $todo['completed'] ? '✓' : '' ?></span>
                            </button>
                        </form>
                        
                        <div class="todo-content">
                            <div class="todo-text"><?= $todo['text'] ?></div>
                            <div class="todo-meta">
                                <small>Created: <?= date('M j, Y', strtotime($todo['created_at'])) ?></small>
                                <?php if ($todo['updated_at'] !== $todo['created_at']): ?>
                                    <small>• Updated: <?= date('M j, Y', strtotime($todo['updated_at'])) ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="todo-actions">
                            <button class="btn-edit" onclick="editTodo('<?= $todo['id'] ?>', '<?= htmlspecialchars($todo['text'], ENT_QUOTES) ?>')">Edit</button>
                            <form method="post" class="delete-form">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="todo_id" value="<?= $todo['id'] ?>">
                                <button type="submit" class="btn-delete">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h3>Edit Todo</h3>
            <form method="post" id="editForm">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="todo_id" id="editTodoId">
                <input type="text" name="todo_text" id="editTodoText" required>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editTodo(id, text) {
            document.getElementById('editTodoId').value = id;
            document.getElementById('editTodoText').value = text;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target === modal) {
                closeEditModal();
            }
        }
    </script>
</body>
</html>
```

### Step 4: Styling (style.css)
```css
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 20px;
}

.container {
    max-width: 600px;
    margin: 0 auto;
    background: white;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    overflow: hidden;
}

header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    text-align: center;
}

header h1 {
    font-size: 2.5rem;
    margin-bottom: 10px;
}

.subtitle {
    opacity: 0.9;
    font-size: 1.1rem;
}

.stats {
    display: flex;
    justify-content: space-around;
    padding: 20px;
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
}

.stat-item {
    text-align: center;
}

.stat-number {
    display: block;
    font-size: 2rem;
    font-weight: bold;
    color: #667eea;
}

.stat-label {
    color: #6c757d;
    font-size: 0.9rem;
}

.message {
    padding: 15px 20px;
    margin: 20px;
    border-radius: 5px;
    font-weight: 500;
}

.message.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.message.error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.add-todo {
    padding: 20px;
}

.todo-form {
    display: flex;
    gap: 10px;
}

.todo-form input[type="text"] {
    flex: 1;
    padding: 12px;
    border: 2px solid #e9ecef;
    border-radius: 5px;
    font-size: 1rem;
}

.todo-form input[type="text"]:focus {
    outline: none;
    border-color: #667eea;
}

.btn {
    padding: 12px 20px;
    border: none;
    border-radius: 5px;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-primary {
    background: #667eea;
    color: white;
}

.btn-primary:hover {
    background: #5a6fd8;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #545b62;
}

.filters {
    display: flex;
    border-bottom: 1px solid #e9ecef;
}

.filter-tab {
    flex: 1;
    padding: 15px;
    text-align: center;
    text-decoration: none;
    color: #6c757d;
    border-bottom: 3px solid transparent;
    transition: all 0.3s ease;
}

.filter-tab:hover {
    color: #495057;
    background: #f8f9fa;
}

.filter-tab.active {
    color: #667eea;
    border-bottom-color: #667eea;
}

.todo-list {
    padding: 20px;
}

.empty-state {
    text-align: center;
    padding: 40px;
    color: #6c757d;
}

.todo-item {
    display: flex;
    align-items: center;
    padding: 15px;
    border-bottom: 1px solid #e9ecef;
    transition: background-color 0.3s ease;
}

.todo-item:hover {
    background: #f8f9fa;
}

.todo-item.completed .todo-text {
    text-decoration: line-through;
    color: #6c757d;
}

.checkbox {
    width: 24px;
    height: 24px;
    border: 2px solid #667eea;
    border-radius: 50%;
    margin-right: 15px;
    cursor: pointer;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.checkbox:hover {
    background: #f0f4ff;
}

.todo-item.completed .checkbox {
    background: #667eea;
}

.checkmark {
    color: white;
    font-size: 14px;
}

.todo-content {
    flex: 1;
}

.todo-text {
    font-size: 1rem;
    margin-bottom: 5px;
}

.todo-meta {
    color: #6c757d;
    font-size: 0.8rem;
}

.todo-actions {
    display: flex;
    gap: 10px;
}

.btn-edit, .btn-delete {
    padding: 8px 12px;
    border: none;
    border-radius: 3px;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-edit {
    background: #28a745;
    color: white;
}

.btn-edit:hover {
    background: #218838;
}

.btn-delete {
    background: #dc3545;
    color: white;
}

.btn-delete:hover {
    background: #c82333;
}

.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
}

.modal-content {
    background: white;
    margin: 15% auto;
    padding: 30px;
    border-radius: 10px;
    width: 90%;
    max-width: 500px;
}

.modal-content h3 {
    margin-bottom: 20px;
    color: #333;
}

.modal-content input[type="text"] {
    width: 100%;
    padding: 12px;
    border: 2px solid #e9ecef;
    border-radius: 5px;
    font-size: 1rem;
    margin-bottom: 20px;
}

.form-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.delete-form {
    display: inline;
}

@media (max-width: 768px) {
    .container {
        margin: 10px;
        border-radius: 10px;
    }
    
    header h1 {
        font-size: 2rem;
    }
    
    .todo-form {
        flex-direction: column;
    }
    
    .todo-item {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .todo-actions {
        margin-top: 10px;
        width: 100%;
        justify-content: flex-end;
    }
}
```

## 🎯 Challenges and Enhancements

### Easy Challenges
1. **Add Due Dates**: Extend todos to include due dates
2. **Priority Levels**: Add priority (High, Medium, Low)
3. **Categories**: Organize todos by categories
4. **Search**: Add search functionality

### Intermediate Challenges
1. **Database Integration**: Replace session storage with MySQL
2. **User Accounts**: Add user authentication
3. **Export/Import**: Export todos to CSV/JSON
4. **Recurring Tasks**: Add recurring todo functionality

### Advanced Challenges
1. **API Endpoints**: Create REST API for todos
2. **Real-time Updates**: Use WebSockets for real-time sync
3. **Mobile App**: Create a mobile version
4. **Collaboration**: Allow sharing todos between users

## 🧪 Testing Your Application

### Manual Testing Checklist
- [ ] Add a new todo
- [ ] Mark todo as complete
- [ ] Edit an existing todo
- [ ] Delete a todo
- [ ] Filter by status (All, Active, Completed)
- [ ] Verify statistics update correctly
- [ ] Test empty state
- [ ] Test responsive design

### Test Cases
1. **Empty Input**: Try adding empty todo
2. **Long Text**: Add very long todo text
3. **Special Characters**: Use special characters in todos
4. **Session Persistence**: Verify todos persist across page refreshes
5. **Multiple Operations**: Perform multiple operations in sequence

## 📚 What You've Learned

After completing this project, you've mastered:
- ✅ Session management
- ✅ Form processing and validation
- ✅ CRUD operations
- ✅ Array manipulation
- ✅ Conditional rendering
- ✅ Responsive web design
- ✅ JavaScript integration
- ✅ Error handling
- ✅ Code organization

## 🚀 Next Steps

1. **Enhance the Project**: Implement some of the challenges above
2. **Add Database**: Replace session storage with MySQL
3. **Build API**: Create REST API endpoints
4. **Create Tests**: Write unit tests for your functions
5. **Deploy**: Deploy to a live server

---

**Ready for the next project?** ➡️ [Blog System](../blog-system/README.md)
