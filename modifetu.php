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
    <div class="col-lg-8">
      <div class="card shadow mb-4">
        <div class="card-header bg-warning text-dark py-3">
          <h5 class="m-0 font-weight-bold"><i class="fas fa-user-edit mr-2"></i>Modifier l'Étudiant #<?= h($etudiant['NumEtu']) ?></h5>
        </div>
        <div class="card-body p-4">
          <form action="majetu.php" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="numetu" value="<?= h($etudiant['NumEtu']) ?>">
            <input type="hidden" name="niveauetu" value="<?= h($etudiant['CodeNiveau']) ?>">
            <input type="hidden" name="mentionetu" value="<?= h($etudiant['CodeMention']) ?>">

            <div class="form-row">
              <div class="form-group col-md-6">
                <label class="font-weight-bold">Nom de famille <span class="text-danger">*</span> :</label>
                <input class="form-control" name="nometu" value="<?= h($etudiant['NomEtu']) ?>" required autofocus>
              </div>
              <div class="form-group col-md-6">
                <label class="font-weight-bold">Prénoms <span class="text-danger">*</span> :</label>
                <input class="form-control" name="prenometu" value="<?= h($etudiant['PrenomEtu']) ?>" required>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-4">
                <label class="font-weight-bold">Date de naissance :</label>
                <input type="date" class="form-control" name="datenaiss" value="<?= h($etudiant['DateNaiss']) ?>">
              </div>
              <div class="form-group col-md-4">
                <label class="font-weight-bold">N° CIN :</label>
                <input class="form-control" name="cinetu" value="<?= h($etudiant['CinEtu']) ?>">
              </div>
              <div class="form-group col-md-4">
                <label class="font-weight-bold">Genre :</label>
                <select class="form-control" name="sexeetu" required>
                  <option value="Masculin" <?= $etudiant['SexeEtu'] === 'Masculin' ? 'selected' : '' ?>>Masculin</option>
                  <option value="Feminin" <?= $etudiant['SexeEtu'] === 'Feminin' ? 'selected' : '' ?>>Féminin</option>
                </select>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-6">
                <label class="font-weight-bold">Nationalité :</label>
                <input class="form-control" name="nationetu" value="<?= h($etudiant['NationEtu']) ?>">
              </div>
              <div class="form-group col-md-6">
                <label class="font-weight-bold">Téléphone :</label>
                <input class="form-control" name="teletu" value="<?= h($etudiant['TelEtu']) ?>">
              </div>
            </div>

            <div class="form-group">
              <label class="font-weight-bold">Adresse :</label>
              <input class="form-control" name="adresseetu" value="<?= h($etudiant['AdresseEtu']) ?>">
            </div>

            <div class="mt-4 pt-3 border-top text-right">
              <a href="etudiant.php?codeniv=<?= h($etudiant['CodeNiveau']) ?>&codemen=<?= h($etudiant['CodeMention']) ?>" class="btn btn-secondary mr-2">Annuler</a>
              <button type="submit" class="btn btn-warning font-weight-bold px-4 shadow-sm">
                <i class="fas fa-save mr-1"></i>Enregistrer les modifications
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
