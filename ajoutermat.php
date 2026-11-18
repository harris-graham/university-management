<?php
require_once __DIR__ . '/inclus/bd.php';
require_once __DIR__ . '/session.php';
confirm_logged_in();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'Jeton CSRF invalide.');
        redirect('matiere.php');
    }

    $codemat = strtoupper(trim($_POST['codemat'] ?? ''));
    $nommat = trim($_POST['nommat'] ?? '');
    $coeffmat = (int)($_POST['coeffmat'] ?? 1);
    $niveaumat = trim($_POST['niveaumat'] ?? '');
    $mentionmat = trim($_POST['mentionmat'] ?? '');

    try {
        $stmt = $db->prepare('INSERT INTO matiere (CodeMatiere, NomMatiere, CoeffMatiere, CodeNiveau, CodeMention) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$codemat, $nommat, $coeffmat, $niveaumat, $mentionmat]);

        log_audit($db, 'AJOUT_MATIERE', 'Matière', "Ajout matière $codemat ($nommat) pour $niveaumat $mentionmat");
        set_flash('success', "La matière $nommat ($codemat) a été enregistrée avec succès !");

    } catch (Exception $e) {
        error_log('Erreur ajoutermat: ' . $e->getMessage());
        set_flash('danger', 'Erreur lors de l\'enregistrement de la matière : ' . $e->getMessage());
    }

    redirect("matiere.php?codeniv=$niveaumat&codemen=$mentionmat");
}

redirect('matiere.php');
