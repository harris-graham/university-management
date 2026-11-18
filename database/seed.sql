-- =============================================================================
-- GestUniv - Seed Data (gestion_universite)
-- Université EMIT (École de Management et d'Innovation Technologique)
-- =============================================================================

USE `gestion_universite`;

-- -----------------------------------------------------------------------------
-- Années universitaires
-- -----------------------------------------------------------------------------
INSERT INTO `anneeuniv` (`AnneeUniv`) VALUES
  ('2023-2024'),
  ('2024-2025'),
  ('2025-2026')
ON DUPLICATE KEY UPDATE `AnneeUniv` = VALUES(`AnneeUniv`);

-- -----------------------------------------------------------------------------
-- Niveaux académiques
-- -----------------------------------------------------------------------------
INSERT INTO `niveau` (`NumNiveau`, `CodeNiveau`, `NomNiveau`, `NomCycle`) VALUES
  (1, 'L1', 'Première année Licence', 'Licence'),
  (2, 'L2', 'Deuxième année Licence', 'Licence'),
  (3, 'L3', 'Troisième année Licence', 'Licence'),
  (4, 'M1', 'Première année Master', 'Master'),
  (5, 'M2', 'Deuxième année Master', 'Master')
ON DUPLICATE KEY UPDATE `NomNiveau` = VALUES(`NomNiveau`), `NomCycle` = VALUES(`NomCycle`);

-- -----------------------------------------------------------------------------
-- Mentions (Filières)
-- -----------------------------------------------------------------------------
INSERT INTO `mention` (`CodeMention`, `NomMention`) VALUES
  ('IG', 'Informatique de Gestion'),
  ('GB', 'Gestion des Entreprises et du Commerce'),
  ('GF', 'Gestion Financière et Comptable')
ON DUPLICATE KEY UPDATE `NomMention` = VALUES(`NomMention`);

-- -----------------------------------------------------------------------------
-- Classes (combinaisons Niveau - Mention)
-- -----------------------------------------------------------------------------
INSERT INTO `classe` (`CodeNiveau`, `CodeMention`) VALUES
  ('L1', 'IG'), ('L1', 'GB'), ('L1', 'GF'),
  ('L2', 'IG'), ('L2', 'GB'), ('L2', 'GF'),
  ('L3', 'IG'), ('L3', 'GB'), ('L3', 'GF'),
  ('M1', 'IG'), ('M1', 'GB'), ('M1', 'GF'),
  ('M2', 'IG'), ('M2', 'GB'), ('M2', 'GF')
ON DUPLICATE KEY UPDATE `CodeNiveau` = VALUES(`CodeNiveau`);

-- -----------------------------------------------------------------------------
-- Utilisateurs par défaut
-- Mot de passe par défaut : admin123 (hashé en bcrypt)
-- Le code supporte aussi 'admin123' en clair lors du premier login et le migre automatiquement.
-- Hash bcrypt de 'admin123' : $2y$10$vI8aWBnW3fID.ZQ4/zo1G.q1lR5eXyv2b.gJ84tE7n7jG25D186kS
-- -----------------------------------------------------------------------------
INSERT INTO `utilisateur` (`IdUGU`, `NomCGU`, `MdpCGU`, `RoleUGU`, `AbrRole`, `Email`) VALUES
  (1, 'admin', '$2y$10$vI8aWBnW3fID.ZQ4/zo1G.q1lR5eXyv2b.gJ84tE7n7jG25D186kS', 'Chef de Scolarité', 'CSCO', 'admin@emit.mg'),
  (2, 'secretaire', '$2y$10$vI8aWBnW3fID.ZQ4/zo1G.q1lR5eXyv2b.gJ84tE7n7jG25D186kS', 'Secrétaire Générale', 'SCR', 'secretaire@emit.mg'),
  (3, 'responsable', '$2y$10$vI8aWBnW3fID.ZQ4/zo1G.q1lR5eXyv2b.gJ84tE7n7jG25D186kS', 'Responsable de Mention', 'RM', 'responsable@emit.mg')
ON DUPLICATE KEY UPDATE `RoleUGU` = VALUES(`RoleUGU`), `AbrRole` = VALUES(`AbrRole`);

-- -----------------------------------------------------------------------------
-- Corps Enseignant (Professeurs)
-- -----------------------------------------------------------------------------
INSERT INTO `professeur` (`IdProf`, `NomProf`, `PrenomProf`, `EmailProf`, `TelProf`, `GradeProf`, `SpecialiteProf`, `DateRecrutement`) VALUES
  (1, 'RAKOTO', 'Jean Luc', 'jeanluc.rakoto@emit.mg', '+261340123456', 'Maître de Conférences', 'Génie Logiciel & Bases de Données', '2019-09-01'),
  (2, 'ANDRIANIRINA', 'Mamy Haja', 'mamy.andri@emit.mg', '+261320234567', 'Professeur Titulaire', 'Comptabilité & Analyse Financière', '2015-10-15'),
  (3, 'RASOANANDRASANA', 'Voahirana', 'voahirana.rasoa@emit.mg', '+261330345678', 'Docteur', 'Management Stratégique & Marketing', '2021-02-01'),
  (4, 'RANAIVOSON', 'Tahina', 'tahina.ranaivo@emit.mg', '+261340456789', 'Enseignant-Chercheur', 'Réseaux Informatiques & Télécoms', '2020-11-10')
ON DUPLICATE KEY UPDATE `NomProf` = VALUES(`NomProf`);

-- -----------------------------------------------------------------------------
-- Matières exemplaires
-- -----------------------------------------------------------------------------
INSERT INTO `matiere` (`CodeMatiere`, `NomMatiere`, `CoeffMatiere`, `CodeNiveau`, `CodeMention`) VALUES
  ('PROG1', 'Algorithmique et Programmation C/PHP', 3, 'L1', 'IG'),
  ('BDD1', 'Conception des Bases de Données relationnelles', 3, 'L1', 'IG'),
  ('ARCH1', 'Architecture des Ordinateurs & Systèmes', 2, 'L1', 'IG'),
  ('MATH1', 'Mathématiques appliquées & Statistiques', 2, 'L1', 'IG'),
  ('ECO1', 'Économie Générale & Organisation des Entreprises', 2, 'L1', 'GB'),
  ('COMPT1', 'Comptabilité Générale et Financière', 3, 'L1', 'GF')
ON DUPLICATE KEY UPDATE `NomMatiere` = VALUES(`NomMatiere`), `CoeffMatiere` = VALUES(`CoeffMatiere`);

-- -----------------------------------------------------------------------------
-- Affectation Enseignants - Matières
-- -----------------------------------------------------------------------------
INSERT INTO `enseigner` (`IdProf`, `CodeMatiere`, `AnneeUniv`) VALUES
  (1, 'PROG1', '2024-2025'),
  (1, 'BDD1', '2024-2025'),
  (4, 'ARCH1', '2024-2025'),
  (2, 'COMPT1', '2024-2025'),
  (3, 'ECO1', '2024-2025')
ON DUPLICATE KEY UPDATE `IdProf` = VALUES(`IdProf`);

-- -----------------------------------------------------------------------------
-- Étudiants inscrits (Exemples)
-- -----------------------------------------------------------------------------
INSERT INTO `etudiant` (`NumEtu`, `NomEtu`, `PrenomEtu`, `DateNaiss`, `CinEtu`, `SexeEtu`, `NationEtu`, `TelEtu`, `AdresseEtu`, `EmailEtu`, `DateInscr`, `AnneeUniv`, `CodeNiveau`, `CodeMention`) VALUES
  (101, 'RANDRIANARISOA', 'Tojo Harivelo', '2004-03-12', '101234567890', 'Masculin', 'Malagasy', '+261341122334', 'Lot II B 45 Antananarivo', 'tojo.randria@student.emit.mg', '2024-10-02', '2024-2025', 'L1', 'IG'),
  (102, 'RAFARALAHY', 'Aina Mirana', '2005-07-21', '101234567891', 'Feminin', 'Malagasy', '+261324455667', 'Lot IV G 12 Fianarantsoa', 'aina.rafara@student.emit.mg', '2024-10-03', '2024-2025', 'L1', 'IG'),
  (103, 'ANDRIATSIMANIRY', 'Faly Ny Aina', '2003-11-05', '101234567892', 'Masculin', 'Malagasy', '+261337788990', 'Tanambao Fianarantsoa', 'faly.andria@student.emit.mg', '2024-10-04', '2024-2025', 'L1', 'IG'),
  (104, 'RAZAFIMANDIMBY', 'Sitrakiniaina', '2004-09-18', '101234567893', 'Feminin', 'Malagasy', '+261349900112', 'Tsianolondroa', 'sitraka.razafy@student.emit.mg', '2024-10-05', '2024-2025', 'L1', 'GB'),
  (105, 'RAMANANTOANINA', 'Eric Michel', '2003-01-30', '101234567894', 'Masculin', 'Malagasy', '+261321144778', 'Ampasambazaha', 'eric.ramanan@student.emit.mg', '2024-10-06', '2024-2025', 'L1', 'GF')
ON DUPLICATE KEY UPDATE `NomEtu` = VALUES(`NomEtu`);

-- -----------------------------------------------------------------------------
-- Examens
-- -----------------------------------------------------------------------------
INSERT INTO `examen` (`CodeExam`, `CodeNiveau`, `CodeMention`, `CodeMatiere`, `DateExam`) VALUES
  ('L1-IG-PROG1_2024-2025', 'L1', 'IG', 'PROG1', '2025-01-20'),
  ('L1-IG-BDD1_2024-2025', 'L1', 'IG', 'BDD1', '2025-01-22'),
  ('L1-IG-ARCH1_2024-2025', 'L1', 'IG', 'ARCH1', '2025-01-25')
ON DUPLICATE KEY UPDATE `DateExam` = VALUES(`DateExam`);

-- -----------------------------------------------------------------------------
-- Notes d'examens (Contrôles)
-- -----------------------------------------------------------------------------
INSERT INTO `controle` (`CodeExam`, `NumEtu`, `Note`) VALUES
  ('L1-IG-PROG1_2024-2025', 101, 14.50),
  ('L1-IG-PROG1_2024-2025', 102, 16.00),
  ('L1-IG-PROG1_2024-2025', 103, 08.50),
  ('L1-IG-BDD1_2024-2025', 101, 13.00),
  ('L1-IG-BDD1_2024-2025', 102, 15.50),
  ('L1-IG-BDD1_2024-2025', 103, 11.00),
  ('L1-IG-ARCH1_2024-2025', 101, 12.00),
  ('L1-IG-ARCH1_2024-2025', 102, 14.00)
ON DUPLICATE KEY UPDATE `Note` = VALUES(`Note`);

-- -----------------------------------------------------------------------------
-- Emploi du Temps (Séances de cours)
-- -----------------------------------------------------------------------------
INSERT INTO `emploi_du_temps` (`IdSeance`, `CodeNiveau`, `CodeMention`, `CodeMatiere`, `IdProf`, `JourSemaine`, `HeureDebut`, `HeureFin`, `Salle`, `AnneeUniv`) VALUES
  (1, 'L1', 'IG', 'PROG1', 1, 'Lundi', '08:00:00', '11:00:00', 'Labo Info 1', '2024-2025'),
  (2, 'L1', 'IG', 'BDD1', 1, 'Mardi', '14:00:00', '17:00:00', 'Salle 12', '2024-2025'),
  (3, 'L1', 'IG', 'ARCH1', 4, 'Jeudi', '08:00:00', '10:00:00', 'Amphi A', '2024-2025')
ON DUPLICATE KEY UPDATE `Salle` = VALUES(`Salle`);

-- -----------------------------------------------------------------------------
-- Notifications de démarrage
-- -----------------------------------------------------------------------------
INSERT INTO `notification` (`IdUGU`, `Message`, `TypeNotif`, `Lu`) VALUES
  (1, 'Bienvenue dans la nouvelle version 2.0 de GestUniv !', 'success', 0),
  (1, 'Les emplois du temps du premier semestre 2024-2025 sont disponibles.', 'info', 0),
  (2, 'Pensez à vérifier les inscriptions en attente pour la mention IG.', 'warning', 0);

-- -----------------------------------------------------------------------------
-- Journal d'audit initial
-- -----------------------------------------------------------------------------
INSERT INTO `audit_log` (`IdUGU`, `Action`, `Cible`, `Details`, `IpAddress`) VALUES
  (1, 'INITIALISATION', 'Base de données', 'Initialisation du schéma et des données de base GestUniv v2.0', '127.0.0.1');
