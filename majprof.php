<?php
require_once __DIR__ . '/inclus/bd.php';
require_once __DIR__ . '/session.php';
confirm_logged_in();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'Jeton CSRF invalide.');
        redirect('professeur.php');
    }

    $idprof = (int)($_POST['idprof'] ?? 0);
    $nom = trim($_POST['nomprof'] ?? '');
    $prenom = trim($_POST['prenomprof'] ?? '');
    $email = trim($_POST['emailprof'] ?? '');
    $tel = trim($_POST['telprof'] ?? '');
    $grade = trim($_POST['gradeprof'] ?? '');
    $specialite = trim($_POST['specialiteprof'] ?? '');

    try {
        $stmt = $db->prepare('UPDATE professeur SET NomProf = ?, PrenomProf = ?, EmailProf = ?, TelProf = ?, GradeProf = ?, SpecialiteProf = ? WHERE IdProf = ?');
        $stmt->execute([$nom, $prenom, $email, $tel, $grade, $specialite, $idprof]);

        log_audit($db, 'MODIF_PROFESSEUR', 'Professeur', "Mise à jour ID #$idprof ($nom $prenom)");
        set_flash('success', "Les informations de l'enseignant $nom $prenom ont été mises à jour !");
    } catch (Exception $e) {
        error_log('Erreur majprof: ' . $e->getMessage());
        set_flash('danger', 'Erreur lors de la mise à jour.');
    }
}

redirect('professeur.php');
