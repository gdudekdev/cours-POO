<?php
// CETTE PAGE CORRESPOND AU DELETE DU CRUD

require_once $_SERVER["DOCUMENT_ROOT"] . "/admin/include/function.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/admin/include/protect.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/admin/include/connect.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/admin/include/config.php";


// Ici on va récupérer l'id de l'élément delete sur notre formulaire pour identifier quel champ on doit supprimer

// Dans le cas de profils admin différents, il faudra ajouter une condition supplémentaire pour vérifier le status de l'utilisateur
if (isset($_GET["id"]) && is_numeric($_GET["id"])) {

  $id = $_GET["id"];
  $pathFile = $_SERVER["DOCUMENT_ROOT"] . "/upload/";

  // On interrroge le serveur pour savoir si il y a un enregistrement pour product_image associé à l'id
  $stmt = $db->prepare("SELECT product_image FROM table_product WHERE product_id = :product_id AND product_image IS NOT NULL AND product_image <> ''");
  $stmt->bindValue(":product_id", $id);
  $stmt->execute();

  // On vérifie donc qu'on a bien récupéré une chaine de caractère
  if ($row = $stmt->fetch()) {

    // On parcours ensuite notre confugration pour savoir combien de phpto doivent être supprimés
    foreach (IMG_CONFIG as $prefix => $value) {

      if (file_exists($pathFile . $prefix . "_" . $row["product_image"])) {
        unlink($pathFile . $prefix . "_" . $row["product_image"]);
      }
    }
  }

  // Il faudra également gérer le cas où on veut simplement supprimer l'image sans la remplacer

  // Il faudra créer en javascript, une fenêtre contextuelle de validation de la demnade de suppression car la suppression est actuellement trop brusque
  $stmt = $db->prepare("DELETE FROM table_product WHERE product_id=:product_id");
  // On associe ensutie la valeur de id à product_id
  $stmt->execute([":product_id" => $_GET["id"]]);
}

redirect("index.php");
