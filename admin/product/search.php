<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/admin/include/function.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/admin/include/protect.php";

// Ici on vérifie si l'utilisateur n'a pas cliqué sur "reset" sur le crud. Si non alors on créé les cookies
if (isset($_GET["reset"]) && $_GET["reset"] == 1) {
  unset($_COOKIE["search_keyword"]);
  setcookie("search_keyword", "", time() - 10);
} else {
  foreach ($_POST as $key => $value) {
    // Pour définir un cookie, 3 paramètres: le nom, la valeur et la durée de vie
    setcookie("search_" . $key, $value, time() + 30 * 24 * 60 * 60);
  }
}



redirect("index.php");