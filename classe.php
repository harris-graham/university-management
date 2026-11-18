<?php
  require_once __DIR__ . '/inclus/barrenav.php';

  try {
    $stmt = $db->query(
      'SELECT c.CodeNiveau, c.CodeMention, m.NomMention, n.NomNiveau, n.NomCycle, COUNT(e.NumEtu) as NbEtu ' .
      'FROM classe c ' .
      'JOIN mention m ON c.CodeMention = m.CodeMention ' .
      'JOIN niveau n ON c.CodeNiveau = n.CodeNiveau ' .
      'LEFT JOIN etudiant e ON c.CodeNiveau = e.CodeNiveau AND c.CodeMention = e.CodeMention ' .
      'GROUP BY c.CodeNiveau, c.CodeMention ' .
      'ORDER BY n.NumNiveau, c.CodeMention'
    );
    $classes = $stmt->fetchAll();
  } catch (Exception $e) {
    $classes = [];
  }
?>

  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
      <h3 class="font-weight-bold text-primary mb-1">
        <i class="fas fa-layer-group mr-2"></i>Promotions & Classes Académiques
      </h3>
      <p class="text-muted small mb-0">Consultez les filières, cycles d'études et effectifs de l'université.</p>
    </div>
    <a href="inscription.php" class="btn btn-primary font-weight-bold shadow-sm">
      <i class="fas fa-user-plus mr-1"></i>Inscrire un étudiant
    </a>
  </div>

  <div class="card shadow mb-4">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
          <thead class="thead-dark">
            <tr>
              <th>Classe</th>
              <th>Mention (Filière)</th>
              <th>Niveau</th>
              <th>Cycle</th>
              <th>Effectif</th>
              <th class="text-center" style="width: 250px;">Modules & Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($classes as $classe): ?>
            <tr>
              <td class="font-weight-bold align-middle">
                <span class="badge badge-primary px-2 py-1 font-weight-bold" style="font-size: 0.95rem;">
                  <?= h($classe['CodeNiveau']) ?> <?= h($classe['CodeMention']) ?>
                </span>
              </td>
              <td class="align-middle font-weight-bold text-dark"><?= h($classe['NomMention']) ?></td>
              <td class="align-middle"><?= h($classe['NomNiveau']) ?></td>
              <td class="align-middle"><span class="badge badge-info"><?= h($classe['NomCycle']) ?></span></td>
              <td class="align-middle text-center font-weight-bold"><?= h($classe['NbEtu']) ?></td>
              <td class="text-center align-middle">
                <div class="btn-group btn-group-sm">
                  <a href="etudiant.php?codeniv=<?= h($classe['CodeNiveau']) ?>&codemen=<?= h($classe['CodeMention']) ?>" class="btn btn-primary" title="Liste des étudiants">
                    <i class="fas fa-users mr-1"></i>Étudiants
                  </a>
                  <a href="matiere.php?codeniv=<?= h($classe['CodeNiveau']) ?>&codemen=<?= h($classe['CodeMention']) ?>" class="btn btn-info" title="Matières">
                    <i class="fas fa-book mr-1"></i>Matières
                  </a>
                  <a href="examen.php?codeniv=<?= h($classe['CodeNiveau']) ?>&codemen=<?= h($classe['CodeMention']) ?>" class="btn btn-warning" title="Examens">
                    <i class="fas fa-edit mr-1"></i>Examens
                  </a>
                  <a href="resultat.php?codeniv=<?= h($classe['CodeNiveau']) ?>&codemen=<?= h($classe['CodeMention']) ?>" class="btn btn-success" title="Résultats">
                    <i class="fas fa-tasks mr-1"></i>Résultats
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
