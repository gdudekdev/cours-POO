<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/include/function.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/admin/include/connect.php";

if (isset($_POST["admin_mail"]) && isset($_POST["admin_password"])) {
    $stmt = $db->prepare("SELECT * FROM table_admin WHERE admin_mail=:admin_mail");
    $stmt->execute([":admin_mail" => $_POST["admin_mail"]]);

    if ($row = $stmt->fetch()) {
        if (password_verify($_POST["admin_password"], $row["admin_password"])) {
            session_start();
            $_SESSION['is_logged'] = true; // Assurez-vous d'utiliser la même clé de session
            redirect("index.php");
        } else {
            echo "Identifiant ou mot de passe incorrect";
        }
    } else {
        echo "Identifiant ou mot de passe incorrect";
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
    <fieldset>
        <legend>Connexion Admin</legend>
        <form action="login.php" method="post">
            <label for="email">Email:</label><br>
            <input id="email" type="email" name="admin_mail" required>
            <br>
            <label for="password">Mot de passe:</label><br>
            <input id="password" type="password" name="admin_password" required>
            <br><br>
            <input type="submit" value="Se connecter">
        </form>
    </fieldset>
</body>

</html>