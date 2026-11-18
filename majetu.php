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
    $datenaiss = !empty($_POST['datenaiss']) ? $_POST['datenaiss'] : null;
    $cinetu = trim($_POST['cinetu'] ?? '');
    $sexeetu = trim($_POST['sexeetu'] ?? 'Masculin');
    $nationetu = trim($_POST['nationetu'] ?? 'Malagasy');
    $teletu = trim($_POST['teletu'] ?? '');
    $adresseetu = trim($_POST['adresseetu'] ?? '');
    $niveauetu = trim($_POST['niveauetu'] ?? 'L1');
    $mentionetu = trim($_POST['mentionetu'] ?? 'IG');

    try {
        $stmt = $db->prepare(
            'UPDATE etudiant SET NomEtu = ?, PrenomEtu = ?, DateNaiss = ?, CinEtu = ?, SexeEtu = ?, NationEtu = ?, TelEtu = ?, AdresseEtu = ? WHERE NumEtu = ?'
        );
        $stmt->execute([$nometu, $prenometu, $datenaiss, $cinetu, $sexeetu, $nationetu, $teletu, $adresseetu, $numetu]);

        log_audit($db, 'MODIF_ETUDIANT', 'Etudiant', "Mise à jour coordonnées étudiant #$numetu ($nometu $prenometu)");
        set_flash('success', "Les informations de l'étudiant $nometu $prenometu ont été mises à jour !");

    } catch (Exception $e) {
        error_log('Erreur majetu: ' . $e->getMessage());
        set_flash('danger', 'Erreur lors de la mise à jour de l\'étudiant.');
    }

    redirect("etudiant.php?codeniv=$niveauetu&codemen=$mentionetu");
}

redirect('accueil.php');
