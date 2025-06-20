<?php
require_once 'config.php';

// Get article slug from URL
$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('Location: index.php');
    exit;
}

// Get article with category
$stmt = $pdo->prepare("
    SELECT a.*, c.name as category_name, c.slug as category_slug 
    FROM articles a 
    LEFT JOIN categories c ON a.category_id = c.id 
    WHERE a.slug = :slug
");
$stmt->execute(['slug' => $slug]);
$article = $stmt->fetch();

if (!$article) {
    header('Location: index.php');
    exit;
}

// Update views count
$stmt = $pdo->prepare("UPDATE articles SET views = views + 1 WHERE id = :id");
$stmt->execute(['id' => $article['id']]);

// Get categories for sidebar
$stmt = $pdo->prepare("SELECT * FROM categories ORDER BY name");
$stmt->execute();
$categories = $stmt->fetchAll();

// Get recent articles
$stmt = $pdo->prepare("
    SELECT a.*, c.name as category_name 
    FROM articles a 
    LEFT JOIN categories c ON a.category_id = c.id 
    WHERE a.id != :current_id 
    ORDER BY a.created_at DESC 
    LIMIT 5
");
$stmt->execute(['current_id' => $article['id']]);
$recentArticles = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['title']); ?> - Jalan Santai</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="navbar">
            <div class="nav-brand">
                <a href="index.php">Jalan Santai</a>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php">Beranda</a></li>
                <li><a href="#tentang">Tentang</a></li>
                <li><a href="#kontak">Kontak</a></li>
            </ul>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="content-wrapper">
                <!-- Article Content -->
                <article class="article-detail">
                    <header class="article-header">
                        <h1><?php echo htmlspecialchars($article['title']); ?></h1>
                        <div class="article-meta">
                            <span class="date">Ditulis pada <?php echo formatDate($article['created_at']); ?></span>
                            <span class="author">by <?php echo htmlspecialchars($article['author']); ?></span>
                            <?php if ($article['category_name']): ?>
                                <span class="category-tag">
                                    <a href="category.php?slug=<?php echo $article['category_slug']; ?>">
                                        <?php echo htmlspecialchars($article['category_name']); ?>
                                    </a>
                                </span>
                            <?php endif; ?>
                        </div>
                    </header>

                    <?php if ($article['image']): ?>
                        <div class="article-featured-image">
                            <img src="images/<?php echo htmlspecialchars($article['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($article['title']); ?>">
                        </div>
                    <?php endif; ?>

                    <div class="article-body">
                        <?php echo nl2br(htmlspecialchars($article['content'])); ?>
                    </div>

                    <footer class="article-footer">
                        <div class="article-actions">
                            <a href="index.php" class="back-button">← Kembali</a>
                            <div class="article-stats">
                                <span class="views">👁 <?php echo number_format($article['views']); ?> views</span>
                            </div>
                        </div>
                    </footer>
                </article>

                <!-- Sidebar -->
                <aside class="sidebar">
                    <!-- Search -->
                    <div class="widget search-widget">
                        <h3>Pencarian</h3>
                        <form method="GET" action="index.php" class="search-form">
                            <input type="text" name="search" placeholder="Masukkan kata kunci..." required>
                            <button type="submit">Go!</button>
                        </form>
                    </div>

                    <!-- Categories -->
                    <div class="widget categories-widget">
                        <h3>Kategori</h3>
                        <ul class="category-list">
                            <?php foreach ($categories as $category): ?>
                                <li>
                                    <a href="category.php?slug=<?php echo $category['slug']; ?>">
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Recent Articles -->
                    <div class="widget recent-widget">
                        <h3>Artikel Terkait</h3>
                        <ul class="recent-list">
                            <?php foreach ($recentArticles as $recentArticle): ?>
                                <li>
                                    <a href="article.php?slug=<?php echo $recentArticle['slug']; ?>">
                                        <?php echo htmlspecialchars($recentArticle['title']); ?>
                                    </a>
                                    <small><?php echo formatDate($recentArticle['created_at']); ?></small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- About -->
                    <div class="widget about-widget">
                        <h3>Tentang</h3>
                        <p>
                            Sekedar buah tangan catatan wisata dan jalan-jalan ke tempat wisata seputar Malang Raya. 
                            Tidak menutup kemungkinan juga akan ke daerah lain. Komentar dan saran silahkan 
                            ditinggalkan di kontak.
                        </p>
                    </div>
                </aside>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; Jalan Santai 2024</p>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>