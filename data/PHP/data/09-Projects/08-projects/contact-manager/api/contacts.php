<?php
// Contacts API Endpoint

// Include main application
require_once '../index.php';

// Set response headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get request method and path
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$pathParts = explode('/', trim($path, '/'));

// Remove 'api' from path
if ($pathParts[0] === 'api') {
    array_shift($pathParts);
}

$resource = $pathParts[0] ?? '';
$id = $pathParts[1] ?? null;

try {
    switch ($method) {
        case 'GET':
            if ($id) {
                // Get single contact
                $contactData = $contact->getById($id);
                
                if ($contactData) {
                    echo json_encode([
                        'success' => true,
                        'data' => $contactData,
                        'message' => 'Contact retrieved successfully'
                    ]);
                } else {
                    http_response_code(404);
                    echo json_encode([
                        'success' => false,
                        'error' => 'Contact not found',
                        'message' => 'The requested contact does not exist'
                    ]);
                }
            } else {
                // Get all contacts with optional filters
                $categoryId = $_GET['category_id'] ?? null;
                $search = $_GET['search'] ?? '';
                $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
                
                if ($search) {
                    $contacts = $contact->search($search, ['category_id' => $categoryId]);
                } else {
                    $contacts = $contact->getAll($limit, $categoryId);
                }
                
                echo json_encode([
                    'success' => true,
                    'data' => $contacts,
                    'count' => count($contacts),
                    'message' => 'Contacts retrieved successfully'
                ]);
            }
            break;
            
        case 'POST':
            // Create new contact
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'Invalid JSON',
                    'message' => 'Request body must contain valid JSON'
                ]);
                break;
            }
            
            // Process tags if provided
            if (isset($input['tags']) && is_string($input['tags'])) {
                $input['tags'] = array_map('trim', explode(',', $input['tags']));
            }
            
            $result = $contact->create($input);
            
            if ($result['success']) {
                $newContact = $contact->getById($result['contact_id']);
                http_response_code(201);
                echo json_encode([
                    'success' => true,
                    'data' => $newContact,
                    'message' => 'Contact created successfully'
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'Validation error',
                    'message' => $result['message']
                ]);
            }
            break;
            
        case 'PUT':
            // Update existing contact
            if (!$id) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'Contact ID required',
                    'message' => 'Contact ID must be provided for updates'
                ]);
                break;
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'Invalid JSON',
                    'message' => 'Request body must contain valid JSON'
                ]);
                break;
            }
            
            // Process tags if provided
            if (isset($input['tags']) && is_string($input['tags'])) {
                $input['tags'] = array_map('trim', explode(',', $input['tags']));
            }
            
            $result = $contact->update($id, $input);
            
            if ($result['success']) {
                $updatedContact = $contact->getById($id);
                echo json_encode([
                    'success' => true,
                    'data' => $updatedContact,
                    'message' => 'Contact updated successfully'
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'Update failed',
                    'message' => $result['message']
                ]);
            }
            break;
            
        case 'DELETE':
            // Delete contact
            if (!$id) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'Contact ID required',
                    'message' => 'Contact ID must be provided for deletion'
                ]);
                break;
            }
            
            $result = $contact->delete($id);
            
            if ($result['success']) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Contact deleted successfully'
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'Delete failed',
                    'message' => $result['message']
                ]);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'error' => 'Method not allowed',
                'message' => 'HTTP method ' . $method . ' is not supported'
            ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Internal server error',
        'message' => $e->getMessage()
    ]);
}
?>
