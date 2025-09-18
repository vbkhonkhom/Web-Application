<?php
require 'config.php';
$id = (int)($_GET['id'] ?? 0);
include 'header.php';
?>
<h1>สั่งซื้อสำเร็จ</h1>
<p>ขอบคุณสำหรับการสั่งซื้อ หมายเลขคำสั่งซื้อของคุณคือ <b>#<?=$id?></b></p>
<a class="btn" href="index.php">กลับไปหน้าสินค้า</a>
<?php include 'footer.php'; ?>
