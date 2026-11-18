<?php
/**
 * GestUniv - Gestion des sessions et authentification
 */

require_once __DIR__ . '/inclus/config.php';
require_once __DIR__ . '/inclus/functions.php';

/**
 * Vérifier si l'utilisateur est connecté
 *
 * @return bool
 */
function logged_in() {
    return isset($_SESSION['IdUGU']) && !empty($_SESSION['IdUGU']);
}

/**
 * Restreindre l'accès aux utilisateurs connectés uniquement
 */
function confirm_logged_in() {
    if (!logged_in()) {
        set_flash('warning', 'Veuillez vous connecter pour accéder à cette page.');
        redirect('connexion.php');
    }
}
