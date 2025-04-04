<?php
session_start();
// On change ici la valeur de SESSION car il y a une latence entre la destruction de la session et son effectivité. pour s'assurer qu'un utilisateur ne profite pas d'une session encore active malgré le logout, on change le contenu de la variable session.
$_SESSION["is_logged"] = "L'utilisateur n'est pas authentifié.";
session_destroy();
header("Location:login.php");
exit();