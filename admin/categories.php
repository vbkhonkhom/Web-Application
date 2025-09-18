<?php
require '../config.php'; require 'guard.php'; include '../header.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  if ($name === '') $error = "กรุณากรอกชื่อหมวดหมู่";
  else {
    $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
    $stmt->execute([$name]);
    header('Location: /ecommerce/admin/categories.php');
    exit;
  }
}
$cats = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>
<h1>จัดการหมวดหมู่</h1>
<?php if ($error): ?><p style="color:#b91c1c;"><?=$error?></p><?php endif; ?>
<form method="post" style="max-width:360px; margin-bottom:16px;">
  <label>ชื่อหมวดหมู่</label>
  <input type="text" name="name" required>
  <br><br>
  <button class="btn" type="submit">เพิ่ม</button>
</form>

<table class="table">
  <thead><tr><th>ID</th><th>ชื่อ</th></tr></thead>
  <tbody>
  <?php foreach ($cats as $c): ?>
    <tr><td><?=$c['id']?></td><td><?=htmlspecialchars($c['name'])?></td></tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php include '../footer.php'; ?>
