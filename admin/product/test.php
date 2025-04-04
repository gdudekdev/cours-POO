<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/include/function.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/include/protect.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/include/connect.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/php/Product.class.php";

$stmt = $db -> prepare("SELECT * FROM table_product");
$stmt->execute();

if($row = $stmt -> fetch(PDO::FETCH_ASSOC)){
      $product = new Product($row);
      var_dump($product);
}
?>