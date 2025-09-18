<?php
require '../config.php'; require 'guard.php'; include '../header.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  $desc = trim($_POST['description'] ?? '');
  $price = (float)($_POST['price'] ?? 0);
  $category_id = $_POST['category_id'] ? (int)$_POST['category_id'] : null;

  // จัดการอัปโหลดไฟล์
  $image_path = null;
  if (!empty($_FILES['image']['name'])) {
    $allow = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $type = mime_content_type($_FILES['image']['tmp_name']);
    if (!isset($allow[$type])) {
      $error = "อนุญาตเฉพาะ JPG/PNG/WebP";
    } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
      $error = "ไฟล์ใหญ่เกิน 2MB";
    } else {
      $ext = $allow[$type];
      $newName = 'uploads/'.date('Ymd_His').'_'.bin2hex(random_bytes(4)).'.'.$ext;
      if (!move_uploaded_file($_FILES['image']['tmp_name'], __DIR__.'/../'.$newName)) {
        $error = "อัปโหลดไฟล์ไม่สำเร็จ";
      } else {
        $image_path = $newName;
      }
    }
  }

  if (!$error) {
    if ($name === '' || $price <= 0) {
      $error = "กรุณากรอกชื่อและราคา (>0)";
    } else {
      $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image_url, category_id) VALUES (?,?,?,?,?)");
      $stmt->execute([$name, $desc, $price, $image_path, $category_id]);
      header("Location: /ecommerce/admin/");
      exit;
    }
  }
}

$cats = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>
<h1>เพิ่มสินค้า</h1>
<?php if (!empty($error)) echo "<p style='color:#b91c1c;'>$error</p>"; ?>
<form method="post" enctype="multipart/form-data" class="row" style="max-width:640px;">
  <div style="flex:1 1 100%;">
    <label>ชื่อสินค้า</label>
    <input type="text" name="name" required>
  </div>
  <div style="flex:1 1 100%;">
    <label>รายละเอียด</label>
    <textarea name="description" rows="4"></textarea>
  </div>
  <div style="flex:1 1 50%;">
    <label>ราคา (บาท)</label>
    <input type="text" name="price" required>
  </div>
  <div style="flex:1 1 50%;">
    <label>หมวดหมู่</label>
    <select name="category_id">
      <option value="">-- ไม่ระบุ --</option>
      <?php foreach ($cats as $c): ?>
        <option value="<?=$c['id']?>"><?=htmlspecialchars($c['name'])?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div style="flex:1 1 100%;">
    <label>รูปภาพสินค้า (JPG/PNG/WebP ≤ 2MB)</label>
    <input type="file" name="image" accept="image/*">
  </div>
  <div>
    <button type="submit" class="btn">บันทึก</button>
  </div>
</form>
<?php include '../footer.php'; ?>
