<?php
require_once __DIR__ . '/inclus/bd.php';
require_once __DIR__ . '/session.php';
confirm_logged_in();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'Jeton CSRF invalide.');
        redirect('emploi_du_temps.php');
    }

    $codeniv = trim($_POST['codeniv'] ?? '');
    $codemen = trim($_POST['codemen'] ?? '');
    $codemat = trim($_POST['codematiere'] ?? '');
    $idprof = !empty($_POST['idprof']) ? (int)$_POST['idprof'] : null;
    $jour = trim($_POST['joursemaine'] ?? '');
    $debut = trim($_POST['heuredebut'] ?? '');
    $fin = trim($_POST['heurefin'] ?? '');
    $salle = trim($_POST['salle'] ?? '');
    $annee = trim($_POST['anneeuniv'] ?? '');

    try {
        $stmt = $db->prepare('INSERT INTO emploi_du_temps (CodeNiveau, CodeMention, CodeMatiere, IdProf, JourSemaine, HeureDebut, HeureFin, Salle, AnneeUniv) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$codeniv, $codemen, $codemat, $idprof, $jour, $debut, $fin, $salle, $annee]);

        log_audit($db, 'AJOUT_SEANCE_EDT', 'Emploi du temps', "Séance $codemat pour $codeniv $codemen le $jour de $debut à $fin");
        set_flash('success', 'La séance a été planifiée avec succès !');
    } catch (Exception $e) {
        error_log('Erreur ajoutseance: ' . $e->getMessage());
        set_flash('danger', 'Erreur lors de la programmation de la séance.');
    }

    redirect("emploi_du_temps.php?codeniv=$codeniv&codemen=$codemen");
}

redirect('emploi_du_temps.php');
