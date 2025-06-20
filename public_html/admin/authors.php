<?php
require_once 'config.php';
requireLogin();

$pdo = getConnection();
$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_author':
                $old_author = trim($_POST['old_author']);
                $new_author = trim($_POST['new_author']);
                
                if (!empty($old_author) && !empty($new_author)) {
                    try {
                        $stmt = $pdo->prepare("UPDATE articles SET author = ? WHERE author = ?");
                        $stmt->execute([$new_author, $old_author]);
                        $affected = $stmt->rowCount();
                        $message = "Berhasil mengupdate $affected artikel dengan penulis '$old_author' menjadi '$new_author'";
                    } catch (PDOException $e) {
                        $error = 'Error: ' . $e->getMessage();
                    }
                } else {
                    $error = 'Nama penulis tidak boleh kosong!';
                }
                break;
        }
    }
}

// Get all unique authors with article count
$stmt = $pdo->query("
    SELECT author, COUNT(*) as article_count, 
           MIN(created_at) as first_article, 
           MAX(created_at) as last_article,
           SUM(views) as total_views
    FROM articles 
    GROUP BY author 
    ORDER BY article_count DESC
");
$authors = $stmt->fetchAll();

// Get author for editing
$editAuthor = null;
if (isset($_GET['edit'])) {
    $editAuthor = $_GET['edit'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Penulis - Admin Panel</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f8f9fa; }
        .header { 
            background: #007bff; color: white; padding: 15px 20px; 
            display: flex; justify-content: space-between; align-items: center; 
        }
        .header h1 { font-size: 24px; }
        .back-btn { 
            background: #6c757d; color: white; padding: 8px 16px; 
            text-decoration: none; border-radius: 4px; 
        }
        .back-btn:hover { background: #545b62; }
        .container { max-width: 1000px; margin: 0 auto; padding: 20px; }
        .card { background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .card-header { padding: 20px; border-bottom: 1px solid #eee; }
        .card-body { padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], select { 
            width: 100%; padding: 10px; border: 1px solid #ddd; 
            border-radius: 4px; font-size: 14px; 
        }
        .btn { 
            padding: 10px 20px; border: none; border-radius: 4px; 
            cursor: pointer; text-decoration: none; display: inline-block; 
        }
        .btn-primary { background: #007bff; color: white; }
        .btn-primary:hover { background: #0056b3; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-secondary:hover { background: #545b62; }
        .btn-warning { background: #ffc107; color: #212529; }
        .btn-warning:hover { background: #e0a800; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        .table th { background: #f8f9fa; font-weight: bold; }
        .alert { padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; align-items: end; }
        @media (max-width: 768px) { .form-row { grid-template-columns: 1fr; } }
        .stats-card { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; 
        }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
        .stat-item { text-align: center; }
        .stat-number { font-size: 24px; font-weight: bold; margin-bottom: 5px; }
        .stat-label { font-size: 14px; opacity: 0.9; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Kelola Penulis</h1>
        <a href="dashboard.php" class="back-btn">← Kembali</a>
    </div>

    <div class="container">
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (count($authors) > 0): ?>
        <div class="stats-card">
            <h3 style="margin-bottom: 20px;">Statistik Penulis</h3>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number"><?php echo count($authors); ?></div>
                    <div class="stat-label">Total Penulis</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo array_sum(array_column($authors, 'article_count')); ?></div>
                    <div class="stat-label">Total Artikel</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo number_format(array_sum(array_column($authors, 'total_views'))); ?></div>
                    <div class="stat-label">Total Views</div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h3>Edit Nama Penulis</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>Info:</strong> Mengubah nama penulis akan memperbarui semua artikel yang ditulis oleh penulis tersebut.
                </div>
                
                <form method="POST">
                    <input type="hidden" name="action" value="update_author">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="old_author">Nama Penulis Lama:</label>
                            <select id="old_author" name="old_author" required>
                                <option value="">Pilih Penulis</option>
                                <?php foreach ($authors as $author): ?>
                                <option value="<?php echo htmlspecialchars($author['author']); ?>"
                                        <?php echo ($editAuthor && $editAuthor === $author['author']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($author['author']); ?> 
                                    (<?php echo $author['article_count']; ?> artikel)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_author">Nama Penulis Baru:</label>
                            <input type="text" id="new_author" name="new_author" required 
                                   value="<?php echo $editAuthor ? htmlspecialchars($editAuthor) : ''; ?>"
                                   placeholder="Masukkan nama penulis baru">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Update Nama Penulis</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Daftar Penulis</h3>
            </div>
            <div class="card-body">
                <?php if (empty($authors)): ?>
                    <div style="text-align: center; color: #666; padding: 40px;">
                        Belum ada penulis. Tambahkan artikel terlebih dahulu.
                    </div>
                <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama Penulis</th>
                            <th>Jumlah Artikel</th>
                            <th>Total Views</th>
                            <th>Artikel Pertama</th>
                            <th>Artikel Terakhir</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($authors as $author): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($author['author']); ?></strong></td>
                            <td><?php echo number_format($author['article_count']); ?></td>
                            <td><?php echo number_format($author['total_views']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($author['first_article'])); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($author['last_article'])); ?></td>
                            <td>
                                <a href="authors.php?edit=<?php echo urlencode($author['author']); ?>" 
                                   class="btn btn-warning">Edit</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Auto-fill new author field when selecting old author
        document.getElementById('old_author').addEventListener('change', function() {
            const newAuthorField = document.getElementById('new_author');
            if (this.value && !newAuthorField.value) {
                newAuthorField.value = this.value;
                newAuthorField.focus();
                newAuthorField.select();
            }
        });
    </script>
</body>
</html>