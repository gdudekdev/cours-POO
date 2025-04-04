<?php
/**
 *  Cleans a string to make it a valid filename.
 * 
 * @param mixed $str
 */
function cleanFilename($str)
{
      $result = strtolower($str);

      $charKo = [' ', '/', '\\', '?', '%', '*', ':', '|', '"', '<', '>', '.', 'é', 'è', 'ê', 'à', 'ç', 'ù', 'ô', 'î', 'ï', 'â', 'ä', 'ë', 'ü', 'û', 'ÿ', 'œ', '€'];
      $charOk = ['-', '-', '-', '', '', '', '', '', '', '', '', '', 'e', 'e', 'e', 'a', 'c', 'u', 'o', 'i', 'i', 'a', 'a', 'e', 'u', 'u', 'y', 'oe', 'euro'];

      $result = str_replace($charKo, $charOk, $result);

      return trim($result, "_");
}

function uploadProductImage($file, $postData, $id, $db ,$erase=true)
{
      if (!isset($file['product_image'])) {
            return "Aucune image reçue";
      }

      $path = $_SERVER['DOCUMENT_ROOT'] . "/upload/";
      if ($file['product_image']['error'] != 0) {
            return "Erreur lors de l'upload de l'image";
      }

      $extension = strtolower(pathinfo($file['product_image']['name'], PATHINFO_EXTENSION));
      if (!validateImage($file['product_image'], $extension)) {
            return "L'extension de l'image n'est pas valide";
      }

      if($erase)removeExistingProductImages($file, $postData, $id, $db);
      $filename = cleanFilename("bdshop_" . $postData["product_serie"] . "_" . $postData["product_name"]);
      $filename = resolveFilenameConflict($path, $filename);

      move_uploaded_file($file['product_image']['tmp_name'], $path . $filename . "." . $extension);
      processImage($path, $filename, $extension);

      try {
            $stmt = $db->prepare("UPDATE table_product SET product_image = :product_image WHERE product_id = :product_id");
            $stmt->bindValue(":product_image", $filename . ".webp");
            $stmt->bindValue(":product_id", $id, PDO::PARAM_INT);
            $stmt->execute();
      } catch (PDOException $e) {
            return "Erreur SQL : " . $e->getMessage();
      }

      return "Image uploadée et traitée avec succès";
}

function validateImage($image, $extension)
{
      return ("image/" . str_replace("jpg", "jpeg", $extension) === $image['type']) && in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
}

function resolveFilenameConflict($path, $filename)
{
      $is_file_search = true;
      $count = 1;
      while ($is_file_search) {
            $is_file_search = false;
            foreach (IMG_CONFIG as $key => $value) {
                  if (file_exists($path . $key . "_" . $filename . ($count > 1 ? "(" . $count . ")" : "") . ".webp")) {
                        $is_file_search = true;
                        break;
                  }
            }
            if ($is_file_search)
                  $count++;
      }
      return $count > 1 ? $filename . "(" . $count . ")" : $filename;
}

function processImage($path, $filename, $extension)
{
      foreach (IMG_CONFIG as $prefix => $info) {
            $filePath = $path . $filename . "." . $extension;
            list($srcWidth, $srcHeight) = getimagesize($filePath);

            $destWidth = $info['width'];
            $destHeight = $info['height'];
            $ratioSrc = $srcWidth / $srcHeight;
            $ratioDest = $destWidth / $destHeight;

            if ($ratioSrc > $ratioDest) {
                  $newHeight = $srcHeight;
                  $newWidth = round($srcHeight * $ratioDest);
                  $srcX = round(($srcWidth - $newWidth) / 2);
                  $srcY = 0;
            } else {
                  $newWidth = $srcWidth;
                  $newHeight = round($srcWidth / $ratioDest);
                  $srcX = 0;
                  $srcY = round(($srcHeight - $newHeight) / 2);
            }

            $dest = imagecreatetruecolor($destWidth, $destHeight);
            $imagecreatefrom = "imagecreatefrom" . str_replace("jpg", "jpeg", $extension);
            $src = $imagecreatefrom($filePath);

            imagecopyresampled($dest, $src, 0, 0, $srcX, $srcY, $destWidth, $destHeight, $newWidth, $newHeight);
            imagewebp($dest, $path . $prefix . "_" . $filename . ".webp", 100);

            imagedestroy($dest);
            imagedestroy($src);
      }

      if (file_exists($filePath)) {
            unlink($filePath);
      }
}
function removeExistingProductImages($file, $postData, $db, $id)
{
    if ($id == 0) {
        return "Pas d'images à nettoyer";
    }
    try {
        $stmt = $db->prepare("SELECT product_image FROM table_product WHERE product_id = :product_id AND 
                                                                    product_image IS NOT NULL AND
                                                                    product_image <> ''");
        $stmt->bindValue(":product_id", $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($row = $stmt->fetch()) {
            $filename = pathinfo($row['product_image'], PATHINFO_FILENAME);
            $path = $_SERVER['DOCUMENT_ROOT'] . "/upload/";

            foreach (IMG_CONFIG as $prefix => $config) {
                $filePath = $path . $prefix . "_" . $filename . ".webp"; 
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $updateStmt = $db->prepare("UPDATE table_product SET product_image = NULL WHERE product_id = :product_id");
            $updateStmt->bindValue(":product_id", $id, PDO::PARAM_INT);
            $updateStmt->execute();

            return "Les images ont été nettoyées avec succès";
        } else {
            return "Aucune image à nettoyer";
        }
    } catch (PDOException $e) {
        return "Erreur SQL : " . $e->getMessage();
    }
}
