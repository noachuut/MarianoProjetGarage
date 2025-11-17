<?php
session_start();
require 'db.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
$user_id = (int)$_SESSION['user_id'];

// récupérer items
$stmt = $pdo->prepare("
  SELECT p.id as pid, p.quantite, v.*
  FROM panier p
  JOIN voitures v ON p.id_voiture = v.id
  WHERE p.id_utilisateur = ?
");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll();

// validation (checkout)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    try {
        $pdo->beginTransaction();
        foreach ($items as $it) {
            // vérif stock
            if ($it['stock'] < $it['quantite']) throw new Exception("Stock insuffisant pour {$it['marque']} {$it['modele']}");
            // insérer commande
            $pdo->prepare("INSERT INTO commandes (id_utilisateur, id_voiture, quantite) VALUES (?, ?, ?)")
                ->execute([$user_id, $it['id'], $it['quantite']]);
            // décrémenter stock
            $pdo->prepare("UPDATE voitures SET stock = stock - ? WHERE id = ?")
                ->execute([$it['quantite'], $it['id']]);
            // rendre indisponible si stock <=0
            $pdo->prepare("UPDATE voitures SET disponible = 0 WHERE id = ? AND stock <= 0")
                ->execute([$it['id']]);
        }
        // vider panier
        $pdo->prepare("DELETE FROM panier WHERE id_utilisateur = ?")->execute([$user_id]);
        $pdo->commit();
        header('Location: index.php?info=commande_ok');
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = $e->getMessage();
    }
}
?>
<!doctype html>
<html lang="fr">
<head><meta charset="utf-8"><title>Panier</title><link rel="stylesheet" href="MG.css"></head>
<body>
<?php if(!empty($error)): ?><div style="color:red"><?=htmlspecialchars($error)?></div><?php endif; ?>
<h1>Mon panier</h1>
<?php if(count($items)===0): ?>
  <p>Panier vide</p>
<?php else: ?>
  <table style="margin:auto;">
    <tr><th>Voiture</th><th>Prix</th><th>Qté</th><th>Total</th></tr>
    <?php $total = 0; foreach($items as $it): 
        $sub = $it['prix'] * $it['quantite']; $total += $sub;
    ?>
      <tr>
        <td><?=htmlspecialchars($it['marque'].' '.$it['modele'])?></td>
        <td><?=number_format($it['prix'],0,',',' ')?> €</td>
        <td><?= (int)$it['quantite'] ?></td>
        <td><?=number_format($sub,0,',',' ')?> €</td>
      </tr>
    <?php endforeach; ?>
    <tr><td colspan="3">Total</td><td><?=number_format($total,0,',',' ')?> €</td></tr>
  </table>

  <form method="POST" style="text-align:center;margin-top:20px;">
    <button type="submit" name="checkout">Valider la commande</button>
  </form>
<?php endif; ?>
<p style="text-align:center"><a href="index.php">← Retour</a></p>
</body>
</html>
