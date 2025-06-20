<?php
require_once 'config.php';

// Get category slug from URL
$categorySlug = $_GET['slug'] ?? '';

if (empty($categorySlug)) {
    header('Location: index.php');
    exit;
}

// Get category
$stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = :slug");
$stmt->execute(['slug' => $categorySlug]);
$category = $stmt->fetch();

if (!$category) {
    header('Location: index.php');
    exit;
}

// Get articles in this category
$stmt = $pdo->prepare("
    SELECT a.*, c.name as category_name, c.slug as category_slug 
    FROM articles a 
    LEFT JOIN categories c ON a.category_id = c.id 
    WHERE c.slug = :category_slug 
    ORDER BY a.created_at DESC
");
$stmt->execute(['category_slug' => $categorySlug]);
$articles = $stmt->fetchAll();

// Get all categories for sidebar
$stmt = $pdo->prepare("SELECT * FROM categories ORDER BY name");
$stmt->execute();
$categories = $stmt->fetchAll();

// Count articles in current category
$articleCount = count($articles);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori: <?php echo htmlspecialchars($category['name']); ?> - Jalan Santai</title>
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
            <section class="hero category-hero">
                <h1>Kategori: <?php echo htmlspecialchars($category['name']); ?></h1>
                <p><?php echo $articleCount; ?> artikel ditemukan</p>
                <nav class="breadcrumb">
                    <a href="index.php">Beranda</a> > 
                    <span><?php echo htmlspecialchars($category['name']); ?></span>
                </nav>
            </section>

            <div class="content-wrapper">
                <!-- Articles Section -->
                <section class="articles-section">
                    <?php if (empty($articles)): ?>
                        <div class="no-articles">
                            <h3>Tidak ada artikel dalam kategori ini</h3>
                            <p>Belum ada artikel yang dipublish dalam kategori <?php echo htmlspecialchars($category['name']); ?>.</p>
                            <a href="index.php" class="btn-primary">Kembali ke Beranda</a>
                        </div>
                    <?php else: ?>
                        <div class="category-articles">
                            <?php foreach ($articles as $article): ?>
                                <article class="article-card">
                                    <?php if ($article['image']): ?>
                                        <div class="article-image">
                                            <img src="images/<?php echo htmlspecialchars($article['image']); ?>" 
                                                 alt="<?php echo htmlspecialchars($article['title']); ?>">
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="article-content">
                                        <div class="article-meta">
                                            <span class="date"><?php echo formatDate($article['created_at']); ?></span>
                                            <span class="category">
                                                <?php echo htmlspecialchars($article['category_name']); ?>
                                            </span>
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
                        </div>
                    <?php endif; ?>
                </section>

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
                            <?php foreach ($categories as $cat): ?>
                                <li class="<?php echo $cat['slug'] === $categorySlug ? 'active' : ''; ?>">
                                    <a href="category.php?slug=<?php echo $cat['slug']; ?>">
                                        <?php echo htmlspecialchars($cat['name']); ?>
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
                            Tidak menutup kemungkingan juga akan ke daerah lain. Komentar dan saran silahkan 
                            ditinggalkan di kontak.
                        </p>
                    </div>

                    <!-- Back to Home -->
                    <div class="widget">
                        <a href="index.php" class="btn-secondary">← Kembali ke Beranda</a>
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
                