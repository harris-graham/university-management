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
             'HAVING COUNT(c.Note) = COUNT(DISTINCT ex.CodeExam) AND (SUM(m.CoeffMatiere * c.Note) / SUM(m.CoeffMatiere)) >= 10 ' .
             'ORDER BY MoyGen DESC';

    $stmt = $db->prepare($query);
    $stmt->execute([$codeniv, $codemen]);
    $admis = $stmt->fetchAll();
  } catch (Exception $e) {
    $admis = [];
  }
?>

  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
      <a href="resultat.php?codeniv=<?= h($codeniv) ?>&codemen=<?= h($codemen) ?>" class="btn btn-sm btn-outline-secondary mb-2">
        <i class="fas fa-arrow-left mr-1"></i>Retour aux résultats
      </a>
      <h3 class="font-weight-bold text-success mb-1">
        <i class="fas fa-check-circle mr-2"></i>Liste des Étudiants Admis : <?= h($codeniv) ?> <?= h($codemen) ?>
      </h3>
      <p class="text-muted small mb-0">Total des admis : <strong><?= count($admis) ?></strong></p>
    </div>
    <button class="btn btn-sm btn-success font-weight-bold shadow-sm" onclick="window.print();">
      <i class="fas fa-print mr-1"></i>Imprimer la liste officielle
    </button>
  </div>

  <div class="card shadow mb-4 border-left-success">
    <div class="card-header bg-white py-3">
      <h6 class="m-0 font-weight-bold text-success">
        <i class="fas fa-award mr-1"></i>Étudiants ayant validé leur semestre (Moyenne &ge; 10/20)
      </h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
          <thead class="thead-dark">
            <tr>
              <th style="width: 80px;">Rang</th>
              <th style="width: 100px;">N° Matr.</th>
              <th>Nom & Prénoms</th>
              <th class="text-center" style="width: 160px;">Moyenne Générale</th>
              <th class="text-center" style="width: 140px;">Décision</th>
              <th class="text-center" style="width: 120px;">Relevé</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $r = 1;
              foreach ($admis as $a):
            ?>
            <tr>
              <td class="text-center font-weight-bold"><?= $r ?></td>
              <td class="text-center font-weight-bold">#<?= h($a['NumEtu']) ?></td>
              <td class="font-weight-bold text-dark"><?= h($a['NomEtu']) ?> <?= h($a['PrenomEtu']) ?></td>
              <td class="text-center font-weight-bold text-success" style="font-size: 1.1rem;">
                <?= number_format((float)$a['MoyGen'], 2) ?> / 20
              </td>
              <td class="text-center">
                <span class="badge badge-success px-3 py-2 font-weight-bold">ADMIS(E)</span>
              </td>
              <td class="text-center">
                <a href="bulletin.php?numetu=<?= h($a['NumEtu']) ?>" class="btn btn-sm btn-outline-success">
                  <i class="fas fa-file-alt mr-1"></i>Bulletin
                </a>
              </td>
            </tr>
            <?php
              $r++;
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
