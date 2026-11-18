<?php
  require_once __DIR__ . '/inclus/barrenav.php';

  $codeniv = $_GET['codeniv'] ?? 'L1';
  $codemen = $_GET['codemen'] ?? 'IG';
?>

  <div class="row justify-content-center">
    <div class="col-lg-7">
      <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white py-3">
          <h5 class="m-0 font-weight-bold"><i class="fas fa-plus mr-2"></i>Ajouter une Matière (<?= h($codeniv) ?> <?= h($codemen) ?>)</h5>
        </div>
        <div class="card-body p-4">
          <form action="ajoutermat.php" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="niveaumat" value="<?= h($codeniv) ?>">
            <input type="hidden" name="mentionmat" value="<?= h($codemen) ?>">

            <div class="form-group">
              <label class="font-weight-bold">Code / Abréviation de la matière <span class="text-danger">*</span> :</label>
              <input class="form-control" name="codemat" placeholder="ex: PROG1, BDD, MATH" maxlength="10" required autofocus>
              <small class="form-text text-muted">Code unique pour identifier la matière (ex: PROG1).</small>
            </div>

            <div class="form-group">
              <label class="font-weight-bold">Intitulé complet <span class="text-danger">*</span> :</label>
              <input class="form-control" name="nommat" placeholder="ex: Algorithmique et Structures de Données" maxlength="100" required>
            </div>

            <div class="form-group">
              <label class="font-weight-bold">Coefficient / Crédits <span class="text-danger">*</span> :</label>
              <input type="number" min="1" max="10" class="form-control" name="coeffmat" value="2" required>
            </div>

            <div class="p-3 bg-light rounded mb-3">
              <small class="text-muted d-block">Classe de rattachement : <strong><?= h($codeniv) ?> <?= h($codemen) ?></strong></small>
            </div>

            <div class="mt-4 pt-3 border-top text-right">
              <a href="matiere.php?codeniv=<?= h($codeniv) ?>&codemen=<?= h($codemen) ?>" class="btn btn-secondary mr-2">Annuler</a>
              <button type="submit" class="btn btn-primary font-weight-bold px-4 shadow-sm">
                <i class="fas fa-check mr-1"></i>Enregistrer la matière
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
