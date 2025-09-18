<?php
require 'config.php'; require 'functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$p = $stmt->fetch();
if (!$p) { die("ไม่พบสินค้า"); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $qty = max(1, (int)($_POST['qty'] ?? 1));
  add_to_cart($p['id'], $p['name'], $p['price'], $qty, $p['image_url']);
  header("Location: cart.php");
  exit;
}

include 'header.php';
?>
<div class="row">
  <div style="flex:1 1 320px;">
    <img src="<?=htmlspecialchars($p['image_url'])?>" alt="" style="width:100%;border-radius:12px;">
  </div>
  <div style="flex:1 1 320px;">
    <h1><?=htmlspecialchars($p['name'])?></h1>
    <p class="price">฿<?=money($p['price'])?></p>
    <p><?=nl2br(htmlspecialchars($p['description']))?></p>
    <form method="post">
      <label>จำนวน</label>
      <input type="text" name="qty" value="1" style="width:120px;">
      <br><br>
      <button type="submit" class="btn">เพิ่มลงตะกร้า</button>
    </form>
  </div>
</div>
<?php include 'footer.php'; ?>
