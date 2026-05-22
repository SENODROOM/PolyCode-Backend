<?php
// Home page view
$categories = $category->getAll();
$categoryId = $_GET['category'] ?? null;
$search = $_GET['search'] ?? '';

if ($search) {
    $contacts = $contact->search($search, ['category_id' => $categoryId]);
    $pageTitle = "Search Results: " . htmlspecialchars($search);
} elseif ($categoryId) {
    $contacts = $contact->getAll(100, $categoryId);
    $category = $category->getById($categoryId);
    $pageTitle = "Category: " . htmlspecialchars($category['name']);
} else {
    $contacts = $contact->getAll();
    $pageTitle = "All Contacts";
}
?>

<div class="card">
    <h1><?= $pageTitle ?></h1>
    
    <!-- Search Form -->
    <form method="get" class="search-form">
        <input type="hidden" name="action" value="home">
        <input type="text" name="search" placeholder="Search contacts..." value="<?= htmlspecialchars($search) ?>">
        <select name="category">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $categoryId == $cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn">Search</button>
    </form>
    
    <!-- Category Filter -->
    <div style="margin-bottom: 20px;">
        <strong>Filter by Category:</strong>
        <div style="margin-top: 10px;">
            <a href="?action=home" class="btn btn-sm <?= !$categoryId ? 'btn-primary' : 'btn-secondary' ?>">All</a>
            <?php foreach ($categories as $cat): ?>
                <a href="?action=home&category=<?= $cat['id'] ?>" class="btn btn-sm <?= $categoryId == $cat['id'] ? 'btn-primary' : 'btn-secondary' ?>">
                    <?= htmlspecialchars($cat['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="card">
    <?php if (empty($contacts)): ?>
        <p>No contacts found.</p>
        <a href="?action=add" class="btn btn-success">Add Your First Contact</a>
    <?php else: ?>
        <p>Found <?= count($contacts) ?> contacts</p>
        
        <div class="contact-grid">
            <?php foreach ($contacts as $contact): ?>
                <div class="contact-card">
                    <div class="contact-name">
                        <?= htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']) ?>
                    </div>
                    
                    <?php if ($contact['email']): ?>
                        <div class="contact-info">📧 <?= htmlspecialchars($contact['email']) ?></div>
                    <?php endif; ?>
                    
                    <?php if ($contact['phone']): ?>
                        <div class="contact-info">📱 <?= htmlspecialchars($contact['phone']) ?></div>
                    <?php endif; ?>
                    
                    <?php if ($contact['company']): ?>
                        <div class="contact-info">🏢 <?= htmlspecialchars($contact['company']) ?></div>
                    <?php endif; ?>
                    
                    <?php if ($contact['job_title']): ?>
                        <div class="contact-info">💼 <?= htmlspecialchars($contact['job_title']) ?></div>
                    <?php endif; ?>
                    
                    <?php if ($contact['category_name']): ?>
                        <div class="contact-category" style="background-color: <?= $contact['category_color'] ?>; color: white;">
                            <?= htmlspecialchars($contact['category_name']) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($contact['tags'])): ?>
                        <div class="contact-tags">
                            <?php foreach ($contact['tags'] as $tag): ?>
                                <span class="tag"><?= htmlspecialchars($tag) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="contact-actions">
                        <a href="?action=view&id=<?= $contact['id'] ?>" class="btn btn-sm">View</a>
                        <a href="?action=edit&id=<?= $contact['id'] ?>" class="btn btn-sm btn-secondary">Edit</a>
                        
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="form_type" value="delete_contact">
                            <input type="hidden" name="contact_id" value="<?= $contact['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div class="card">
    <h3>Quick Stats</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 15px;">
        <div style="text-align: center;">
            <div style="font-size: 2rem; font-weight: bold; color: #007bff;"><?= count($contact->getAll()) ?></div>
            <div>Total Contacts</div>
        </div>
        <div style="text-align: center;">
            <div style="font-size: 2rem; font-weight: bold; color: #28a745;"><?= count($categories) ?></div>
            <div>Categories</div>
        </div>
        <div style="text-align: center;">
            <div style="font-size: 2rem; font-weight: bold; color: #ffc107;"><?= count($contact->search('', [])) ?></div>
            <div>Searchable Fields</div>
        </div>
    </div>
</div>
