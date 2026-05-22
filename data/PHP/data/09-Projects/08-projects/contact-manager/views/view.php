<?php
// View contact page
$contactId = $_GET['id'] ?? 0;
$contactData = $contact->getById($contactId);

if (!$contactData) {
    echo '<div class="card"><p>Contact not found.</p></div>';
    return;
}
?>

<div class="card">
    <h1><?= htmlspecialchars($contactData['first_name'] . ' ' . $contactData['last_name']) ?></h1>
    
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
        <div>
            <!-- Contact Information -->
            <h3>Contact Information</h3>
            
            <?php if ($contactData['email']): ?>
                <div style="margin-bottom: 15px;">
                    <strong>Email:</strong> 
                    <a href="mailto:<?= htmlspecialchars($contactData['email']) ?>" style="color: #007bff;">
                        <?= htmlspecialchars($contactData['email']) ?>
                    </a>
                </div>
            <?php endif; ?>
            
            <?php if ($contactData['phone']): ?>
                <div style="margin-bottom: 15px;">
                    <strong>Phone:</strong> 
                    <a href="tel:<?= htmlspecialchars($contactData['phone']) ?>" style="color: #007bff;">
                        <?= htmlspecialchars($contactData['phone']) ?>
                    </a>
                </div>
            <?php endif; ?>
            
            <?php if ($contactData['company']): ?>
                <div style="margin-bottom: 15px;">
                    <strong>Company:</strong> <?= htmlspecialchars($contactData['company']) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($contactData['job_title']): ?>
                <div style="margin-bottom: 15px;">
                    <strong>Job Title:</strong> <?= htmlspecialchars($contactData['job_title']) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($contactData['address']): ?>
                <div style="margin-bottom: 15px;">
                    <strong>Address:</strong><br>
                    <?= nl2br(htmlspecialchars($contactData['address'])) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($contactData['birthday']): ?>
                <div style="margin-bottom: 15px;">
                    <strong>Birthday:</strong> <?= date('F j, Y', strtotime($contactData['birthday'])) ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($contactData['notes'])): ?>
                <div style="margin-bottom: 15px;">
                    <strong>Notes:</strong><br>
                    <?= nl2br(htmlspecialchars($contactData['notes'])) ?>
                </div>
            <?php endif; ?>
            
            <!-- Tags -->
            <?php if (!empty($contactData['tags'])): ?>
                <div style="margin-bottom: 15px;">
                    <strong>Tags:</strong><br>
                    <div style="margin-top: 5px;">
                        <?php foreach ($contactData['tags'] as $tag): ?>
                            <span class="tag"><?= htmlspecialchars($tag) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Category -->
            <?php if ($contactData['category_name']): ?>
                <div style="margin-bottom: 15px;">
                    <strong>Category:</strong> 
                    <span class="contact-category" style="background-color: <?= $contactData['category_color'] ?>; color: white;">
                        <?= htmlspecialchars($contactData['category_name']) ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>
        
        <div>
            <!-- Actions -->
            <div class="card" style="margin-bottom: 20px;">
                <h3>Actions</h3>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="?action=edit&id=<?= $contactData['id'] ?>" class="btn btn-secondary">Edit Contact</a>
                    
                    <form method="post">
                        <input type="hidden" name="form_type" value="delete_contact">
                        <input type="hidden" name="contact_id" value="<?= $contactData['id'] ?>">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this contact?')">Delete Contact</button>
                    </form>
                    
                    <a href="?action=home" class="btn btn-secondary">Back to Contacts</a>
                </div>
            </div>
            
            <!-- Quick Contact -->
            <div class="card">
                <h3>Quick Contact</h3>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <?php if ($contactData['email']): ?>
                        <a href="mailto:<?= htmlspecialchars($contactData['email']) ?>" class="btn" style="text-align: center;">
                            📧 Send Email
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($contactData['phone']): ?>
                        <a href="tel:<?= htmlspecialchars($contactData['phone']) ?>" class="btn" style="text-align: center;">
                            📱 Call Phone
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($contactData['address']): ?>
                        <a href="https://maps.google.com/?q=<?= urlencode($contactData['address']) ?>" target="_blank" class="btn" style="text-align: center;">
                            🗺️ View on Map
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <h3>Contact Details</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 15px;">
        <div>
            <strong>Contact ID:</strong><br>
            <?= $contactData['id'] ?>
        </div>
        <div>
            <strong>Created:</strong><br>
            <?= date('M j, Y H:i:s', strtotime($contactData['created_at'])) ?>
        </div>
        <div>
            <strong>Last Updated:</strong><br>
            <?= date('M j, Y H:i:s', strtotime($contactData['updated_at'])) ?>
        </div>
        <?php if ($contactData['category_name']): ?>
            <div>
                <strong>Category:</strong><br>
                <span style="background-color: <?= $contactData['category_color'] ?>; color: white; padding: 2px 8px; border-radius: 12px;">
                    <?= htmlspecialchars($contactData['category_name']) ?>
                </span>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <h3>Similar Contacts</h3>
    <p>Contacts with similar information:</p>
    
    <?php
    // Find similar contacts (same company or category)
    $similarContacts = [];
    
    if ($contactData['company']) {
        $companyContacts = $contact->getAll(100);
        foreach ($companyContacts as $c) {
            if ($c['id'] != $contactData['id'] && 
                strtolower($c['company']) == strtolower($contactData['company'])) {
                $similarContacts[] = $c;
            }
        }
    }
    
    if (empty($similarContacts) && $contactData['category_id']) {
        $categoryContacts = $contact->getAll(100, $contactData['category_id']);
        foreach ($categoryContacts as $c) {
            if ($c['id'] != $contactData['id']) {
                $similarContacts[] = $c;
            }
        }
    }
    
    // Limit to 3 similar contacts
    $similarContacts = array_slice($similarContacts, 0, 3);
    ?>
    
    <?php if (!empty($similarContacts)): ?>
        <div class="contact-grid" style="margin-top: 15px;">
            <?php foreach ($similarContacts as $similar): ?>
                <div class="contact-card">
                    <div class="contact-name">
                        <?= htmlspecialchars($similar['first_name'] . ' ' . $similar['last_name']) ?>
                    </div>
                    
                    <?php if ($similar['company']): ?>
                        <div class="contact-info">🏢 <?= htmlspecialchars($similar['company']) ?></div>
                    <?php endif; ?>
                    
                    <?php if ($similar['email']): ?>
                        <div class="contact-info">📧 <?= htmlspecialchars($similar['email']) ?></div>
                    <?php endif; ?>
                    
                    <div class="contact-actions">
                        <a href="?action=view&id=<?= $similar['id'] ?>" class="btn btn-sm">View</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No similar contacts found.</p>
    <?php endif; ?>
</div>
