CREATE TABLE IF NOT EXISTS reservations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  patient_nom VARCHAR(100) NOT NULL,
  patient_email VARCHAR(150) NOT NULL,
  patient_tel VARCHAR(20),
  medecin VARCHAR(100) NOT NULL,
  specialite VARCHAR(100) NOT NULL,
  date_rdv DATE NOT NULL,
  heure_rdv TIME NOT NULL,
  motif VARCHAR(255),
  statut ENUM('confirme','en_attente','annule') DEFAULT 'en_attente',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
INSERT IGNORE INTO reservations (id, patient_nom, patient_email, patient_tel, medecin, specialite, date_rdv, heure_rdv, motif, statut) VALUES
(1, 'Marie Dupont', 'marie@email.com', '613-555-0101', 'Dr. Tremblay', 'Medecine generale', '2026-04-25', '09:00:00', 'Bilan annuel', 'confirme'),
(2, 'Jean Martin', 'jean@email.com', '613-555-0102', 'Dr. Nguyen', 'Cardiologie', '2026-04-26', '10:30:00', 'Douleurs thoraciques', 'en_attente'),
(3, 'Sophie Bernard', 'sophie@email.com', '613-555-0103', 'Dr. Tremblay', 'Medecine generale', '2026-04-28', '14:00:00', 'Suivi diabete', 'confirme');
