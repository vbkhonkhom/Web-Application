<?php
require '../config.php'; require 'guard.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
$stmt->execute([$id]);
$p = $stmt->fetch();
if (!$p) { die('ไม่พบสินค้า'); }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_check($_POST['csrf'] ?? '')) {
    $error = 'CSRF ไม่ถูกต้อง';
  } else {
    $name = trim($_POST['name'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $category_id = $_POST['category_id'] ? (int)$_POST['category_id'] : null;

    // เริ่มจากรูปเดิมไว้ก่อน
    $newImage = $p['image_url'];

    // ถ้ามีการเลือกไฟล์รูปใหม่
    if (!empty($_FILES['image']['name'])) {
      // เตรียมโฟลเดอร์ปลายทาง
      $uploadDir = __DIR__ . '/../uploads/';
      if (!is_dir($uploadDir)) {
        // สร้างโฟลเดอร์ถ้ายังไม่มี
        if (!mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
          $error = 'ไม่สามารถสร้างโฟลเดอร์ uploads ได้';
        }
      }

      if (!$error) {
        // ตรวจชนิด/ขนาดไฟล์
        $allow = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
        $tmp   = $_FILES['image']['tmp_name'] ?? '';
        // บางเครื่องไม่มี mime_content_type ให้ fallback เป็น type จาก $_FILES
        $type  = ($tmp && function_exists('mime_content_type')) ? mime_content_type($tmp) : ($_FILES['image']['type'] ?? '');

        if (!isset($allow[$type])) {
          $error = "อนุญาตเฉพาะไฟล์ JPG/PNG/WebP";
        } elseif (($_FILES['image']['size'] ?? 0) > 2 * 1024 * 1024) {
          $error = "ไฟล์ใหญ่เกิน 2MB";
        } else {
          $ext    = $allow[$type];
          $fname  = date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
          $target = $uploadDir . $fname;

          if (!move_uploaded_file($tmp, $target)) {
            $error = "อัปโหลดไฟล์ไม่สำเร็จ (สิทธิ์ไฟล์หรือโฟลเดอร์ไม่ถูกต้อง)";
          } else {
            // เซ็ตพาธใหม่ (เก็บแบบ relative)
            $newImage = 'uploads/' . $fname;

            // ลบไฟล์เก่าเฉพาะที่อยู่ใน uploads/
            if (!empty($p['image_url']) && str_starts_with($p['image_url'], 'uploads/')) {
              $old = __DIR__ . '/../' . $p['image_url'];
              if (is_file($old)) @unlink($old);
            }
          }
        }
      }
    }

    if (!$error) {
      if ($name === '' || $price <= 0) {
        $error = "กรุณากรอกชื่อและราคา (>0)";
      } else {
        $u = $pdo->prepare("UPDATE products
            SET name=?, description=?, price=?, image_url=?, category_id=?
            WHERE id=?");
        $u->execute([$name, $desc, $price, $newImage, $category_id, $id]);
        header("Location: /ecommerce/admin/");
        exit;
      }
    }
  }
}

$cats = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
include '../header.php';
?>
<h1>แก้ไขสินค้า #<?=$p['id']?></h1>
<?php if ($error): ?><p style="color:#b91c1c;"><?=$error?></p><?php endif; ?>

<form method="post" enctype="multipart/form-data" class="row" style="max-width:640px;">
  <?php csrf_field(); ?>
  <div style="flex:1 1 100%;">
    <label>ชื่อสินค้า</label>
    <input type="text" name="name" value="<?=htmlspecialchars($p['name'])?>" required>
  </div>
  <div style="flex:1 1 100%;">
    <label>รายละเอียด</label>
    <textarea name="description" rows="4"><?=htmlspecialchars($p['description'])?></textarea>
  </div>
  <div style="flex:1 1 50%;">
    <label>ราคา (บาท)</label>
    <input type="text" name="price" value="<?=htmlspecialchars($p['price'])?>" required>
  </div>
  <div style="flex:1 1 50%;">
    <label>หมวดหมู่</label>
    <select name="category_id">
      <option value="">-- ไม่ระบุ --</option>
      <?php foreach ($cats as $c): ?>
        <option value="<?=$c['id']?>" <?=$p['category_id']==$c['id']?'selected':''?>>
          <?=htmlspecialchars($c['name'])?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div style="flex:1 1 100%;">
    <label>รูปปัจจุบัน</label><br>
    <?php if ($p['image_url']): ?>
      <img src="<?=htmlspecialchars($p['image_url'])?>" style="height:100px;border-radius:10px">
    <?php else: ?>
      <span style="color:#888">ไม่มีรูป</span>
    <?php endif; ?>
  </div>

  <div style="flex:1 1 100%;">
    <label>อัปโหลดรูปใหม่ (ไม่ใส่ = ใช้รูปเดิม)</label>
    <input type="file" name="image" accept="image/*">
  </div>

  <div>
    <button class="btn" type="submit">บันทึกการเปลี่ยนแปลง</button>
    <a class="btn secondary" href="/ecommerce/admin/">ยกเลิก</a>
  </div>
</form>
<?php include '../footer.php'; ?>
