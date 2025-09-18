<?php
function money($n) {
  return number_format($n, 2);
}

function add_to_cart($product_id, $name, $price, $qty = 1, $image = null) {
  if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
  if (!isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id] = [
      'name' => $name,
      'price' => (float)$price,
      'qty' => 0,
      'image' => $image
    ];
  }
  $_SESSION['cart'][$product_id]['qty'] += $qty;
}

function cart_items() {
  return $_SESSION['cart'] ?? [];
}

function cart_total() {
  $sum = 0;
  foreach (cart_items() as $pid => $item) {
    $sum += $item['price'] * $item['qty'];
  }
  return $sum;
}
