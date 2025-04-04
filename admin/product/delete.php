<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/include/function.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/include/protect.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/include/connect.php";


if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $stmt = $db->prepare("DELETE FROM table_product WHERE product_id = :product_id");
    $stmt->execute(["product_id" => $_GET['id']]);
}
redirect("index.php");
