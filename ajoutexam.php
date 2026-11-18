<?php
  require_once __DIR__ . '/inclus/barrenav.php';

  $codeniv = $_GET['codeniv'] ?? 'L1';
  $codemen = $_GET['codemen'] ?? 'IG';
  $anneeActive = get_active_academic_year($db);

  try {
    $stmtMat = $db->prepare('SELECT * FROM matiere WHERE CodeNiveau = ? AND CodeMention = ? ORDER BY NomMatiere');
    $stmtMat->execute([$codeniv, $codemen]);
    $matieres = $stmtMat->fetchAll();
  } catch (Exception $e) {
    $matieres = [];
  }
?>

  <div class="row justify-content-center">
    <div class="col-lg-7">
      <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white py-3">
          <h5 class="m-0 font-weight-bold"><i class="fas fa-plus mr-2"></i>Programmer un Examen (<?= h($codeniv) ?> <?= h($codemen) ?>)</h5>
        </div>
        <div class="card-body p-4">
          <form action="ajouterexam.php" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="niveauexam" value="<?= h($codeniv) ?>">
            <input type="hidden" name="mentionexam" value="<?= h($codemen) ?>">

            <div class="form-group">
              <label class="font-weight-bold">Année universitaire :</label>
              <input class="form-control bg-light font-weight-bold text-center text-primary" name="anneeuniv" value="<?= h($anneeActive) ?>" readonly>
            </div>

            <div class="form-group">
              <label class="font-weight-bold">Matière concernée <span class="text-danger">*</span> :</label>
              <select class="form-control" name="matiereexam" required autofocus>
                <option value="" disabled selected>Sélectionner la matière</option>
                <?php foreach ($matieres as $m): ?>
                  <option value="<?= h($m['CodeMatiere']) ?>"><?= h($m['NomMatiere']) ?> (<?= h($m['CodeMatiere']) ?>)</option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label class="font-weight-bold">Date de passage de l'examen <span class="text-danger">*</span> :</label>
              <input type="date" class="form-control" name="dateexam" value="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="p-3 bg-light rounded mb-3">
              <small class="text-muted d-block">Classe ciblée : <strong><?= h($codeniv) ?> <?= h($codemen) ?></strong></small>
            </div>

            <div class="mt-4 pt-3 border-top text-right">
              <a href="examen.php?codeniv=<?= h($codeniv) ?>&codemen=<?= h($codemen) ?>" class="btn btn-secondary mr-2">Annuler</a>
              <button type="submit" class="btn btn-primary font-weight-bold px-4 shadow-sm">
                <i class="fas fa-check mr-1"></i>Confirmer et créer l'examen
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

<?php
  include __DIR__ . '/inclus/modal.php';
?>
