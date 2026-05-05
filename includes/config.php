<?php
session_start();

$host = 'localhost';
$user = 'root';
$pass = 'root';
$db = 'ecommerce_db';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
?>