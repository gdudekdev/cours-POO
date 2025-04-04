<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/admin/include/function.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "/admin/include/connect.php";
// Ici pas besoin du fichier protect sinon login vérifier une auhentification qui n'existe pas

// On veut ensuite comparer les données entré par l'utilisateur
if (isset($_POST["admin_mail"]) && isset($_POST["admin_password"])) {
  $stmt = $db->prepare("SELECT admin_password FROM table_admin WHERE admin_mail = :admin_mail");
  $stmt->bindValue(":admin_mail", $_POST["admin_mail"]);
  // BindValue rattache une valeur au marqueur.
  // BindParam rattache une variable au marqueur.
  // Cette fonction va vérifier que la valeur des paramètres ne contient pas de requêtes SQL.
  $stmt->execute();

  if ($row = $stmt->fetch()) {
    if (password_verify($_POST["admin_password"], $row["admin_password"])) {
      session_start();
      $_SESSION["is_logged"] = "L'utilisateur est authentifié.";
      // Ici, à terme, plutôt qu'une chaine de caractère écrite à la main, on  va préférer incorporer un token d'authentification.
      redirect("index.php");
    } else {
      echo "login ou mot de passe incorrect";
    }
  } else {
    echo "login ou mot de passe incorrect";
  }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
</head>

<body>

  <!-- On veut s'assurer que l'utilisateur PEUT s'authentifier:
  - Quel est son login
  - Quel est son mot de passe
  On vérifier ça dans la VUE en HTML -->
  <form action="login.php" method="POST">

    <label for="mail">Mail:</label>
    <input type="text" name="admin_mail" id="mail" placeholder="mail">
    <br>
    <label for="password">Mot de passe:</label>
    <input type="password" name="admin_password" id="password" placeholder="mot de passe">
    <br>
    <input type="submit" value="Se connecter">

  </form>

</body>

</html>