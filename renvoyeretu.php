<?php
require_once __DIR__ . '/inclus/bd.php';
require_once __DIR__ . '/session.php';
confirm_logged_in();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'Jeton CSRF invalide.');
        redirect('accueil.php');
    }

    $numetu = (int)($_POST['numetu'] ?? 0);
    $nometu = trim($_POST['nometu'] ?? '');
    $prenometu = trim($_POST['prenometu'] ?? '');
    $niveauetu = trim($_POST['niveauetu'] ?? 'L1');
    $mentionetu = trim($_POST['mentionetu'] ?? 'IG');

    try {
        $stmt = $db->prepare('DELETE FROM etudiant WHERE NumEtu = ?');
        $stmt->execute([$numetu]);

        log_audit($db, 'RENVOI_ETUDIANT', 'Etudiant', "Renvoi / radiation étudiant #$numetu ($nometu $prenometu)");
        set_flash('success', "L'étudiant $nometu $prenometu a été officiellement renvoyé.");

    } catch (Exception $e) {
        error_log('Erreur renvoyeretu: ' . $e->getMessage());
        set_flash('danger', 'Erreur lors du renvoi de l\'étudiant.');
    }

    redirect("etudiant.php?codeniv=$niveauetu&codemen=$mentionetu");
}

redirect('accueil.php');
