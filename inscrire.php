<?php
require_once __DIR__ . '/inclus/bd.php';
require_once __DIR__ . '/session.php';
confirm_logged_in();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'Jeton CSRF invalide.');
        redirect('inscription.php');
    }

    $nometu = trim($_POST['nometu'] ?? '');
    $prenometu = trim($_POST['prenometu'] ?? '');
    $datenaiss = !empty($_POST['datenaiss']) ? $_POST['datenaiss'] : null;
    $cinetu = trim($_POST['cinetu'] ?? '');
    $sexeetu = trim($_POST['sexeetu'] ?? 'Masculin');
    $nationetu = trim($_POST['nationetu'] ?? 'Malagasy');
    $teletu = trim($_POST['teletu'] ?? '');
    $adresseetu = trim($_POST['adresseetu'] ?? '');
    $dateinscr = !empty($_POST['dateinscr']) ? $_POST['dateinscr'] : date('Y-m-d');
    $anneeuniv = trim($_POST['anneeuniv'] ?? '');
    $niveauetu = trim($_POST['niveauetu'] ?? '');
    $mentionetu = trim($_POST['mentionetu'] ?? '');

    try {
        $stmt = $db->prepare(
            'INSERT INTO etudiant (NomEtu, PrenomEtu, DateNaiss, CinEtu, SexeEtu, NationEtu, TelEtu, AdresseEtu, DateInscr, AnneeUniv, CodeNiveau, CodeMention) ' .
            'VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $nometu, $prenometu, $datenaiss, $cinetu, $sexeetu,
            $nationetu, $teletu, $adresseetu, $dateinscr, $anneeuniv,
            $niveauetu, $mentionetu
        ]);

        $newId = $db->lastInsertId();

        log_audit($db, 'INSCRIPTION_ETUDIANT', 'Etudiant', "Nouvelle inscription #$newId ($nometu $prenometu en $niveauetu $mentionetu)");
        set_flash('success', "L'étudiant $nometu $prenometu a été inscrit avec succès (Matricule #$newId) !");

    } catch (Exception $e) {
        error_log('Erreur inscrire: ' . $e->getMessage());
        set_flash('danger', "Erreur lors de l'inscription : " . $e->getMessage());
    }
}

redirect('inscription.php');
