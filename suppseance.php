<?php
require_once __DIR__ . '/inclus/bd.php';
require_once __DIR__ . '/session.php';
confirm_logged_in();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'Jeton CSRF invalide.');
        redirect('emploi_du_temps.php');
    }

    $idseance = (int)($_POST['idseance'] ?? 0);
    $codeniv = trim($_POST['codeniv'] ?? 'L1');
    $codemen = trim($_POST['codemen'] ?? 'IG');

    try {
        $stmt = $db->prepare('DELETE FROM emploi_du_temps WHERE IdSeance = ?');
        $stmt->execute([$idseance]);

        log_audit($db, 'SUPP_SEANCE_EDT', 'Emploi du temps', "Suppression séance ID #$idseance");
        set_flash('success', 'Séance supprimée de l\'emploi du temps.');
    } catch (Exception $e) {
        error_log('Erreur suppseance: ' . $e->getMessage());
        set_flash('danger', 'Erreur lors de la suppression de la séance.');
    }

    redirect("emploi_du_temps.php?codeniv=$codeniv&codemen=$codemen");
}

redirect('emploi_du_temps.php');
