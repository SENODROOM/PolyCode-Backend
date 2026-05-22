<?php
// View single post page
$blog = new BlogPost();
$postId = $_GET['id'] ?? 0;
$post = $blog->getById($postId);

if (!$post) {
    echo '<div class="card"><p>Post not found.</p></div>';
    return;
}
?>

<div class="card">
    <h1><?= htmlspecialchars($post['title']) ?></h1>
    
    <div class="post-meta">
        By <?= htmlspecialchars($post['author_name']) ?> • 
        <?= date('M j, Y H:i', strtotime($post['created_at'])) ?>
        <?php if ($post['updated_at'] != $post['created_at']): ?>
            • Updated: <?= date('M j, Y H:i', strtotime($post['updated_at'])) ?>
        <?php endif; ?>
    </div>
    
    <div style="margin: 20px 0; line-height: 1.8; font-size: 1.1rem;">
        <?= nl2br(htmlspecialchars($post['content'])) ?>
    </div>
    
    <div class="post-actions">
        <a href="?action=home" class="btn">← Back to Posts</a>
        
        <?php if (isLoggedIn() && ($_SESSION['user_id'] == $post['author_id'] || $_SESSION['role'] === 'admin')): ?>
            <a href="?action=edit_post&id=<?= $post['id'] ?>" class="btn">Edit Post</a>
            
            <form method="post" style="display: inline;">
                <input type="hidden" name="form_type" value="delete_post">
                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this post?')">Delete</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<div class="card" style="margin-top: 20px;">
    <h3>Share this post</h3>
    <p>
        <a href="#" onclick="window.open('https://twitter.com/intent/tweet?text=<?= urlencode($post['title']) ?>&url=<?= urlencode('http://localhost:8000/?action=view_post&id=' . $post['id']) ?>', '_blank'); return false;" class="btn">Share on Twitter</a>
        <a href="#" onclick="window.open('https://www.facebook.com/sharer/sharer.php?u=<?= urlencode('http://localhost:8000/?action=view_post&id=' . $post['id']) ?>', '_blank'); return false;" class="btn">Share on Facebook</a>
    </p>
</div>
