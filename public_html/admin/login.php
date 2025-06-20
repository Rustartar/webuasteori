<?php
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header('Location: dashboard.php');
        exit();
    } else {
        $error = 'Username atau password salah!';
    }
}

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Admin Panel</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .login-container { 
            display: flex; justify-content: center; align-items: center; 
            min-height: 100vh; padding: 20px; 
        }
        .login-form { 
            background: white; padding: 40px; border-radius: 8px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 100%; max-width: 400px; 
        }
        .login-form h2 { text-align: center; margin-bottom: 30px; color: #333; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; color: #555; font-weight: bold; }
        input[type="text"], input[type="password"] { 
            width: 100%; padding: 12px; border: 1px solid #ddd; 
            border-radius: 4px; font-size: 14px; 
        }
        .btn { 
            width: 100%; padding: 12px; background: #007bff; 
            color: white; border: none; border-radius: 4px; 
            font-size: 16px; cursor: pointer; 
        }
        .btn:hover { background: #0056b3; }
        .error { 
            background: #f8d7da; color: #721c24; padding: 10px; 
            border-radius: 4px; margin-bottom: 20px; text-align: center; 
        }
        .info { color: #666; text-align: center; margin-top: 20px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="login-container">
        <form class="login-form" method="POST">
            <h2>Login Admin</h2>
            
            <?php if ($error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn">Login</button>
            
            <div class="info">
                Default: username = admin, password = admin123
            </div>
        </form>
    </div>
</body>
</html>