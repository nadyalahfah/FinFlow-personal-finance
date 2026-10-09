<?php
require_once 'db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!empty($name) && !empty($email) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email sudah terdaftar!';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $hashed_password]);
            $user_id = $pdo->lastInsertId();

            $stmt_wallet = $pdo->prepare("INSERT INTO wallets (user_id, name, balance) VALUES (?, ?, ?)");
            $stmt_wallet->execute([$user_id, 'Kas Tunai', 0]);
            $stmt_wallet->execute([$user_id, 'Bank BCA', 0]);

            $stmt_cat = $pdo->prepare("INSERT INTO categories (user_id, name, type, monthly_budget) VALUES (?, ?, ?, ?)");
            $stmt_cat->execute([$user_id, 'Makanan', 'expense', 1500000]);
            $stmt_cat->execute([$user_id, 'Transportasi', 'expense', 500000]);
            $stmt_cat->execute([$user_id, 'Gaji', 'income', 0]);

            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $name;
            header("Location: index.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>FinFlow - Registrasi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="auth-container">
        <h2>Daftar Akun FinFlow</h2>
        <?php if ($error): ?><p class="alert-danger"><?php echo $error; ?></p><?php endif; ?>
        <form action="register.php" method="POST">
            <div class="form-group"><label>Nama Lengkap</label><input type="text" name="name" required></div>
            <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
            <div class="form-group"><label>Kata Sandi</label><input type="password" name="password" required></div>
            <button type="submit" class="btn-submit">Daftar Sekarang</button>
        </form>
        <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </div>
</body>
</html>