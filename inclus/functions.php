<?php
/**
 * GestUniv - Fonctions utilitaires et aides globales
 */

require_once __DIR__ . '/config.php';

/**
 * Échappement sécurisé pour prévenir les failles XSS
 *
 * @param mixed $str
 * @return string
 */
function h($str) {
    if ($str === null) {
        return '';
    }
    return htmlspecialchars((string)$str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Enregistrement d'un message flash en session
 *
 * @param string $type ('success', 'danger', 'warning', 'info')
 * @param string $message
 */
function set_flash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Récupération et suppression du message flash
 *
 * @return array|null
 */
function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Redirection HTTP sécurisée
 *
 * @param string $url
 */
function redirect($url) {
    if (!headers_sent()) {
        header("Location: $url");
        exit();
    }
    echo '<script>window.location.href = ' . json_encode($url) . ';</script>';
    echo '<noscript><meta http-equiv="refresh" content="0;url=' . h($url) . '"></noscript>';
    exit();
}

/**
 * Vérification des permissions par rôle
 *
 * @param string|array $allowed_roles (ex: 'CSCO', ['CSCO', 'RM'])
 */
function require_role($allowed_roles) {
    $roles = (array)$allowed_roles;
    $currentRole = $_SESSION['AbrRole'] ?? '';
    if (!in_array($currentRole, $roles, true)) {
        set_flash('danger', 'Accès interdit. Vos privilèges ne vous permettent pas d\'accéder à cette section.');
        redirect('accueil.php');
    }
}

/**
 * Journalisation dans la table d'audit
 *
 * @param PDO $db
 * @param string $action
 * @param string|null $cible
 * @param string|null $details
 */
function log_audit($db, $action, $cible = null, $details = null) {
    try {
        $idUser = $_SESSION['IdUGU'] ?? null;
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        // En cas d'IP multiples via proxy
        if (strpos($ip, ',') !== false) {
            $ip = trim(explode(',', $ip)[0]);
        }

        $stmt = $db->prepare('INSERT INTO audit_log (IdUGU, Action, Cible, Details, IpAddress) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$idUser, $action, $cible, $details, substr($ip, 0, 45)]);
    } catch (Exception $e) {
        // Éviter de bloquer l'application si la table audit_log n'est pas encore créée
        error_log('Audit log error: ' . $e->getMessage());
    }
}

/**
 * Obtenir le nombre de notifications non lues
 *
 * @param PDO $db
 * @return int
 */
function get_unread_notifications($db) {
    if (!isset($_SESSION['IdUGU'])) {
        return 0;
    }
    try {
        $stmt = $db->prepare('SELECT COUNT(*) FROM notification WHERE IdUGU = ? AND Lu = 0');
        $stmt->execute([$_SESSION['IdUGU']]);
        return (int)$stmt->fetchColumn();
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * Créer une notification pour un utilisateur
 *
 * @param PDO $db
 * @param int $userId
 * @param string $message
 * @param string $type
 */
function create_notification($db, $userId, $message, $type = 'info') {
    try {
        $stmt = $db->prepare('INSERT INTO notification (IdUGU, Message, TypeNotif) VALUES (?, ?, ?)');
        $stmt->execute([$userId, $message, $type]);
    } catch (Exception $e) {
        error_log('Notification error: ' . $e->getMessage());
    }
}

/**
 * Obtenir la dernière année universitaire active
 *
 * @param PDO $db
 * @return string
 */
function get_active_academic_year($db) {
    try {
        $stmt = $db->query('SELECT AnneeUniv FROM anneeuniv ORDER BY AnneeUniv DESC LIMIT 1');
        $annee = $stmt->fetchColumn();
        return $annee ?: date('Y') . '-' . (date('Y') + 1);
    } catch (Exception $e) {
        return date('Y') . '-' . (date('Y') + 1);
    }
}

/**
 * Formater une date au format français
 *
 * @param string|null $date
 * @return string
 */
function format_date_fr($date) {
    if (!$date || $date === '0000-00-00') {
        return '-';
    }
    $timestamp = strtotime($date);
    if (!$timestamp) {
        return $date;
    }
    return date('d/m/Y', $timestamp);
}
