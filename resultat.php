<?php
  require_once __DIR__ . '/inclus/barrenav.php';

  $codeniv = $_GET['codeniv'] ?? 'L1';
  $codemen = $_GET['codemen'] ?? 'IG';

  try {
    $query = 'SELECT et.CodeNiveau, et.CodeMention, et.NumEtu, et.NomEtu, et.PrenomEtu, ' .
             'COUNT(c.Note) AS NbControle, COUNT(DISTINCT ex.CodeExam) AS NbExamen, ' .
             '(SUM(m.CoeffMatiere * c.Note) / SUM(m.CoeffMatiere)) AS MoyGen ' .
             'FROM etudiant et ' .
             'JOIN examen ex ON et.CodeNiveau = ex.CodeNiveau AND et.CodeMention = ex.CodeMention ' .
             'JOIN matiere m ON ex.CodeMatiere = m.CodeMatiere ' .
             'LEFT JOIN controle c ON et.NumEtu = c.NumEtu AND ex.CodeExam = c.CodeExam ' .
             'WHERE et.CodeNiveau = ? AND et.CodeMention = ? ' .
             'GROUP BY et.NumEtu ' .
             'ORDER BY MoyGen DESC';

    $stmt = $db->prepare($query);
    $stmt->execute([$codeniv, $codemen]);
    $resultats = $stmt->fetchAll();
  } catch (Exception $e) {
    $resultats = [];
  }
?>

  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
      <a href="classe.php" class="btn btn-sm btn-outline-secondary mb-2">
        <i class="fas fa-arrow-left mr-1"></i>Retour aux classes
      </a>
      <h3 class="font-weight-bold text-primary mb-1">
        <i class="fas fa-tasks mr-2"></i>Résultats Généraux : <?= h($codeniv) ?> <?= h($codemen) ?>
      </h3>
      <p class="text-muted small mb-0">Classement officiel et délibérations des examens de la promotion.</p>
    </div>
    <div>
      <a href="resadmis.php?codeniv=<?= h($codeniv) ?>&codemen=<?= h($codemen) ?>" class="btn btn-sm btn-success font-weight-bold shadow-sm mr-1">
        <i class="fas fa-check-circle mr-1"></i>Admis
      </a>
      <a href="resnonadmis.php?codeniv=<?= h($codeniv) ?>&codemen=<?= h($codemen) ?>" class="btn btn-sm btn-danger font-weight-bold shadow-sm mr-2">
        <i class="fas fa-times-circle mr-1"></i>Non-admis
      </a>
      <a href="export.php?type=resultats&codeniv=<?= h($codeniv) ?>&codemen=<?= h($codemen) ?>" class="btn btn-sm btn-outline-primary font-weight-bold shadow-sm">
        <i class="fas fa-file-excel mr-1"></i>Exporter CSV
      </a>
    </div>
  </div>

  <div class="card shadow mb-4">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
          <thead class="thead-dark">
            <tr>
              <th style="width: 80px;">Rang</th>
              <th style="width: 100px;">N° Matr.</th>
              <th>Nom & Prénoms</th>
              <th class="text-center" style="width: 160px;">Moyenne Générale / 20</th>
              <th class="text-center" style="width: 180px;">Décision du Jury</th>
              <th class="text-center" style="width: 140px;">Détails</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $rang = 1;
              foreach ($resultats as $res):
                $moy = $res['MoyGen'] !== null ? (float)$res['MoyGen'] : null;
                $incomplet = ($res['NbControle'] < $res['NbExamen']);
            ?>
            <tr>
              <td class="font-weight-bold text-center align-middle">
                <?php if ($rang === 1 && $moy !== null && $moy >= 10): ?>
                  <span class="badge badge-warning text-dark"><i class="fas fa-trophy mr-1"></i>1er</span>
                <?php else: ?>
                  <?= $rang ?>
                <?php endif; ?>
              </td>
              <td class="font-weight-bold text-center align-middle">#<?= h($res['NumEtu']) ?></td>
              <td class="font-weight-bold text-dark align-middle"><?= h($res['NomEtu']) ?> <?= h($res['PrenomEtu']) ?></td>
              <td class="text-center align-middle font-weight-bold" style="font-size: 1.15rem;">
                <?php if ($moy !== null): ?>
                  <span class="<?= ($moy < 10) ? 'text-danger' : 'text-success' ?>">
                    <?= number_format($moy, 2) ?>
                  </span>
                <?php else: ?>
                  <span class="text-muted font-italic">-</span>
                <?php endif; ?>
              </td>
              <td class="text-center align-middle">
                <?php if ($incomplet): ?>
                  <span class="badge badge-warning px-2 py-1"><i class="fas fa-clock mr-1"></i>Rattrapage requis</span>
                <?php elseif ($moy !== null && $moy >= 10): ?>
                  <span class="badge badge-success px-2 py-1"><i class="fas fa-check mr-1"></i>Admis(e)</span>
                <?php else: ?>
                  <span class="badge badge-danger px-2 py-1"><i class="fas fa-times mr-1"></i>Non admis(e)</span>
                <?php endif; ?>
              </td>
              <td class="text-center align-middle">
                <a href="detailnote.php?numetu=<?= h($res['NumEtu']) ?>" class="btn btn-sm btn-primary" title="Relevé détaillé">
                  <i class="fas fa-list-alt mr-1"></i>Notes
                </a>
              </td>
            </tr>
            <?php
              $rang++;
              endforeach;
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

<?php
  include __DIR__ . '/inclus/modal.php';
?>
