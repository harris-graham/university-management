-- =============================================================================
-- GestUniv - Database Schema (gestion_universite)
-- Université EMIT (École de Management et d'Innovation Technologique)
-- =============================================================================

CREATE DATABASE IF NOT EXISTS `gestion_universite`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `gestion_universite`;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `audit_log`;
DROP TABLE IF EXISTS `notification`;
DROP TABLE IF EXISTS `presence`;
DROP TABLE IF EXISTS `emploi_du_temps`;
DROP TABLE IF EXISTS `enseigner`;
DROP TABLE IF EXISTS `professeur`;
DROP TABLE IF EXISTS `controle`;
DROP TABLE IF EXISTS `examen`;
DROP TABLE IF EXISTS `matiere`;
DROP TABLE IF EXISTS `etudiant`;
DROP TABLE IF EXISTS `utilisateur`;
DROP TABLE IF EXISTS `classe`;
DROP TABLE IF EXISTS `mention`;
DROP TABLE IF EXISTS `niveau`;
DROP TABLE IF EXISTS `anneeuniv`;

SET FOREIGN_KEY_CHECKS = 1;

-- -----------------------------------------------------------------------------
-- 1. Table: anneeuniv (Année Universitaire)
-- -----------------------------------------------------------------------------
CREATE TABLE `anneeuniv` (
  `AnneeUniv` VARCHAR(9) NOT NULL,
  PRIMARY KEY (`AnneeUniv`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 2. Table: niveau (Niveaux académiques L1..M2)
-- -----------------------------------------------------------------------------
CREATE TABLE `niveau` (
  `NumNiveau` INT NOT NULL,
  `CodeNiveau` VARCHAR(5) NOT NULL,
  `NomNiveau` VARCHAR(50) NOT NULL,
  `NomCycle` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`NumNiveau`),
  UNIQUE KEY `uk_code_niveau` (`CodeNiveau`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 3. Table: mention (Filières / Mentions)
-- -----------------------------------------------------------------------------
CREATE TABLE `mention` (
  `CodeMention` VARCHAR(5) NOT NULL,
  `NomMention` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`CodeMention`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 4. Table: classe (Croisement Niveau - Mention)
-- -----------------------------------------------------------------------------
CREATE TABLE `classe` (
  `CodeNiveau` VARCHAR(5) NOT NULL,
  `CodeMention` VARCHAR(5) NOT NULL,
  PRIMARY KEY (`CodeNiveau`, `CodeMention`),
  CONSTRAINT `fk_classe_niveau` FOREIGN KEY (`CodeNiveau`) REFERENCES `niveau` (`CodeNiveau`) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT `fk_classe_mention` FOREIGN KEY (`CodeMention`) REFERENCES `mention` (`CodeMention`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 5. Table: utilisateur (Comptes administratifs / Scolarité)
-- -----------------------------------------------------------------------------
CREATE TABLE `utilisateur` (
  `IdUGU` INT AUTO_INCREMENT NOT NULL,
  `NomCGU` VARCHAR(30) NOT NULL,
  `MdpCGU` VARCHAR(255) NOT NULL,
  `RoleUGU` VARCHAR(50) NOT NULL,
  `AbrRole` VARCHAR(10) NOT NULL,
  `Email` VARCHAR(100) NULL,
  `DateCreation` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`IdUGU`),
  UNIQUE KEY `uk_nom_cgu` (`NomCGU`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 6. Table: etudiant (Étudiants inscrits)
-- -----------------------------------------------------------------------------
CREATE TABLE `etudiant` (
  `NumEtu` INT AUTO_INCREMENT NOT NULL,
  `NomEtu` VARCHAR(50) NOT NULL,
  `PrenomEtu` VARCHAR(60) NOT NULL,
  `DateNaiss` DATE NULL,
  `CinEtu` VARCHAR(20) NULL,
  `SexeEtu` ENUM('Masculin', 'Feminin') NOT NULL,
  `NationEtu` VARCHAR(30) NOT NULL DEFAULT 'Malagasy',
  `TelEtu` VARCHAR(20) NULL,
  `AdresseEtu` VARCHAR(100) NULL,
  `EmailEtu` VARCHAR(100) NULL,
  `DateInscr` DATE NULL,
  `AnneeUniv` VARCHAR(9) NULL,
  `CodeNiveau` VARCHAR(5) NOT NULL,
  `CodeMention` VARCHAR(5) NOT NULL,
  PRIMARY KEY (`NumEtu`),
  KEY `idx_etudiant_classe` (`CodeNiveau`, `CodeMention`),
  KEY `idx_etudiant_annee` (`AnneeUniv`),
  CONSTRAINT `fk_etudiant_annee` FOREIGN KEY (`AnneeUniv`) REFERENCES `anneeuniv` (`AnneeUniv`) ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT `fk_etudiant_classe` FOREIGN KEY (`CodeNiveau`, `CodeMention`) REFERENCES `classe` (`CodeNiveau`, `CodeMention`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 7. Table: matiere (Unités d'enseignement / Cours)
-- -----------------------------------------------------------------------------
CREATE TABLE `matiere` (
  `CodeMatiere` VARCHAR(10) NOT NULL,
  `NomMatiere` VARCHAR(100) NOT NULL,
  `CoeffMatiere` INT NOT NULL DEFAULT 1,
  `CodeNiveau` VARCHAR(5) NOT NULL,
  `CodeMention` VARCHAR(5) NOT NULL,
  PRIMARY KEY (`CodeMatiere`),
  KEY `idx_matiere_classe` (`CodeNiveau`, `CodeMention`),
  CONSTRAINT `fk_matiere_classe` FOREIGN KEY (`CodeNiveau`, `CodeMention`) REFERENCES `classe` (`CodeNiveau`, `CodeMention`) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 8. Table: examen (Sessions d'examens planifiées)
-- -----------------------------------------------------------------------------
CREATE TABLE `examen` (
  `CodeExam` VARCHAR(50) NOT NULL,
  `CodeNiveau` VARCHAR(5) NOT NULL,
  `CodeMention` VARCHAR(5) NOT NULL,
  `CodeMatiere` VARCHAR(10) NOT NULL,
  `DateExam` DATE NOT NULL,
  PRIMARY KEY (`CodeExam`),
  KEY `idx_exam_matiere` (`CodeMatiere`),
  KEY `idx_exam_classe` (`CodeNiveau`, `CodeMention`),
  CONSTRAINT `fk_exam_classe` FOREIGN KEY (`CodeNiveau`, `CodeMention`) REFERENCES `classe` (`CodeNiveau`, `CodeMention`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_exam_matiere` FOREIGN KEY (`CodeMatiere`) REFERENCES `matiere` (`CodeMatiere`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 9. Table: controle (Notes d'examens)
-- -----------------------------------------------------------------------------
CREATE TABLE `controle` (
  `CodeExam` VARCHAR(50) NOT NULL,
  `NumEtu` INT NOT NULL,
  `Note` DECIMAL(5,2) NULL,
  PRIMARY KEY (`CodeExam`, `NumEtu`),
  KEY `idx_controle_etu` (`NumEtu`),
  CONSTRAINT `fk_controle_exam` FOREIGN KEY (`CodeExam`) REFERENCES `examen` (`CodeExam`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_controle_etu` FOREIGN KEY (`NumEtu`) REFERENCES `etudiant` (`NumEtu`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 10. Table: professeur (Corps enseignant) [NOUVELLE FONCTIONNALITE]
-- -----------------------------------------------------------------------------
CREATE TABLE `professeur` (
  `IdProf` INT AUTO_INCREMENT NOT NULL,
  `NomProf` VARCHAR(50) NOT NULL,
  `PrenomProf` VARCHAR(60) NOT NULL,
  `EmailProf` VARCHAR(100) NULL,
  `TelProf` VARCHAR(20) NULL,
  `GradeProf` VARCHAR(50) DEFAULT 'Enseignant',
  `SpecialiteProf` VARCHAR(100) NULL,
  `DateRecrutement` DATE NULL,
  PRIMARY KEY (`IdProf`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 11. Table: enseigner (Affectation professeur-matière) [NOUVELLE FONCTIONNALITE]
-- -----------------------------------------------------------------------------
CREATE TABLE `enseigner` (
  `IdProf` INT NOT NULL,
  `CodeMatiere` VARCHAR(10) NOT NULL,
  `AnneeUniv` VARCHAR(9) NOT NULL,
  PRIMARY KEY (`IdProf`, `CodeMatiere`, `AnneeUniv`),
  CONSTRAINT `fk_ens_prof` FOREIGN KEY (`IdProf`) REFERENCES `professeur` (`IdProf`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_ens_mat` FOREIGN KEY (`CodeMatiere`) REFERENCES `matiere` (`CodeMatiere`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_ens_annee` FOREIGN KEY (`AnneeUniv`) REFERENCES `anneeuniv` (`AnneeUniv`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 12. Table: emploi_du_temps (Séances de cours / EDT) [NOUVELLE FONCTIONNALITE]
-- -----------------------------------------------------------------------------
CREATE TABLE `emploi_du_temps` (
  `IdSeance` INT AUTO_INCREMENT NOT NULL,
  `CodeNiveau` VARCHAR(5) NOT NULL,
  `CodeMention` VARCHAR(5) NOT NULL,
  `CodeMatiere` VARCHAR(10) NOT NULL,
  `IdProf` INT NULL,
  `JourSemaine` ENUM('Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi') NOT NULL,
  `HeureDebut` TIME NOT NULL,
  `HeureFin` TIME NOT NULL,
  `Salle` VARCHAR(30) NULL,
  `AnneeUniv` VARCHAR(9) NOT NULL,
  PRIMARY KEY (`IdSeance`),
  KEY `idx_edt_classe` (`CodeNiveau`, `CodeMention`),
  CONSTRAINT `fk_edt_classe` FOREIGN KEY (`CodeNiveau`, `CodeMention`) REFERENCES `classe` (`CodeNiveau`, `CodeMention`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_edt_matiere` FOREIGN KEY (`CodeMatiere`) REFERENCES `matiere` (`CodeMatiere`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_edt_prof` FOREIGN KEY (`IdProf`) REFERENCES `professeur` (`IdProf`) ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT `fk_edt_annee` FOREIGN KEY (`AnneeUniv`) REFERENCES `anneeuniv` (`AnneeUniv`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 13. Table: presence (Présences et Assiduité) [NOUVELLE FONCTIONNALITE]
-- -----------------------------------------------------------------------------
CREATE TABLE `presence` (
  `IdPresence` INT AUTO_INCREMENT NOT NULL,
  `NumEtu` INT NOT NULL,
  `IdSeance` INT NOT NULL,
  `DatePresence` DATE NOT NULL,
  `Statut` ENUM('Present','Absent','Excuse') NOT NULL DEFAULT 'Present',
  `Motif` VARCHAR(255) NULL,
  PRIMARY KEY (`IdPresence`),
  KEY `idx_presence_seance` (`IdSeance`, `DatePresence`),
  CONSTRAINT `fk_presence_etu` FOREIGN KEY (`NumEtu`) REFERENCES `etudiant` (`NumEtu`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_presence_seance` FOREIGN KEY (`IdSeance`) REFERENCES `emploi_du_temps` (`IdSeance`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 14. Table: notification (Centre de notifications) [NOUVELLE FONCTIONNALITE]
-- -----------------------------------------------------------------------------
CREATE TABLE `notification` (
  `IdNotif` INT AUTO_INCREMENT NOT NULL,
  `IdUGU` INT NOT NULL,
  `Message` VARCHAR(255) NOT NULL,
  `TypeNotif` ENUM('info','warning','success','danger') NOT NULL DEFAULT 'info',
  `Lu` TINYINT(1) NOT NULL DEFAULT 0,
  `DateCreation` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`IdNotif`),
  KEY `idx_notif_user` (`IdUGU`),
  CONSTRAINT `fk_notif_user` FOREIGN KEY (`IdUGU`) REFERENCES `utilisateur` (`IdUGU`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 15. Table: audit_log (Journal d'activité / Sécurité) [NOUVELLE FONCTIONNALITE]
-- -----------------------------------------------------------------------------
CREATE TABLE `audit_log` (
  `IdLog` INT AUTO_INCREMENT NOT NULL,
  `IdUGU` INT NULL,
  `Action` VARCHAR(50) NOT NULL,
  `Cible` VARCHAR(100) NULL,
  `Details` TEXT NULL,
  `IpAddress` VARCHAR(45) NULL,
  `DateLog` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`IdLog`),
  KEY `idx_audit_user` (`IdUGU`),
  CONSTRAINT `fk_audit_user` FOREIGN KEY (`IdUGU`) REFERENCES `utilisateur` (`IdUGU`) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
