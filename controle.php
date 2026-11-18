<?php
  require_once __DIR__ . '/inclus/barrenav.php';

  $codeexam = trim($_GET['codeexam'] ?? '');
  $codeniv = $_GET['codeniv'] ?? '';
  $codemen = $_GET['codemen'] ?? '';

  $stmtExam = $db->prepare(
    'SELECT e.*, m.NomMatiere, m.CoeffMatiere FROM examen e JOIN matiere m ON e.CodeMatiere = m.CodeMatiere WHERE e.CodeExam = ?'
  );
  $stmtExam->execute([$codeexam]);
  $examen = $stmtExam->fetch();

  if (!$examen) {
      set_flash('danger', 'Examen introuvable.');
      redirect('classe.php');
  }

  $codeniv = $examen['CodeNiveau'];
  $codemen = $examen['CodeMention'];

  // Liste des étudiants avec leur note (si saisie)
  $stmtControle = $db->prepare(
    'SELECT et.NumEtu, et.NomEtu, et.PrenomEtu, c.Note ' .
    'FROM etudiant et ' .
    'LEFT JOIN controle c ON et.NumEtu = c.NumEtu AND c.CodeExam = ? ' .
    'WHERE et.CodeNiveau = ? AND et.CodeMention = ? ' .
    'ORDER BY et.NomEtu, et.PrenomEtu'
  );
  $stmtControle->execute([$codeexam, $codeniv, $codemen]);
  $controles = $stmtControle->fetchAll();
?>

  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
      <a href="examen.php?codeniv=<?= h($codeniv) ?>&codemen=<?= h($codemen) ?>" class="btn btn-sm btn-outline-secondary mb-2">
        <i class="fas fa-arrow-left mr-1"></i>Retour aux examens
      </a>
      <h3 class="font-weight-bold text-primary mb-1">
        <i class="fas fa-clipboard-check mr-2"></i>Saisie des Notes de Contrôle
      </h3>
      <p class="text-muted small mb-0">
        Matière : <strong><?= h($examen['NomMatiere']) ?></strong> | Classe : <strong><?= h($codeniv) ?> <?= h($codemen) ?></strong> | Date : <strong><?= h(format_date_fr($examen['DateExam'])) ?></strong>
      </p>
    </div>
    <div class="badge badge-light border p-2 text-dark font-weight-bold">
      Code Examen : <code><?= h($codeexam) ?></code>
    </div>
  </div>

  <div class="card shadow mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
      <h6 class="m-0 font-weight-bold text-primary">
        <i class="fas fa-list mr-1"></i>Liste des étudiants inscrits (<?= count($controles) ?>)
      </h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
          <thead class="thead-dark">
            <tr>
              <th style="width: 100px;">N° Matr.</th>
              <th>Nom & Prénoms</th>
              <th class="text-center" style="width: 150px;">Note attribuée / 20</th>
              <th class="text-center" style="width: 140px;">Statut</th>
              <th class="text-center" style="width: 160px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($controles as $c): ?>
            <tr>
              <td class="font-weight-bold text-center align-middle">#<?= h($c['NumEtu']) ?></td>
              <td class="font-weight-bold text-dark align-middle"><?= h($c['NomEtu']) ?> <?= h($c['PrenomEtu']) ?></td>
              <td class="text-center align-middle font-weight-bold" style="font-size: 1.1rem;">
                <?php if ($c['Note'] !== null): ?>
                  <span class="<?= ((float)$c['Note'] < 10) ? 'text-danger font-weight-bold' : 'text-success font-weight-bold' ?>">
                    <?= number_format((float)$c['Note'], 2) ?>
                  </span>
                <?php else: ?>
                  <span class="text-muted font-italic">-</span>
                <?php endif; ?>
              </td>
              <td class="text-center align-middle">
                <?php if ($c['Note'] !== null): ?>
                  <span class="badge badge-success px-2 py-1"><i class="fas fa-check mr-1"></i>Noté</span>
                <?php else: ?>
                  <span class="badge badge-warning px-2 py-1"><i class="fas fa-clock mr-1"></i>Non noté</span>
                <?php endif; ?>
              </td>
              <td class="text-center align-middle">
                <div class="btn-group btn-group-sm">
                  <?php if ($c['Note'] === null): ?>
                    <a href="ajoutnote.php?codeexam=<?= h($codeexam) ?>&numetu=<?= h($c['NumEtu']) ?>" class="btn btn-primary" title="Attribuer la note">
                      <i class="fas fa-plus mr-1"></i>Noter
                    </a>
                  <?php else: ?>
                    <a href="modifnote.php?codeexam=<?= h($codeexam) ?>&numetu=<?= h($c['NumEtu']) ?>" class="btn btn-warning" title="Modifier la note">
                      <i class="fas fa-edit"></i>
                    </a>
                    <a href="suppnote.php?codeexam=<?= h($codeexam) ?>&numetu=<?= h($c['NumEtu']) ?>" class="btn btn-danger" title="Effacer la note">
                      <i class="fas fa-trash"></i>
                    </a>
                  <?php endif; ?>
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
