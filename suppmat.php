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
?>

  <div class="row justify-content-center">
    <div class="col-lg-6">
      <div class="card shadow border-left-danger mb-4">
        <div class="card-header bg-danger text-white py-3">
          <h5 class="m-0 font-weight-bold"><i class="fas fa-trash-alt mr-2"></i>Suppression de Matière</h5>
        </div>
        <div class="card-body p-4 text-center">
          <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
          <h5>Êtes-vous certain de vouloir supprimer cette matière ?</h5>
          <div class="p-3 bg-light rounded text-left my-3" style="border: 1px solid #ddd;">
            <p class="mb-1"><strong>Code Matière :</strong> <?= h($matiere['CodeMatiere']) ?></p>
            <p class="mb-1"><strong>Intitulé :</strong> <?= h($matiere['NomMatiere']) ?></p>
            <p class="mb-0"><strong>Coefficient :</strong> <?= h($matiere['CoeffMatiere']) ?></p>
          </div>
          <form action="effacermat.php" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="codemat" value="<?= h($matiere['CodeMatiere']) ?>">
            <input type="hidden" name="nommat" value="<?= h($matiere['NomMatiere']) ?>">
            <input type="hidden" name="niveaumat" value="<?= h($matiere['CodeNiveau']) ?>">
            <input type="hidden" name="mentionmat" value="<?= h($matiere['CodeMention']) ?>">

            <a href="matiere.php?codeniv=<?= h($matiere['CodeNiveau']) ?>&codemen=<?= h($matiere['CodeMention']) ?>" class="btn btn-secondary mr-2">Annuler</a>
            <button type="submit" class="btn btn-danger font-weight-bold shadow-sm">
              <i class="fas fa-trash mr-1"></i>Oui, supprimer
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

<?php
  include __DIR__ . '/inclus/modal.php';
?>
