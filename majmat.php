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
    $coeffmat = (int)($_POST['coeffmat'] ?? 1);
    $niveaumat = trim($_POST['niveaumat'] ?? '');
    $mentionmat = trim($_POST['mentionmat'] ?? '');

    try {
        $stmt = $db->prepare('UPDATE matiere SET NomMatiere = ?, CoeffMatiere = ?, CodeNiveau = ?, CodeMention = ? WHERE CodeMatiere = ?');
        $stmt->execute([$nommat, $coeffmat, $niveaumat, $mentionmat, $codemat]);

        log_audit($db, 'MODIF_MATIERE', 'Matière', "Mise à jour matière $codemat ($nommat)");
        set_flash('success', "La matière $nommat a été modifiée avec succès !");

    } catch (Exception $e) {
        error_log('Erreur majmat: ' . $e->getMessage());
        set_flash('danger', 'Erreur lors de la modification de la matière.');
    }

    redirect("matiere.php?codeniv=$niveaumat&codemen=$mentionmat");
}

redirect('matiere.php');
