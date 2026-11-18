<?php
  require_once __DIR__ . '/inclus/barrenav.php';

  $codeexam = trim($_GET['codeexam'] ?? '');
  $stmt = $db->prepare(
    'SELECT e.*, m.NomMatiere FROM examen e JOIN matiere m ON e.CodeMatiere = m.CodeMatiere WHERE e.CodeExam = ?'
  );
  $stmt->execute([$codeexam]);
  $examen = $stmt->fetch();

  if (!$examen) {
      set_flash('danger', 'Examen introuvable.');
      redirect('classe.php');
  }
?>

  <div class="row justify-content-center">
    <div class="col-lg-7">
      <div class="card shadow mb-4">
        <div class="card-header bg-warning text-dark py-3">
          <h5 class="m-0 font-weight-bold"><i class="fas fa-calendar-alt mr-2"></i>Modifier la date de l'examen</h5>
        </div>
        <div class="card-body p-4">
          <form action="majexam.php" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="codeexam" value="<?= h($examen['CodeExam']) ?>">
            <input type="hidden" name="niveauexam" value="<?= h($examen['CodeNiveau']) ?>">
            <input type="hidden" name="mentionexam" value="<?= h($examen['CodeMention']) ?>">

            <div class="form-group">
              <label class="font-weight-bold">Identifiant Examen :</label>
              <input class="form-control bg-light font-weight-bold" value="<?= h($examen['CodeExam']) ?>" readonly>
            </div>

            <div class="form-group">
              <label class="font-weight-bold">Matière :</label>
              <input class="form-control bg-light" value="<?= h($examen['NomMatiere']) ?>" readonly>
            </div>

            <div class="form-group">
              <label class="font-weight-bold">Classe :</label>
              <input class="form-control bg-light" value="<?= h($examen['CodeNiveau']) ?> <?= h($examen['CodeMention']) ?>" readonly>
            </div>

            <div class="form-group">
              <label class="font-weight-bold">Nouvelle date d'épreuve <span class="text-danger">*</span> :</label>
              <input type="date" class="form-control" name="dateexam" value="<?= h($examen['DateExam']) ?>" required autofocus>
            </div>

            <div class="mt-4 pt-3 border-top text-right">
              <a href="examen.php?codeniv=<?= h($examen['CodeNiveau']) ?>&codemen=<?= h($examen['CodeMention']) ?>" class="btn btn-secondary mr-2">Annuler</a>
              <button type="submit" class="btn btn-warning font-weight-bold px-4 shadow-sm">
                <i class="fas fa-save mr-1"></i>Enregistrer la nouvelle date
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
