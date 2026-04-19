<?php
session_start();

$medecins = [
  'tremblay' => ['nom' => 'Dr. Tremblay', 'password' => 'Tremblay2026!'],
  'nguyen'   => ['nom' => 'Dr. Nguyen',   'password' => 'Nguyen2026!'],
  'okonkwo'  => ['nom' => 'Dr. Okonkwo',  'password' => 'Okonkwo2026!'],
];

// Deconnexion
if (isset($_GET['logout'])) {
  session_destroy();
  header("Location: admin.php");
  exit;
}

// Tentative de connexion
if (isset($_POST['username'], $_POST['mdp'])) {
  $u = $_POST['username'];
  if (isset($medecins[$u]) && $_POST['mdp'] === $medecins[$u]['password']) {
    $_SESSION['medecin'] = $u;
    $_SESSION['nom']     = $medecins[$u]['nom'];
  } else {
    $login_error = true;
  }
}

// Page de connexion si pas connecte
if (!isset($_SESSION['medecin'])) {
  ?>
  <!DOCTYPE html>
  <html lang="fr">
  <head>
    <meta charset="UTF-8">
    <title>Espace medecin - Connexion</title>
    <style>
      * { margin:0; padding:0; box-sizing:border-box; }
      body { font-family: 'Segoe UI', Arial, sans-serif; background: #f0f4f8; display: flex; align-items: center; justify-content: center; height: 100vh; }
      .box { background: white; padding: 40px; border-radius: 14px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); text-align: center; width: 360px; }
      .logo { font-size: 22px; font-weight: 700; color: #0a3d62; margin-bottom: 6px; }
      .subtitle { color: #888; font-size: 13px; margin-bottom: 28px; }
      label { display: block; text-align: left; font-size: 13px; font-weight: 600; color: #555; margin-bottom: 6px; }
      select, input { width: 100%; padding: 11px 14px; border: 1.5px solid #dde3ed; border-radius: 8px; font-size: 14px; margin-bottom: 16px; background: #fafbfd; }
      select:focus, input:focus { outline: none; border-color: #1e6fa8; }
      button { background: linear-gradient(135deg, #0a3d62, #1e6fa8); color: white; border: none; padding: 13px; border-radius: 9px; width: 100%; font-size: 15px; font-weight: 600; cursor: pointer; }
      button:hover { opacity: 0.9; }
      .err { background: #f8d7da; color: #721c24; padding: 10px 14px; border-radius: 8px; font-size: 13px; margin-bottom: 16px; }
      .back { display: block; margin-top: 16px; font-size: 13px; color: #1e6fa8; text-decoration: none; }
      .back:hover { text-decoration: underline; }
    </style>
  </head>
  <body>
    <div class="box">
      <div class="logo">CliniqueBoreal</div>
      <div class="subtitle">Espace reserve au personnel medical</div>
      <?php if (isset($login_error)): ?>
      <div class="err">Identifiants incorrects. Veuillez reessayer.</div>
      <?php endif; ?>
      <form method="POST">
        <label>Medecin</label>
        <select name="username" required>
          <option value="">-- Selectionner --</option>
          <option value="tremblay">Dr. Tremblay</option>
          <option value="nguyen">Dr. Nguyen</option>
          <option value="okonkwo">Dr. Okonkwo</option>
        </select>
        <label>Mot de passe</label>
        <input type="password" name="mdp" placeholder="Mot de passe" required>
        <button type="submit">Connexion</button>
      </form>
      <a href="index.php" class="back">Retour au site patient</a>
    </div>
  </body>
  </html>
  <?php
  exit;
}

// Connexion base de donnees
$host = 'localhost';
$db   = 'clinique_db';
$user = 'clinique_user';
$pass = 'Boreal2026!';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { die("Connexion echouee"); }

$medecin_connecte = $_SESSION['nom'];

// Changement de statut
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['statut'])) {
  $id = (int)$_POST['id'];
  $statut = $conn->real_escape_string($_POST['statut']);
  $m = $conn->real_escape_string($medecin_connecte);
  $conn->query("UPDATE reservations SET statut='$statut' WHERE id=$id AND medecin='$m'");
  header("Location: admin.php");
  exit;
}

// Reservations du medecin connecte seulement
$m_escaped = $conn->real_escape_string($medecin_connecte);
$reservations   = $conn->query("SELECT * FROM reservations WHERE medecin='$m_escaped' ORDER BY date_rdv ASC, heure_rdv ASC");
$stats_total    = $conn->query("SELECT COUNT(*) as n FROM reservations WHERE medecin='$m_escaped'")->fetch_assoc()['n'];
$stats_attente  = $conn->query("SELECT COUNT(*) as n FROM reservations WHERE medecin='$m_escaped' AND statut='en_attente'")->fetch_assoc()['n'];
$stats_confirme = $conn->query("SELECT COUNT(*) as n FROM reservations WHERE medecin='$m_escaped' AND statut='confirme'")->fetch_assoc()['n'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>CliniqueBoreal - Espace medecin</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: 'Segoe UI', Arial, sans-serif; background: #f0f4f8; color: #333; }
    header { background: linear-gradient(135deg, #0a3d62, #1e6fa8); color: white; padding: 22px 40px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 10px rgba(0,0,0,0.2); }
    header h1 { font-size: 22px; }
    header p { font-size: 13px; color: #b0d4f1; margin-top: 3px; }
    .header-links { display: flex; gap: 10px; }
    .header-btn { color: white; text-decoration: none; padding: 8px 18px; border-radius: 6px; font-size: 13px; border: 1px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.15); }
    .header-btn.logout { background: rgba(220,53,69,0.4); border-color: rgba(220,53,69,0.6); }
    .header-btn:hover { opacity: 0.85; }
    .container { max-width: 1100px; margin: 0 auto; padding: 30px 20px; }
    .stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
    .stat-card { background: white; border-radius: 12px; padding: 22px 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.07); border-top: 4px solid #1e6fa8; }
    .stat-card.attente { border-top-color: #f0ad4e; }
    .stat-card.confirme { border-top-color: #28a745; }
    .stat-card .num { font-size: 36px; font-weight: 700; color: #0a3d62; }
    .stat-card .label { font-size: 13px; color: #888; margin-top: 4px; }
    .card { background: white; border-radius: 14px; padding: 28px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
    .card h2 { font-size: 17px; color: #0a3d62; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e8f0fe; }
    table { width: 100%; border-collapse: collapse; font-size: 14px; }
    th { background: #0a3d62; color: white; padding: 12px 14px; text-align: left; font-weight: 600; }
    td { padding: 12px 14px; border-bottom: 1px solid #eee; vertical-align: middle; }
    tr:hover td { background: #f7f9fc; }
    .badge { padding: 4px 11px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; white-space: nowrap; }
    .confirme { background: #d4edda; color: #155724; }
    .en_attente { background: #fff3cd; color: #856404; }
    .annule { background: #f8d7da; color: #721c24; }
    .actions { display: flex; gap: 6px; flex-wrap: wrap; }
    .btn-action { border: none; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; }
    .btn-confirmer { background: #28a745; color: white; }
    .btn-annuler { background: #dc3545; color: white; }
    .btn-attente { background: #f0ad4e; color: white; }
    .btn-action:hover { opacity: 0.85; }
    .empty { text-align: center; color: #aaa; padding: 30px; font-size: 14px; }
    footer { text-align: center; padding: 20px; background: #0a3d62; color: #b0d4f1; font-size: 12px; margin-top: 20px; }
  </style>
</head>
<body>
<header>
  <div>
    <h1>CliniqueBoreal &mdash; <?=htmlspecialchars($medecin_connecte)?></h1>
    <p>Mes reservations du jour</p>
  </div>
  <div class="header-links">
    <a href="?logout=1" class="header-btn logout">Deconnexion</a>
  </div>
</header>
<div class="container">
  <div class="stats">
    <div class="stat-card"><div class="num"><?=$stats_total?></div><div class="label">Total mes reservations</div></div>
    <div class="stat-card attente"><div class="num"><?=$stats_attente?></div><div class="label">En attente</div></div>
    <div class="stat-card confirme"><div class="num"><?=$stats_confirme?></div><div class="label">Confirmees</div></div>
  </div>
  <div class="card">
    <h2>Mes reservations</h2>
    <?php if ($stats_total == 0): ?>
    <div class="empty">Aucune reservation pour le moment.</div>
    <?php else: ?>
    <table>
      <tr><th>Patient</th><th>Contact</th><th>Specialite</th><th>Date</th><th>Heure</th><th>Motif</th><th>Statut</th><th>Actions</th></tr>
      <?php while($r = $reservations->fetch_assoc()): ?>
      <tr>
        <td><strong><?=htmlspecialchars($r['patient_nom'])?></strong></td>
        <td><?=htmlspecialchars($r['patient_email'])?><br><small style="color:#888"><?=htmlspecialchars($r['patient_tel'])?></small></td>
        <td><?=htmlspecialchars($r['specialite'])?></td>
        <td><?=date('d/m/Y', strtotime($r['date_rdv']))?></td>
        <td><?=substr($r['heure_rdv'],0,5)?></td>
        <td><?=htmlspecialchars($r['motif'])?></td>
        <td><span class="badge <?=$r['statut']?>"><?=str_replace('_',' ',$r['statut'])?></span></td>
        <td>
          <div class="actions">
            <?php if($r['statut'] !== 'confirme'): ?>
            <form method="POST"><input type="hidden" name="id" value="<?=$r['id']?>"><input type="hidden" name="statut" value="confirme"><button class="btn-action btn-confirmer">Confirmer</button></form>
            <?php endif; ?>
            <?php if($r['statut'] !== 'annule'): ?>
            <form method="POST"><input type="hidden" name="id" value="<?=$r['id']?>"><input type="hidden" name="statut" value="annule"><button class="btn-action btn-annuler">Annuler</button></form>
            <?php endif; ?>
            <?php if($r['statut'] !== 'en_attente'): ?>
            <form method="POST"><input type="hidden" name="id" value="<?=$r['id']?>"><input type="hidden" name="statut" value="en_attente"><button class="btn-action btn-attente">En attente</button></form>
            <?php endif; ?>
          </div>
        </td>
      </tr>
      <?php endwhile; ?>
    </table>
    <?php endif; ?>
  </div>
</div>
<footer><p>CliniqueBoreal 2026 &mdash; Espace administration</p></footer>
</body>
</html>
