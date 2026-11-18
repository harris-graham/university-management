<?php
/**
 * GestUniv - Exportation de données en format CSV / Excel
 */

require_once __DIR__ . '/inclus/bd.php';
require_once __DIR__ . '/session.php';
confirm_logged_in();

$type = $_GET['type'] ?? 'etudiants';
$codeNiv = $_GET['codeniv'] ?? '';
$codeMen = $_GET['codemen'] ?? '';

$filename = "export_{$type}_" . date('Ymd_His') . ".csv";

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');
// UTF-8 BOM pour bon affichage des accents sous Microsoft Excel
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

if ($type === 'etudiants') {
    fputcsv($output, ['Numéro', 'Nom', 'Prénom', 'Date Naissance', 'CIN', 'Sexe', 'Nationalité', 'Téléphone', 'Adresse', 'Niveau', 'Mention', 'Année Univ'], ';');

    if (!empty($codeNiv) && !empty($codeMen)) {
        $stmt = $db->prepare('SELECT NumEtu, NomEtu, PrenomEtu, DateNaiss, CinEtu, SexeEtu, NationEtu, TelEtu, AdresseEtu, CodeNiveau, CodeMention, AnneeUniv FROM etudiant WHERE CodeNiveau = ? AND CodeMention = ? ORDER BY NomEtu');
        $stmt->execute([$codeNiv, $codeMen]);
    } else {
        $stmt = $db->query('SELECT NumEtu, NomEtu, PrenomEtu, DateNaiss, CinEtu, SexeEtu, NationEtu, TelEtu, AdresseEtu, CodeNiveau, CodeMention, AnneeUniv FROM etudiant ORDER BY CodeNiveau, CodeMention, NomEtu');
    }

    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        fputcsv($output, $row, ';');
    }

} elseif ($type === 'resultats') {
    fputcsv($output, ['Classe', 'Numéro', 'Nom', 'Prénom', 'Moyenne Générale', 'Statut'], ';');

    $query = 'SELECT et.CodeNiveau, et.CodeMention, et.NumEtu, et.NomEtu, et.PrenomEtu, ' .
             'COUNT(c.Note) AS NbControle, COUNT(ex.CodeExam) AS NbExamen, ' .
             '(SUM(m.CoeffMatiere * c.Note) / SUM(m.CoeffMatiere)) AS MoyGen ' .
             'FROM etudiant et ' .
             'JOIN examen ex ON et.CodeNiveau = ex.CodeNiveau AND et.CodeMention = ex.CodeMention ' .
             'JOIN matiere m ON ex.CodeMatiere = m.CodeMatiere ' .
             'LEFT JOIN controle c ON et.NumEtu = c.NumEtu AND ex.CodeExam = c.CodeExam ';

    if (!empty($codeNiv) && !empty($codeMen)) {
        $query .= 'WHERE et.CodeNiveau = ? AND et.CodeMention = ? ';
        $query .= 'GROUP BY et.NumEtu ORDER BY MoyGen DESC';
        $stmt = $db->prepare($query);
        $stmt->execute([$codeNiv, $codeMen]);
    } else {
        $query .= 'GROUP BY et.NumEtu ORDER BY et.CodeNiveau, et.CodeMention, MoyGen DESC';
        $stmt = $db->query($query);
    }

    while ($row = $stmt->fetch()) {
        $moy = $row['MoyGen'] !== null ? number_format((float)$row['MoyGen'], 2) : '0.00';
        $statut = 'Admis';
        if ($row['NbControle'] < $row['NbExamen']) {
            $statut = 'Incomplet (Rattrapage)';
        } elseif ((float)$row['MoyGen'] < 10) {
            $statut = 'Non admis';
        }

        fputcsv($output, [
            $row['CodeNiveau'] . ' ' . $row['CodeMention'],
            $row['NumEtu'],
            $row['NomEtu'],
            $row['PrenomEtu'],
            $moy,
            $statut
        ], ';');
    }
}

fclose($output);
exit();
