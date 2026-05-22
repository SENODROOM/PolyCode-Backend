<?php
// Add contact page view
$categories = $category->getAll();
?>

<div class="card">
    <h1>Add New Contact</h1>
    
    <form method="post">
        <input type="hidden" name="form_type" value="add_contact">
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <div class="form-group">
                    <label for="first_name">First Name *</label>
                    <input type="text" id="first_name" name="first_name" required>
                </div>
                
                <div class="form-group">
                    <label for="last_name">Last Name *</label>
                    <input type="text" id="last_name" name="last_name" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email">
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone">
                </div>
            </div>
            
            <div>
                <div class="form-group">
                    <label for="company">Company</label>
                    <input type="text" id="company" name="company">
                </div>
                
                <div class="form-group">
                    <label for="job_title">Job Title</label>
                    <input type="text" id="job_title" name="job_title">
                </div>
                
                <div class="form-group">
                    <label for="birthday">Birthday</label>
                    <input type="date" id="birthday" name="birthday">
                </div>
                
                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id">
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label for="address">Address</label>
            <textarea id="address" name="address" placeholder="Enter full address"></textarea>
        </div>
        
        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea id="notes" name="notes" placeholder="Additional notes about this contact"></textarea>
        </div>
        
        <div class="form-group">
            <label for="tags">Tags (comma-separated)</label>
            <input type="text" id="tags" name="tags" placeholder="e.g., friend, colleague, developer">
            <small>Separate multiple tags with commas</small>
        </div>
        
        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-success">Add Contact</button>
            <a href="?action=home" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<div class="card">
    <h3>Tips for Adding Contacts</h3>
    <ul>
        <li>First name and last name are required fields</li>
        <li>Email and phone numbers help identify duplicate contacts</li>
        <li>Use categories to organize your contacts effectively</li>
        <li>Tags help with searching and filtering contacts</li>
        <li>Notes are great for remembering important details</li>
    </ul>
</div>
