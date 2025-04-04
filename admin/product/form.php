<?php
// CETTE PAGE CORRESPOND AU CREATE ET AU UPDATE DU CRUD

require_once $_SERVER["DOCUMENT_ROOT"] . "/admin/include/function.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/admin/include/protect.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/admin/include/connect.php";



// Il faut démarquer le create du update. Dans le cas du create, le formulaire sera à remplir
// Dans le cas du update, le formulaire sera préremplie par la BDD

// Si il  ya un ID alors c'est une modification car il est propre à un champ en particulier.
// Si pas d'id, alors c'est un ajout car il ne dépend d'aucun autre champ.

$product_id = 0;
$product_serie = "";
$product_name = "";
$product_slug = "";
$product_volume = 0;
$product_description = "";
$product_price = 0.00;
$product_stock = 0;
$product_publisher = "";
$product_author = "";
$product_cartoonist = "";
$product_resume = "";
$product_status = 1;
$product_date = date("Y-m-d");
$product_type_id = 0;

// Vérifier l'existence de l'id n'est plus suffisant pour savoir si c'est un ajout ou une modification, on vérifier donc maintenant la valeur de l'id:
if (isset($_GET["id"]) && is_numeric($_GET["id"]) && $_GET["id"] != 0) {
  // le && ne vérifier même pas la seconde condition donc pas d'erreur, alors que and vérifier forcément toutes les conditions
  $stmt = $db->prepare("SELECT * FROM table_product WHERE product_id = :product_id");
  $stmt->bindValue(":product_id", $_GET["id"]);
  $stmt->execute();

  if ($row = $stmt->fetch()) {
    $product_slug = $row["product_slug"];
    $product_name = $row["product_name"];
    $product_serie = $row["product_serie"];
    $product_volume = $row["product_volume"];
    $product_description = $row["product_description"];
    $product_price = $row["product_price"];
    $product_stock = $row["product_stock"];
    $product_publisher = $row["product_publisher"];
    $product_author = $row["product_author"];
    $product_cartoonist = $row["product_cartoonist"];
    $product_resume = $row["product_resume"];
    $product_status = $row["product_status"];
    $product_date = $row["product_date"];
    $product_type_id = $row["product_type_id"];
  }
};


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Formulaire d'ajout</title>
</head>

<body>

  <h1>Formulaire d'ajout (create / update)</h1>

  <form action="process.php" method="post" enctype="multipart/form-data">
    <!-- L'attribut enctype="multipart/form-data" spécifier que le formulaire accepte autre chose que du texte -->

    <label for="product_slug">Référence du produit: </label>
    <input type="text" name="product_slug" id="product_slug" value="<?= $product_slug ?>" placeholder="BD00000">
    <br>

    <label for="product_name">Nom du produit: </label>
    <input type="text" name="product_name" id="product_name" value="<?= $product_name ?>">
    <br>

    <label for="product_serie">Série du produit: </label>
    <input type="text" name="product_serie" id="product_serie" value="<?= $product_serie ?>">
    <br>

    <label for="product_volume">Volume du produit: </label>
    <input type="number" name="product_volume" id="product_volume" value="<?= $product_volume ?>">
    <br>

    <label for="product_description">Description du produit: </label>
    <input type="text" name="product_description" id="product_description" value="<?= $product_description ?>">
    <br>

    <label for="product_price">Prix du produit: </label>
    <input type="number" name="product_price" id="product_price" value="<?= $product_price ?>" step="0.01">
    <br>

    <label for="product_stock">Stock du produit: </label>
    <input type="number" name="product_stock" id="product_stock" value="<?= $product_stock ?>">
    <br>

    <label for="product_publisher">Editeur du produit: </label>
    <input type="text" name="product_publisher" id="product_publisher" value="<?= $product_publisher ?>">
    <br>

    <label for="product_author">Auteur du produit: </label>
    <input type="text" name="product_author" id="product_author" value="<?= $product_author ?>">
    <br>

    <label for="product_cartoonist">Illustrateur du produit: </label>
    <input type="text" name="product_cartoonist" id="product_cartoonist" value="<?= $product_cartoonist ?>">
    <br>

    <label for="product_resume">Résumé du produit: </label>
    <input type="text" name="product_resume" id="product_resume" value="<?= $product_resume ?>">
    <br>

    <label for="product_date">Date de parution du produit: </label>
    <input type="date" name="product_date" id="product_date" value="<?= $product_date ?>">
    <br>

    <label for="product_status">Statut du produit: </label>
    <input type="text" name="product_status" id="product_status" value="<?= $product_status ?>">
    <br>

    <label for="product_status">Image: </label>
    <input type="file" name="product_image" id="product_image" value="">
    <br>


    <!-- On créé un champ caché non visible par l'utilisateur, qui servira à vérifier uniquement le produit ciblé par le formulaire: -->
    <input type="hidden" name="formCU" value="1234">
    <input type="hidden" name="product_id" value="<?= isset($_GET["id"]) ? $_GET["id"] : $product_id ?>">
    <!-- Cela sert également à vérifier que process traitera bien les information de CE formulaire et pas d'un autre -->

    <input type="submit" value="Enregistrer">
  </form>

</body>

</html>