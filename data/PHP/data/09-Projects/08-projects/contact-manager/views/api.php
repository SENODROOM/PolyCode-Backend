<?php
// API documentation and testing
$baseUrl = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
?>

<div class="card">
    <h1>Contact Manager API</h1>
    
    <p>RESTful API for managing contacts programmatically. All endpoints return JSON responses.</p>
    
    <h3>Base URL</h3>
    <code><?= $baseUrl ?>/api</code>
    
    <h3>Authentication</h3>
    <p>Currently no authentication required (for demo purposes). In production, implement API key or OAuth authentication.</p>
    
    <h3>Endpoints</h3>
    
    <div style="margin-top: 20px;">
        <h4>GET /api/contacts</h4>
        <p>Get all contacts or filter by parameters.</p>
        
        <strong>Parameters:</strong>
        <ul>
            <li><code>category_id</code> (optional) - Filter by category ID</li>
            <li><code>search</code> (optional) - Search query</li>
            <li><code>limit</code> (optional) - Number of results (default: 50)</li>
        </ul>
        
        <strong>Example:</strong>
        <code><?= $baseUrl ?>/api/contacts?search=john&limit=10</code>
        
        <div style="margin-top: 10px;">
            <button onclick="testAPI('GET', '/api/contacts')" class="btn btn-sm btn-secondary">Test This Endpoint</button>
        </div>
    </div>
    
    <div style="margin-top: 20px;">
        <h4>GET /api/contacts/{id}</h4>
        <p>Get a specific contact by ID.</p>
        
        <strong>Example:</strong>
        <code><?= $baseUrl ?>/api/contacts/1</code>
        
        <div style="margin-top: 10px;">
            <button onclick="testAPI('GET', '/api/contacts/1')" class="btn btn-sm btn-secondary">Test This Endpoint</button>
        </div>
    </div>
    
    <div style="margin-top: 20px;">
        <h4>POST /api/contacts</h4>
        <p>Create a new contact.</p>
        
        <strong>Request Body:</strong>
        <pre style="background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto;">
{
  "first_name": "John",
  "last_name": "Doe",
  "email": "john@example.com",
  "phone": "555-0101",
  "company": "Tech Corp",
  "job_title": "Software Engineer",
  "address": "123 Main St, City, State",
  "birthday": "1990-05-15",
  "notes": "Software developer",
  "category_id": 3,
  "tags": ["developer", "php", "javascript"]
}</pre>
        
        <div style="margin-top: 10px;">
            <button onclick="testAPI('POST', '/api/contacts', sampleContact)" class="btn btn-sm btn-secondary">Test This Endpoint</button>
        </div>
    </div>
    
    <div style="margin-top: 20px;">
        <h4>PUT /api/contacts/{id}</h4>
        <p>Update an existing contact.</p>
        
        <strong>Request Body:</strong>
        <pre style="background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto;">
{
  "first_name": "John Updated",
  "email": "john.updated@example.com",
  "phone": "555-0102"
}</pre>
        
        <div style="margin-top: 10px;">
            <button onclick="testAPI('PUT', '/api/contacts/1', sampleUpdate)" class="btn btn-sm btn-secondary">Test This Endpoint</button>
        </div>
    </div>
    
    <div style="margin-top: 20px;">
        <h4>DELETE /api/contacts/{id}</h4>
        <p>Delete a contact.</p>
        
        <strong>Example:</strong>
        <code><?= $baseUrl ?>/api/contacts/1</code>
        
        <div style="margin-top: 10px;">
            <button onclick="testAPI('DELETE', '/api/contacts/999')" class="btn btn-sm btn-secondary">Test This Endpoint (Safe ID)</button>
        </div>
    </div>
    
    <div style="margin-top: 20px;">
        <h4>GET /api/categories</h4>
        <p>Get all categories.</p>
        
        <strong>Example:</strong>
        <code><?= $baseUrl ?>/api/categories</code>
        
        <div style="margin-top: 10px;">
            <button onclick="testAPI('GET', '/api/categories')" class="btn btn-sm btn-secondary">Test This Endpoint</button>
        </div>
    </div>
</div>

