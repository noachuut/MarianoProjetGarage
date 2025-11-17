<?php
session_start();
session_unset();
session_destroy();
header("Location: index.php");
exit;
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Déconnexion</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
            background-color: #f1f1f1;
        }
        .message {
            background-color:rgba(0, 95, 29, 0.86);
            color: white;
            padding: 20px;
            border-radius: 10px;
            width: 300px;
            margin: 0 auto;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="message">
        <h2><?php echo $message; ?></h2>
        <p>Vous allez être redirigé vers la page d'accueil...</p>
    </div>
</body>
</html>

