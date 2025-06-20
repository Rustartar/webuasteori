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
                $title = trim($_POST['title']);
                $slug = strtolower(str_replace(' ', '-', preg_replace('/[^A-Za-z0-9\s]/', '', trim($_POST['title']))));
                $content = trim($_POST['content']);
                $excerpt = trim($_POST['excerpt']);
                $category_id = $_POST['category_id'];
                $author = trim($_POST['author']);
                
                if (!empty($title) && !empty($content)) {
                    try {
                        $stmt = $pdo->prepare("INSERT INTO articles (title, slug, content, excerpt, category_id, author) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$title, $slug, $content, $excerpt, $category_id, $author]);
                        $message = 'Artikel berhasil ditambahkan!';
                    } catch (PDOException $e) {
                        $error = 'Error: ' . $e->getMessage();
                    }
                } else {
                    $error = 'Judul dan konten tidak boleh kosong!';
                }
                break;
                
            case 'edit':
                $id = $_POST['id'];
                $title = trim($_POST['title']);
                $slug = strtolower(str_replace(' ', '-', preg_replace('/[^A-Za-z0-9\s]/', '', trim($_POST['title']))));
                $content = trim($_POST['content']);
                $excerpt = trim($_POST['excerpt']);
                $category_id = $_POST['category_id'];
                $author = trim($_POST['author']);
                
                if (!empty($title) && !empty($content)) {
                    try {
                        $stmt = $pdo->prepare("UPDATE articles SET title = ?, slug = ?, content = ?, excerpt = ?, category_id = ?, author = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                        $stmt->execute([$title, $slug, $content, $excerpt, $category_id, $author, $id]);
                        $message = 'Artikel berhasil diupdate!';
                    } catch (PDOException $e) {
                        $error = 'Error: ' . $e->getMessage();
                    }
                } else {
                    $error = 'Judul dan konten tidak boleh kosong!';
                }
                break;
                
            case 'delete':
                $id = $_POST['id'];
                try {
                    $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
                    $stmt->execute([$id]);
                    $message = 'Artikel berhasil dihapus!';
                } catch (PDOException $e) {
                    $error = 'Error: ' . $e->getMessage();
                }
                break;
        }
    }
}

// Get all articles with categories
$stmt = $pdo->query("
    SELECT a.*, c.name as category_name 
    FROM articles a 
    LEFT JOIN categories c ON a.category_id = c.id 
    ORDER BY a.created_at DESC
");
$articles = $stmt->fetchAll();

// Get all categories for dropdown
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll();

// Get article for editing
$editArticle = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editArticle = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Artikel - Admin Panel</title>
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
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .card { background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .card-header { padding: 20px; border-bottom: 1px solid #eee; }
        .card-body { padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], select, textarea { 
            width: 100%; padding: 10px; border: 1px solid #ddd; 
            border-radius: 4px; font-size: 14px; 
        }
        textarea { height: 200px; resize: vertical; }
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
        .btn-danger { background: #dc3545; color: white; }
        .btn-danger:hover { background: #c82333; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        .table th { background: #f8f9fa; font-weight: bold; }
        .alert { padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background: