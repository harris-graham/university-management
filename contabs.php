<?php
  require_once __DIR__ . '/inclus/barrenav.php';

  $codeexam = trim($_GET['codeexam'] ?? '');

  $stmtExam = $db->prepare(
    'SELECT e.*, m.NomMatiere FROM examen e JOIN matiere m ON e.CodeMatiere = m.CodeMatiere WHERE e.CodeExam = ?'
  );
  $stmtExam->execute([$codeexam]);
  $examen = $stmtExam->fetch();

  if (!$examen) {
      set_flash('danger', 'Examen introuvable.');
      redirect('classe.php');
  }

  $codeniv = $examen['CodeNiveau'];
  $codemen = $examen['CodeMention'];

  // Étudiants n'ayant aucune note saisie pour cet examen
  $stmtAbs = $db->prepare(
    'SELECT et.NumEtu, et.NomEtu, et.PrenomEtu, et.TelEtu ' .
    'FROM etudiant et ' .
    'LEFT JOIN controle c ON et.NumEtu = c.NumEtu AND c.CodeExam = ? ' .
    'WHERE et.CodeNiveau = ? AND et.CodeMention = ? AND c.Note IS NULL ' .
    'ORDER BY et.NomEtu, et.PrenomEtu'
  );
  $stmtAbs->execute([$codeexam, $codeniv, $codemen]);
  $absents = $stmtAbs->fetchAll();
?>

  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
      <a href="examen.php?codeniv=<?= h($codeniv) ?>&codemen=<?= h($codemen) ?>" class="btn btn-sm btn-outline-secondary mb-2">
        <i class="fas fa-arrow-left mr-1"></i>Retour aux examens
      </a>
      <h3 class="font-weight-bold text-danger mb-1">
        <i class="fas fa-user-times mr-2"></i>Liste des Étudiants Absents à l'Épreuve
      </h3>
      <p class="text-muted small mb-0">
        Matière : <strong><?= h($examen['NomMatiere']) ?></strong> | Classe : <strong><?= h($codeniv) ?> <?= h($codemen) ?></strong>
      </p>
    </div>
    <a href="controle.php?codeexam=<?= h($codeexam) ?>&codeniv=<?= h($codeniv) ?>&codemen=<?= h($codemen) ?>" class="btn btn-primary font-weight-bold shadow-sm">
      <i class="fas fa-edit mr-1"></i>Saisir les notes
    </a>
  </div>

  <div class="card shadow mb-4 border-left-danger">
    <div class="card-header bg-white py-3">
      <h6 class="m-0 font-weight-bold text-danger">
        <i class="fas fa-exclamation-circle mr-1"></i>Étudiants sans note enregistrée (<?= count($absents) ?>)
      </h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
          <thead class="thead-dark">
            <tr>
              <th style="width: 100px;">N° Matr.</th>
              <th>Nom & Prénoms</th>
              <th>Téléphone</th>
              <th class="text-center" style="width: 140px;">Statut</th>
              <th class="text-center" style="width: 120px;">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($absents as $a): ?>
            <tr>
              <td class="font-weight-bold text-center">#<?= h($a['NumEtu']) ?></td>
              <td class="font-weight-bold text-dark"><?= h($a['NomEtu']) ?> <?= h($a['PrenomEtu']) ?></td>
              <td><?= h($a['TelEtu'] ?: '-') ?></td>
              <td class="text-center">
                <span class="badge badge-danger px-2 py-1">Absent / En attente</span>
              </td>
              <td class="text-center">
                <a href="ajoutnote.php?codeexam=<?= h($codeexam) ?>&numetu=<?= h($a['NumEtu']) ?>" class="btn btn-sm btn-primary">
                  <i class="fas fa-plus mr-1"></i>Noter
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
