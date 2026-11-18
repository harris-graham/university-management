<?php
/**
 * GestUniv - Configuration générale
 * EMIT (École de Management et d'Innovation Technologique)
 */

if (!defined('APP_NAME')) {
    define('APP_NAME', 'EMIT - GestUniv');
    define('APP_VERSION', '2.0.0');
    define('APP_INSTITUTION', 'École de Management et d\'Innovation Technologique');

    // Paramètres de connexion MySQL
    define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
    define('DB_PORT', getenv('DB_PORT') ?: '3306');
    define('DB_USER', getenv('DB_USER') ?: 'root');
    define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
    define('DB_NAME', getenv('DB_NAME') ?: 'gestion_universite');
    define('DB_CHARSET', 'utf8mb4');

    // Environnement: 'development' ou 'production'
    define('APP_ENV', getenv('APP_ENV') ?: 'development');
}

// Démarrer la session si non déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Génération d'un jeton CSRF
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validation d'un jeton CSRF
 */
function verify_csrf_token($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Champ HTML caché CSRF
 */
function csrf_field() {
    $token = generate_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}
