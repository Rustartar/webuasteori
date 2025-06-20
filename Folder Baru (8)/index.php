<?php
require_once 'config.php';

// Get articles with categories
$stmt = $pdo->prepare("
    SELECT a.*, c.name as category_name, c.slug as category_slug 
    FROM articles a 
    LEFT JOIN categories c ON a.category_id = c.id 
    ORDER BY a.created_at DESC
");
$stmt->execute();
$articles = $stmt->fetchAll();

// Get categories for sidebar
$stmt = $pdo->prepare("SELECT * FROM categories ORDER BY name");
$stmt->execute();
$categories = $stmt->fetchAll();

// Handle search
$searchQuery = '';
$searchResults = [];
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchQuery = trim($_GET['search']);
    $stmt = $pdo->prepare("
        SELECT a.*, c.name as category_name, c.slug as category_slug 
        FROM articles a 
        LEFT JOIN categories c ON a.category_id = c.id 
        WHERE a.title LIKE :search OR a.content LIKE :search
        ORDER BY a.created_at DESC
    ");
    $stmt->execute(['search' => "%$searchQuery%"]);
    $searchResults = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jalan Santai - Blog Catatan Wisata dan Jalan-jalan</title>
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
            <!-- Hero Section -->
            <section class="hero">
                <h1>Selamat Datang di Blog Kami!</h1>
                <p>Blog Catatan Wisata dan Jalan-jalan</p>
            </section>

            <div class="content-wrapper">
                <!-- Articles Section -->
                <section class="articles-section">
                    <?php if (!empty($searchQuery)): ?>
                        <h2>Hasil Pencarian untuk: "<?php echo htmlspecialchars($searchQuery); ?>"</h2>
                        <?php $articlesToShow = $searchResults; ?>
                    <?php else: ?>
                        <?php $articlesToShow = $articles; ?>
                    <?php endif; ?>

                    <?php if (empty($articlesToShow)): ?>
                        <p>Tidak ada artikel yang ditemukan.</p>
                    <?php else: ?>
                        <?php foreach ($articlesToShow as $index => $article): ?>
                            <article class="article-card <?php echo $index === 0 ? 'featured' : ''; ?>">
                                <?php if ($article['image']): ?>
                                    <div class="article-image">
                                        <img src="images/<?php echo htmlspecialchars($article['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($article['title']); ?>">
                                    </div>
                                <?php endif; ?>
                                
                                <div class="article-content">
                                    <div class="article-meta">
                                        <span class="date"><?php echo formatDate($article['created_at']); ?></span>
                                        <?php if ($article['category_name']): ?>
                                            <span class="category">
                                                <a href="category.php?slug=<?php echo $article['category_slug']; ?>">
                                                    <?php echo htmlspecialchars($article['category_name']); ?>
                                                </a>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <h2 class="article-title">
                                        <a href="article.php?slug=<?php echo $article['slug']; ?>">
                                            <?php echo htmlspecialchars($article['title']); ?>
                                        </a>
                                    </h2>
                                    
                                    <p class="article-excerpt">
                                        <?php echo htmlspecialchars(truncateText($article['excerpt'] ?: strip_tags($article['content']))); ?>
                                    </p>
                                    
                                    <a href="article.php?slug=<?php echo $article['slug']; ?>" class="read-more">
                                        Selengkapnya →
                                    </a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </section>

                <!-- Sidebar -->
                <aside class="sidebar">
                    <!-- Search -->
                    <div class="widget search-widget">
                        <h3>Pencarian</h3>
                        <form method="GET" action="index.php" class="search-form">
                            <input type="text" name="search" placeholder="Masukkan kata kunci..." 
                                   value="<?php echo htmlspecialchars($searchQuery); ?>">
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

                    <!-- About -->
                    <div class="widget about-widget">
                        <h3>Tentang</h3>
                        <p>
                            Sekedar buah tangan catatan wisata dan jalan-jalan ke tempat wisata seputar Malang Raya. 
                            Tidak menutup kemungkinan juga akan ke daerah lain. Komentar dan saran silahkan 
                            ditinggalkan di kontak.
                        </p>
                    </div>

                    <!-- Recent Articles -->
                    <?php if (!empty($searchQuery)): ?>
                        <div class="widget recent-widget">
                            <h3>Artikel Terkait</h3>
                            <ul class="recent-list">
                                <?php foreach (array_slice($articles, 0, 3) as $recentArticle): ?>
                                    <li>
                                        <a href="article.php?slug=<?php echo $recentArticle['slug']; ?>">
                                            <?php echo htmlspecialchars($recentArticle['title']); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
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