# Project 4: Contact Manager 📇

A comprehensive contact management system with advanced features like search, categorization, and data export capabilities.

## 🎯 Learning Objectives

After completing this project, you will:
- Build complex database queries and relationships
- Implement advanced search and filtering
- Create data import/export functionality
- Build REST API endpoints
- Implement data validation and security
- Create responsive admin interfaces
- Handle large datasets efficiently

## 🛠️ Features

### Contact Management
- ✅ Add, edit, delete contacts
- ✅ Contact categories and groups
- ✅ Advanced search functionality
- ✅ Contact details and notes
- ✅ Profile pictures
- ✅ Custom fields support

### Search & Filter
- ✅ Full-text search
- ✅ Filter by category
- ✅ Filter by tags
- ✅ Advanced filtering options
- ✅ Search history
- ✅ Saved searches

### Data Management
- ✅ Import from CSV
- ✅ Export to CSV/JSON
- ✅ Bulk operations
- ✅ Data validation
- ✅ Duplicate detection
- ✅ Backup and restore

### API Features
- ✅ REST API endpoints
- ✅ JSON responses
- ✅ API authentication
- ✅ Rate limiting
- ✅ Documentation
- ✅ Error handling

## 📁 Project Structure

```
contact-manager/
├── README.md           # This file
├── index.php          # Main application
├── api/
│   ├── contacts.php   # API endpoints
│   └── auth.php       # API authentication
├── config/
│   ├── database.php   # Database configuration
│   └── config.php     # Application settings
├── classes/
│   ├── Contact.php    # Contact class
│   ├── Category.php   # Category class
│   ├── Search.php     # Search class
│   └── API.php        # API handler
├── admin/
│   ├── index.php      # Admin dashboard
│   ├── import.php     # Import functionality
│   └── export.php     # Export functionality
├── assets/
│   ├── css/
│   │   └── style.css  # Main stylesheet
│   ├── js/
│   │   ├── app.js     # Main JavaScript
│   │   └── search.js  # Search functionality
│   └── uploads/       # Profile pictures
└── database/
    └── setup.sql      # Database schema
```

## 🚀 Getting Started

### Prerequisites
- PHP 7.4 or higher
- MySQL/MariaDB database
- Web server (Apache, Nginx)
- GD extension for image processing

### Database Setup

1. **Create Database**
   ```sql
   CREATE DATABASE contact_manager;
   ```

2. **Import Schema**
   Run the SQL commands from `database/setup.sql`

### Configuration

1. **Database Configuration**
   Edit `config/database.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'contact_manager');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   ```

### Running the Application

1. **Navigate to project directory**
   ```bash
   cd php-learning-guide/08-projects/contact-manager
   ```

2. **Start PHP server**
   ```bash
   php -S localhost:8000
   ```

3. **Access the application**
   - Main site: `http://localhost:8000`
   - Admin panel: `http://localhost:8000/admin`
   - API: `http://localhost:8000/api`

## 📖 Database Schema

### Contacts Table
```sql
CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(50),
    company VARCHAR(255),
    job_title VARCHAR(255),
    address TEXT,
    birthday DATE,
    notes TEXT,
    profile_picture VARCHAR(255),
    category_id INT,
    tags JSON,
    custom_fields JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);
```

### Categories Table
```sql
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    color VARCHAR(7) DEFAULT '#007bff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Search History Table
```sql
CREATE TABLE search_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    query VARCHAR(255) NOT NULL,
    filters JSON,
    results_count INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## 🔧 Core Classes

