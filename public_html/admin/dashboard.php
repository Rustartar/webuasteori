<?php
require_once 'config.php';
requireLogin();

$pdo = getConnection();

// Get statistics
$stmt = $pdo->query("SELECT COUNT(*) as total FROM articles");
$totalArticles = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM categories");
$totalCategories = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT SUM(views) as total FROM articles");
$totalViews = $stmt->fetch()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Panel</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f8f9fa; }
        .header { 
            background: #007bff; color: white; padding: 15px 20px; 
            display: flex; justify-content: space-between; align-items: center; 
        }
        .header h1 { font-size: 24px; }
        .user-info { display: flex; align-items: center; gap: 15px; }
        .logout-btn { 
            background: #dc3545; color: white; padding: 8px 16px; 
            text-decoration: none; border-radius: 4px; 
        }
        .logout-btn:hover { background: #c82333; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .stats { 
            display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); 
            gap: 20px; margin-bottom: 30px; 
        }
        .stat-card { 
            background: white; padding: 20px; border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center; 
        }
        .stat-number { font-size: 36px; font-weight: bold; color: #007bff; margin-bottom: 10px; }
        .stat-label { color: #666; font-size: 14px; }
        .menu-grid { 
            display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 20px; 
        }
        .menu-item { 
            background: white; padding: 30px; border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center; 
            text-decoration: none; color: #333; transition: transform 0.2s; 
        }
        .menu-item:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.15); }
        .menu-icon { font-size: 48px; margin-bottom: 15px; }
        .menu-title { font-size: 18px; font-weight: bold; margin-bottom: 10px; }
        .menu-desc { color: #666; font-size: 14px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Admin Panel</h1>
        <div class="user-info">
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="container">
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo $totalArticles; ?></div>
                <div class="stat-label">Total Artikel</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $totalCategories; ?></div>
                <div class="stat-label">Total Kategori</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($totalViews); ?></div>
                <div class="stat-label">Total Views</div>
            </div>
        </div>

        <div class="menu-grid">
            <a href="categories.php" class="menu-item">
                <div class="menu-icon">📁</div>
                <div class="menu-title">Kelola Kategori</div>
                <div class="menu-desc">Tambah, edit, dan hapus kategori artikel</div>
            </a>
            
            <a href="articles.php" class="menu-item">
                <div class="menu-icon">📰</div>
                <div class="menu-title">Kelola Artikel</div>
                <div class="menu-desc">Tambah, edit, dan hapus artikel</div>
            </a>
            
            <a href="authors.php" class="menu-item">
                <div class="menu-icon">👤</div>
                <div class="menu-title">Kelola Penulis</div>
                <div class="menu-desc">Manajemen penulis artikel</div>
            </a>
        </div>
    </div>
</body>
</html>