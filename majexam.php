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
    $niveauexam = trim($_POST['niveauexam'] ?? 'L1');
    $mentionexam = trim($_POST['mentionexam'] ?? 'IG');
    $dateexam = trim($_POST['dateexam'] ?? date('Y-m-d'));

    try {
        $stmt = $db->prepare('UPDATE examen SET DateExam = ? WHERE CodeExam = ?');
        $stmt->execute([$dateexam, $codeexam]);

        log_audit($db, 'MODIF_EXAMEN', 'Examen', "Modification date examen $codeexam au $dateexam");
        set_flash('success', "La date de l'examen a été mise à jour avec succès !");

    } catch (Exception $e) {
        error_log('Erreur majexam: ' . $e->getMessage());
        set_flash('danger', 'Erreur lors de la modification de la date d\'examen.');
    }

    redirect("examen.php?codeniv=$niveauexam&codemen=$mentionexam");
}

redirect('examen.php');
