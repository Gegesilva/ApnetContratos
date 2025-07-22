<?php
include_once("config.php");

try {
    $pdo = new PDO("sqlsrv:Server=$server;Database=$base", $usuarioBanco, $SenhaBanco);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão PDO: " . $e->getMessage());
}