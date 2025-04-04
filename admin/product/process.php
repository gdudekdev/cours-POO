<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/include/function.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/include/protect.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/include/connect.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/include/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/include/image_upload.php";

if (isset($_POST["formCU"]) && $_POST["formCU"] == "ok") {
    $fields = [
        "product_serie",
        "product_name",
        "product_date",
        "product_volume",
        "product_author",
        "product_description",
        "product_resume",
        "product_stock",
        "product_price",
        "product_publisher",
        "product_cartoonist",
        "product_slug"
    ];

    $queryValues = array_map(fn($field) => ":$field", $fields);
    $querySet = implode(", ", array_map(fn($field) => "$field = :$field", $fields));
    
    if ($_POST["product_id"] == 0) {
        $stmt = $db->prepare("INSERT INTO table_product (" . implode(", ", $fields) . ") VALUES (" . implode(", ", $queryValues) . ")");
    } else {
        $stmt = $db->prepare("UPDATE table_product SET $querySet WHERE product_id = :product_id");
        $stmt->bindValue(":product_id", $_POST["product_id"], PDO::PARAM_INT);
    }
    
    foreach ($fields as $field) {
        $stmt->bindValue(":$field", $_POST[$field]);
    }
    
    $stmt->execute();
    
    // 🔹 Récupérer le bon ID après l'exécution de la requête
    if ($_POST["product_id"] == 0) {
        $id = $db->lastInsertId();
    } else {
        $id = $_POST['product_id'];
    }
    
    // 🔹 Vérifier que l’ID est bien défini avant l'upload
    if (!empty($id) && isset($_FILES['product_image'])) {
        $erase=false;
        if(isset($_POST['image_erase']))$erase=true;
        uploadProductImage($_FILES, $_POST, $id, $db ,$erase);
    }
    
}
redirect("index.php");