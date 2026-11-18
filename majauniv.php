<?php
require_once __DIR__ . '/inclus/bd.php';
require_once __DIR__ . '/session.php';
confirm_logged_in();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'Jeton CSRF invalide.');
        redirect('accueil.php');
    }

    $anneeuniv = trim($_POST['anneeuniv'] ?? '');

    if (!empty($anneeuniv)) {
        try {
            $stmt = $db->prepare('INSERT INTO anneeuniv (AnneeUniv) VALUES (?) ON DUPLICATE KEY UPDATE AnneeUniv = VALUES(AnneeUniv)');
            $stmt->execute([$anneeuniv]);

            log_audit($db, 'NOUVELLE_ANNEE_UNIV', 'Année universitaire', "Ouverture année $anneeuniv");
            set_flash('success', "L'année universitaire $anneeuniv a bien été enregistrée et activée !");

        } catch (Exception $e) {
            error_log('Erreur majauniv: ' . $e->getMessage());
            set_flash('danger', 'Erreur lors de l\'enregistrement de l\'année universitaire.');
        }
    }
}

redirect('accueil.php');
