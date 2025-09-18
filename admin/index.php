<?php require '../config.php'; require 'guard.php'; include '../header.php';

echo "<p>Hello <b>".htmlspecialchars($_SESSION['admin_username'])."</b> | <a href='/ecommerce/admin/logout.php'>ออกจากระบบ</a></p>";

$products = $pdo->query("
  SELECT p.*, c.name AS category_name
  FROM products p
  LEFT JOIN categories c ON c.id = p.category_id
  ORDER BY p.id DESC
")->fetchAll();
?>
<h1>แอดมิน: รายการสินค้า</h1>
<p>
  <a class="btn" href="add_product.php">+ เพิ่มสินค้า</a>
  <a class="btn secondary" href="categories.php">จัดการหมวดหมู่</a>
</p>
<table class="table">
  <thead>
    <tr><th>ID</th><th>ชื่อ</th><th>หมวดหมู่</th><th>ราคา</th><th>ภาพ</th></tr>
  </thead>
  <tbody>
  <?php foreach ($products as $p): ?>
    <tr>
      <td><?=$p['id']?></td>
      <td><?=htmlspecialchars($p['name'])?></td>
      <td><?=htmlspecialchars($p['category_name'] ?? '-')?></td>
      <td>฿<?=number_format($p['price'],2)?></td>
      <td><img src="<?=htmlspecialchars($p['image_url'])?>" style="height:40px"></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php include '../footer.php'; ?>