### Contact Class
```php
<?php
class Contact {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        // Validate required fields
        if (empty($data['first_name']) || empty($data['last_name'])) {
            return ['success' => false, 'message' => 'First name and last name are required'];
        }
        
        // Validate email if provided
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }
        
        // Check for duplicates
        if ($this->isDuplicate($data)) {
            return ['success' => false, 'message' => 'Contact with this email or phone already exists'];
        }
        
        // Prepare data
        $contactData = [
            'first_name' => htmlspecialchars(trim($data['first_name'])),
            'last_name' => htmlspecialchars(trim($data['last_name'])),
            'email' => !empty($data['email']) ? htmlspecialchars(trim($data['email'])) : null,
            'phone' => !empty($data['phone']) ? htmlspecialchars(trim($data['phone'])) : null,
            'company' => !empty($data['company']) ? htmlspecialchars(trim($data['company'])) : null,
            'job_title' => !empty($data['job_title']) ? htmlspecialchars(trim($data['job_title'])) : null,
            'address' => !empty($data['address']) ? htmlspecialchars(trim($data['address'])) : null,
            'birthday' => !empty($data['birthday']) ? $data['birthday'] : null,
            'notes' => !empty($data['notes']) ? htmlspecialchars(trim($data['notes'])) : null,
            'category_id' => !empty($data['category_id']) ? (int)$data['category_id'] : null,
            'tags' => !empty($data['tags']) ? json_encode($data['tags']) : null,
            'custom_fields' => !empty($data['custom_fields']) ? json_encode($data['custom_fields']) : null
        ];
        
        try {
            $contactId = $this->db->insert('contacts', $contactData);
            return ['success' => true, 'contact_id' => $contactId];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to create contact'];
        }
    }
    
    public function getAll($limit = 50, $offset = 0, $categoryId = null) {
        $sql = "SELECT c.*, cat.name as category_name, cat.color as category_color
                FROM contacts c
                LEFT JOIN categories cat ON c.category_id = cat.id";
        
        $params = [];
        
        if ($categoryId) {
            $sql .= " WHERE c.category_id = ?";
            $params[] = $categoryId;
        }
        
        $sql .= " ORDER BY c.last_name, c.first_name LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $this->db->query($sql, $params);
        $contacts = $stmt->fetchAll();
        
        // Decode JSON fields
        foreach ($contacts as &$contact) {
            $contact['tags'] = json_decode($contact['tags'] ?? '[]', true);
            $contact['custom_fields'] = json_decode($contact['custom_fields'] ?? '{}', true);
        }
        
        return $contacts;
    }
    
    public function getById($id) {
        $sql = "SELECT c.*, cat.name as category_name, cat.color as category_color
                FROM contacts c
                LEFT JOIN categories cat ON c.category_id = cat.id
                WHERE c.id = ?";
        $stmt = $this->db->query($sql, [$id]);
        $contact = $stmt->fetch();
        
        if ($contact) {
            $contact['tags'] = json_decode($contact['tags'] ?? '[]', true);
            $contact['custom_fields'] = json_decode($contact['custom_fields'] ?? '{}', true);
        }
        
        return $contact;
    }
    
    public function update($id, $data) {
        // Validate email if provided
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }
        
        $allowedFields = [
            'first_name', 'last_name', 'email', 'phone', 'company', 
            'job_title', 'address', 'birthday', 'notes', 'category_id', 'tags', 'custom_fields'
        ];
        
        $updateData = [];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                if (in_array($field, ['tags', 'custom_fields'])) {
                    $updateData[$field] = json_encode($data[$field]);
                } elseif (in_array($field, ['first_name', 'last_name', 'email', 'phone', 'company', 'job_title', 'address', 'notes'])) {
                    $updateData[$field] = htmlspecialchars(trim($data[$field]));
                } else {
                    $updateData[$field] = $data[$field];
                }
            }
        }
        
        if (empty($updateData)) {
            return ['success' => false, 'message' => 'No valid fields to update'];
        }
        
        try {
            $this->db->update('contacts', $updateData, 'id = ?', [$id]);
            return ['success' => true, 'message' => 'Contact updated successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to update contact'];
        }
    }
    
    public function delete($id) {
        try {
            $this->db->delete('contacts', 'id = ?', [$id]);
            return ['success' => true, 'message' => 'Contact deleted successfully'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to delete contact'];
        }
    }
    
    public function search($query, $filters = []) {
        $searchTerm = "%$query%";
        $sql = "SELECT c.*, cat.name as category_name, cat.color as category_color
                FROM contacts c
                LEFT JOIN categories cat ON c.category_id = cat.id
                WHERE (c.first_name LIKE ? OR c.last_name LIKE ? OR c.email LIKE ? OR 
                       c.phone LIKE ? OR c.company LIKE ? OR c.notes LIKE ?)";
        
        $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm];
        
        // Apply filters
        if (!empty($filters['category_id'])) {
            $sql .= " AND c.category_id = ?";
            $params[] = $filters['category_id'];
        }
        
        if (!empty($filters['tags'])) {
            foreach ($filters['tags'] as $tag) {
                $sql .= " AND JSON_CONTAINS(c.tags, ?)";
                $params[] = json_encode($tag);
            }
        }
        
        $sql .= " ORDER BY c.last_name, c.first_name";
        
        $stmt = $this->db->query($sql, $params);
        $contacts = $stmt->fetchAll();
        
        // Decode JSON fields
        foreach ($contacts as &$contact) {
            $contact['tags'] = json_decode($contact['tags'] ?? '[]', true);
            $contact['custom_fields'] = json_decode($contact['custom_fields'] ?? '{}', true);
        }
        
        // Save search history
        $this->saveSearchHistory($query, $filters, count($contacts));
        
        return $contacts;
    }
    
    public function exportToCSV($contacts = null) {
        if ($contacts === null) {
            $contacts = $this->getAll(10000); // Get all contacts
        }
        
        $filename = 'contacts_export_' . date('Y-m-d') . '.csv';
        $filepath = 'exports/' . $filename;
        
        // Create exports directory if not exists
        if (!is_dir('exports')) {
            mkdir('exports', 0755, true);
        }
        
        $file = fopen($filepath, 'w');
        
        // CSV header
        fputcsv($file, ['First Name', 'Last Name', 'Email', 'Phone', 'Company', 'Job Title', 'Address', 'Birthday', 'Notes', 'Category', 'Tags']);
        
        // CSV data
        foreach ($contacts as $contact) {
            $tags = is_array($contact['tags']) ? implode(', ', $contact['tags']) : '';
            fputcsv($file, [
                $contact['first_name'],
                $contact['last_name'],
                $contact['email'] ?? '',
                $contact['phone'] ?? '',
                $contact['company'] ?? '',
                $contact['job_title'] ?? '',
                $contact['address'] ?? '',
                $contact['birthday'] ?? '',
                $contact['notes'] ?? '',
                $contact['category_name'] ?? '',
                $tags
            ]);
        }
        
        fclose($file);
        
        return ['success' => true, 'filename' => $filename, 'filepath' => $filepath];
    }
    
    public function importFromCSV($file) {
        $contacts = [];
        $errors = [];
        
        if (($handle = fopen($file, 'r')) !== false) {
            $header = fgetcsv($handle); // Skip header
            
            $rowNumber = 2; // Start from 2 (after header)
            
            while (($data = fgetcsv($handle)) !== false) {
                try {
                    $contactData = [
                        'first_name' => $data[0] ?? '',
                        'last_name' => $data[1] ?? '',
                        'email' => $data[2] ?? '',
                        'phone' => $data[3] ?? '',
                        'company' => $data[4] ?? '',
                        'job_title' => $data[5] ?? '',
                        'address' => $data[6] ?? '',
                        'birthday' => $data[7] ?? '',
                        'notes' => $data[8] ?? ''
                    ];
                    
                    $result = $this->create($contactData);
                    
                    if ($result['success']) {
                        $contacts[] = $result['contact_id'];
                    } else {
                        $errors[] = "Row $rowNumber: " . $result['message'];
                    }
                } catch (Exception $e) {
                    $errors[] = "Row $rowNumber: " . $e->getMessage();
                }
                
                $rowNumber++;
            }
            
            fclose($handle);
        }
        
        return [
            'success' => true,
            'imported' => count($contacts),
            'errors' => $errors
        ];
    }
    
    private function isDuplicate($data) {
        $sql = "SELECT id FROM contacts WHERE (email = ? OR phone = ?) AND (email IS NOT NULL OR phone IS NOT NULL)";
        $stmt = $this->db->query($sql, [$data['email'] ?? '', $data['phone'] ?? '']);
        return $stmt->fetch() !== false;
    }
    
    private function saveSearchHistory($query, $filters, $resultsCount) {
        $searchData = [
            'query' => $query,
            'filters' => json_encode($filters),
            'results_count' => $resultsCount
        ];
        
        $this->db->insert('search_history', $searchData);
    }
}
?>
```

