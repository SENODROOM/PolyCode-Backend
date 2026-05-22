<?php
// Admin dashboard view
$blog = new BlogPost();
$user = new User();

// Get statistics
$totalPosts = count($blog->getAll('all', 1000));
$publishedPosts = count($blog->getAll('published', 1000));
$draftPosts = count($blog->getAll('draft', 1000));
$totalUsers = count($user->getAll());

// Get recent posts and users
$recentPosts = $blog->getAll('all', 5);
$recentUsers = $user->getAll();
array_splice($recentUsers, 5); // Keep only first 5 users
?>

<div class="card">
    <h1>Admin Dashboard</h1>
    <p>Welcome to the admin panel, <?= htmlspecialchars($_SESSION['username']) ?>!</p>
</div>

<div class="admin-dashboard">
    <div class="stat-card">
        <div class="stat-number"><?= $totalPosts ?></div>
        <div class="stat-label">Total Posts</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-number"><?= $publishedPosts ?></div>
        <div class="stat-label">Published Posts</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-number"><?= $draftPosts ?></div>
        <div class="stat-label">Draft Posts</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-number"><?= $totalUsers ?></div>
        <div class="stat-label">Total Users</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
    <div class="card">
        <h2>Recent Posts</h2>
        <?php if (empty($recentPosts)): ?>
            <p>No posts yet.</p>
        <?php else: ?>
            <?php foreach ($recentPosts as $post): ?>
                <div class="user-item">
                    <div>
                        <strong><?= htmlspecialchars($post['title']) ?></strong>
                        <br>
                        <small>
                            By <?= htmlspecialchars($post['author_name']) ?> • 
                            <?= date('M j, Y', strtotime($post['created_at'])) ?>
                        </small>
                    </div>
                    <div>
                        <span style="color: <?= $post['status'] == 'published' ? '#27ae60' : '#f39c12' ?>;">
                            <?= ucfirst($post['status']) ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <div style="margin-top: 15px;">
            <a href="?action=create_post" class="btn">Create New Post</a>
        </div>
    </div>
    
    <div class="card">
        <h2>Recent Users</h2>
        <?php if (empty($recentUsers)): ?>
            <p>No users yet.</p>
        <?php else: ?>
            <div class="user-list">
                <?php foreach ($recentUsers as $user): ?>
                    <div class="user-item">
                        <div>
                            <strong><?= htmlspecialchars($user['username']) ?></strong>
                            <br>
                            <small><?= htmlspecialchars($user['email']) ?></small>
                        </div>
                        <div>
                            <span style="color: <?= $user['role'] == 'admin' ? '#e74c3c' : '#3498db' ?>;">
                                <?= ucfirst($user['role']) ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card" style="margin-top: 20px;">
    <h2>Quick Actions</h2>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="?action=create_post" class="btn btn-success">Create Post</a>
        <a href="?action=home" class="btn">View Blog</a>
        <a href="?action=logout" class="btn btn-danger">Logout</a>
    </div>
</div>

<div class="card" style="margin-top: 20px;">
    <h2>System Information</h2>
    <ul>
        <li><strong>Application:</strong> <?= APP_NAME ?> v<?= APP_VERSION ?></li>
        <li><strong>PHP Version:</strong> <?= PHP_VERSION ?></li>
        <li><strong>Database:</strong> MySQL</li>
        <li><strong>Current User:</strong> <?= htmlspecialchars($_SESSION['username']) ?> (<?= ucfirst($_SESSION['role']) ?>)</li>
    </ul>
</div>
