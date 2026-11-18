<?php
  require_once __DIR__ . '/inclus/barrenav.php';

  $codeniv = $_GET['codeniv'] ?? 'L1';
  $codemen = $_GET['codemen'] ?? 'IG';

  try {
    $stmt = $db->prepare('SELECT * FROM matiere WHERE CodeNiveau = ? AND CodeMention = ? ORDER BY CodeMatiere');
    $stmt->execute([$codeniv, $codemen]);
    $matieres = $stmt->fetchAll();
  } catch (Exception $e) {
    $matieres = [];
  }
?>

  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
      <a href="classe.php" class="btn btn-sm btn-outline-secondary mb-2">
        <i class="fas fa-arrow-left mr-1"></i>Retour aux classes
      </a>
      <h3 class="font-weight-bold text-primary mb-1">
        <i class="fas fa-book mr-2"></i>Unités d'Enseignement : <?= h($codeniv) ?> <?= h($codemen) ?>
      </h3>
      <p class="text-muted small mb-0">Total des matières configurées : <strong><?= count($matieres) ?></strong></p>
    </div>
    <a href="ajoutmat.php?codeniv=<?= h($codeniv) ?>&codemen=<?= h($codemen) ?>" class="btn btn-primary font-weight-bold shadow-sm">
      <i class="fas fa-plus mr-1"></i>Ajouter une matière
    </a>
  </div>

  <div class="card shadow mb-4">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
          <thead class="thead-dark">
            <tr>
              <th style="width: 140px;">Code Matière</th>
              <th>Intitulé de la Matière</th>
              <th class="text-center" style="width: 120px;">Coefficient</th>
              <th>Niveau</th>
              <th>Mention</th>
              <th class="text-center" style="width: 120px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($matieres as $matiere): ?>
            <tr>
              <td class="font-weight-bold align-middle">
                <span class="badge badge-light border text-primary px-2 py-1"><?= h($matiere['CodeMatiere']) ?></span>
              </td>
              <td class="font-weight-bold text-dark align-middle"><?= h($matiere['NomMatiere']) ?></td>
              <td class="text-center align-middle font-weight-bold"><?= h($matiere['CoeffMatiere']) ?></td>
              <td class="align-middle"><?= h($matiere['CodeNiveau']) ?></td>
              <td class="align-middle"><?= h($matiere['CodeMention']) ?></td>
              <td class="text-center align-middle">
                <div class="btn-group btn-group-sm">
                  <a href="modifmat.php?codemat=<?= h($matiere['CodeMatiere']) ?>" class="btn btn-warning" title="Modifier">
                    <i class="fas fa-edit"></i>
                  </a>
                  <a href="suppmat.php?codemat=<?= h($matiere['CodeMatiere']) ?>" class="btn btn-danger" title="Supprimer">
                    <i class="fas fa-trash"></i>
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
