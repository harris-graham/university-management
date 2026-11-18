<?php
/**
 * GestUniv - Traitement de la connexion utilisateur
 */

require_once __DIR__ . '/inclus/bd.php';
require_once __DIR__ . '/session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification du jeton CSRF
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'Jeton de sécurité CSRF expiré ou invalide. Veuillez réessayer.');
        redirect('connexion.php');
    }

    $nom = trim($_POST['nom'] ?? '');
    $mdp = trim($_POST['mdp'] ?? '');

    if (empty($nom) || empty($mdp)) {
        set_flash('warning', 'Veuillez renseigner le nom d\'utilisateur et le mot de passe.');
        redirect('connexion.php');
    }

    try {
        $stmt = $db->prepare('SELECT IdUGU, NomCGU, MdpCGU, RoleUGU, AbrRole FROM utilisateur WHERE NomCGU = ? LIMIT 1');
        $stmt->execute([$nom]);
        $user = $stmt->fetch();

        $authenticated = false;

        if ($user) {
            // Vérification par hash sécurisé (bcrypt/argon2)
            if (password_verify($mdp, $user['MdpCGU'])) {
                $authenticated = true;
            }
            // Compatibilité ascendante : si le mot de passe dans la base est encore en texte brut
            elseif ($user['MdpCGU'] === $mdp) {
                $authenticated = true;
                // Migration automatique vers bcrypt
                $newHash = password_hash($mdp, PASSWORD_BCRYPT);
                $updateStmt = $db->prepare('UPDATE utilisateur SET MdpCGU = ? WHERE IdUGU = ?');
                $updateStmt->execute([$newHash, $user['IdUGU']]);
            }
        }

        if ($authenticated) {
            // Régénération de l'ID de session pour prévenir la fixation de session
            session_regenerate_id(true);

            $_SESSION['IdUGU'] = $user['IdUGU'];
            $_SESSION['NomCGU'] = $user['NomCGU'];
            $_SESSION['RoleUGU'] = $user['RoleUGU'];
            $_SESSION['AbrRole'] = $user['AbrRole'];
            // Sécurité : NE JAMAIS stocker le mot de passe en session !

            log_audit($db, 'CONNEXION', 'Authentification', 'Connexion réussie en tant que ' . $user['RoleUGU']);
            set_flash('success', 'Bienvenue ! Vous êtes connecté en tant que ' . $user['RoleUGU'] . '.');
            redirect('accueil.php');
        } else {
            log_audit($db, 'ECHEC_CONNEXION', 'Authentification', 'Tentative infructueuse avec le nom: ' . $nom);
            set_flash('danger', 'Identifiants incorrects. Veuillez vérifier vos données de connexion.');
            redirect('connexion.php');
        }

    } catch (Exception $e) {
        error_log('Erreur login: ' . $e->getMessage());
        set_flash('danger', 'Une erreur technique est survenue lors de la tentative de connexion.');
        redirect('connexion.php');
    }
}

redirect('connexion.php');
