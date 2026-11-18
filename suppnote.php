<?php
  require_once __DIR__ . '/inclus/barrenav.php';

  $codeexam = trim($_GET['codeexam'] ?? '');
  $numetu = (int)($_GET['numetu'] ?? 0);

  $stmt = $db->prepare(
    'SELECT ex.CodeExam, ex.CodeNiveau, ex.CodeMention, m.NomMatiere, et.NumEtu, et.NomEtu, et.PrenomEtu, c.Note ' .
    'FROM examen ex ' .
    'JOIN matiere m ON ex.CodeMatiere = m.CodeMatiere ' .
    'JOIN etudiant et ON et.NumEtu = ? ' .
    'JOIN controle c ON c.CodeExam = ex.CodeExam AND c.NumEtu = et.NumEtu ' .
    'WHERE ex.CodeExam = ?'
  );
  $stmt->execute([$numetu, $codeexam]);
  $data = $stmt->fetch();

  if (!$data) {
      set_flash('danger', 'Note introuvable.');
      redirect('examen.php');
  }
?>

  <div class="row justify-content-center">
    <div class="col-lg-6">
      <div class="card shadow border-left-danger mb-4">
        <div class="card-header bg-danger text-white py-3">
          <h5 class="m-0 font-weight-bold"><i class="fas fa-trash-alt mr-2"></i>Suppression de Note</h5>
        </div>
        <div class="card-body p-4 text-center">
          <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
          <h5>Êtes-vous certain de vouloir effacer cette note ?</h5>

          <div class="p-3 bg-light rounded text-left my-3" style="border: 1px solid #ddd;">
            <p class="mb-1"><strong>Étudiant :</strong> #<?= h($data['NumEtu']) ?> - <?= h($data['NomEtu']) ?> <?= h($data['PrenomEtu']) ?></p>
            <p class="mb-1"><strong>Matière :</strong> <?= h($data['NomMatiere']) ?></p>
            <p class="mb-0"><strong>Note actuelle :</strong> <span class="badge badge-primary font-weight-bold" style="font-size: 1rem;"><?= number_format((float)$data['Note'], 2) ?> / 20</span></p>
          </div>

          <form action="effacernote.php" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="codeexam" value="<?= h($data['CodeExam']) ?>">
            <input type="hidden" name="numetu" value="<?= h($data['NumEtu']) ?>">
            <input type="hidden" name="niveau" value="<?= h($data['CodeNiveau']) ?>">
            <input type="hidden" name="mention" value="<?= h($data['CodeMention']) ?>">

            <a href="controle.php?codeexam=<?= h($data['CodeExam']) ?>&codeniv=<?= h($data['CodeNiveau']) ?>&codemen=<?= h($data['CodeMention']) ?>" class="btn btn-secondary mr-2">Annuler</a>
            <button type="submit" class="btn btn-danger font-weight-bold shadow-sm">
              <i class="fas fa-trash mr-1"></i>Oui, effacer la note
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

<?php
  include __DIR__ . '/inclus/modal.php';
?>
