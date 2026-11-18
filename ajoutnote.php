<?php
  require_once __DIR__ . '/inclus/barrenav.php';

  $codeexam = trim($_GET['codeexam'] ?? '');
  $numetu = (int)($_GET['numetu'] ?? 0);

  $stmt = $db->prepare(
    'SELECT ex.CodeExam, ex.CodeNiveau, ex.CodeMention, ex.DateExam, m.NomMatiere, m.CoeffMatiere, et.NumEtu, et.NomEtu, et.PrenomEtu ' .
    'FROM examen ex ' .
    'JOIN matiere m ON ex.CodeMatiere = m.CodeMatiere ' .
    'JOIN etudiant et ON et.NumEtu = ? ' .
    'WHERE ex.CodeExam = ?'
  );
  $stmt->execute([$numetu, $codeexam]);
  $data = $stmt->fetch();

  if (!$data) {
      set_flash('danger', 'Données introuvables.');
      redirect('accueil.php');
  }
?>

  <div class="row justify-content-center">
    <div class="col-lg-6">
      <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white py-3">
          <h5 class="m-0 font-weight-bold"><i class="fas fa-plus-circle mr-2"></i>Attribution de Note d'Examen</h5>
        </div>
        <div class="card-body p-4">
          <form action="ajouternote.php" method="post">
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
              <label class="font-weight-bold">Note sur 20 <span class="text-danger">*</span> :</label>
              <input type="number" step="0.25" min="0" max="20" class="form-control form-control-lg font-weight-bold text-center text-primary" name="note" placeholder="ex: 14.50" required autofocus>
              <small class="form-text text-muted">Barème sur 20 points (décimales autorisées avec virgule ou point).</small>
            </div>

            <div class="mt-4 pt-3 border-top text-right">
              <a href="controle.php?codeexam=<?= h($data['CodeExam']) ?>&codeniv=<?= h($data['CodeNiveau']) ?>&codemen=<?= h($data['CodeMention']) ?>" class="btn btn-secondary mr-2">Annuler</a>
              <button type="submit" class="btn btn-primary font-weight-bold px-4 shadow-sm">
                <i class="fas fa-check mr-1"></i>Enregistrer la note
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