### Search Class
```php
<?php
class Search {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function advancedSearch($params) {
        $sql = "SELECT c.*, cat.name as category_name, cat.color as category_color
                FROM contacts c
                LEFT JOIN categories cat ON c.category_id = cat.id
                WHERE 1=1";
        
        $queryParams = [];
        
        // Name search
        if (!empty($params['name'])) {
            $nameTerm = "%" . $params['name'] . "%";
            $sql .= " AND (c.first_name LIKE ? OR c.last_name LIKE ?)";
            $queryParams[] = $nameTerm;
            $queryParams[] = $nameTerm;
        }
        
        // Email search
        if (!empty($params['email'])) {
            $emailTerm = "%" . $params['email'] . "%";
            $sql .= " AND c.email LIKE ?";
            $queryParams[] = $emailTerm;
        }
        
        // Phone search
        if (!empty($params['phone'])) {
            $phoneTerm = "%" . $params['phone'] . "%";
            $sql .= " AND c.phone LIKE ?";
            $queryParams[] = $phoneTerm;
        }
        
        // Company search
        if (!empty($params['company'])) {
            $companyTerm = "%" . $params['company'] . "%";
            $sql .= " AND c.company LIKE ?";
            $queryParams[] = $companyTerm;
        }
        
        // Category filter
        if (!empty($params['category_id'])) {
            $sql .= " AND c.category_id = ?";
            $queryParams[] = $params['category_id'];
        }
        
        // Birthday filter
        if (!empty($params['birthday_from'])) {
            $sql .= " AND c.birthday >= ?";
            $queryParams[] = $params['birthday_from'];
        }
        
        if (!empty($params['birthday_to'])) {
            $sql .= " AND c.birthday <= ?";
            $queryParams[] = $params['birthday_to'];
        }
        
        // Tags filter
        if (!empty($params['tags'])) {
            foreach ($params['tags'] as $tag) {
                $sql .= " AND JSON_CONTAINS(c.tags, ?)";
                $queryParams[] = json_encode($tag);
            }
        }
        
        // Order and limit
        $orderBy = $params['order_by'] ?? 'last_name';
        $order = $params['order'] ?? 'ASC';
        $limit = $params['limit'] ?? 50;
        $offset = $params['offset'] ?? 0;
        
        $sql .= " ORDER BY c.$orderBy $order LIMIT ? OFFSET ?";
        $queryParams[] = $limit;
        $queryParams[] = $offset;
        
        $stmt = $this->db->query($sql, $queryParams);
        $contacts = $stmt->fetchAll();
        
        // Decode JSON fields
        foreach ($contacts as &$contact) {
            $contact['tags'] = json_decode($contact['tags'] ?? '[]', true);
            $contact['custom_fields'] = json_decode($contact['custom_fields'] ?? '{}', true);
        }
        
        return $contacts;
    }
    
    public function getSearchHistory($limit = 10) {
        $sql = "SELECT * FROM search_history ORDER BY created_at DESC LIMIT ?";
        $stmt = $this->db->query($sql, [$limit]);
        $history = $stmt->fetchAll();
        
        foreach ($history as &$item) {
            $item['filters'] = json_decode($item['filters'] ?? '{}', true);
        }
        
        return $history;
    }
    
    public function getPopularTags($limit = 20) {
        $sql = "SELECT JSON_UNQUOTE(JSON_EXTRACT(tags, '$[*]')) as tag, COUNT(*) as count
                FROM contacts
                WHERE tags IS NOT NULL AND tags != '[]'
                GROUP BY tag
                ORDER BY count DESC
                LIMIT ?";
        $stmt = $this->db->query($sql, [$limit]);
        return $stmt->fetchAll();
    }
}
?>
```

