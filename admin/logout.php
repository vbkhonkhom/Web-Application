<?php
require '../config.php';
unset($_SESSION['admin_id'], $_SESSION['admin_username']);
header('Location: /ecommerce/admin/login.php');
exit;
