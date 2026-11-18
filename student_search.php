<?php
  require_once __DIR__ . '/inclus/barrenav.php';

  $q = trim($_GET['q'] ?? '');
  $results = [];

  if (!empty($q)) {
      try {
          $term = '%' . $q . '%';
          $stmt = $db->prepare(
            'SELECT * FROM etudiant ' .
            'WHERE NomEtu LIKE ? OR PrenomEtu LIKE ? OR NumEtu LIKE ? OR CinEtu LIKE ? OR TelEtu LIKE ? OR CodeMention LIKE ? ' .
            'ORDER BY NomEtu, PrenomEtu'
          );
          $stmt->execute([$term, $term, $term, $term, $term, $term]);
          $results = $stmt->fetchAll();
      } catch (Exception $e) {
          $results = [];
      }
  }
?>

  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
      <h3 class="font-weight-bold text-primary mb-1">
        <i class="fas fa-search mr-2"></i>Résultats de recherche d'étudiants
      </h3>
      <p class="text-muted small mb-0">
        Recherche pour : "<strong><?= h($q) ?></strong>" &bull; <?= count($results) ?> résultat(s) trouvé(s)
      </p>
    </div>
    <a href="inscription.php" class="btn btn-primary font-weight-bold shadow-sm">
      <i class="fas fa-user-plus mr-1"></i>Nouvelle inscription
    </a>
  </div>

  <div class="card shadow mb-4">
    <div class="card-body">
      <?php if (empty($results)): ?>
        <div class="text-center py-5 text-muted">
          <i class="fas fa-user-slash fa-3x mb-3 text-gray-300"></i>
          <h5>Aucun étudiant trouvé</h5>
          <p class="small mb-0">Essayez de rechercher par nom, prénom, numéro d'inscription ou numéro de CIN.</p>
        </div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-striped table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
            <thead class="thead-dark">
              <tr>
                <th>Matricule</th>
                <th>Nom & Prénoms</th>
                <th>Classe</th>
                <th>Date Naissance</th>
                <th>CIN</th>
                <th>Téléphone</th>
                <th class="text-center" style="width: 140px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($results as $e): ?>
              <tr>
                <td class="font-weight-bold">#<?= h($e['NumEtu']) ?></td>
                <td class="font-weight-bold text-dark"><?= h($e['NomEtu']) ?> <?= h($e['PrenomEtu']) ?></td>
                <td><span class="badge badge-primary"><?= h($e['CodeNiveau']) ?> <?= h($e['CodeMention']) ?></span></td>
                <td><?= h(format_date_fr($e['DateNaiss'])) ?></td>
                <td><?= h($e['CinEtu'] ?: '-') ?></td>
                <td><?= h($e['TelEtu'] ?: '-') ?></td>
                <td class="text-center">
                  <div class="btn-group btn-group-sm">
                    <a href="bulletin.php?numetu=<?= h($e['NumEtu']) ?>" class="btn btn-success" title="Bulletin / Relevé">
                      <i class="fas fa-file-alt"></i>
                    </a>
                    <a href="modifetu.php?numetu=<?= h($e['NumEtu']) ?>" class="btn btn-warning" title="Modifier">
                      <i class="fas fa-edit"></i>
                    </a>
                    <a href="reinscription.php?numetu=<?= h($e['NumEtu']) ?>" class="btn btn-info" title="Réinscrire">
                      <i class="fas fa-redo"></i>
                    </a>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>

<?php
  include __DIR__ . '/inclus/modal.php';
?>