### API Class
```php
<?php
class API {
    private $db;
    private $contact;
    private $search;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->contact = new Contact();
        $this->search = new Search();
    }
    
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $pathParts = explode('/', trim($path, '/'));
        
        // Remove 'api' from path
        if ($pathParts[0] === 'api') {
            array_shift($pathParts);
        }
        
        $resource = $pathParts[0] ?? '';
        $id = $pathParts[1] ?? null;
        
        // Set response headers
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        
        if ($method === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
        
        try {
            switch ($resource) {
                case 'contacts':
                    $this->handleContacts($method, $id);
                    break;
                case 'search':
                    $this->handleSearch($method);
                    break;
                case 'categories':
                    $this->handleCategories($method);
                    break;
                case 'export':
                    $this->handleExport($method);
                    break;
                default:
                    $this->sendResponse(404, ['error' => 'Resource not found']);
            }
        } catch (Exception $e) {
            $this->sendResponse(500, ['error' => 'Internal server error']);
        }
    }
    
    private function handleContacts($method, $id) {
        switch ($method) {
            case 'GET':
                if ($id) {
                    $contact = $this->contact->getById($id);
                    if ($contact) {
                        $this->sendResponse(200, $contact);
                    } else {
                        $this->sendResponse(404, ['error' => 'Contact not found']);
                    }
                } else {
                    $contacts = $this->contact->getAll();
                    $this->sendResponse(200, $contacts);
                }
                break;
                
            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                $result = $this->contact->create($data);
                
                if ($result['success']) {
                    $contact = $this->contact->getById($result['contact_id']);
                    $this->sendResponse(201, $contact);
                } else {
                    $this->sendResponse(400, ['error' => $result['message']]);
                }
                break;
                
            case 'PUT':
                if (!$id) {
                    $this->sendResponse(400, ['error' => 'Contact ID required']);
                    return;
                }
                
                $data = json_decode(file_get_contents('php://input'), true);
                $result = $this->contact->update($id, $data);
                
                if ($result['success']) {
                    $contact = $this->contact->getById($id);
                    $this->sendResponse(200, $contact);
                } else {
                    $this->sendResponse(400, ['error' => $result['message']]);
                }
                break;
                
            case 'DELETE':
                if (!$id) {
                    $this->sendResponse(400, ['error' => 'Contact ID required']);
                    return;
                }
                
                $result = $this->contact->delete($id);
                
                if ($result['success']) {
                    $this->sendResponse(200, ['message' => 'Contact deleted successfully']);
                } else {
                    $this->sendResponse(400, ['error' => $result['message']]);
                }
                break;
                
            default:
                $this->sendResponse(405, ['error' => 'Method not allowed']);
        }
    }
    
    private function handleSearch($method) {
        if ($method !== 'GET') {
            $this->sendResponse(405, ['error' => 'Method not allowed']);
            return;
        }
        
        $query = $_GET['q'] ?? '';
        $filters = [];
        
        if (!empty($_GET['category_id'])) {
            $filters['category_id'] = (int)$_GET['category_id'];
        }
        
        if (!empty($_GET['tags'])) {
            $filters['tags'] = explode(',', $_GET['tags']);
        }
        
        $contacts = $this->contact->search($query, $filters);
        $this->sendResponse(200, $contacts);
    }
    
    private function handleCategories($method) {
        if ($method !== 'GET') {
            $this->sendResponse(405, ['error' => 'Method not allowed']);
            return;
        }
        
        $sql = "SELECT * FROM categories ORDER BY name";
        $stmt = $this->db->query($sql);
        $categories = $stmt->fetchAll();
        
        $this->sendResponse(200, $categories);
    }
    
    private function handleExport($method) {
        if ($method !== 'GET') {
            $this->sendResponse(405, ['error' => 'Method not allowed']);
            return;
        }
        
        $format = $_GET['format'] ?? 'csv';
        
        if ($format === 'csv') {
            $result = $this->contact->exportToCSV();
            
            if ($result['success']) {
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $result['filename'] . '"');
                readfile($result['filepath']);
                exit;
            } else {
                $this->sendResponse(500, ['error' => 'Export failed']);
            }
        } else {
            $this->sendResponse(400, ['error' => 'Unsupported format']);
        }
    }
    
    private function sendResponse($statusCode, $data) {
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }
}
?>
```

