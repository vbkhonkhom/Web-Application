<?php
require '../config.php'; require 'guard.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); die('Method Not Allowed'); }
if (!csrf_check($_POST['csrf'] ?? '')) { die('CSRF ไม่ถูกต้อง'); }

$id = (int)($_POST['id'] ?? 0);
$stmt = $pdo->prepare("SELECT image_url FROM products WHERE id=?");
$stmt->execute([$id]);
$p = $stmt->fetch();
if (!$p) { die('ไม่พบสินค้า'); }

// ลบสินค้า
$del = $pdo->prepare("DELETE FROM products WHERE id=?");
$del->execute([$id]);

// ลบไฟล์รูปถ้ามาจาก uploads
if (!empty($p['image_url']) && str_starts_with($p['image_url'], 'uploads/')) {
  $path = __DIR__.'/../'.$p['image_url'];
  if (is_file($path)) @unlink($path);
}

header('Location: /ecommerce/admin/');
exit;
