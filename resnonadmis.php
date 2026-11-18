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
             'HAVING COUNT(c.Note) < COUNT(DISTINCT ex.CodeExam) OR (SUM(m.CoeffMatiere * c.Note) / SUM(m.CoeffMatiere)) < 10 ' .
             'ORDER BY MoyGen ASC';

    $stmt = $db->prepare($query);
    $stmt->execute([$codeniv, $codemen]);
    $nonadmis = $stmt->fetchAll();
  } catch (Exception $e) {
    $nonadmis = [];
  }
?>

  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
      <a href="resultat.php?codeniv=<?= h($codeniv) ?>&codemen=<?= h($codemen) ?>" class="btn btn-sm btn-outline-secondary mb-2">
        <i class="fas fa-arrow-left mr-1"></i>Retour aux résultats
      </a>
      <h3 class="font-weight-bold text-danger mb-1">
        <i class="fas fa-times-circle mr-2"></i>Liste des Non-Admis / Rattrapages : <?= h($codeniv) ?> <?= h($codemen) ?>
      </h3>
      <p class="text-muted small mb-0">Total des étudiants concernés : <strong><?= count($nonadmis) ?></strong></p>
    </div>
    <button class="btn btn-sm btn-outline-secondary font-weight-bold shadow-sm" onclick="window.print();">
      <i class="fas fa-print mr-1"></i>Imprimer la liste
    </button>
  </div>

  <div class="card shadow mb-4 border-left-danger">
    <div class="card-header bg-white py-3">
      <h6 class="m-0 font-weight-bold text-danger">
        <i class="fas fa-exclamation-triangle mr-1"></i>Étudiants ajournés ou en session de rattrapage
      </h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
          <thead class="thead-dark">
            <tr>
              <th style="width: 100px;">N° Matr.</th>
              <th>Nom & Prénoms</th>
              <th class="text-center" style="width: 160px;">Moyenne Obtenue</th>
              <th class="text-center">Motif / Statut de Délibération</th>
              <th class="text-center" style="width: 120px;">Relevé</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($nonadmis as $na):
              $moy = $na['MoyGen'] !== null ? (float)$na['MoyGen'] : null;
              $incomplet = ($na['NbControle'] < $na['NbExamen']);
            ?>
            <tr>
              <td class="text-center font-weight-bold">#<?= h($na['NumEtu']) ?></td>
              <td class="font-weight-bold text-dark"><?= h($na['NomEtu']) ?> <?= h($na['PrenomEtu']) ?></td>
              <td class="text-center font-weight-bold text-danger" style="font-size: 1.1rem;">
                <?= ($moy !== null) ? number_format($moy, 2) . ' / 20' : '-' ?>
              </td>
              <td class="text-center">
                <?php if ($incomplet): ?>
                  <span class="badge badge-warning px-3 py-2 font-weight-bold">
                    <i class="fas fa-clock mr-1"></i>Note incomplète (Session Rattrapage)
                  </span>
                <?php else: ?>
                  <span class="badge badge-danger px-3 py-2 font-weight-bold">
                    <i class="fas fa-times mr-1"></i>Moyenne générale insuffisante (&lt; 10)
                  </span>
                <?php endif; ?>
              </td>
              <td class="text-center">
                <a href="detailnote.php?numetu=<?= h($na['NumEtu']) ?>" class="btn btn-sm btn-primary">
                  <i class="fas fa-list-alt mr-1"></i>Détail
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

<?php
  include __DIR__ . '/inclus/modal.php';
?>
