<?php
  require_once __DIR__ . '/inclus/barrenav.php';

  $codemat = trim($_GET['codemat'] ?? '');
  $stmt = $db->prepare('SELECT * FROM matiere WHERE CodeMatiere = ?');
  $stmt->execute([$codemat]);
  $matiere = $stmt->fetch();

  if (!$matiere) {
      set_flash('danger', 'Matière introuvable.');
      redirect('classe.php');
  }

  $niveaux = $db->query('SELECT * FROM niveau ORDER BY NumNiveau')->fetchAll();
  $mentions = $db->query('SELECT * FROM mention ORDER BY CodeMention')->fetchAll();
?>

  <div class="row justify-content-center">
    <div class="col-lg-7">
      <div class="card shadow mb-4">
        <div class="card-header bg-warning text-dark py-3">
          <h5 class="m-0 font-weight-bold"><i class="fas fa-edit mr-2"></i>Modifier la Matière : <?= h($matiere['CodeMatiere']) ?></h5>
        </div>
        <div class="card-body p-4">
          <form action="majmat.php" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="codemat" value="<?= h($matiere['CodeMatiere']) ?>">

            <div class="form-group">
              <label class="font-weight-bold">Code Matière :</label>
              <input class="form-control bg-light font-weight-bold" value="<?= h($matiere['CodeMatiere']) ?>" readonly>
            </div>

            <div class="form-group">
              <label class="font-weight-bold">Intitulé de la matière <span class="text-danger">*</span> :</label>
              <input class="form-control" name="nommat" value="<?= h($matiere['NomMatiere']) ?>" required autofocus>
            </div>

            <div class="form-group">
              <label class="font-weight-bold">Coefficient :</label>
              <input type="number" min="1" max="10" class="form-control" name="coeffmat" value="<?= h($matiere['CoeffMatiere']) ?>" required>
            </div>

            <div class="form-row">
              <div class="form-group col-md-6">
                <label class="font-weight-bold">Niveau requis :</label>
                <select class="form-control" name="niveaumat" required>
                  <?php foreach ($niveaux as $n): ?>
                    <option value="<?= h($n['CodeNiveau']) ?>" <?= $n['CodeNiveau'] === $matiere['CodeNiveau'] ? 'selected' : '' ?>>
                      <?= h($n['CodeNiveau']) ?> (<?= h($n['NomNiveau']) ?>)
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group col-md-6">
                <label class="font-weight-bold">Mention requise :</label>
                <select class="form-control" name="mentionmat" required>
                  <?php foreach ($mentions as $m): ?>
                    <option value="<?= h($m['CodeMention']) ?>" <?= $m['CodeMention'] === $matiere['CodeMention'] ? 'selected' : '' ?>>
                      <?= h($m['CodeMention']) ?> - <?= h($m['NomMention']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="mt-4 pt-3 border-top text-right">
              <a href="matiere.php?codeniv=<?= h($matiere['CodeNiveau']) ?>&codemen=<?= h($matiere['CodeMention']) ?>" class="btn btn-secondary mr-2">Annuler</a>
              <button type="submit" class="btn btn-warning font-weight-bold px-4 shadow-sm">
                <i class="fas fa-save mr-1"></i>Mettre à jour
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
