<?php
  require_once __DIR__ . '/inclus/barrenav.php';

  $codeniv = $_GET['codeniv'] ?? 'L1';
  $codemen = $_GET['codemen'] ?? 'IG';

  try {
    $stmt = $db->prepare('SELECT * FROM etudiant WHERE CodeNiveau = ? AND CodeMention = ? ORDER BY NomEtu, PrenomEtu');
    $stmt->execute([$codeniv, $codemen]);
    $etudiants = $stmt->fetchAll();
  } catch (Exception $e) {
    $etudiants = [];
  }
?>

  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
      <a href="classe.php" class="btn btn-sm btn-outline-secondary mb-2">
        <i class="fas fa-arrow-left mr-1"></i>Retour aux classes
      </a>
      <h3 class="font-weight-bold text-primary mb-1">
        <i class="fas fa-user-graduate mr-2"></i>Liste des Étudiants : <?= h($codeniv) ?> <?= h($codemen) ?>
      </h3>
      <p class="text-muted small mb-0">Total des inscrits dans cette classe : <strong><?= count($etudiants) ?></strong></p>
    </div>
    <div>
      <a href="export.php?type=etudiants&codeniv=<?= h($codeniv) ?>&codemen=<?= h($codemen) ?>" class="btn btn-sm btn-outline-success font-weight-bold shadow-sm mr-2">
        <i class="fas fa-file-excel mr-1"></i>Exporter en CSV / Excel
      </a>
      <a href="inscription.php" class="btn btn-sm btn-primary font-weight-bold shadow-sm">
        <i class="fas fa-user-plus mr-1"></i>Nouvelle inscription
      </a>
    </div>
  </div>

  <div class="card shadow mb-4">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
          <thead class="thead-dark">
            <tr>
              <th style="width: 80px;">N° Matr.</th>
              <th>Nom & Prénoms</th>
              <th>Date Naissance</th>
              <th>Genre</th>
              <th>Téléphone</th>
              <th>Date Inscription</th>
              <th class="text-center" style="width: 220px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($etudiants as $etudiant): ?>
            <tr>
              <td class="font-weight-bold text-center align-middle">#<?= h($etudiant['NumEtu']) ?></td>
              <td class="font-weight-bold text-dark align-middle">
                <?= h($etudiant['NomEtu']) ?> <?= h($etudiant['PrenomEtu']) ?>
                <small class="text-muted d-block"><?= h($etudiant['CinEtu'] ?: 'Sans CIN') ?></small>
              </td>
              <td class="align-middle"><?= h(format_date_fr($etudiant['DateNaiss'])) ?></td>
              <td class="align-middle">
                <span class="badge <?= $etudiant['SexeEtu'] === 'Masculin' ? 'badge-primary' : 'badge-info' ?>">
                  <?= h($etudiant['SexeEtu']) ?>
                </span>
              </td>
              <td class="align-middle"><?= h($etudiant['TelEtu'] ?: '-') ?></td>
              <td class="align-middle small">
                <?= h(format_date_fr($etudiant['DateInscr'])) ?>
                <span class="text-muted d-block">(<?= h($etudiant['AnneeUniv'] ?? '') ?>)</span>
              </td>
              <td class="text-center align-middle">
                <div class="btn-group btn-group-sm">
                  <a href="bulletin.php?numetu=<?= h($etudiant['NumEtu']) ?>" class="btn btn-success" title="Relevé officiel / Bulletin">
                    <i class="fas fa-file-alt"></i>
                  </a>
                  <a href="modifetu.php?numetu=<?= h($etudiant['NumEtu']) ?>" class="btn btn-warning" title="Modifier informations">
                    <i class="fas fa-edit"></i>
                  </a>
                  <a href="reinscription.php?numetu=<?= h($etudiant['NumEtu']) ?>" class="btn btn-info" title="Passage niveau supérieur / Réinscrire">
                    <i class="fas fa-redo"></i>
                  </a>
                  <a href="renvoietu.php?numetu=<?= h($etudiant['NumEtu']) ?>" class="btn btn-danger" title="Renvoi de l'étudiant">
                    <i class="fas fa-user-times"></i>
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
