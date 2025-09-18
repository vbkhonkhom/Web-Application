<?php
require 'config.php'; require 'functions.php';

$items = cart_items();
if (!$items) { header("Location: cart.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  $phone = trim($_POST['phone'] ?? '');
  $addr = trim($_POST['address'] ?? '');
  $total = cart_total();

  if ($name === '' || $addr === '') {
    $error = "กรุณากรอกชื่อและที่อยู่";
  } else {
    // บันทึกคำสั่งซื้อ + รายการ
    $pdo->beginTransaction();
    try {
      $stmt = $pdo->prepare("INSERT INTO orders (customer_name, customer_phone, customer_address, total) VALUES (?,?,?,?)");
      $stmt->execute([$name, $phone, $addr, $total]);
      $order_id = $pdo->lastInsertId();

      $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, qty, price) VALUES (?,?,?,?)");
      foreach ($items as $pid => $it) {
        $itemStmt->execute([$order_id, $pid, $it['qty'], $it['price']]);
      }

      $pdo->commit();
      // เคลียร์ตะกร้า
      $_SESSION['cart'] = [];
      header("Location: order_success.php?id=".$order_id);
      exit;
    } catch (Exception $e) {
      $pdo->rollBack();
      $error = "บันทึกไม่สำเร็จ: ".$e->getMessage();
    }
  }
}

include 'header.php';
?>
<h1>ชำระเงิน</h1>
<p>ยอดรวม: <b>฿<?=money(cart_total())?></b></p>

<?php if (!empty($error)): ?>
  <p style="color:#b91c1c;"><?=$error?></p>
<?php endif; ?>

<form method="post" class="row" style="max-width:640px;">
  <div style="flex:1 1 320px;">
    <label>ชื่อ-นามสกุล</label>
    <input type="text" name="name" required>
  </div>
  <div style="flex:1 1 320px;">
    <label>โทรศัพท์</label>
    <input type="text" name="phone">
  </div>
  <div style="flex:1 1 100%;">
    <label>ที่อยู่จัดส่ง</label>
    <textarea name="address" rows="4" required></textarea>
  </div>
  <div>
    <button type="submit" class="btn">ยืนยันสั่งซื้อ</button>
  </div>
</form>

<?php include 'footer.php'; ?>
