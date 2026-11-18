<?php
  require_once __DIR__ . '/inclus/barrenav.php';

  $codeniv = $_GET['codeniv'] ?? 'L1';
  $codemen = $_GET['codemen'] ?? 'IG';

  try {
    $stmt = $db->prepare(
      'SELECT e.*, m.NomMatiere, m.CoeffMatiere ' .
      'FROM examen e ' .
      'JOIN matiere m ON e.CodeMatiere = m.CodeMatiere ' .
      'WHERE e.CodeNiveau = ? AND e.CodeMention = ? ' .
      'ORDER BY e.DateExam DESC'
    );
    $stmt->execute([$codeniv, $codemen]);
    $examens = $stmt->fetchAll();
  } catch (Exception $e) {
    $examens = [];
  }
?>

  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
      <a href="classe.php" class="btn btn-sm btn-outline-secondary mb-2">
        <i class="fas fa-arrow-left mr-1"></i>Retour aux classes
      </a>
      <h3 class="font-weight-bold text-primary mb-1">
        <i class="fas fa-edit mr-2"></i>Sessions d'Examens : <?= h($codeniv) ?> <?= h($codemen) ?>
      </h3>
      <p class="text-muted small mb-0">Total des examens planifiés : <strong><?= count($examens) ?></strong></p>
    </div>
    <a href="ajoutexam.php?codeniv=<?= h($codeniv) ?>&codemen=<?= h($codemen) ?>" class="btn btn-primary font-weight-bold shadow-sm">
      <i class="fas fa-plus mr-1"></i>Créer une session d'examen
    </a>
  </div>

  <div class="card shadow mb-4">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
          <thead class="thead-dark">
            <tr>
              <th>Identifiant Examen</th>
              <th>Matière</th>
              <th>Classe</th>
              <th class="text-center">Date de l'épreuve</th>
              <th class="text-center" style="width: 220px;">Saisie & Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($examens as $examen): ?>
            <tr>
              <td class="font-weight-bold align-middle">
                <code><?= h($examen['CodeExam']) ?></code>
              </td>
              <td class="font-weight-bold text-dark align-middle">
                <?= h($examen['NomMatiere']) ?>
                <span class="badge badge-light border ml-1">Coeff <?= h($examen['CoeffMatiere']) ?></span>
              </td>
              <td class="align-middle"><span class="badge badge-primary"><?= h($examen['CodeNiveau']) ?> <?= h($examen['CodeMention']) ?></span></td>
              <td class="text-center align-middle font-weight-bold"><?= h(format_date_fr($examen['DateExam'])) ?></td>
              <td class="text-center align-middle">
                <div class="btn-group btn-group-sm">
                  <a href="controle.php?codeexam=<?= h($examen['CodeExam']) ?>&codeniv=<?= h($codeniv) ?>&codemen=<?= h($codemen) ?>" class="btn btn-primary" title="Saisie des notes">
                    <i class="fas fa-edit mr-1"></i>Notes
                  </a>
                  <a href="contabs.php?codeexam=<?= h($examen['CodeExam']) ?>&codeniv=<?= h($codeniv) ?>&codemen=<?= h($codemen) ?>" class="btn btn-dark" title="Absents à l'examen">
                    <i class="fas fa-user-times mr-1"></i>Absents
                  </a>
                  <a href="modifexam.php?codeexam=<?= h($examen['CodeExam']) ?>&codeniv=<?= h($codeniv) ?>&codemen=<?= h($codemen) ?>" class="btn btn-warning" title="Modifier la date">
                    <i class="fas fa-calendar-alt"></i>
                  </a>
                </div>
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