<div class="card">
    <h3>Response Format</h3>
    
    <h4>Success Response</h4>
    <pre style="background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto;">
{
  "success": true,
  "data": {
    "id": 1,
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@example.com",
    ...
  },
  "message": "Contact retrieved successfully"
}</pre>
    
    <h4>Error Response</h4>
    <pre style="background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto;">
{
  "success": false,
  "error": "Contact not found",
  "message": "The requested contact does not exist"
}</pre>
</div>

<div class="card">
    <h3>API Tester</h3>
    <p>Use the buttons above to test API endpoints, or use the form below for custom requests:</p>
    
    <div style="margin-top: 15px;">
        <div class="form-group">
            <label for="apiMethod">Method:</label>
            <select id="apiMethod">
                <option value="GET">GET</option>
                <option value="POST">POST</option>
                <option value="PUT">PUT</option>
                <option value="DELETE">DELETE</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="apiEndpoint">Endpoint:</label>
            <input type="text" id="apiEndpoint" placeholder="/api/contacts" value="/api/contacts">
        </div>
        
        <div class="form-group">
            <label for="apiBody">Request Body (JSON):</label>
            <textarea id="apiBody" rows="6" placeholder='{"first_name": "John", "last_name": "Doe"}'></textarea>
        </div>
        
        <button onclick="customAPITest()" class="btn btn-success">Send Request</button>
    </div>
    
    <div id="apiResponse" style="margin-top: 20px; display: none;">
        <h4>Response:</h4>
        <pre id="apiResponseContent" style="background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; max-height: 400px;"></pre>
    </div>
</div>

<script>
const sampleContact = {
  first_name: "John",
  last_name: "Doe",
  email: "john@example.com",
  phone: "555-0101",
  company: "Tech Corp",
  job_title: "Software Engineer",
  notes: "API test contact",
  category_id: 3,
  tags: ["api", "test"]
};

const sampleUpdate = {
  first_name: "John Updated",
  email: "john.updated@example.com"
};

function testAPI(method, endpoint, body = null) {
    const url = '<?= $baseUrl ?>' + endpoint;
    const options = {
        method: method,
        headers: {
            'Content-Type': 'application/json',
        }
    };
    
    if (body && (method === 'POST' || method === 'PUT')) {
        options.body = JSON.stringify(body);
    }
    
    fetch(url, options)
        .then(response => response.json())
        .then(data => {
            showAPIResponse(data);
        })
        .catch(error => {
            showAPIResponse({error: error.message});
        });
}

function customAPITest() {
    const method = document.getElementById('apiMethod').value;
    const endpoint = document.getElementById('apiEndpoint').value;
    const bodyText = document.getElementById('apiBody').value;
    
    let body = null;
    if (bodyText.trim()) {
        try {
            body = JSON.parse(bodyText);
        } catch (e) {
            showAPIResponse({error: 'Invalid JSON in request body'});
            return;
        }
    }
    
    testAPI(method, endpoint, body);
}

function showAPIResponse(data) {
    const responseDiv = document.getElementById('apiResponse');
    const responseContent = document.getElementById('apiResponseContent');
    
    responseContent.textContent = JSON.stringify(data, null, 2);
    responseDiv.style.display = 'block';
    
    // Scroll to response
    responseDiv.scrollIntoView({ behavior: 'smooth' });
}
</script>

<div class="card">
    <h3>Usage Examples</h3>
    
    <h4>JavaScript (Fetch API)</h4>
    <pre style="background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto;">
// Get all contacts
fetch('<?= $baseUrl ?>/api/contacts')
  .then(response => response.json())
  .then(data => console.log(data));

// Create a new contact
fetch('<?= $baseUrl ?>/api/contacts', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    first_name: 'John',
    last_name: 'Doe',
    email: 'john@example.com'
  })
})
.then(response => response.json())
.then(data => console.log(data));</pre>
    
    <h4>PHP (cURL)</h4>
    <pre style="background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto;">
&lt;?php
// Get all contacts
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, '<?= $baseUrl ?>/api/contacts');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$data = json_decode($response, true);
curl_close($ch);

// Create a new contact
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, '<?= $baseUrl ?>/api/contacts');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'first_name' => 'John',
    'last_name' => 'Doe',
    'email' => 'john@example.com'
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);
$data = json_decode($response, true);
curl_close($ch);
?&gt;</pre>
    
    <div style="margin-top: 20px;">
        <a href="?action=home" class="btn btn-secondary">Back to Contacts</a>
    </div>
</div>
