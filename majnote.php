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
    $note = (float)str_replace(',', '.', $_POST['note'] ?? 0);
    $niveau = trim($_POST['niveau'] ?? 'L1');
    $mention = trim($_POST['mention'] ?? 'IG');

    if ($note < 0 || $note > 20) {
        set_flash('danger', 'La note doit être comprise entre 0.00 et 20.00.');
        redirect("controle.php?codeexam=$codeexam&codeniv=$niveau&codemen=$mention");
    }

    try {
        $stmt = $db->prepare('UPDATE controle SET Note = ? WHERE CodeExam = ? AND NumEtu = ?');
        $stmt->execute([$note, $codeexam, $numetu]);

        log_audit($db, 'MODIF_NOTE', 'Contrôle', "Modification note étudiant #$numetu à $note ($codeexam)");
        set_flash('success', "La note de l'étudiant #$numetu a été modifiée avec succès ($note/20) !");

    } catch (Exception $e) {
        error_log('Erreur majnote: ' . $e->getMessage());
        set_flash('danger', 'Erreur lors de la modification de la note.');
    }

    redirect("controle.php?codeexam=$codeexam&codeniv=$niveau&codemen=$mention");
}

redirect('examen.php');
