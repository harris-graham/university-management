<?php
require_once __DIR__ . '/inclus/bd.php';
require_once __DIR__ . '/session.php';
confirm_logged_in();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'Jeton CSRF invalide.');
        redirect('presence.php');
    }

    $idseance = (int)($_POST['idseance'] ?? 0);
    $datepresence = trim($_POST['datepresence'] ?? date('Y-m-d'));
    $codeniv = trim($_POST['codeniv'] ?? 'L1');
    $codemen = trim($_POST['codemen'] ?? 'IG');
    $statuts = $_POST['statut'] ?? [];
    $motifs = $_POST['motif'] ?? [];

    if ($idseance > 0 && !empty($statuts)) {
        try {
            $db->beginTransaction();

            $stmtUpsert = $db->prepare(
                'INSERT INTO presence (NumEtu, IdSeance, DatePresence, Statut, Motif) ' .
                'VALUES (?, ?, ?, ?, ?) ' .
                'ON DUPLICATE KEY UPDATE Statut = VALUES(Statut), Motif = VALUES(Motif)'
            );

            // Pour éviter les doublons si la table n'a pas de contrainte unique composite
            // Supprimer d'abord les présences existantes pour cette séance et cette date
            $stmtDel = $db->prepare('DELETE FROM presence WHERE IdSeance = ? AND DatePresence = ?');
            $stmtDel->execute([$idseance, $datepresence]);

            $stmtIns = $db->prepare('INSERT INTO presence (NumEtu, IdSeance, DatePresence, Statut, Motif) VALUES (?, ?, ?, ?, ?)');

            foreach ($statuts as $numEtu => $statut) {
                $num = (int)$numEtu;
                $st = in_array($statut, ['Present', 'Absent', 'Excuse']) ? $statut : 'Present';
                $mot = trim($motifs[$num] ?? '');
                $stmtIns->execute([$num, $idseance, $datepresence, $st, $mot]);
            }

            $db->commit();

            log_audit($db, 'MARQUER_PRESENCE', 'Assiduité', "Appel enregistré pour séance #$idseance le $datepresence (" . count($statuts) . " étudiants)");
            set_flash('success', 'La feuille de présence a été enregistrée avec succès !');

        } catch (Exception $e) {
            $db->rollBack();
            error_log('Erreur marquerpresence: ' . $e->getMessage());
            set_flash('danger', 'Erreur lors de l\'enregistrement des présences : ' . $e->getMessage());
        }
    }

    redirect("presence.php?codeniv=$codeniv&codemen=$codemen&idseance=$idseance&date=$datepresence");
}

redirect('presence.php');
