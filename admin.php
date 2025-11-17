<?php
// Connexion à la base de données
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "garage";
$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// --- AJOUT D'UNE VOITURE ---
if (isset($_POST['ajouter'])) {
    $marque = $_POST['marque'];
    $modele = $_POST['modele'];
    $annee = $_POST['annee'];
    $prix = $_POST['prix'];
    $kilometrage = $_POST['kilometrage'];
    $description = $_POST['description'];
    $disponible = isset($_POST['disponible']) ? 1 : 0;

    // Gestion de l'image
    $image = "";
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        $image = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    }

    $sql = "INSERT INTO voitures (marque, modele, annee, prix, kilometrage, description, image, disponible)
            VALUES ('$marque', '$modele', '$annee', '$prix', '$kilometrage', '$description', '$image', '$disponible')";
    $conn->query($sql);
    header("Location: admin.php");
    exit;
}

// --- SUPPRESSION D'UNE VOITURE ---
if (isset($_GET['supprimer'])) {
    $id = $_GET['supprimer'];
    $conn->query("DELETE FROM voitures WHERE id=$id");
    header("Location: admin.php");
    exit;
}

// --- MODIFICATION D'UNE VOITURE ---
if (isset($_POST['modifier'])) {
    $id = $_POST['id'];
    $marque = $_POST['marque'];
    $modele = $_POST['modele'];
    $annee = $_POST['annee'];
    $prix = $_POST['prix'];
    $kilometrage = $_POST['kilometrage'];
    $description = $_POST['description'];
    $disponible = isset($_POST['disponible']) ? 1 : 0;

    // Si nouvelle image, on remplace
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        $image = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        $conn->query("UPDATE voitures SET image='$image' WHERE id=$id");
    }

    $sql = "UPDATE voitures SET 
            marque='$marque', modele='$modele', annee='$annee',
            prix='$prix', kilometrage='$kilometrage',
            description='$description', disponible='$disponible'
            WHERE id=$id";
    $conn->query($sql);
    header("Location: admin.php");
    exit;
}

// --- RÉCUPÉRATION DES VOITURES ---
$result = $conn->query("SELECT * FROM voitures ORDER BY date_ajout DESC");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Gestion des Voitures</title>
    <style>
        body { font-family: Arial; margin: 40px; background-color: #f4f4f4; }
        h1 { text-align: center; color: #333; }
        form { background: white; padding: 20px; border-radius: 10px; margin-bottom: 40px; }
        input, textarea { width: 100%; margin: 5px 0; padding: 8px; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: center; }
        img { max-width: 120px; }
        button { background-color: #007bff; color: white; border: none; padding: 10px; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        .danger { background-color: #dc3545; }
        .danger:hover { background-color: #b02a37; }
    </style>
</head>
<body>

<h1>Gestion des Voitures</h1>

<!-- Formulaire d'ajout -->
<h2>Ajouter une voiture</h2>
<form method="POST" enctype="multipart/form-data">
    <input type="text" name="marque" placeholder="Marque" required>
    <input type="text" name="modele" placeholder="Modèle" required>
    <input type="number" name="annee" placeholder="Année" required>
    <input type="number" name="prix" placeholder="Prix (€)" required>
    <input type="number" name="kilometrage" placeholder="Kilométrage (km)" required>
    <textarea name="description" placeholder="Description..."></textarea>
    <label><input type="checkbox" name="disponible" checked> Disponible</label><br>
    <input type="file" name="image" accept="image/*"><br><br>
    <button type="submit" name="ajouter">Ajouter</button>
</form>

<!-- Liste des voitures -->
<h2>Liste des voitures</h2>
<table>
    <tr>
        <th>Image</th><th>Marque</th><th>Modèle</th><th>Année</th><th>Prix</th>
        <th>Kilométrage</th><th>Disponible</th><th>Actions</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?php if ($row['image']) echo "<img src='uploads/{$row['image']}' alt='voiture'>"; ?></td>
        <td><?= htmlspecialchars($row['marque']) ?></td>
        <td><?= htmlspecialchars($row['modele']) ?></td>
        <td><?= $row['annee'] ?></td>
        <td><?= $row['prix'] ?> €</td>
        <td><?= $row['kilometrage'] ?> km</td>
        <td><?= $row['disponible'] ? 'Oui' : 'Non' ?></td>
        <td>
            <form method="POST" enctype="multipart/form-data" style="display:inline-block;">
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <input type="text" name="marque" value="<?= htmlspecialchars($row['marque']) ?>">
                <input type="text" name="modele" value="<?= htmlspecialchars($row['modele']) ?>">
                <input type="number" name="annee" value="<?= $row['annee'] ?>">
                <input type="number" name="prix" value="<?= $row['prix'] ?>">
                <input type="number" name="kilometrage" value="<?= $row['kilometrage'] ?>">
                <textarea name="description"><?= htmlspecialchars($row['description']) ?></textarea>
                <label><input type="checkbox" name="disponible" <?= $row['disponible'] ? 'checked' : '' ?>> Disponible</label>
                <input type="file" name="image" accept="image/*">
                <button type="submit" name="modifier">Modifier</button>
            </form>
            <a href="?supprimer=<?= $row['id'] ?>" onclick="return confirm('Supprimer cette voiture ?')">
                <button class="danger">Supprimer</button>
            </a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
<a href="logout.php">Se déconnecter</a>
</body>
</html>
