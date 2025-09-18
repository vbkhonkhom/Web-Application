<?php require 'config.php'; require 'functions.php'; include 'header.php';

$q = trim($_GET['q'] ?? '');
$cat = $_GET['category'] ?? '';
$min = $_GET['min'] ?? '';
$max = $_GET['max'] ?? '';

$where = [];
$params = [];

if ($q !== '') { $where[] = "(p.name LIKE ? OR p.description LIKE ?)"; $params[] = "%$q%"; $params[] = "%$q%"; }
if ($cat !== '') { $where[] = "p.category_id = ?"; $params[] = (int)$cat; }
if ($min !== '' && is_numeric($min)) { $where[] = "p.price >= ?"; $params[] = (float)$min; }
if ($max !== '' && is_numeric($max)) { $where[] = "p.price <= ?"; $params[] = (float)$max; }

$sql = "
  SELECT p.*, c.name AS category_name
  FROM products p
  LEFT JOIN categories c ON c.id = p.category_id
";
if ($where) $sql .= " WHERE ".implode(" AND ", $where);
$sql .= " ORDER BY p.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$cats = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>
<h1>สินค้า</h1>

<form method="get" class="row" style="gap:12px; margin-bottom:16px;">
  <input type="text" name="q" placeholder="ค้นหาชื่อ/คำอธิบาย..." value="<?=htmlspecialchars($q)?>" style="flex:1 1 240px;">
  <select name="category" style="flex:1 1 200px;">
    <option value="">ทุกหมวดหมู่</option>
    <?php foreach ($cats as $c): ?>
      <option value="<?=$c['id']?>" <?=($cat==$c['id']?'selected':'')?>>
        <?=htmlspecialchars($c['name'])?>
      </option>
    <?php endforeach; ?>
  </select>
  <input type="text" name="min" placeholder="ราคาขั้นต่ำ" value="<?=htmlspecialchars($min)?>" style="width:140px;">
  <input type="text" name="max" placeholder="ราคาสูงสุด" value="<?=htmlspecialchars($max)?>" style="width:140px;">
  <button class="btn" type="submit">ค้นหา</button>
  <a class="btn secondary" href="index.php">ล้างตัวกรอง</a>
</form>

<div class="grid">
  <?php foreach ($products as $p): ?>
    <div class="card">
      <img src="<?=htmlspecialchars($p['image_url'] ?: 'https://picsum.photos/seed/no/600/400')?>" alt="">
      <div class="price">฿<?=money($p['price'])?></div>
      <div style="font-weight:600;"><?=htmlspecialchars($p['name'])?></div>
      <div style="color:#666; font-size:13px;"><?=htmlspecialchars($p['category_name'] ?? '-')?></div>
      <a class="btn secondary" href="product.php?id=<?=$p['id']?>">ดูรายละเอียด</a>
    </div>
  <?php endforeach; ?>
  <?php if (!$products): ?>
    <p>ไม่พบสินค้าตามเงื่อนไขที่ค้นหา</p>
  <?php endif; ?>
</div>
<?php include 'footer.php'; ?>
