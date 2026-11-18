<?php
  require_once __DIR__ . '/inclus/barrenav.php';

  $numetu = (int)($_GET['numetu'] ?? 0);
  $stmt = $db->prepare('SELECT * FROM etudiant WHERE NumEtu = ?');
  $stmt->execute([$numetu]);
  $etudiant = $stmt->fetch();

  if (!$etudiant) {
      set_flash('danger', 'Étudiant introuvable.');
      redirect('accueil.php');
  }
?>

  <div class="row justify-content-center">
    <div class="col-lg-6">
      <div class="card shadow border-left-danger mb-4">
        <div class="card-header bg-danger text-white py-3">
          <h5 class="m-0 font-weight-bold"><i class="fas fa-user-times mr-2"></i>Confirmation de Renvoi / Radiation</h5>
        </div>
        <div class="card-body p-4 text-center">
          <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
          <h5 class="font-weight-bold">Êtes-vous certain de vouloir renvoyer cet étudiant ?</h5>
          <p class="text-muted small">Cette action supprimera son inscription dans la promotion active.</p>

          <div class="p-3 bg-light rounded text-left my-3" style="border: 1px solid #ddd;">
            <p class="mb-1"><strong>N° Matricule :</strong> #<?= h($etudiant['NumEtu']) ?></p>
            <p class="mb-1"><strong>Nom & Prénoms :</strong> <?= h($etudiant['NomEtu']) ?> <?= h($etudiant['PrenomEtu']) ?></p>
            <p class="mb-0"><strong>Classe actuelle :</strong> <?= h($etudiant['CodeNiveau']) ?> <?= h($etudiant['CodeMention']) ?></p>
          </div>

          <form action="renvoyeretu.php" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="numetu" value="<?= h($etudiant['NumEtu']) ?>">
            <input type="hidden" name="nometu" value="<?= h($etudiant['NomEtu']) ?>">
            <input type="hidden" name="prenometu" value="<?= h($etudiant['PrenomEtu']) ?>">
            <input type="hidden" name="niveauetu" value="<?= h($etudiant['CodeNiveau']) ?>">
            <input type="hidden" name="mentionetu" value="<?= h($etudiant['CodeMention']) ?>">

            <a href="etudiant.php?codeniv=<?= h($etudiant['CodeNiveau']) ?>&codemen=<?= h($etudiant['CodeMention']) ?>" class="btn btn-secondary mr-2">
              <i class="fas fa-times mr-1"></i>Annuler
            </a>
            <button type="submit" class="btn btn-danger font-weight-bold shadow-sm">
              <i class="fas fa-user-slash mr-1"></i>Confirmer le renvoi
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

<?php
  include __DIR__ . '/inclus/modal.php';
?>
