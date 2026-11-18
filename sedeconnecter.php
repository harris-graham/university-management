<?php
/**
 * GestUniv - Déconnexion sécurisée
 */

require_once __DIR__ . '/inclus/bd.php';
require_once __DIR__ . '/session.php';

if (logged_in()) {
    log_audit($db, 'DECONNEXION', 'Authentification', 'Déconnexion manuelle de l\'utilisateur');
}

// Nettoyage complet de la session
$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();
session_start();
set_flash('info', 'Vous avez été déconnecté avec succès.');
redirect('connexion.php');
