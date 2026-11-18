<?php
/**
 * GestUniv - Connexion à la base de données MySQL via PDO
 */

require_once __DIR__ . '/config.php';

try {
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        DB_HOST,
        DB_PORT,
        DB_NAME,
        DB_CHARSET
    );

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ];

    $db = new PDO($dsn, DB_USER, DB_PASS, $options);

} catch (PDOException $e) {
    // Si la base de données n'est pas encore créée, afficher un message explicite avec lien vers schema.sql
    if (APP_ENV === 'development') {
        die(
            '<div style="font-family:Segoe UI,sans-serif;max-width:700px;margin:50px auto;padding:25px;border:1px solid #e3e6f0;border-radius:8px;background:#fff;box-shadow:0 4px 6px rgba(0,0,0,0.1);">' .
            '<h2 style="color:#e74a3b;">Erreur de connexion à la base de données</h2>' .
            '<p>Impossible de se connecter à la base MySQL <strong>' . htmlspecialchars(DB_NAME) . '</strong> sur <strong>' . htmlspecialchars(DB_HOST) . '</strong>.</p>' .
            '<p style="background:#f8f9fc;padding:12px;border-left:4px solid #e74a3b;font-family:monospace;font-size:13px;">' . htmlspecialchars($e->getMessage()) . '</p>' .
            '<hr style="border:0;border-top:1px solid #eaecf4;margin:20px 0;">' .
            '<h4>Comment initialiser la base de données ?</h4>' .
            '<ol style="line-height:1.7;">' .
            '<li>Assurez-vous que votre serveur MySQL est démarré (WampServer, XAMPP ou Laragon).</li>' .
            '<li>Importez le fichier <code>database/schema.sql</code> pour créer les tables.</li>' .
            '<li>Importez le fichier <code>database/seed.sql</code> pour charger les données de test.</li>' .
            '<li>Vous pouvez aussi exécuter dans votre terminal :<br><code>mysql -u root -p &lt; database/schema.sql</code><br><code>mysql -u root -p gestion_universite &lt; database/seed.sql</code></li>' .
            '</ol>' .
            '</div>'
        );
    } else {
        die('Erreur de connexion à la base de données. Veuillez contacter l\'administrateur.');
    }
}
