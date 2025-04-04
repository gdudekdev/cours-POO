<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/include/function.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/include/protect.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/include/connect.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/php/Product.class.php";

// PAGINATION

// Nombre de page via le dropdown
$defaultPerPage = 20;
$nbPerPage = filter_input(INPUT_GET, 'nbPerPage', FILTER_VALIDATE_INT) ?: $defaultPerPage;
// Page actuellement sélectionnée
$currentPage = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
$offset = ($currentPage - 1) * $nbPerPage;

// définition des paramètres nécessaires pour la pagination
$total_products_stmt = $db->prepare("SELECT COUNT(*) FROM table_product");
$total_products_stmt->execute();
$total_products = $total_products_stmt->fetch()[0];
$total_pages = max(1, ceil($total_products / $nbPerPage));

// RECHERCHE
$sql = "SELECT * FROM table_product WHERE (1=1) ";
$keyword = "";
$bind = [];

if(isset($_POST['keyword'])){
    $keyword = $_POST['keyword'];
}
if (!empty($keyword)){
    $sql .= "AND(product_name   LIKE :keyword1 COLLATE utf8mb3_general_ci
             OR  product_serie  LIKE :keyword2 COLLATE utf8mb3_general_ci
             OR  product_author LIKE :keyword3 COLLATE utf8mb3_general_ci
             OR  product_slug   LIKE :keyword4 COLLATE utf8mb3_general_ci )
            ";
    $bind[":keyword1"]='%' . $keyword . '%';
    $bind[":keyword2"]='%' . $keyword . '%';
    $bind[":keyword3"]='%' . $keyword . '%';
    $bind[":keyword4"]='%' . $keyword . '%';
}

$sql .= "ORDER BY product_id DESC LIMIT :offset, :nbPerPage";

// Requête PAGINATION + RECHERCHE
$stmt = $db->prepare($sql);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':nbPerPage', $nbPerPage, PDO::PARAM_INT);
if(!empty($keyword)){
    foreach ($bind as $key=>$value) {
        $stmt -> bindValue( $key,$value , PDO::PARAM_STR);
    }
}
$stmt->execute();
$recordset = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produits</title>
    <link rel="stylesheet" href="index.css">
</head>

<body>
    <a href="../index.php" class="add-button">Retour</a><br><br>

    <!-- Barre de recherche -->
    <form action="index.php" method="post">
        <label for="keyword"></label>
        <input type="text" name="keyword" id="keyword" value=<?= hsc($keyword);?>>
        <input type="submit" value="Rechercher">
    </form>
    <br>
    <!-- Lien de réinitialisation -->
    <a href="index.php" class="add-button">Réinitialiser</a>
    <!-- Dropdown nombre d'éléments par page -->
    <form action="index.php" method="get" class="per-page-form">
        <label for="nbPerPage">Éléments par page :</label>
        <select name="nbPerPage" id="nbPerPage" onchange="this.form.submit()">
            <?php foreach ([10, 20, 50, 100] as $value) { ?>
                <option value="<?= $value ?>" <?= $nbPerPage == $value ? 'selected' : '' ?>><?= $value ?></option>
            <?php } ?>
        </select>
    </form>
    <div class="pagination">
        <?= generatePagination($currentPage, $total_pages, $nbPerPage, $baseUrl = 'index.php', $param = "page") ?>
    </div>
    <table>
        <tr>
            <?php $columns = ["References" => "product_slug", "Date" => "product_date", "Titre" => "product_name", "Serie" => "product_serie", "Volume" => "product_volume", "Auteur" => "product_author", "Description" => "product_description", "Resume" => "product_resume", "Stock" => "product_stock", "Prix" => "product_price", "Publication" => "product_publisher", "Dessinateur" => "product_cartoonist"];
            foreach ($columns as $label => $key) { ?>
                <th><?= $label ?></th>
            <?php } ?>
            <th><a href="form.php" class="add-button">Ajouter</a></th>
        </tr>
        <?php foreach ($recordset as $row) {
            $product = new Product($row);
            ?>
            <tr>
                <?php foreach ($columns as $key) {
                    $method  = 'get' . ucfirst(str_replace('product_','',$key));
                     ?>
                    <td><?= $product->{$method}(); ?></td>
                <?php } ?>
                <td class="action-links">
                    <a href="form.php?id=<?= hsc($row['product_id']) ?>">Modif.</a>
                    <a href="delete.php?id=<?= hsc($row['product_id']) ?>">Supp.</a>
                </td>
            </tr>
        <?php } ?>
    </table>
    <div class="pagination">
        <?= generatePagination($currentPage, $total_pages, $nbPerPage) ?>
    </div>
</body>

</html>