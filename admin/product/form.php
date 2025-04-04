<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/include/function.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/include/protect.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/include/connect.php";

// Initialisation du formulaire (nécessaire pour que l'on puisse ajouter un produit et le modifier avec le même formulaire)
$product_serie = "";
$product_name = "";
$product_id = 0;
$product_date = date("Y-m-d");
$product_volume = "";
$product_author = "";
$product_description = "";
$product_resume = "";
$product_stock = "";
$product_price = "";
$product_publisher = "";
$product_cartoonist = "";
$product_slug = "";
$product_image = "";

if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
    $stmt = $db->prepare("SELECT * FROM table_product WHERE product_id = :product_id");
    $stmt->bindValue(":product_id", $_GET["id"]);
    $stmt->execute();

    if ($row = $stmt->fetch()) {
        $product_date = $row["product_date"];
        $product_serie = $row["product_serie"];
        $product_name = $row["product_name"];
        $product_id = $row["product_id"];
        $product_volume = $row["product_volume"];
        $product_author = $row["product_author"];
        $product_description = $row["product_description"];
        $product_resume = $row["product_resume"];
        $product_stock = $row["product_stock"];
        $product_price = $row["product_price"];
        $product_publisher = $row["product_publisher"];
        $product_cartoonist = $row["product_cartoonist"];
        $product_slug = $row["product_slug"];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire Produit</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        form {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            margin-top: 200px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            background-color: hsl(279, 28.10%, 49.60%);
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: hsl(279, 27.10%, 39.80%);
        }

        a {
            display: inline-block;
            margin-top: 20px;
            color: #333;
            text-decoration: none;
            font-size: 14px;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <form action="process.php" method="post" enctype="multipart/form-data">
        <label for="product_serie">Serie</label>
        <input type="text" name="product_serie" id="product_serie" value="<?= hsc($product_serie) ?>">

        <label for="product_name">Titre</label>
        <input type="text" name="product_name" id="product_name" value="<?= hsc($product_name) ?>">

        <label for="product_volume">Volume</label>
        <input type="text" name="product_volume" id="product_volume" value="<?= hsc($product_volume) ?>">

        <label for="product_author">Auteur</label>
        <input type="text" name="product_author" id="product_author" value="<?= hsc($product_author) ?>">

        <label for="product_description">Description</label>
        <input type="text" name="product_description" id="product_description" value="<?= hsc($product_description) ?>">

        <label for="product_resume">Resume</label>
        <input type="text" name="product_resume" id="product_resume" value="<?= hsc($product_resume) ?>">

        <label for="product_stock">Stock</label>
        <input type="number" name="product_stock" id="product_stock" value="<?= hsc($product_stock) ?>">

        <label for="product_price">Prix</label>
        <input type="number" name="product_price" id="product_price" step="0.01" value="<?= hsc($product_price) ?>">

        <label for="product_date">Date</label>
        <input type="date" name="product_date" id="product_date" value="<?= hsc($product_date) ?>">

        <label for="product_publisher">Publication</label>
        <input type="text" name="product_publisher" id="product_publisher" value="<?= hsc($product_publisher) ?>">

        <label for="product_cartoonist">Dessinateur</label>
        <input type="text" name="product_cartoonist" id="product_cartoonist" value="<?= hsc($product_cartoonist) ?>">

        <label for="product_slug">Références</label>
        <input type="text" name="product_slug" id="product_slug" value="<?= hsc($product_slug) ?>">

        <label for="product_image">Image</label>
        <input type="file" name="product_image" id="product_image" value="<?= hsc($product_image) ?>" accept="image/*">

        <?php if ($product_id != 0){ ?>
            <label>
                Suppression des images présentes
                <input type="checkbox" name="image_erase" checked>
            </label>
        <?php } ?>



        <input type="hidden" name="product_id" value="<?= hsc($product_id) ?>">
        <input type="hidden" name="formCU" value="ok">
        <input type="submit" value="Enregistrer">

        <a href="index.php">Retour</a>
    </form>
</body>

</html>