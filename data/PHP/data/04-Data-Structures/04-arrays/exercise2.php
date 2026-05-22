<?php
    // Exercise 2: Contact List
    
    echo "<h2>Contact List Management</h2>";
    
    // Initial contact list
    $contacts = [
        [
            "id" => 1,
            "name" => "John Doe",
            "email" => "john@example.com",
            "phone" => "123-456-7890",
            "category" => "friend"
        ],
        [
            "id" => 2,
            "name" => "Jane Smith",
            "email" => "jane@example.com",
            "phone" => "234-567-8901",
            "category" => "work"
        ],
        [
            "id" => 3,
            "name" => "Bob Johnson",
            "email" => "bob@example.com",
            "phone" => "345-678-9012",
            "category" => "family"
        ]
    ];
    
    // Function to add a contact
    function addContact(&$contacts, $name, $email, $phone, $category) {
        $newId = max(array_column($contacts, 'id')) + 1;
        $contacts[] = [
            "id" => $newId,
            "name" => $name,
            "email" => $email,
            "phone" => $phone,
            "category" => $category
        ];
        return $newId;
    }
    
    // Function to remove a contact
    function removeContact(&$contacts, $id) {
        $contacts = array_filter($contacts, function($contact) use ($id) {
            return $contact['id'] !== $id;
        });
        $contacts = array_values($contacts); // Re-index array
    }
    
    // Function to search contacts
    function searchContacts($contacts, $searchTerm) {
        $results = [];
        foreach ($contacts as $contact) {
            if (stripos($contact['name'], $searchTerm) !== false ||
                stripos($contact['email'], $searchTerm) !== false ||
                stripos($contact['phone'], $searchTerm) !== false) {
                $results[] = $contact;
            }
        }
        return $results;
    }
    
    // Function to display contacts in different formats
    function displayContacts($contacts, $format = 'table') {
        if (empty($contacts)) {
            echo "<p>No contacts found.</p>";
            return;
        }
        
        switch ($format) {
            case 'table':
                echo "<table border='1' cellpadding='5'>";
                echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Category</th></tr>";
                foreach ($contacts as $contact) {
                    echo "<tr>";
                    echo "<td>{$contact['id']}</td>";
                    echo "<td>{$contact['name']}</td>";
                    echo "<td>{$contact['email']}</td>";
                    echo "<td>{$contact['phone']}</td>";
                    echo "<td>{$contact['category']}</td>";
                    echo "</tr>";
                }
                echo "</table>";
                break;
                
            case 'cards':
                foreach ($contacts as $contact) {
                    echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px; width: 300px;'>";
                    echo "<strong>{$contact['name']}</strong><br>";
                    echo "Email: {$contact['email']}<br>";
                    echo "Phone: {$contact['phone']}<br>";
                    echo "Category: {$contact['category']}<br>";
                    echo "</div>";
                }
                break;
                
            case 'list':
                echo "<ul>";
                foreach ($contacts as $contact) {
                    echo "<li><strong>{$contact['name']}</strong> - {$contact['email']} ({$contact['category']})</li>";
                }
                echo "</ul>";
                break;
        }
    }
    
    // Function to get contacts by category
    function getContactsByCategory($contacts, $category) {
        return array_filter($contacts, function($contact) use ($category) {
            return $contact['category'] === $category;
        });
    }
    
    // Function to get contact statistics
    function getContactStats($contacts) {
        $categories = [];
        foreach ($contacts as $contact) {
            if (!isset($categories[$contact['category']])) {
                $categories[$contact['category']] = 0;
            }
            $categories[$contact['category']]++;
        }
        
        return [
            'total' => count($contacts),
            'categories' => $categories
        ];
    }
    
    // Demonstrate the functions
    echo "<h3>Initial Contact List:</h3>";
    displayContacts($contacts, 'table');
    
    echo "<h3>Add New Contacts:</h3>";
    $newId1 = addContact($contacts, "Alice Wilson", "alice@example.com", "456-789-0123", "friend");
    $newId2 = addContact($contacts, "Charlie Brown", "charlie@example.com", "567-890-1234", "work");
    echo "Added contacts with IDs: $newId1 and $newId2<br><br>";
    
    echo "<h3>Updated Contact List:</h3>";
    displayContacts($contacts, 'cards');
    
    echo "<h3>Search Contacts:</h3>";
    echo "Searching for 'john':<br>";
    $searchResults = searchContacts($contacts, 'john');
    displayContacts($searchResults, 'list');
    
    echo "<br>Searching for 'example.com':<br>";
    $searchResults = searchContacts($contacts, 'example.com');
    echo "Found " . count($searchResults) . " contacts<br>";
    
    echo "<h3>Contacts by Category:</h3>";
    echo "<strong>Work Contacts:</strong><br>";
    $workContacts = getContactsByCategory($contacts, 'work');
    displayContacts($workContacts, 'list');
    
    echo "<br><strong>Friend Contacts:</strong><br>";
    $friendContacts = getContactsByCategory($contacts, 'friend');
    displayContacts($friendContacts, 'list');
    
    echo "<h3>Contact Statistics:</h3>";
    $stats = getContactStats($contacts);
    echo "Total contacts: " . $stats['total'] . "<br>";
    echo "Contacts by category:<br>";
    foreach ($stats['categories'] as $category => $count) {
        echo "- $category: $count<br>";
    }
    
    echo "<h3>Remove Contact:</h3>";
    echo "Removing contact with ID 2 (Jane Smith)...<br>";
    removeContact($contacts, 2);
    
    echo "<h3>Final Contact List:</h3>";
    displayContacts($contacts, 'table');
    
    echo "<h3>Final Statistics:</h3>";
    $finalStats = getContactStats($contacts);
    echo "Total contacts: " . $finalStats['total'] . "<br>";
    echo "Contacts by category:<br>";
    foreach ($finalStats['categories'] as $category => $count) {
        echo "- $category: $count<br>";
    }
?>