## 🎯 Challenges and Enhancements

### Easy Challenges
1. **Contact Groups**: Create groups for organizing contacts
2. **Custom Fields**: Add user-defined custom fields
3. **Contact Notes**: Add detailed notes system
4. **Birthday Reminders**: Birthday notification system

### Intermediate Challenges
1. **Email Integration**: Send emails to contacts
2. **Calendar Sync**: Sync with external calendars
3. **Advanced Search**: Full-text search with indexing
4. **Contact Relationships**: Link related contacts

### Advanced Challenges
1. **Real-time Sync**: Real-time contact synchronization
2. **Mobile API**: Dedicated mobile app API
3. **Machine Learning**: Contact deduplication AI
4. **Multi-tenant**: Multi-user contact management

## 🧪 Testing Your Application

### Manual Testing Checklist
- [ ] Contact CRUD operations
- [ ] Search functionality
- [ ] Import/Export features
- [ ] API endpoints
- [ ] Data validation
- [ ] Bulk operations
- [ ] Category management
- [ ] Tag system

### API Testing
- [ ] GET /api/contacts
- [ ] POST /api/contacts
- [ ] PUT /api/contacts/{id}
- [ ] DELETE /api/contacts/{id}
- [ ] GET /api/search
- [ ] GET /api/export

## 📚 What You've Learned

After completing this project, you've mastered:
- ✅ Advanced database operations
- ✅ Search implementation
- ✅ Data import/export
- ✅ REST API development
- ✅ JSON data handling
- ✅ File processing
- ✅ Complex queries
- ✅ Data validation
- ✅ Error handling

## 🚀 Next Steps

1. **Add Authentication**: User accounts and permissions
2. **Real-time Updates**: WebSocket integration
3. **Mobile App**: React Native or Flutter app
4. **Cloud Storage**: Cloud backup integration
5. **Advanced Search**: Elasticsearch integration

---

**Ready for the next project?** ➡️ [Weather App](../weather-app/README.md)
