<?php
$host = 'localhost';
$db   = 'clinique_db';
$user = 'clinique_user';
$pass = 'Boreal2026!';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { die("Connexion echouee"); }

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nom    = $conn->real_escape_string($_POST['nom']);
  $email  = $conn->real_escape_string($_POST['email']);
  $tel    = $conn->real_escape_string($_POST['tel']);
  $medecin = $conn->real_escape_string($_POST['medecin']);
  $specialite = $conn->real_escape_string($_POST['specialite']);
  $date   = $conn->real_escape_string($_POST['date']);
  $heure  = $conn->real_escape_string($_POST['heure']);
  $motif  = $conn->real_escape_string($_POST['motif']);
  $sql = "INSERT INTO reservations (patient_nom, patient_email, patient_tel, medecin, specialite, date_rdv, heure_rdv, motif) VALUES ('$nom','$email','$tel','$medecin','$specialite','$date','$heure','$motif')";
  if ($conn->query($sql)) { $message = 'success'; }
}

$reservations = $conn->query("SELECT * FROM reservations ORDER BY date_rdv ASC, heure_rdv ASC");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CliniqueBoreal - Prise de rendez-vous</title>
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: 'Segoe UI', Arial, sans-serif; background: #f0f4f8; color: #333; }
    header { background: linear-gradient(135deg, #0a3d62, #1e6fa8); color: white; padding: 25px 40px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 10px rgba(0,0,0,0.2); }
    .header-left h1 { font-size: 28px; letter-spacing: 1px; }
    .header-left p { font-size: 13px; color: #b0d4f1; margin-top: 4px; }
    .admin-link { background: rgba(255,255,255,0.15); color: white; text-decoration: none; padding: 8px 18px; border-radius: 6px; font-size: 13px; border: 1px solid rgba(255,255,255,0.3); }
    .admin-link:hover { background: rgba(255,255,255,0.25); }
    .container { max-width: 1100px; margin: 0 auto; padding: 30px 20px; }
    .grid { display: grid; grid-template-columns: 1fr 1.5fr; gap: 30px; margin-bottom: 30px; }
    .card { background: white; border-radius: 14px; padding: 28px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
    .card h2 { font-size: 17px; color: #0a3d62; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #e8f0fe; }
    .form-group { margin-bottom: 15px; }
    label { display: block; font-size: 13px; font-weight: 600; color: #555; margin-bottom: 5px; }
    input, select, textarea { width: 100%; padding: 10px 14px; border: 1.5px solid #dde3ed; border-radius: 8px; font-size: 14px; background: #fafbfd; transition: border-color 0.2s; }
    input:focus, select:focus, textarea:focus { outline: none; border-color: #1e6fa8; background: white; }
    textarea { resize: vertical; min-height: 75px; }
    .row2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .btn { background: linear-gradient(135deg, #0a3d62, #1e6fa8); color: white; border: none; padding: 13px; border-radius: 9px; font-size: 15px; font-weight: 600; width: 100%; cursor: pointer; margin-top: 4px; }
    .btn:hover { opacity: 0.9; }
    .alert { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; }
    .medecins { display: flex; flex-direction: column; gap: 10px; }
    .med-card { background: #f5f8ff; border-radius: 10px; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between; border-left: 4px solid #1e6fa8; }
    .med-info strong { font-size: 14px; color: #0a3d62; display: block; }
    .med-info span { font-size: 12px; color: #888; }
    .badge-dispo { background: #d4edda; color: #155724; font-size: 11px; padding: 3px 10px; border-radius: 10px; font-weight: 600; }
    table { width: 100%; border-collapse: collapse; font-size: 14px; }
    th { background: #0a3d62; color: white; padding: 12px 14px; text-align: left; font-weight: 600; }
    td { padding: 11px 14px; border-bottom: 1px solid #eee; vertical-align: middle; }
    tr:hover td { background: #f7f9fc; }
    .badge { padding: 4px 11px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; white-space: nowrap; }
    .confirme { background: #d4edda; color: #155724; }
    .en_attente { background: #fff3cd; color: #856404; }
    .annule { background: #f8d7da; color: #721c24; }
    footer { text-align: center; padding: 20px; background: #0a3d62; color: #b0d4f1; font-size: 12px; margin-top: 10px; }
  </style>
</head>
<body>
<header>
  <div class="header-left">
    <h1>CliniqueBoreal</h1>
    <p>Votre sante, notre priorite &mdash; Prise de rendez-vous en ligne | <a href="suivi.php" style="color:#b0d4f1;">Suivre ma reservation</a></p>
  </div>
</header>
<div class="container">
  <?php if ($message === 'success'): ?>
  <div class="alert">Votre reservation a ete enregistree avec succes. Vous serez contacte pour confirmation.</div>
  <?php endif; ?>
  <div class="grid">
    <div class="card">
      <h2>Nouvelle reservation</h2>
      <form method="POST">
        <div class="form-group"><label>Nom complet *</label><input type="text" name="nom" required placeholder="Ex: Marie Dupont"></div>
        <div class="row2">
          <div class="form-group"><label>Email *</label><input type="email" name="email" required placeholder="email@exemple.com"></div>
          <div class="form-group"><label>Telephone</label><input type="tel" name="tel" placeholder="613-555-0100"></div>
        </div>
        <div class="form-group"><label>Medecin *</label>
          <select name="medecin" required>
            <option value="">-- Choisir --</option>
            <option value="Dr. Tremblay">Dr. Tremblay</option>
            <option value="Dr. Nguyen">Dr. Nguyen</option>
            <option value="Dr. Okonkwo">Dr. Okonkwo</option>
          </select>
        </div>
        <div class="form-group"><label>Specialite *</label>
          <select name="specialite" required>
            <option value="">-- Choisir --</option>
            <option value="Medecine generale">Medecine generale</option>
            <option value="Cardiologie">Cardiologie</option>
            <option value="Pediatrie">Pediatrie</option>
            <option value="Dermatologie">Dermatologie</option>
          </select>
        </div>
        <div class="row2">
          <div class="form-group"><label>Date *</label><input type="date" name="date" required min="<?php echo date('Y-m-d'); ?>"></div>
          <div class="form-group"><label>Heure *</label>
            <select name="heure" required>
              <option value="">-- Heure --</option>
              <?php foreach(['08:00','09:00','09:30','10:00','10:30','11:00','13:00','13:30','14:00','14:30','15:00','15:30','16:00'] as $h): ?>
              <option value="<?=$h?>"><?=$h?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-group"><label>Motif de consultation</label><textarea name="motif" placeholder="Decrivez brievement votre motif..."></textarea></div>
        <button type="submit" class="btn">Confirmer la reservation</button>
      </form>
    </div>
    <div class="card">
      <h2>Notre equipe medicale</h2>
      <div class="medecins">
        <div class="med-card"><div class="med-info"><strong>Dr. Tremblay</strong><span>Medecine generale</span></div><span class="badge-dispo">Disponible</span></div>
        <div class="med-card"><div class="med-info"><strong>Dr. Nguyen</strong><span>Cardiologie</span></div><span class="badge-dispo">Disponible</span></div>
        <div class="med-card"><div class="med-info"><strong>Dr. Okonkwo</strong><span>Pediatrie / Dermatologie</span></div><span class="badge-dispo">Disponible</span></div>
      </div>
    </div>
  </div>
  <div class="card">
    <h2>Liste des reservations</h2>
    <table>
      <tr><th>Patient</th><th>Contact</th><th>Medecin</th><th>Specialite</th><th>Date</th><th>Heure</th><th>Motif</th><th>Statut</th></tr>
      <?php while($r = $reservations->fetch_assoc()): ?>
      <tr>
        <td><strong><?=htmlspecialchars($r['patient_nom'])?></strong></td>
        <td><?=htmlspecialchars($r['patient_email'])?><br><small style="color:#888"><?=htmlspecialchars($r['patient_tel'])?></small></td>
        <td><?=htmlspecialchars($r['medecin'])?></td>
        <td><?=htmlspecialchars($r['specialite'])?></td>
        <td><?=date('d/m/Y', strtotime($r['date_rdv']))?></td>
        <td><?=substr($r['heure_rdv'],0,5)?></td>
        <td><?=htmlspecialchars($r['motif'])?></td>
        <td><span class="badge <?=$r['statut']?>"><?=str_replace('_',' ',$r['statut'])?></span></td>
      </tr>
      <?php endwhile; ?>
    </table>
  </div>
</div>
<footer><p>CliniqueBoreal 2026 &mdash; Tous droits reserves</p></footer>
</body>
</html>
