<?php

// CETTE PAGE TRAITE LES INFORMATIONS DU FORMULAIRE (form.php)

require_once $_SERVER["DOCUMENT_ROOT"] . "/admin/include/function.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/admin/include/protect.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/admin/include/connect.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/admin/include/config.php";

// On vérifier que les informations reçues viennent du bon formuaire (sécurité supplémentaire)
if (isset($_POST["formCU"]) && $_POST["formCU"] == "1234") {

  // Si l'id du formulaire est égal à zéro alors c'est un ajout dans la base de données
  if ($_POST["product_id"] == "0") {
    // On vérifier ensuite les informations du formulaire (sécurité supplémentaire)
    $stmt = $db->prepare(
      "INSERT INTO table_product (
      product_slug,
      product_name,
      product_serie,
      product_volume,
      product_description, 
      product_price,
      product_stock,
      product_publisher,
      product_author,
      product_cartoonist,
      product_resume,
      product_date,
      product_status
      ) VALUES (
      :product_slug,
      :product_name,
      :product_serie,
      :product_volume,
      :product_description, 
      :product_price,
      :product_stock,
      :product_publisher,
      :product_author,
      :product_cartoonist,
      :product_resume,
      :product_date,
      :product_status
      )"
    );
    $stmt->bindValue(":product_slug", $_POST["product_slug"]);
    $stmt->bindValue(":product_name", $_POST["product_name"]);
    $stmt->bindValue(":product_serie", $_POST["product_serie"]);
    $stmt->bindValue(":product_volume", $_POST["product_volume"]);
    $stmt->bindValue(":product_description", $_POST["product_description"]);
    $stmt->bindValue(":product_price", $_POST["product_price"]);
    $stmt->bindValue(":product_stock", $_POST["product_stock"]);
    $stmt->bindValue(":product_publisher", $_POST["product_publisher"]);
    $stmt->bindValue(":product_author", $_POST["product_author"]);
    $stmt->bindValue(":product_cartoonist", $_POST["product_cartoonist"]);
    $stmt->bindValue(":product_resume", $_POST["product_resume"]);
    $stmt->bindValue(":product_date", $_POST["product_date"]);
    $stmt->bindValue(":product_status", $_POST["product_status"]);
    // $stmt->bindValue(":product_type_id", $_POST["product_type_id"]);
    $stmt->execute();

    // On stock l'index
    $id = $db->lastInsertId();
  } else {
    // Si l'id n'est pas égal à zéro alors c'est une modification
    $stmt = $db->prepare(
      "UPDATE table_product 
      SET 
      product_id = :product_id,
      product_serie = :product_serie, 
      product_name = :product_name,
      product_slug = :product_slug,
      product_name = :product_name,
      product_serie = :product_serie,
      product_volume = :product_volume,
      product_description = :product_description,
      product_price = :product_price,
      product_stock = :product_stock,
      product_publisher = :product_publisher,
      product_author = :product_author,
      product_cartoonist = :product_cartoonist,
      product_resume = :product_resume,
      product_status = :product_status
      WHERE product_id = :product_id"
    );
    $stmt->bindValue(":product_id", $_POST["product_id"]);
    $stmt->bindValue(":product_slug", $_POST["product_slug"]);
    $stmt->bindValue(":product_name", $_POST["product_name"]);
    $stmt->bindValue(":product_serie", $_POST["product_serie"]);
    $stmt->bindValue(":product_volume", $_POST["product_volume"]);
    $stmt->bindValue(":product_description", $_POST["product_description"]);
    $stmt->bindValue(":product_price", $_POST["product_price"]);
    $stmt->bindValue(":product_stock", $_POST["product_stock"]);
    $stmt->bindValue(":product_publisher", $_POST["product_publisher"]);
    $stmt->bindValue(":product_author", $_POST["product_author"]);
    $stmt->bindValue(":product_cartoonist", $_POST["product_cartoonist"]);
    $stmt->bindValue(":product_resume", $_POST["product_resume"]);
    $stmt->bindValue(":product_date", $_POST["product_date"]);
    $stmt->bindValue(":product_status", $_POST["product_status"]);
    // $stmt->bindValue(":product_type_id", $_POST["product_type_id"]);
    $stmt->execute();

    // On stock l'index
    $id = $_POST["product_id"];
  }

  // On traite ici l'image transféré dans le formulmaire

  // On vérifier qu'une image a été envoyé
  if (isset($_FILES["product_image"])) {

    // On vérifie que le transfert de fichier ne renvoie pas d'erreur (donc que le fichier soit bien parvenu)
    if ($_FILES["product_image"]["error"] == 0) {
      $extension = strtolower(pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION));
      $pathFile = $_SERVER["DOCUMENT_ROOT"] . "/upload/";

      // On vérifie si le type de l'image correspond bien aux types accepté par notre programme (sans prendre en compte l'extension de base qui peut être fausse)
      if ($_FILES["product_image"]["type"] == "image/" . str_replace("jpg", "jpeg", $extension) && in_array($extension, ["jpg", "jpeg", "png", "gif", "webp"])) {

        // On va supprimer l'image actuellement enregistré dans le cas où il y en a une ET is on est dans le cas d'une modification
        if ($_POST["product_id"] > 0) {

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
        }


        $filename = "bdshop-" . $_POST["product_serie"] . "-" . $_POST["product_name"];
        $filename = cleanFilename($filename);


        // On boucle dans le dossier upload tant que le nom de fichier recherché est déjà pris et on veut le faire pour chaque extension de fichier (foreach)
        $is_found = false;
        $count = 1;
        while ($is_found) {
          $is_found = false;
          foreach (IMG_CONFIG as $key => $value) {
            if (file_exists($pathFile . $key . "_" . $filename . ($count > 1 ? "(" . $count . ")" : "") . ".webp")) {
              $is_found = true;
              break;
            }
          }
          $is_found ? $count++ : "";
        }
        if ($count > 1) {
          $filename .= "(" . $count . ")";
        }


        // On positionne alors le fichier avec son nouveau nom dans notre dossier
        move_uploaded_file($_FILES["product_image"]["tmp_name"], $pathFile . $filename . "." . $extension);

        // On initialise les variables qui vont changer à chque boucle pour retravailler l'image qui vient d'être créé (par souci d'optimisation)
        $srcPrefix = "";
        $srcExtension = $extension;

        // On veut ensuite créer une version de l'image pour chaque type d'extension défini, donc on exécute la suite pour chaque extension du tableau
        foreach (IMG_CONFIG as $prefix => $info) {

          // On va ensuite chercher l'image pour pouvoir la redimensionner
          $srcSize = getimagesize($pathFile . $srcPrefix . $filename . "." . $srcExtension);

          // On récupère les dimensions 
          $srcWidth = $srcSize[0];
          $srcHeight = $srcSize[1];

          // On définit les points de départ de l'image source à redimensionner et et l'image cible.
          $srcX = 0;
          $srcY = 0;

          // On définit les points de départ de l'image de destination à redimensionner et et l'image cible.
          $destX = 0;
          $destY = 0;

          // On va chercher ici dans le tableau info les largeur et hauteur relatif au préfix consulté
          $destWidth = $info["width"];
          $destHeight = $info["height"];

          // On vérifie ici si l'image a besoin d'être rogné
          if (!$info["crop"]) {

            // On vérifier si l'image est au format protrait ou paysage pour définir la contraite
            if ($srcWidth > $srcHeight) {
              $destHeight = round(($srcHeight * $destWidth) / $srcWidth);

              // On va également vérifier que l'image (EN LARGEUR) importé ne soit pas plus petite que la taille attendue, si c'est le cas on fais correspondre les dimensions de l'image de destination avec les dimensions d'origine
              if ($srcWidth <= $destWidth) {
                $destWidth = $srcWidth;
                $destHeight = $srcHeight;
              }

            } else {
              $destWidth = round(($srcWidth * $destHeight) / $srcHeight);

              // On vérifie EN LONGUEUR que l'image d'origine ne soit pas plus petite que la taille attendue
              if ($srcWidth <= $destWidth) {
                $destWidth = $srcWidth;
                $destHeight = $srcHeight;
              }
            }

            // Si la variable "crop" est sur true, alors l'image a besoin d'être rogné et une autre procédure s'applique
          } else {

            // Pour définir le point de découpe sur l'image d'origine, on cherche la largeur (ou hauteur) de l'image à supprimer et on la diviser par 2.
            // (($srcWidth - $srcHeight) / 2), 0, $srcHeight, $srcWidth

            // On vérifie de nouveau que l'image est au format paysage
            if ($srcWidth > $srcHeight) {
              $srcX = round(($srcWidth - $srcHeight) / 2);
              $srcWidth = $srcHeight;

              // Sinon elle est au format portrait
            } else {
              $srcY = round(($srcHeight - $srcWidth) / 2);
              $srcHeight = $srcWidth;
            }

          }


          // On définit une toile vide qui ne contient pas de pixel
          $dest = imagecreatetruecolor($destWidth, $destHeight);

          // Et on créé une image de l'image en concordance avec son type
          // switch ($extension) {
          //   case "png":
          //     $src = imagecreatefrompng($pathFile . $srcPrefix . $filename . "." . $srcExtension);
          //     break;
          //   case "gif":
          //     $src = imagecreatefromgif($pathFile . $srcPrefix . $filename . "." . $srcExtension);
          //     break;
          //   case "webp":
          //     $src = imagecreatefromwebp($pathFile . $srcPrefix . $filename . "." . $srcExtension);
          //     break;
          //   default:
          //     $src = imagecreatefromjpeg($pathFile . $srcPrefix . $filename . "." . $srcExtension);
          //     break;
          // }

          // OU PLUS SIMPLEMENT
          $src = ("imagecreatefrom" . str_replace("jpg", "jpeg", $srcExtension))($pathFile . $srcPrefix . $filename . "." . $srcExtension);

          // On effectue une copie de l'image uploadé
          imagecopyresampled($dest, $src, $destX, $destY, $srcX, $srcY, $destWidth, $destHeight, $srcWidth, $srcHeight);

          // Et on l'enregistre au format webp
          imagewebp($dest, $pathFile . $prefix . "_" . $filename . ".webp", 100);

          // On applique l'extension webp à l'extension à rechercher pour itentifier l'image qui vient d'être créé
          $srcExtension = "webp";

          // On ne change le préfix pour l'image suivante que si l'image qu'on vient de traiter n'est pas une image rogné.
          if (!$info["crop"]) {
            $srcPrefix = $prefix . "_";
          }

        }
      }

      if (file_exists($pathFile . $filename . "." . $extension)) {
        unlink($pathFile . $filename . "." . $extension);
      }

      // On effectue une requete pour le cas ou une image a été traité
      $stmt = $db->prepare("UPDATE table_product SET product_image = :product_image WHERE product_id = :product_id");
      // Il faut rajoute l'extension car elle n'existe pas dans le nom originel
      $stmt->bindValue(":product_image", $filename . ".webp");
      $stmt->bindValue(":product_id", $id);
      $stmt->execute();

    }
  }
  var_dump($_FILES["product_image"]);

}


redirect("index.php");