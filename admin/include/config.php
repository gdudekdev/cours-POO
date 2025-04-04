<?php

// lg 1200 * 900
// md 800 * 600
// sm 200 * 200
// xs 200 * 150

// On utilise ici la fonction define pour rentre une variable constante et donc globale pour le reste du projet.
// IL EST IMPERATIF DE LES TRIER DANS L ORDRE DECROISSANT pour faire fonctionner correctement le redimensionnement d'image.
define("IMG_CONFIG", [
  "lg" => ["width" => 1200, "height" => 900, "crop" => false],
  "md" => ["width" => 800, "height" => 600, "crop" => false],
  "sm" => ["width" => 200, "height" => 200, "crop" => true],
  "xs" => ["width" => 200, "height" => 150, "crop" => false]
]);

function test()
{
  //  on pourrait ici utiliser global pour accéder à la variable size, mais cette méthode est trop général
  // global $SIZE;
  var_dump(IMG_CONFIG);
}
