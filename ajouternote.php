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

    // Validation note entre 0 et 20
    if ($note < 0 || $note > 20) {
        set_flash('danger', 'La note doit être comprise entre 0.00 et 20.00.');
        redirect("controle.php?codeexam=$codeexam&codeniv=$niveau&codemen=$mention");
    }

    try {
        $stmtCheck = $db->prepare('SELECT COUNT(*) FROM controle WHERE CodeExam = ? AND NumEtu = ?');
        $stmtCheck->execute([$codeexam, $numetu]);

        if ($stmtCheck->fetchColumn() > 0) {
            // Mise à jour si existe déjà
            $stmtUpdate = $db->prepare('UPDATE controle SET Note = ? WHERE CodeExam = ? AND NumEtu = ?');
            $stmtUpdate->execute([$note, $codeexam, $numetu]);
            set_flash('info', "La note de l'étudiant #$numetu a été mise à jour ($note/20).");
        } else {
            // Insertion
            $stmtInsert = $db->prepare('INSERT INTO controle (CodeExam, NumEtu, Note) VALUES (?, ?, ?)');
            $stmtInsert->execute([$codeexam, $numetu, $note]);
            set_flash('success', "La note de l'étudiant #$numetu ($note/20) a bien été enregistrée.");
        }

        log_audit($db, 'NOTE_CONTROLE', 'Contrôle', "Attribution note $note pour étudiant #$numetu à l'examen $codeexam");

    } catch (Exception $e) {
        error_log('Erreur ajouternote: ' . $e->getMessage());
        set_flash('danger', 'Erreur lors de l\'enregistrement de la note.');
    }

    redirect("controle.php?codeexam=$codeexam&codeniv=$niveau&codemen=$mention");
}

redirect('examen.php');
