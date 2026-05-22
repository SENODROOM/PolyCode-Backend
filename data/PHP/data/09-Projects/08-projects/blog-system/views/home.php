<?php
// Home page view
$blog = new BlogPost();
$search = $_GET['search'] ?? '';

if ($search) {
    $posts = $blog->search($search);
    $pageTitle = "Search Results for: " . htmlspecialchars($search);
} else {
    $posts = $blog->getAll('published', 10);
    $pageTitle = "Latest Posts";
}
?>

<div class="card">
    <h1><?= $pageTitle ?></h1>
    
    <?php if (!$search): ?>
        <form method="get" style="margin-bottom: 20px;">
            <input type="hidden" name="action" value="home">
            <div class="form-group">
                <input type="text" name="search" placeholder="Search posts..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="btn">Search</button>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php if (empty($posts)): ?>
    <div class="card">
        <p>No posts found.</p>
    </div>
<?php else: ?>
    <div class="post-list">
        <?php foreach ($posts as $post): ?>
            <div class="post-card">
                <h2 class="post-title">
                    <a href="?action=view_post&id=<?= $post['id'] ?>" style="color: #2c3e50; text-decoration: none;">
                        <?= htmlspecialchars($post['title']) ?>
                    </a>
                </h2>
                
                <div class="post-meta">
                    By <?= htmlspecialchars($post['author_name']) ?> • 
                    <?= date('M j, Y', strtotime($post['created_at'])) ?>
                </div>
                
                <div class="post-excerpt">
                    <?= htmlspecialchars($post['excerpt']) ?>
                </div>
                
                <div class="post-actions">
                    <a href="?action=view_post&id=<?= $post['id'] ?>" class="btn">Read More</a>
                    
                    <?php if (isLoggedIn() && ($_SESSION['user_id'] == $post['author_id'] || $_SESSION['role'] === 'admin')): ?>
                        <a href="?action=edit_post&id=<?= $post['id'] ?>" class="btn">Edit</a>
                        
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="form_type" value="delete_post">
                            <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
