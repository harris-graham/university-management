<?php
require_once __DIR__ . '/inclus/bd.php';
require_once __DIR__ . '/session.php';
confirm_logged_in();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'Jeton CSRF invalide.');
        redirect('examen.php');
    }

    $codeexam = trim($_POST['codeexam'] ?? '');
    $numetu = (int)($_POST['numetu'] ?? 0);
    $niveau = trim($_POST['niveau'] ?? 'L1');
    $mention = trim($_POST['mention'] ?? 'IG');

    try {
        $stmt = $db->prepare('DELETE FROM controle WHERE CodeExam = ? AND NumEtu = ?');
        $stmt->execute([$codeexam, $numetu]);

        log_audit($db, 'SUPP_NOTE', 'Contrôle', "Suppression note étudiant #$numetu pour $codeexam");
        set_flash('success', "La note de l'étudiant #$numetu a été retirée.");

    } catch (Exception $e) {
        error_log('Erreur effacernote: ' . $e->getMessage());
        set_flash('danger', 'Erreur lors de la suppression de la note.');
    }

    redirect("controle.php?codeexam=$codeexam&codeniv=$niveau&codemen=$mention");
}

redirect('examen.php');
