<?php
// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=garage', 'root', '');

// Étape 1 : Ajouter la colonne `immatriculation` si elle n'existe pas encore
$pdo->exec("ALTER TABLE voitures ADD COLUMN IF NOT EXISTS immatriculation VARCHAR(20)");

// Étape 2 : Récupérer les voitures sans immatriculation
$stmt = $pdo->query("SELECT id FROM voitures WHERE immatriculation IS NULL OR immatriculation = ''");
$voitures = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Étape 3 : Mettre à jour avec des valeurs uniques temporaires
foreach ($voitures as $voiture) {
    $immatriculation = 'TMP-' . $voiture['id']; // Générer une immatriculation unique
    $update = $pdo->prepare("UPDATE voitures SET immatriculation = :immatriculation WHERE id = :id");
    $update->execute([':immatriculation' => $immatriculation, ':id' => $voiture['id']]);
}

echo "Mise à jour des immatriculations terminée !";

// Étape 4 : Modifier la colonne pour la rendre UNIQUE et NOT NULL
$pdo->exec("ALTER TABLE voitures MODIFY immatriculation VARCHAR(20) NOT NULL UNIQUE");

echo "La colonne 'immatriculation' est maintenant UNIQUE et NOT NULL.";
?>
