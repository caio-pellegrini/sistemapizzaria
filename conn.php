<?php
/* Usaremos PDO para fazer a conexão com banco */ 

session_start(); /* PHP inicia uma sessão de usuário*/

$user = "root";
$pass = "";
$db = "pizzaria";
$host = "127.0.0.1";

try {
    // Código que pode lançar exceções
    $conn = new PDO("mysql:host={$host};dbname={$db}", $user, $pass);

    // habilitar os erros

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

} catch (PDOException $e) {

    print "Error: " . $e->getMessage() ."<br/>";
    die();
}