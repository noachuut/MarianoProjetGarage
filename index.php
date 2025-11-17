<?php
session_start();
require 'db.php';

// message après actions
$info = $_GET['info'] ?? '';

// récupération des véhicules (tous)
$stmt = $pdo->query("SELECT * FROM voitures ORDER BY date_ajout DESC");
$voitures = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Manash Garage</title>
<link rel="stylesheet" href="MG.css">
</head>
<body>
<nav>
  <div class="nav-left"><img src="Images/Logo_cm.jpg" alt="Logo"></div>
  <ul>
    <li><a href="index.php">Accueil</a></li>
    <li><a href="#contact">Contact</a></li>
    <?php if(isset($_SESSION['user_id'])): ?>
      <?php if(($_SESSION['role'] ?? '') === 'admin'): ?>
        <li><a href="admin.php">Admin</a></li>
      <?php else: ?>
        <li><a href="mon_compte.php">Mon compte</a></li>
      <?php endif; ?>
      <li><a href="logout.php">Déconnexion</a></li>
    <?php else: ?>
      <li><a href="#auth">Connexion / Inscription</a></li>
    <?php endif; ?>
  </ul>
</nav>

<main style="padding-top:110px;">
  <?php if($info === 'commande_ok'): ?>
    <div style="background:#4caf50;color:#fff;padding:10px;margin:10px;">✅ Commande enregistrée</div>
  <?php elseif($info === 'panier_ok'): ?>
    <div style="background:#2196f3;color:#fff;padding:10px;margin:10px;">👍 Ajouté au panier</div>
  <?php endif; ?>

  <section class="voitures">
    <h2>Nos véhicules disponibles</h2>
    <div class="voitures-container">
      <?php foreach($voitures as $v): 
          // choisir chemin image : uploads/ si stocké ainsi
          $img = !empty($v['image']) ? $v['image'] : 'Images/default-car.jpg';
          if(!preg_match('#^uploads/#',$img) && !preg_match('#^Images/#',$img)){
              $img = 'uploads/'.basename($img);
          }
      ?>
        <div class="voiture-card">
          <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($v['marque'].' '.$v['modele']) ?>">
          <h3><?= htmlspecialchars($v['marque'].' '.$v['modele']) ?></h3>
          <p>Année : <?= htmlspecialchars($v['annee']) ?></p>
          <p>Kilométrage : <?= number_format($v['kilometrage'],0,',',' ') ?> km</p>
          <p>Prix : <?= number_format($v['prix'], 0, ',', ' ') ?> €</p>
          <p>Stock : <?= (int)$v['stock'] ?></p>
          <p><?= nl2br(htmlspecialchars($v['description'])) ?></p>

          <div style="margin-top:10px;">
            <?php if($v['stock'] > 0 && $v['disponible']): ?>
              <form action="ajouter_panier.php" method="POST" style="display:inline-block;">
                <input type="hidden" name="voiture_id" value="<?= (int)$v['id'] ?>">
                <button type="submit">Ajouter au panier</button>
              </form>

              <form action="commander.php" method="POST" style="display:inline-block;">
                <input type="hidden" name="voiture_id" value="<?= (int)$v['id'] ?>">
                <button type="submit">Acheter maintenant</button>
              </form>
            <?php else: ?>
              <button disabled style="background:gray">Non disponible</button>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- Bloc Auth -->
  <section id="auth" style="padding:20px;">
    <?php if(!isset($_SESSION['user_id'])): ?>
      <div style="display:flex;gap:30px;flex-wrap:wrap;justify-content:center;">
        <div style="max-width:380px;background:#fff;padding:15px;border-radius:8px;">
          <h3>Connexion</h3>
          <form action="login.php" method="POST">
            <input type="email" name="email" placeholder="Email" required style="width:100%;padding:8px;margin:6px 0;">
            <input type="password" name="mot_de_passe" placeholder="Mot de passe" required style="width:100%;padding:8px;margin:6px 0;">
            <button type="submit">Se connecter</button>
          </form>
        </div>

        <div style="max-width:380px;background:#fff;padding:15px;border-radius:8px;">
          <h3>Inscription</h3>
          <form action="register.php" method="POST">
            <input type="text" name="nom" placeholder="Nom complet" required style="width:100%;padding:8px;margin:6px 0;">
            <input type="email" name="email" placeholder="Email" required style="width:100%;padding:8px;margin:6px 0;">
            <input type="password" name="mot_de_passe" placeholder="Mot de passe" required style="width:100%;padding:8px;margin:6px 0;">
            <button type="submit">S'inscrire</button>
          </form>
        </div>
      </div>
    <?php else: ?>
      <div style="background:#fff;padding:12px;border-radius:8px;display:inline-block;">
        Bonjour <strong><?= htmlspecialchars($_SESSION['username']) ?></strong> — <a href="mon_compte.php">Mon compte</a>
      </div>
    <?php endif; ?>
  </section>
</main>

<footer>
  <div class="footer-content">
    <div class="footer-column">
      <h3>Contactez-nous</h3>
      <p>+687 98.37.87</p>
      <p>contact@manashgarage.nc</p>
    </div>
    <div class="footer-column">
      <h3>Nos Réseaux</h3>
      <a href="#"><img src="Images/logo-instagram.png" alt="Instagram"></a>
      <a href="#"><img src="Images/logo_messenger.jpg" alt="Messenger"></a>
    </div>
    <div class="footer-column">
      <h3>Modes de paiement</h3>
      <div class="payment-icons">
        <img src="Images/visa_logo.jpg" alt="Visa">
        <img src="Images/paypal_logo.jpg" alt="PayPal">
      </div>
    </div>
  </div>
</footer>
</body>
</html>
