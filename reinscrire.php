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
    $dateinscr = !empty($_POST['dateinscr']) ? $_POST['dateinscr'] : date('Y-m-d');
    $anneeuniv = trim($_POST['anneeuniv'] ?? '');
    $niveauetu = trim($_POST['niveauetu'] ?? '');
    $mentionetu = trim($_POST['mentionetu'] ?? '');

    try {
        $stmt = $db->prepare('UPDATE etudiant SET DateInscr = ?, AnneeUniv = ?, CodeNiveau = ? WHERE NumEtu = ?');
        $stmt->execute([$dateinscr, $anneeuniv, $niveauetu, $numetu]);

        log_audit($db, 'REINSCRIPTION_ETUDIANT', 'Etudiant', "Réinscription étudiant #$numetu vers $niveauetu ($anneeuniv)");
        set_flash('success', "La réinscription de $nometu $prenometu en $niveauetu $mentionetu est confirmée !");

    } catch (Exception $e) {
        error_log('Erreur reinscrire: ' . $e->getMessage());
        set_flash('danger', 'Erreur lors de la réinscription.');
    }

    redirect("etudiant.php?codeniv=$niveauetu&codemen=$mentionetu");
}

redirect('accueil.php');
