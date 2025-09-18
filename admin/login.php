<?php
require '../config.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $password = $_POST['password'] ?? '';

  $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
  $stmt->execute([$username]);
  $admin = $stmt->fetch();

  if ($admin && password_verify($password, $admin['password_hash'])) {
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_username'] = $admin['username'];
    header('Location: /ecommerce/admin/');
    exit;
  } else {
    $error = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
  }
}
?>
<!doctype html>
<html lang="th">
<head>
<meta charset="utf-8"><title>Admin Login</title>
<link href="/ecommerce/styles.css" rel="stylesheet">
</head>
<body class="container">
  <h1>เข้าสู่ระบบแอดมิน</h1>
  <?php if ($error): ?><p style="color:#b91c1c;"><?=$error?></p><?php endif; ?>
  <form method="post" style="max-width:360px">
    <label>ชื่อผู้ใช้</label>
    <input type="text" name="username" required>
    <label>รหัสผ่าน</label>
    <input type="password" name="password" required>
    <br><br>
    <button class="btn" type="submit">เข้าสู่ระบบ</button>
  </form>
</body>
</html>
