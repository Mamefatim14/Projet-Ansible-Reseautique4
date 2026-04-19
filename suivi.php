<?php
$host = 'localhost';
$db   = 'clinique_db';
$user = 'clinique_user';
$pass = 'Boreal2026!';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { die("Connexion echouee"); }

$reservations = [];
$recherche = false;

if (isset($_POST['email'])) {
  $recherche = true;
  $email = $conn->real_escape_string($_POST['email']);
  $result = $conn->query("SELECT * FROM reservations WHERE patient_email='$email' ORDER BY date_rdv ASC, heure_rdv ASC");
  while ($r = $result->fetch_assoc()) {
    $reservations[] = $r;
  }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>CliniqueBoreal - Suivi de reservation</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: 'Segoe UI', Arial, sans-serif; background: #f0f4f8; color: #333; }
    header { background: linear-gradient(135deg, #0a3d62, #1e6fa8); color: white; padding: 25px 40px; box-shadow: 0 2px 10px rgba(0,0,0,0.2); }
    header h1 { font-size: 28px; }
    header p { font-size: 13px; color: #b0d4f1; margin-top: 4px; }
    .container { max-width: 750px; margin: 40px auto; padding: 0 20px; }
    .card { background: white; border-radius: 14px; padding: 32px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 24px; }
    .card h2 { font-size: 18px; color: #0a3d62; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e8f0fe; }
    .form-row { display: flex; gap: 12px; }
    input[type=email] { flex: 1; padding: 12px 16px; border: 1.5px solid #dde3ed; border-radius: 8px; font-size: 14px; background: #fafbfd; }
    input[type=email]:focus { outline: none; border-color: #1e6fa8; }
    button { background: linear-gradient(135deg, #0a3d62, #1e6fa8); color: white; border: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; white-space: nowrap; }
    button:hover { opacity: 0.9; }
    .rdv-card { border: 1px solid #e8f0fe; border-radius: 10px; padding: 20px; margin-bottom: 14px; border-left: 5px solid #1e6fa8; }
    .rdv-card.confirme { border-left-color: #28a745; }
    .rdv-card.annule { border-left-color: #dc3545; }
    .rdv-card.en_attente { border-left-color: #f0ad4e; }
    .rdv-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
    .rdv-header strong { font-size: 16px; color: #0a3d62; }
    .badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; }
    .badge.confirme { background: #d4edda; color: #155724; }
    .badge.en_attente { background: #fff3cd; color: #856404; }
    .badge.annule { background: #f8d7da; color: #721c24; }
    .rdv-details { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 13px; color: #555; }
    .rdv-details span strong { color: #333; }
    .empty { text-align: center; color: #aaa; padding: 30px; font-size: 14px; }
    .msg-confirme { background: #d4edda; color: #155724; border-radius: 8px; padding: 12px 16px; margin-bottom: 10px; font-size: 13px; }
    .msg-attente { background: #fff3cd; color: #856404; border-radius: 8px; padding: 12px 16px; margin-bottom: 10px; font-size: 13px; }
    .msg-annule { background: #f8d7da; color: #721c24; border-radius: 8px; padding: 12px 16px; margin-bottom: 10px; font-size: 13px; }
    .back { display: inline-block; margin-top: 10px; font-size: 13px; color: #1e6fa8; text-decoration: none; }
    .back:hover { text-decoration: underline; }
    footer { text-align: center; padding: 20px; background: #0a3d62; color: #b0d4f1; font-size: 12px; margin-top: 20px; }
  </style>
</head>
<body>
<header>
  <h1>CliniqueBoreal</h1>
  <p>Suivi de votre reservation</p>
</header>
<div class="container">
  <div class="card">
    <h2>Consulter le statut de ma reservation</h2>
    <form method="POST">
      <div class="form-row">
        <input type="email" name="email" placeholder="Entrez votre adresse email" required value="<?=isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''?>">
        <button type="submit">Rechercher</button>
      </div>
    </form>
  </div>

  <?php if ($recherche): ?>
    <?php if (empty($reservations)): ?>
    <div class="card">
      <div class="empty">Aucune reservation trouvee pour cet email.</div>
    </div>
    <?php else: ?>
      <?php foreach ($reservations as $r): ?>
      <div class="rdv-card <?=$r['statut']?>">
        <div class="rdv-header">
          <strong><?=htmlspecialchars($r['medecin'])?></strong>
          <span class="badge <?=$r['statut']?>"><?=str_replace('_',' ',$r['statut'])?></span>
        </div>
        <?php if ($r['statut'] === 'confirme'): ?>
        <div class="msg-confirme">Votre rendez-vous est confirme. Veuillez vous presenter 10 minutes avant l'heure prevue.</div>
        <?php elseif ($r['statut'] === 'en_attente'): ?>
        <div class="msg-attente">Votre reservation est en cours de traitement. Vous serez contacte prochainement.</div>
        <?php elseif ($r['statut'] === 'annule'): ?>
        <div class="msg-annule">Votre rendez-vous a ete annule. Veuillez contacter la clinique pour reprendre un rendez-vous.</div>
        <?php endif; ?>
        <div class="rdv-details">
          <span><strong>Specialite :</strong> <?=htmlspecialchars($r['specialite'])?></span>
          <span><strong>Date :</strong> <?=date('d/m/Y', strtotime($r['date_rdv']))?></span>
          <span><strong>Heure :</strong> <?=substr($r['heure_rdv'],0,5)?></span>
          <span><strong>Motif :</strong> <?=htmlspecialchars($r['motif'] ?: 'Non specifie')?></span>
        </div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  <?php endif; ?>

  <a href="index.php" class="back">Retour au site</a>
</div>
<footer><p>CliniqueBoreal 2026 &mdash; Tous droits reserves</p></footer>
</body>
</html>
