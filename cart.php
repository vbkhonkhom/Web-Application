<?php
require 'config.php';
require 'functions.php';

/* ─────────────────────────────
   จัดการคำขอจากฟอร์ม
   ───────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_check($_POST['csrf'] ?? '')) {
    $error = 'CSRF ไม่ถูกต้อง';
  } else {
    // 1) ลบทั้งหมด
    if (isset($_POST['clear'])) {
      $_SESSION['cart'] = [];
      $msg = 'ลบตะกร้าทั้งหมดแล้ว';
    }
    // 2) ลบรายการเดี่ยว (ปุ่มลบส่ง pid มาที่ name="remove" ผ่าน value)
    elseif (isset($_POST['remove'])) {
      $pid = (int)$_POST['remove'];
      unset($_SESSION['cart'][$pid]);
      $msg = 'ลบสินค้าออกจากตะกร้าแล้ว';
    }
    // 3) อัปเดตจำนวนหลายรายการ
    elseif (isset($_POST['update']) && !empty($_POST['qty']) && is_array($_POST['qty'])) {
      foreach ($_POST['qty'] as $pid => $q) {
        $pid = (int)$pid;
        $q   = max(0, (int)$q);
        if ($q === 0) {
          unset($_SESSION['cart'][$pid]);        // ใส่ 0 = ลบ
        } elseif (isset($_SESSION['cart'][$pid])) {
          $_SESSION['cart'][$pid]['qty'] = $q;   // อัปเดตจำนวน
        }
      }
      $msg = 'อัปเดตตะกร้าเรียบร้อย';
    }
  }
}

$items = cart_items();

include 'header.php';
?>
<h1>ตะกร้าสินค้า</h1>

<?php if (!empty($error)): ?>
  <p style="color:#b91c1c;"><?=$error?></p>
<?php elseif (!empty($msg)): ?>
  <p style="color:#16a34a;"><?=$msg?></p>
<?php endif; ?>

<?php if (!$items): ?>
  <p>ยังไม่มีสินค้าในตะกร้า</p>
  <a class="btn" href="/ecommerce/index.php">เลือกซื้อสินค้าต่อ</a>
<?php else: ?>
<form method="post">
  <?php csrf_field(); ?>
  <table class="table">
    <thead>
      <tr>
        <th>สินค้า</th>
        <th style="width:140px;">จำนวน</th>
        <th>ราคา/ชิ้น</th>
        <th>ราคารวม</th>
        <th style="width:110px;">ลบ</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($items as $pid => $it): ?>
        <tr>
          <td style="display:flex;align-items:center;gap:10px;">
            <?php if (!empty($it['image'])): ?>
              <img src="/ecommerce/<?=htmlspecialchars($it['image'])?>" alt="" style="height:36px;width:36px;object-fit:cover;border-radius:6px;">
            <?php endif; ?>
            <span><?=htmlspecialchars($it['name'])?></span>
          </td>
          <td>
            <input type="number" name="qty[<?=$pid?>]" value="<?=$it['qty']?>" min="0" style="width:100%;max-width:100px;">
          </td>
          <td>฿<?=money($it['price'])?></td>
          <td>฿<?=money($it['price'] * $it['qty'])?></td>
          <td>
            <button class="btn danger" type="submit" name="remove" value="<?=$pid?>" onclick="return confirm('ลบสินค้านี้ออกจากตะกร้า?')">ลบ</button>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <p style="text-align:right;font-weight:700;">ยอดรวม: ฿<?=money(cart_total())?></p>

  <div style="display:flex; gap:10px; justify-content:flex-end; flex-wrap:wrap;">
    <a class="btn secondary" href="/ecommerce/index.php">เลือกซื้อสินค้าต่อ</a>
    <button class="btn danger" name="clear" type="submit" onclick="return confirm('ลบสินค้าทั้งหมดออกจากตะกร้า?')">ลบทั้งหมด</button>
    <button class="btn" name="update" type="submit">อัปเดตจำนวน</button>
    <a class="btn" href="/ecommerce/checkout.php">ไปชำระเงิน</a>
  </div>
</form>
<?php endif; ?>

<?php include 'footer.php'; ?>
