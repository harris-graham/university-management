<?php
require_once __DIR__ . '/inclus/bd.php';
require_once __DIR__ . '/session.php';
confirm_logged_in();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'Jeton CSRF invalide.');
        redirect('matiere.php');
    }

    $codemat = trim($_POST['codemat'] ?? '');
    $nommat = trim($_POST['nommat'] ?? '');
    $niveaumat = trim($_POST['niveaumat'] ?? 'L1');
    $mentionmat = trim($_POST['mentionmat'] ?? 'IG');

    try {
        $stmt = $db->prepare('DELETE FROM matiere WHERE CodeMatiere = ?');
        $stmt->execute([$codemat]);

        log_audit($db, 'SUPP_MATIERE', 'Matière', "Suppression matière $codemat ($nommat)");
        set_flash('success', "La matière $nommat a été supprimée avec succès.");

    } catch (Exception $e) {
        error_log('Erreur effacermat: ' . $e->getMessage());
        set_flash('danger', 'Impossible de supprimer cette matière (elle est associée à des examens ou cours).');
    }

    redirect("matiere.php?codeniv=$niveaumat&codemen=$mentionmat");
}

redirect('matiere.php');
