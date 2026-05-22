<?php
// Create post page view
$user = getCurrentUser();
?>

<div class="card">
    <h1>Create New Post</h1>
    
    <form method="post">
        <input type="hidden" name="form_type" value="create_post">
        
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>
        </div>
        
        <div class="form-group">
            <label for="content">Content:</label>
            <textarea id="content" name="content" required placeholder="Write your post content here..."></textarea>
        </div>
        
        <div class="form-group">
            <label for="status">Status:</label>
            <select id="status" name="status">
                <option value="draft">Draft</option>
                <option value="published">Published</option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-success">Create Post</button>
        <a href="?action=home" class="btn" style="margin-left: 10px;">Cancel</a>
    </form>
</div>

<div class="card" style="margin-top: 20px;">
    <h3>Writing Tips</h3>
    <ul>
        <li>Write a compelling title that grabs attention</li>
        <li>Structure your content with clear paragraphs</li>
        <li>Use headings and lists to improve readability</li>
        <li>Proofread before publishing</li>
        <li>Consider your audience when writing</li>
    </ul>
</div>
