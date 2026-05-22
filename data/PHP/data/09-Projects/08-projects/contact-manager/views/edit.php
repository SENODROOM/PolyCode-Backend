<?php
// Edit contact page view
$contactId = $_GET['id'] ?? 0;
$contactData = $contact->getById($contactId);
$categories = $category->getAll();

if (!$contactData) {
    echo '<div class="card"><p>Contact not found.</p></div>';
    return;
}
?>

<div class="card">
    <h1>Edit Contact</h1>
    
    <form method="post">
        <input type="hidden" name="form_type" value="edit_contact">
        <input type="hidden" name="contact_id" value="<?= $contactData['id'] ?>">
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <div class="form-group">
                    <label for="first_name">First Name *</label>
                    <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($contactData['first_name']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="last_name">Last Name *</label>
                    <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($contactData['last_name']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($contactData['email'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($contactData['phone'] ?? '') ?>">
                </div>
            </div>
            
            <div>
                <div class="form-group">
                    <label for="company">Company</label>
                    <input type="text" id="company" name="company" value="<?= htmlspecialchars($contactData['company'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="job_title">Job Title</label>
                    <input type="text" id="job_title" name="job_title" value="<?= htmlspecialchars($contactData['job_title'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="birthday">Birthday</label>
                    <input type="date" id="birthday" name="birthday" value="<?= htmlspecialchars($contactData['birthday'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id">
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $contactData['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label for="address">Address</label>
            <textarea id="address" name="address"><?= htmlspecialchars($contactData['address'] ?? '') ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea id="notes" name="notes"><?= htmlspecialchars($contactData['notes'] ?? '') ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="tags">Tags (comma-separated)</label>
            <input type="text" id="tags" name="tags" value="<?= htmlspecialchars(is_array($contactData['tags']) ? implode(', ', $contactData['tags']) : '') ?>">
            <small>Separate multiple tags with commas</small>
        </div>
        
        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-success">Update Contact</button>
            <a href="?action=view&id=<?= $contactData['id'] ?>" class="btn btn-secondary">View Contact</a>
            <a href="?action=home" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<div class="card">
    <h3>Contact Information</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 15px;">
        <div>
            <strong>Contact ID:</strong><br>
            <?= $contactData['id'] ?>
        </div>
        <div>
            <strong>Created:</strong><br>
            <?= date('M j, Y H:i', strtotime($contactData['created_at'])) ?>
        </div>
        <div>
            <strong>Last Updated:</strong><br>
            <?= date('M j, Y H:i', strtotime($contactData['updated_at'])) ?>
        </div>
        <?php if ($contactData['category_name']): ?>
            <div>
                <strong>Current Category:</strong><br>
                <span style="background-color: <?= $contactData['category_color'] ?>; color: white; padding: 2px 8px; border-radius: 12px;">
                    <?= htmlspecialchars($contactData['category_name']) ?>
                </span>
            </div>
        <?php endif; ?>
    </div>
</div>
