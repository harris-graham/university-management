<?php
require_once __DIR__ . '/inclus/barrenav.php';

$idprof = (int)($_GET['idprof'] ?? $_POST['idprof'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'Jeton CSRF invalide.');
        redirect('professeur.php');
    }

    try {
        $stmt = $db->prepare('DELETE FROM professeur WHERE IdProf = ?');
        $stmt->execute([$idprof]);

        log_audit($db, 'SUPP_PROFESSEUR', 'Professeur', "Suppression enseignant ID #$idprof");
        set_flash('success', 'L\'enseignant a été retiré avec succès.');
    } catch (Exception $e) {
        error_log('Erreur suppprof: ' . $e->getMessage());
        set_flash('danger', 'Impossible de supprimer cet enseignant (peut-être affecté à des cours).');
    }
    redirect('professeur.php');
}

$stmt = $db->prepare('SELECT * FROM professeur WHERE IdProf = ?');
$stmt->execute([$idprof]);
$prof = $stmt->fetch();

if (!$prof) {
    set_flash('danger', 'Enseignant introuvable.');
    redirect('professeur.php');
}
?>

  <div class="row justify-content-center">
    <div class="col-lg-6">
      <div class="card shadow border-left-danger mb-4">
        <div class="card-header bg-danger text-white py-3">
          <h5 class="m-0 font-weight-bold"><i class="fas fa-trash-alt mr-2"></i>Confirmer la suppression</h5>
        </div>
        <div class="card-body p-4 text-center">
          <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
          <h5>Êtes-vous sûr de vouloir supprimer cet enseignant ?</h5>
          <div class="p-3 bg-light rounded my-3 text-left">
            <p class="mb-1"><strong>Nom & Prénoms :</strong> <?= h($prof['NomProf']) ?> <?= h($prof['PrenomProf']) ?></p>
            <p class="mb-1"><strong>Grade :</strong> <?= h($prof['GradeProf']) ?></p>
            <p class="mb-0"><strong>Spécialité :</strong> <?= h($prof['SpecialiteProf']) ?></p>
          </div>
          <form action="suppprof.php" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="idprof" value="<?= h($prof['IdProf']) ?>">
            <a href="professeur.php" class="btn btn-secondary mr-2"><i class="fas fa-times mr-1"></i>Annuler</a>
            <button type="submit" class="btn btn-danger font-weight-bold"><i class="fas fa-trash mr-1"></i>Oui, supprimer définitivement</button>
          </form>
        </div>
      </div>
    </div>
  </div>

<?php
  include __DIR__ . '/inclus/modal.php';
?>
