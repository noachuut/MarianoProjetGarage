<?php
require 'db.php';

try {
    $pdo = new PDO("mysql:host=localhost;dbname=garage;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

$id = intval($_GET['id']);

// Réduit le stock de 1
$pdo->query("UPDATE voitures SET stock = stock - 1 WHERE id = $id AND stock > 0");

// Si stock <= 0 → désactive la voiture
$pdo->query("UPDATE voitures SET disponible = 0 WHERE stock <= 0");

header("Location: index.php?achat=ok");
exit;
?>
