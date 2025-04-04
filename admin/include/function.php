<?php
/**
 * Redirects the user to the specified path.
 *
 * This function sends an HTTP header to the client to redirect them to the given path
 * and then terminates the script execution.
 *
 * @param string $path The path to redirect to. This can be a relative or absolute URL.
 *
 * @return void
 */
function redirect($path)
{
    header('Location:' . $path);
    exit();
}

/**
 * Escapes special characters in a string for use in HTML.
 *
 * This function checks if the provided string is null. If it is, it returns an empty string.
 * Otherwise, it uses the `htmlspecialchars` function to convert special characters to HTML entities.
 *
 * @param string|null $string The input string to be escaped. If null, an empty string is returned.
 * @return string The escaped string, or an empty string if the input was null.
 */
function hsc($string)
{
    return (is_null($string) ? "" : htmlspecialchars($string));
}

/**
 * Generates pagination links.
 *
 * @param mixed $currentPage : à lire directement via method GET sur l'attribut param
 * @param mixed $total_pages : valeur calculée au préalable dans le modèle
 * @param mixed $nbPerPage : à définir par l'utilisateur;
 * @param mixed $baseUrl : l'url auquel va renvoyer les liens
 * @param mixed $param : attribut name que l'on va utiliser via GET (le même que pour currentPage)
 * @return bool|string
 */
// A PLACER DANS LE MODELE DE LA PAGE
////////////////////////////////////////////////////////////////////////////////////////////////////////////
// $defaultPerPage = 20;
// // Nombre de page via le dropdown
// $nbPerPage = filter_input(INPUT_GET, 'nbPerPage', FILTER_VALIDATE_INT) ?: $defaultPerPage;
// // Page actuellement sélectionnée
// $currentPage = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
// $offset = ($currentPage - 1) * $nbPerPage;

// // définition des paramètres nécessaires pour la pagination
// $total_products_stmt = $db->prepare("SELECT COUNT(*) FROM table_product");
// $total_products_stmt->execute();
// $total_products = $total_products_stmt->fetch()[0];
// $total_pages = max(1, ceil($total_products / $nbPerPage));

// // Requête correspondant au numéro de la page
// $stmt = $db->prepare("SELECT * FROM table_product ORDER BY product_id DESC LIMIT :offset, :nbPerPage");
// $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
// $stmt->bindValue(':nbPerPage', $nbPerPage, PDO::PARAM_INT);
// $stmt->execute();
// $recordset = $stmt->fetchAll();
////////////////////////////////////////////////////////////////////////////////////////////////////////////
function generatePagination($currentPage, $total_pages, $nbPerPage, $baseUrl = 'index.php', $param = "page")
{
    if ($currentPage < 1) {
        $currentPage = 1;
    }
    if ($currentPage > $total_pages) {
        $currentPage = $total_pages;
    }
    if ($total_pages > 1) {
        ob_start(); ?>

        <?php if ($currentPage > 1) { ?>
            <a href="<?= $baseUrl ?>?<?= $param ?>=<?= $currentPage - 1 ?>&nbPerPage=<?= $nbPerPage ?>">&laquo; Précédent</a>
        <?php } ?>

        <?php if ($currentPage > 3) { ?>
            <a href="<?= $baseUrl ?>?<?= $param ?>=1&nbPerPage=<?= $nbPerPage ?>">1</a>
            <?php if ($currentPage > 4) { ?>
                <span class='inactive'>...</span>
            <?php } ?>
        <?php } ?>

        <?php for ($i = max(1, $currentPage - 2); $i <= min($total_pages, $currentPage + 2); $i++) { ?>
            <a href="<?= $baseUrl ?>?<?= $param ?>=<?= $i ?>&nbPerPage=<?= $nbPerPage ?>"
                class="<?= $i == $currentPage ? 'active' : '' ?>"><?= $i ?></a>
        <?php } ?>

        <?php if ($currentPage < $total_pages - 2) { ?>
            <?php if ($currentPage < $total_pages - 3) { ?>
                <span class='inactive'>...</span>
            <?php } ?>
            <a href="<?= $baseUrl ?>?<?= $param ?>=<?= $total_pages ?>&nbPerPage=<?= $nbPerPage ?>"><?= $total_pages ?></a>
        <?php } ?>

        <?php if ($currentPage < $total_pages) { ?>
            <a href="<?= $baseUrl ?>?<?= $param ?>=<?= $currentPage + 1 ?>&nbPerPage=<?= $nbPerPage ?>">Suivant &raquo;</a>
        <?php } ?>
        <?php return ob_get_clean();
    } else {
        return 0;
    }
}