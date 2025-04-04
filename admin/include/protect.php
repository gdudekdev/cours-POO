<?php
session_start();
if (!isset($_SESSION["is_logged"]) || $_SESSION["is_logged"] != "L'utilisateur est authentifié.") {
  header("Location:/admin/login.php");
  exit();
}
;
