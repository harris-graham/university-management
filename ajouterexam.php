<?php
require_once __DIR__ . '/inclus/bd.php';
require_once __DIR__ . '/session.php';
confirm_logged_in();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'Jeton CSRF invalide.');
        redirect('examen.php');
    }

    $anneeuniv = trim($_POST['anneeuniv'] ?? '');
    $niveauexam = trim($_POST['niveauexam'] ?? '');
    $mentionexam = trim($_POST['mentionexam'] ?? '');
    $matiereexam = trim($_POST['matiereexam'] ?? '');
    $dateexam = trim($_POST['dateexam'] ?? date('Y-m-d'));

    // Format du code d'examen standard
    $codeexam = "{$niveauexam}-{$mentionexam}-{$matiereexam}_{$anneeuniv}";

    try {
        $stmt = $db->prepare('INSERT INTO examen (CodeExam, CodeNiveau, CodeMention, CodeMatiere, DateExam) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$codeexam, $niveauexam, $mentionexam, $matiereexam, $dateexam]);

        log_audit($db, 'AJOUT_EXAMEN', 'Examen', "Création examen $codeexam le $dateexam");
        set_flash('success', "L'examen a bien été programmé et enregistré !");

    } catch (Exception $e) {
        error_log('Erreur ajouterexam: ' . $e->getMessage());
        set_flash('danger', "Erreur lors de l'enregistrement de l'examen (vérifiez s'il n'existe pas déjà).");
    }

    redirect("examen.php?codeniv=$niveauexam&codemen=$mentionexam");
}

redirect('examen.php');
