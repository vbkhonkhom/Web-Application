<?php
require 'config.php'; require 'functions.php';

if (isset($_POST['update'])) {
  // อัปเดตจำนวน
  foreach ($_POST['qty'] as $pid => $q) {
    $q = max(0, (int)$q);
    if ($q === 0) unset($_SESSION['cart'][$pid]);
    else $_SESSION['cart'][$pid]['qty'] = $q;
  }
}

$items = cart_items();
include 'header.php';
?>
<h1>ตะกร้าสินค้า</h1>

<?php if (!$items): ?>
  <p>ยังไม่มีสินค้าในตะกร้า</p>
<?php else: ?>
<form method="post">
  <table class="table">
    <thead>
      <tr>
        <th>สินค้า</th><th>จำนวน</th><th>ราคา/ชิ้น</th><th>ราคารวม</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($items as $pid => $it): ?>
        <tr>
          <td><?=htmlspecialchars($it['name'])?></td>
          <td><input type="text" name="qty[<?=$pid?>]" value="<?=$it['qty']?>" style="width:80px"></td>
          <td>฿<?=money($it['price'])?></td>
          <td>฿<?=money($it['price'] * $it['qty'])?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <p style="text-align:right;font-weight:700;">ยอดรวม: ฿<?=money(cart_total())?></p>
  <div style="display:flex; gap:10px; justify-content:flex-end;">
    <button type="submit" name="update" class="btn secondary">อัปเดตจำนวน</button>
    <a class="btn" href="checkout.php">ไปชำระเงิน</a>
  </div>
</form>
<?php endif; ?>

<?php include 'footer.php'; ?>
