<?php
// Edit post page view
$blog = new BlogPost();
$postId = $_GET['id'] ?? 0;
$post = $blog->getById($postId);

if (!$post) {
    echo '<div class="card"><p>Post not found.</p></div>';
    return;
}

// Check if user can edit this post
if ($_SESSION['user_id'] != $post['author_id'] && $_SESSION['role'] !== 'admin') {
    echo '<div class="card"><p>You don\'t have permission to edit this post.</p></div>';
    return;
}
?>

<div class="card">
    <h1>Edit Post</h1>
    
    <form method="post">
        <input type="hidden" name="form_type" value="edit_post">
        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
        
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($post['title']) ?>" required>
        </div>
        
        <div class="form-group">
            <label for="content">Content:</label>
            <textarea id="content" name="content" required><?= htmlspecialchars($post['content']) ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="status">Status:</label>
            <select id="status" name="status">
                <option value="draft" <?= $post['status'] == 'draft' ? 'selected' : '' ?>>Draft</option>
                <option value="published" <?= $post['status'] == 'published' ? 'selected' : '' ?>>Published</option>
            </select>
        </div>
        
        <div style="margin-bottom: 15px;">
            <small>
                <strong>Created:</strong> <?= date('M j, Y H:i', strtotime($post['created_at'])) ?><br>
                <?php if ($post['updated_at'] != $post['created_at']): ?>
                    <strong>Last Updated:</strong> <?= date('M j, Y H:i', strtotime($post['updated_at'])) ?>
                <?php endif; ?>
            </small>
        </div>
        
        <button type="submit" class="btn btn-success">Update Post</button>
        <a href="?action=home" class="btn" style="margin-left: 10px;">Cancel</a>
    </form>
</div>
