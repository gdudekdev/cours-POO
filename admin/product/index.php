<?php
// CETTE PAGE CORRESPOND AU READ DU CRUD

require_once $_SERVER["DOCUMENT_ROOT"] . "/admin/include/function.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/admin/include/protect.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/admin/include/connect.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/php/Product.class.php";

// ///////////////////////////// RECUPERATION PAGES ///////////////////////////////////////////////////////////

$total = 0;
// On voudrait n'afficher que 50 articles pour chaque page. Cela sous entend 50 lignes + un offset correspondant au numéro de la page -1
$nbPerPage = 30;
// Si rien renseigné alors c'est la première page
$currentPage = 1;
// Sinon c'est une page précisée
if (isset($_GET["page"])) {
  $currentPage = $_GET["page"];
}

$stmt = $db->prepare("SELECT count(*) AS total FROM table_product");
$stmt->execute();
if ($rowTotal = $stmt->fetch()) {
  $total = $rowTotal["total"];
}

// ///////////////////////////// RECUPERATION ARTICLES ///////////////////////////////////////////////////////////
// On ajoute les critères de recherche présent dans le formulaire pour compléter la requête

$sql = "SELECT * FROM table_product WHERE (1=1)";

$bind    = [];
$keyword = "";
if (isset($_COOKIE["search_keyword"])) {
  $keyword = $_COOKIE["search_keyword"];
}

if (! empty($keyword)) {
  $sql .= " AND product_name LIKE :keyword COLLATE utf8mb3_general_ci
            OR product_serie LIKE :keyword2 COLLATE utf8mb3_general_ci";
  $bind["keyword"]  = "%" . $keyword . "%";
  $bind["keyword2"] = "%" . $keyword . "%";
}

$sql .= " ORDER BY product_id ASC LIMIT :offset, :limit";
$stmt = $db->prepare($sql);

if (! empty($keyword)) {
  foreach ($bind as $key => $value) {
    $stmt->bindValue($key, $value);
  }
}

$stmt->bindValue(":offset", ($currentPage - 1) * $nbPerPage, PDO::PARAM_INT);
// Ici on est obligé de préciser avec PDO le type de valeur qu'on associe
$stmt->bindValue(":limit", $nbPerPage, PDO::PARAM_INT);

$stmt->execute();

$recordset = $stmt->fetchAll();
// recordset contient maintenant un tableau indexé de tableau associatif

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Produits</title>
</head>

<body>
  <h1>Liste des produits</h1>

  <form action="search.php" method="post" class="search">
    <input type="search" name="keyword" value="<?php echo hsc($keyword) ?>">
    <input type="submit" value="Rechercher">
  </form>
  <a href="search.php?reset=1" class="button">Reset</a>
  <a href="form.php" class="button">Ajouter</a>

  <table>
    <tr>

      <th>id_produit</th>
      <th>titre_produit</th>
      <th>série_produit</th>
      <th>photo_produit</th>
      <th colspan="2">Action</th>
    </tr>

    <!-- On affiche ici les informations récupéré de la base de données -->
    <?php
    foreach ($recordset as $row) {
      $product = new Product($row);
    ?>
      <tr>
        <td> <?= $product->getId() ?> </td>
        <td> <?= $product->getName() ?> </td>
        <td> <?= $product->getSerie() ?> </td>
        <td> <?= hsc($row["product_image"]); ?> </td>
        <!-- Rappel: chemin relatif s'écrit sans "/", absolu commence par un "/" -->
        <td> <a href="form.php?id=<?= hsc($row['product_id']) ?>">Modif</a> </td>
        <!-- La méthode get s'écrit avec un ? pour ajouter derrière les informations -->
        <td> <a href="delete.php?id=<?= hsc($row['product_id']) ?>">Supp</a> </td>
      </tr>
    <?php } ?>

  </table>
  <style>
    .search {
      width: 100%;
      display: flex;
      justify-content: center;
    }

    .search>* {
      margin: 0 10px;
    }


    .pagination {
      display: flex;
      justify-content: center;
      padding: 0;
      list-style: none;
    }

    .pagination>li {
      width: 25px;
    }

    table {
      margin-top: 10px;
      width: 100%;
    }

    table>tbody>tr>td,
    table>tbody>tr>th {
      padding: 10px;
      border: 1px solid;
    }

    .button {
      border: 1px solid grey;
      background-color: lightblue;
      border-radius: 5px;
      text-align: center;
      padding: 5px;
    }
  </style>


  <?php displayPagination(ceil($total / $nbPerPage), $currentPage) ?>

  <a href="index.php" class="button">Retour en haut</a>

</body>

</html>