<?php
session_start();
require 'db.php';
// Vérifier si le client est connecté
if (!isset($_SESSION['client'])) {
    header("Location: login.php");
    exit();
}

$pdo = new PDO('mysql:host=localhost;dbname=garage', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Récupération des infos du client
$requete = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = :id");
$requete->execute([':id' => $_SESSION['client']]);
$client = $requete->fetch(PDO::FETCH_ASSOC);

if (!$client) {
    echo "Erreur : utilisateur introuvable.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon compte</title>
</head>
<body>
    <h1>Bienvenue, <?php echo htmlspecialchars($client['nom']); ?> !</h1>

    <p>Vous êtes connecté en tant que <strong>client</strong>.</p>

    <h2>Actions disponibles :</h2>

    <ul>
        <li><a href="panier.php">🛒 Voir mon panier</a></li>
        <li><a href="commandes.php">📦 Voir mes commandes</a></li>
        <li><a href="modifier_infos.php">✏ Modifier mes informations</a></li>
        <li><a href="logout.php">🚪 Se déconnecter</a></li>
    </ul>

</body>
</html>
