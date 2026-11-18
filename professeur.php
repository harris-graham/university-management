<?php
  require_once __DIR__ . '/inclus/barrenav.php';

  try {
    $stmt = $db->query('SELECT * FROM professeur ORDER BY NomProf, PrenomProf');
    $professeurs = $stmt->fetchAll();
  } catch (Exception $e) {
    $professeurs = [];
  }
?>

  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
      <h3 class="font-weight-bold text-primary mb-1">
        <i class="fas fa-chalkboard-teacher mr-2"></i>Gestion du Corps Enseignant
      </h3>
      <p class="text-muted small mb-0">Consultez et gérez la liste des professeurs et chargés de cours.</p>
    </div>
    <a href="ajoutprof.php" class="btn btn-primary shadow-sm font-weight-bold">
      <i class="fas fa-plus fa-sm text-white-50 mr-1"></i>Ajouter un enseignant
    </a>
  </div>

  <div class="card shadow mb-4">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
          <thead class="thead-dark">
            <tr>
              <th>ID</th>
              <th>Nom & Prénoms</th>
              <th>Grade</th>
              <th>Spécialité</th>
              <th>Email</th>
              <th>Téléphone</th>
              <th class="text-center" style="width: 140px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($professeurs as $prof): ?>
            <tr>
              <td>#<?= h($prof['IdProf']) ?></td>
              <td class="font-weight-bold text-dark"><?= h($prof['NomProf']) ?> <?= h($prof['PrenomProf']) ?></td>
              <td><span class="badge badge-info"><?= h($prof['GradeProf'] ?? 'Enseignant') ?></span></td>
              <td><?= h($prof['SpecialiteProf'] ?? '-') ?></td>
              <td><a href="mailto:<?= h($prof['EmailProf']) ?>"><?= h($prof['EmailProf'] ?? '-') ?></a></td>
              <td><?= h($prof['TelProf'] ?? '-') ?></td>
              <td class="text-center">
                <div class="btn-group btn-group-sm">
                  <a href="modifprof.php?idprof=<?= h($prof['IdProf']) ?>" class="btn btn-warning" title="Modifier">
                    <i class="fas fa-edit"></i>
                  </a>
                  <a href="suppprof.php?idprof=<?= h($prof['IdProf']) ?>" class="btn btn-danger" title="Supprimer">
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
