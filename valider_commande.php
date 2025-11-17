<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
$user_id = (int)$_SESSION['user_id'];
$voiture_id = (int)($_POST['voiture_id'] ?? 0);
if ($voiture_id <= 0) { header('Location: index.php'); exit; }

try {
    $pdo->beginTransaction();
    // lock & check
    $stmt = $pdo->prepare("SELECT stock, disponible FROM voitures WHERE id = ? FOR UPDATE");
    $stmt->execute([$voiture_id]);
    $v = $stmt->fetch();
    if (!$v || $v['disponible']==0 || $v['stock'] <= 0) throw new Exception("Indisponible");
    // insert commande
    $pdo->prepare("INSERT INTO commandes (id_utilisateur, id_voiture, quantite) VALUES (?, ?, 1)")
        ->execute([$user_id, $voiture_id]);
    // update stock
    $pdo->prepare("UPDATE voitures SET stock = stock - 1 WHERE id = ?")->execute([$voiture_id]);
    $pdo->prepare("UPDATE voitures SET disponible = 0 WHERE id = ? AND stock <= 0")->execute([$voiture_id]);
    $pdo->commit();
    header('Location: index.php?info=commande_ok');
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    header('Location: index.php?info=commande_fail');
    exit;
}
