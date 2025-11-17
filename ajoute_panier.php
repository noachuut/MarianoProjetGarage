<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$voiture_id = (int)($_POST['voiture_id'] ?? 0);
$user_id = (int)$_SESSION['user_id'];

if ($voiture_id > 0) {
    // vérifier si déjà dans panier -> incrémenter sinon insérer
    $stmt = $pdo->prepare("SELECT id, quantite FROM panier WHERE id_utilisateur = ? AND id_voiture = ?");
    $stmt->execute([$user_id, $voiture_id]);
    $row = $stmt->fetch();
    if ($row) {
        $pdo->prepare("UPDATE panier SET quantite = quantite + 1 WHERE id = ?")->execute([$row['id']]);
    } else {
        $pdo->prepare("INSERT INTO panier (id_utilisateur, id_voiture, quantite) VALUES (?, ?, 1)")
            ->execute([$user_id, $voiture_id]);
    }
}
header('Location: index.php?info=panier_ok');
exit;
