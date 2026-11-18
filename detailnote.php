<?php
  require_once __DIR__ . '/inclus/barrenav.php';

  $numetu = (int)($_GET['numetu'] ?? 0);
  $stmtEtu = $db->prepare('SELECT * FROM etudiant WHERE NumEtu = ?');
  $stmtEtu->execute([$numetu]);
  $etudiant = $stmtEtu->fetch();

  if (!$etudiant) {
      set_flash('danger', 'Étudiant introuvable.');
      redirect('accueil.php');
  }

  $codeniv = $etudiant['CodeNiveau'];
  $codemen = $etudiant['CodeMention'];

  // Liste des notes
  $stmtNotes = $db->prepare(
    'SELECT m.NomMatiere, m.CoeffMatiere, ex.DateExam, ex.CodeExam, c.Note ' .
    'FROM examen ex ' .
    'JOIN matiere m ON ex.CodeMatiere = m.CodeMatiere ' .
    'LEFT JOIN controle c ON c.CodeExam = ex.CodeExam AND c.NumEtu = ? ' .
    'WHERE ex.CodeNiveau = ? AND ex.CodeMention = ? ' .
    'ORDER BY m.NomMatiere'
  );
  $stmtNotes->execute([$numetu, $codeniv, $codemen]);
  $notes = $stmtNotes->fetchAll();

  $totalPoints = 0;
  $totalCoeff = 0;
  $incomplete = false;

  foreach ($notes as $n) {
      if ($n['Note'] === null) {
          $incomplete = true;
      } else {
          $totalPoints += ((float)$n['Note'] * (int)$n['CoeffMatiere']);
      }
      $totalCoeff += (int)$n['CoeffMatiere'];
  }
  $moyenne = ($totalCoeff > 0 && !$incomplete) ? round($totalPoints / $totalCoeff, 2) : 0;
?>

  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
      <a href="resultat.php?codeniv=<?= h($codeniv) ?>&codemen=<?= h($codemen) ?>" class="btn btn-sm btn-outline-secondary mb-2">
        <i class="fas fa-arrow-left mr-1"></i>Retour aux résultats
      </a>
      <h3 class="font-weight-bold text-primary mb-1">
        <i class="fas fa-file-invoice mr-2"></i>Détail des Notes de l'Étudiant
      </h3>
      <p class="text-muted small mb-0">Classe : <strong><?= h($codeniv) ?> <?= h($codemen) ?></strong></p>
    </div>
    <a href="bulletin.php?numetu=<?= h($numetu) ?>" class="btn btn-success font-weight-bold shadow-sm">
      <i class="fas fa-print mr-1"></i>Imprimer le bulletin officiel
    </a>
  </div>

  <div class="card shadow mb-4">
    <div class="card-header bg-white py-3">
      <div class="row align-items-center">
        <div class="col-sm-6">
          <h5 class="font-weight-bold text-dark mb-0">
            <i class="fas fa-user-graduate text-primary mr-2"></i>#<?= h($etudiant['NumEtu']) ?> - <?= h($etudiant['NomEtu']) ?> <?= h($etudiant['PrenomEtu']) ?>
          </h5>
        </div>
        <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
          <span class="badge badge-primary px-3 py-2 font-weight-bold">
            Moyenne : <?= number_format($moyenne, 2) ?> / 20
          </span>
          <?php if ($incomplete): ?>
            <span class="badge badge-warning px-2 py-2 ml-1">Incomplet (Rattrapage)</span>
          <?php elseif ($moyenne >= 10): ?>
            <span class="badge badge-success px-2 py-2 ml-1">Admis</span>
          <?php else: ?>
            <span class="badge badge-danger px-2 py-2 ml-1">Ajourné</span>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
          <thead class="thead-dark">
            <tr>
              <th>Matière</th>
              <th class="text-center" style="width: 120px;">Coefficient</th>
              <th class="text-center" style="width: 140px;">Date d'Examen</th>
              <th class="text-center" style="width: 150px;">Note / 20</th>
              <th class="text-center" style="width: 150px;">Total pondéré</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($notes as $note):
              $pt = ($note['Note'] !== null) ? ((float)$note['Note'] * (int)$note['CoeffMatiere']) : '-';
            ?>
            <tr>
              <td class="font-weight-bold text-dark align-middle"><?= h($note['NomMatiere']) ?></td>
              <td class="text-center align-middle font-weight-bold"><?= h($note['CoeffMatiere']) ?></td>
              <td class="text-center align-middle"><?= h(format_date_fr($note['DateExam'])) ?></td>
              <td class="text-center align-middle font-weight-bold" style="font-size: 1.05rem;">
                <?php if ($note['Note'] !== null): ?>
                  <span class="<?= ((float)$note['Note'] < 10) ? 'text-danger font-weight-bold' : 'text-success font-weight-bold' ?>">
                    <?= number_format((float)$note['Note'], 2) ?>
                  </span>
                <?php else: ?>
                  <span class="badge badge-warning">En attente</span>
                <?php endif; ?>
              </td>
              <td class="text-center align-middle font-weight-bold">
                <?= ($pt !== '-') ? number_format($pt, 2) : '-' ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot class="bg-light font-weight-bold">
            <tr>
              <td class="text-right">TOTAL :</td>
              <td class="text-center"><?= $totalCoeff ?></td>
              <td></td>
              <td class="text-center text-primary h5 mb-0"><?= number_format($moyenne, 2) ?> / 20</td>
              <td class="text-center text-primary h5 mb-0"><?= number_format($totalPoints, 2) ?></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>

<?php
  include __DIR__ . '/inclus/modal.php';
?>
