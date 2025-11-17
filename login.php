<?php
session_start();

$pdo = new PDO("mysql:host=localhost;dbname=garage;charset=utf8", "root", "", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"] ?? '';
    $motDePasse = $_POST["mot_de_passe"] ?? '';

    $requete = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email");
    $requete->execute(['email' => $email]);
    $utilisateur = $requete->fetch(PDO::FETCH_ASSOC);

    if ($utilisateur && password_verify($motDePasse, $utilisateur['mot_de_passe'])) {

        $_SESSION['user_id'] = $utilisateur['id'];
        $_SESSION['role'] = $utilisateur['role'];
        $_SESSION['username'] = $utilisateur['nom'];

        if ($utilisateur['role'] === 'admin') {
            header("Location: admin.php");
        } else {
            header("Location: index.php");
        }
        exit;
    } else {
        $message = "Identifiants incorrects.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Administrateur</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f1f1f1;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .login-container {
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 100%;
        }

        .login-container h2 {
            margin-bottom: 25px;
            color: #333;
        }

        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
        }

        .login-container button {
            background-color: #333;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .login-container button:hover {
            background-color: #555;
        }

        .error-message {
            color: red;
            margin-bottom: 15px;
            font-size: 14px;
        }

        @media (max-width: 500px) {
            .login-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Connexion Admin</h2>

        <?php if ($message): ?>
            <div class="error-message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <input type="text" name="identifiant" placeholder="Email ou Nom d'utilisateur">
            <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
            <button type="submit">Se connecter</button>
        </form>
    </div>
</body>
</html>


















