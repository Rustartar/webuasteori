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
            case 'add':
                $name = trim($_POST['name']);
                $slug = strtolower(str_replace(' ', '-', trim($_POST['name'])));
                
                if (!empty($name)) {
                    try {
                        $stmt = $pdo->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)");
                        $stmt->execute([$name, $slug]);
                        $message = 'Kategori berhasil ditambahkan!';
                    } catch (PDOException $e) {
                        $error = 'Error: ' . $e->getMessage();
                    }
                } else {
                    $error = 'Nama kategori tidak boleh kosong!';
                }
                break;
                
            case 'edit':
                $id = $_POST['id'];
                $name = trim($_POST['name']);
                $slug = strtolower(str_replace(' ', '-', trim($_POST['name'])));
                
                if (!empty($name)) {
                    try {
                        $stmt = $pdo->prepare("UPDATE categories SET name = ?, slug = ? WHERE id = ?");
                        $stmt->execute([$name, $slug, $id]);
                        $message = 'Kategori berhasil diupdate!';
                    } catch (PDOException $e) {
                        $error = 'Error: ' . $e->getMessage();
                    }
                } else {
                    $error = 'Nama kategori tidak boleh kosong!';
                }
                break;
                
            case 'delete':
                $id = $_POST['id'];
                try {
                    // Check if category has articles
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE category_id = ?");
                    $stmt->execute([$id]);
                    $count = $stmt->fetchColumn();
                    
                    if ($count > 0) {
                        $error = 'Kategori tidak dapat dihapus karena masih memiliki artikel!';
                    } else {
                        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
                        $stmt->execute([$id]);
                        $message = 'Kategori berhasil dihapus!';
                    }
                } catch (PDOException $e) {
                    $error = 'Error: ' . $e->getMessage();
                }
                break;
        }
    }
}

// Get all categories
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll();

// Get category for editing
$editCategory = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editCategory = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kategori - Admin Panel</title>
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
        input[type="text"] { 
            width: 100%; padding: 10px; border: 1px solid #ddd; 
            border-radius: 4px; font-size: 14px; 
        }
        .btn { 
            padding: 10px 20px; border: none; border-radius: 4px; 
            cursor: pointer; text-decoration: none; display: inline-block; 
        }
        .btn-primary { background: #007bff; color: white; }
        .btn-primary:hover { background: #0056b3; }
        .btn-success { background: #28a745; color: white; }
        .btn-success:hover { background: #1e7e34; }
        .btn-warning { background: #ffc107; color: #212529; }
        .btn-warning:hover { background: #e0a800; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-danger:hover { background: #c82333; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        .table th { background: #f8f9fa; font-weight: bold; }
        .alert { padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .btn-group { display: flex; gap: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Kelola Kategori</h1>
        <a href="dashboard.php" class="back-btn">← Kembali</a>
    </div>

    <div class="container">
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h3><?php echo $editCategory ? 'Edit Kategori' : 'Tambah Kategori Baru'; ?></h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="<?php echo $editCategory ? 'edit' : 'add'; ?>">
                    <?php if ($editCategory): ?>
                        <input type="hidden" name="id" value="<?php echo $editCategory['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="name">Nama Kategori:</label>
                        <input type="text" id="name" name="name" required 
                               value="<?php echo $editCategory ? htmlspecialchars($editCategory['name']) : ''; ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <?php echo $editCategory ? 'Update Kategori' : 'Tambah Kategori'; ?>
                    </button>
                    
                    <?php if ($editCategory): ?>
                        <a href="categories.php" class="btn btn-secondary">Batal</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Daftar Kategori</h3>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Slug</th>
                            <th>Tanggal Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?php echo $category['id']; ?></td>
                            <td><?php echo htmlspecialchars($category['name']); ?></td>
                            <td><?php echo htmlspecialchars($category['slug']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($category['created_at'])); ?></td>
                            <td>
                                <div class="btn-group">
                                    <a href="categories.php?edit=<?php echo $category['id']; ?>" 
                                       class="btn btn-warning">Edit</a>
                                    <form method="POST" style="display: inline;" 
                                          onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                                        <button type="submit" class="btn btn-danger">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($categories)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #666;">Belum ada kategori</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>