<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <title>My Shop</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="/ecommerce/styles.css" rel="stylesheet">
</head>
<body>
<header class="container header">
  <a href="/ecommerce/index.php" class="brand">
    <img src="/ecommerce/assets/logo.svg" alt="logo" height="28">
    <span>My Shop</span>
  </a>
  <nav class="nav">
    <a href="/ecommerce/index.php">🛍️ สินค้า</a>
    <a href="/ecommerce/cart.php">🛒 ตะกร้า
      <?php
        $count = 0;
        if (!empty($_SESSION['cart'])) {
          foreach ($_SESSION['cart'] as $c) $count += $c['qty'];
        }
        if ($count > 0) echo "<span class='badge'>$count</span>";
      ?>
    </a>
    <a href="/ecommerce/admin/">แอดมิน</a>
  </nav>
</header>
<main class="container">
