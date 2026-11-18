<?php
  require_once __DIR__ . '/inclus/barrenav.php';

  $codeexam = trim($_GET['codeexam'] ?? '');
  $numetu = (int)($_GET['numetu'] ?? 0);

  $stmt = $db->prepare(
    'SELECT ex.CodeExam, ex.CodeNiveau, ex.CodeMention, ex.DateExam, m.NomMatiere, m.CoeffMatiere, et.NumEtu, et.NomEtu, et.PrenomEtu, c.Note ' .
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
      <div class="card shadow mb-4">
        <div class="card-header bg-warning text-dark py-3">
          <h5 class="m-0 font-weight-bold"><i class="fas fa-edit mr-2"></i>Modification de Note</h5>
        </div>
        <div class="card-body p-4">
          <form action="majnote.php" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="codeexam" value="<?= h($data['CodeExam']) ?>">
            <input type="hidden" name="numetu" value="<?= h($data['NumEtu']) ?>">
            <input type="hidden" name="niveau" value="<?= h($data['CodeNiveau']) ?>">
            <input type="hidden" name="mention" value="<?= h($data['CodeMention']) ?>">

            <div class="p-3 bg-light rounded mb-4" style="border: 1px solid #ddd;">
              <p class="mb-1"><strong>Étudiant :</strong> #<?= h($data['NumEtu']) ?> - <?= h($data['NomEtu']) ?> <?= h($data['PrenomEtu']) ?></p>
              <p class="mb-1"><strong>Matière :</strong> <?= h($data['NomMatiere']) ?> (Coeff <?= h($data['CoeffMatiere']) ?>)</p>
              <p class="mb-0"><strong>Date épreuve :</strong> <?= h(format_date_fr($data['DateExam'])) ?></p>
            </div>

            <div class="form-group">
              <label class="font-weight-bold">Nouvelle Note sur 20 <span class="text-danger">*</span> :</label>
              <input type="number" step="0.25" min="0" max="20" class="form-control form-control-lg font-weight-bold text-center text-primary" name="note" value="<?= h($data['Note']) ?>" required autofocus>
            </div>

            <div class="mt-4 pt-3 border-top text-right">
              <a href="controle.php?codeexam=<?= h($data['CodeExam']) ?>&codeniv=<?= h($data['CodeNiveau']) ?>&codemen=<?= h($data['CodeMention']) ?>" class="btn btn-secondary mr-2">Annuler</a>
              <button type="submit" class="btn btn-warning font-weight-bold px-4 shadow-sm">
                <i class="fas fa-save mr-1"></i>Mettre à jour la note
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
