<?php

// FONCTION DE REDIRECTION DE L UTILISATEUR
function redirect($path)
{
    header("Location:" . $path);
    // Le exit sert à stopper l'éxécution du potentiel code non désiré puisque
    exit();
}

// FONCTION DE VERIFICATION DES CARACTERES SPECIAUX DANS UN CHAMPS
function hsc($string)
{
    // Methode classique de déclaration
    // if (is_null($string)) {
    //   return "";
    // } else {
    //   return htmlspecialchars($string);
    // }

    // Methode de déclaration ternaire
    return $string === null ? "" : htmlspecialchars($string);
};

// FONCTION D AFFICHAGE DE LA PAGINATION
function displayPagination($nbPage, $currentPage, $url = "index.php", $param = "page", $limit = 10)
{
    if ($currentPage < 1) {
        $currentPage = 1;
    }

    if ($currentPage > $nbPage) {
        $currentPage = $nbPage;
    }

    if ($nbPage > 1) {
        echo "<ul class='pagination'>";
        echo "<li> <a href=\"" . $url . "\"> &lt;&lt;</a></li>";
        echo "<li> <a href=\"" . $url . ($currentPage > 2 ? "?" . $param . "=" . $currentPage - 1 : "") . "\"> &lt;</a></li>";
        $dots = true;
        for ($i = 1; $i <= $nbPage; $i++) {
            if ($nbPage <= $limit || $i <= 3 || $i >= ($nbPage - 2) || $i == $currentPage || $i == $currentPage - 1 || $i == $currentPage + 1) {
                echo "<li " . ($i == $currentPage ? "class=\"active\"" : "") . "><a href=\"" . $url . ($i > 1 ? "?" . $param . "=" . $i : "") . "\">" . $i . "</a> </li>";
                $dots = true;
            } else {
                if ($dots) {
                    $dots = false;
                    echo "<li class='inactive'>...</li>";
                }
            }
        }
        echo "<li> <a href=\"" . $url . "?" . $param . "=" . ($currentPage < $nbPage - 1 ? $currentPage + 1 : $nbPage) . "\"> &gt;</a></li>";
        echo "<li> <a href=\"" . $url . "?" . $param . "=" . $nbPage . "\"> &gt;&gt;</a></li>";
        echo "</ul>";
    }
}

// Cette fonction sert à transformer une chaine de caractère contenant des caractères non désirés en chaine de caractère standardisés
function cleanFilename($str)
{
    $result = strtolower($str);
    $charKo = ["à", "â", "è", "é", "ê", "@", " ", "\\", ","];
    $charOk = ["a", "a", "e", "e", "e", "-", "-", "", ""];

    $result = str_replace($charKo, $charOk, $result);

    return trim($result, "-");
}

// FONCTION DE CREATION D IMAGE
function createIMG($filename, $extension)
{
    $filename = cleanFilename($filename);

    $pathFile = $_SERVER["DOCUMENT_ROOT"] . "/upload/";

    // On boucle dans le dossier upload tant que le nom de fichier recherché est déjà pris et on veut le faire pour chaque extension de fichier (foreach)
    $is_found = false;
    $count    = 1;
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

    // On initialise les variables qui vont changer à chaque boucle pour retravailler l'image qui vient d'être créé (par souci d'optimisation)
    $srcPrefix    = "";
    $srcExtension = $extension;

    // On veut ensuite créer une version de l'image pour chaque type d'extension défini, donc on exécute la suite pour chaque extension du tableau
    foreach (IMG_CONFIG as $prefix => $info) {

        // On va ensuite chercher l'image pour pouvoir la redimensionner
        $srcSize = getimagesize($pathFile . $srcPrefix . $filename . "." . $srcExtension);

        // On récupère les dimensions
        $srcWidth  = $srcSize[0];
        $srcHeight = $srcSize[1];

        // On définit les points de départ de l'image source à redimensionner et et l'image cible.
        $srcX = 0;
        $srcY = 0;

        // On définit les points de départ de l'image de destination à redimensionner et et l'image cible.
        $destX = 0;
        $destY = 0;

        // On va chercher ici dans le tableau info les largeur et hauteur relatif au préfix consulté
        $destWidth  = $info["width"];
        $destHeight = $info["height"];

        // On vérifie ici si l'image a besoin d'être rogné
        if (! $info["crop"]) {

            // On vérifier si l'image est au format protrait ou paysage pour définir la contraite
            if ($srcWidth > $srcHeight) {
                $destHeight = round(($srcHeight * $destWidth) / $srcWidth);

                // On va également vérifier que l'image (EN LARGEUR) importé ne soit pas plus petite que la taille attendue, si c'est le cas on fais correspondre les dimensions de l'image de destination avec les dimensions d'origine
                if ($srcWidth <= $destWidth) {
                    $destWidth  = $srcWidth;
                    $destHeight = $srcHeight;
                }
            } else {
                $destWidth = round(($srcWidth * $destHeight) / $srcHeight);

                // On vérifie EN LONGUEUR que l'image d'origine ne soit pas plus petite que la taille attendue
                if ($srcWidth <= $destWidth) {
                    $destWidth  = $srcWidth;
                    $destHeight = $srcHeight;
                }
            }

            // Si la variable "crop" est sur true, alors l'image a besoin d'être rogné et une autre procédure s'applique
        } else {

            // Pour définir le point de découpe sur l'image d'origine, on cherche la largeur (ou hauteur) de l'image à supprimer et on la diviser par 2.
            // (($srcWidth - $srcHeight) / 2), 0, $srcHeight, $srcWidth

            // On vérifie de nouveau que l'image est au format paysage
            if ($srcWidth > $srcHeight) {
                $srcX     = round(($srcWidth - $srcHeight) / 2);
                $srcWidth = $srcHeight;

                // Sinon elle est au format portrait
            } else {
                $srcY      = round(($srcHeight - $srcWidth) / 2);
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

        // On applique l'extension webp à l'extension à rechercher pour identifier l'image qui vient d'être créé
        $srcExtension = "webp";

        // On ne change le préfix pour l'image suivante que si l'image qu'on vient de traiter n'est pas une image rogné.
        if (! $info["crop"]) {
            $srcPrefix = $prefix . "_";
        }
    }
}

// spl_autoload_register("loadClass");

// function loadClass($class)
// {
//     var_dump($class);
//     exit();
// }
