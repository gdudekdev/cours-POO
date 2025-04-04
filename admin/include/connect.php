<?php 
$dsn = 'mysql:host=localhost;dbname=bdshop;charset=utf8';
$username = 'root';
$password = '';

try{
$db = new PDO($dsn, $username, $password);
}catch(Exception $e){
    die ($e->getMessage());
}

