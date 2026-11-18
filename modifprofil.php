<?php
/**
 * GestUniv - Mise à jour du profil utilisateur
 */

require_once __DIR__ . '/inclus/bd.php';
require_once __DIR__ . '/session.php';
confirm_logged_in();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'Jeton CSRF invalide.');
        redirect('accueil.php');
    }

    $idutil = $_SESSION['IdUGU'];
    $nomcompte = trim($_POST['nomcompte'] ?? '');
    $mdpcompte = trim($_POST['mdpcompte'] ?? '');

    if (empty($nomcompte) || empty($mdpcompte)) {
        set_flash('warning', 'Tous les champs sont requis.');
        redirect('accueil.php');
    }

    try {
        $hash = password_hash($mdpcompte, PASSWORD_BCRYPT);
        $stmt = $db->prepare('UPDATE utilisateur SET NomCGU = ?, MdpCGU = ? WHERE IdUGU = ?');
        $stmt->execute([$nomcompte, $hash, $idutil]);

        $_SESSION['NomCGU'] = $nomcompte;

        log_audit($db, 'MODIF_PROFIL', 'Utilisateur', 'Mise à jour du mot de passe et nom');
        set_flash('success', 'Votre compte a été mis à jour avec succès !');
    } catch (Exception $e) {
        error_log('Erreur modifprofil: ' . $e->getMessage());
        set_flash('danger', 'Erreur lors de la mise à jour de vos informations.');
    }
}

redirect('accueil.php');
