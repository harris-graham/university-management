<?php
  require_once __DIR__ . '/inclus/barrenav.php';

  $idprof = (int)($_GET['idprof'] ?? 0);
  $stmt = $db->prepare('SELECT * FROM professeur WHERE IdProf = ?');
  $stmt->execute([$idprof]);
  $prof = $stmt->fetch();

  if (!$prof) {
      set_flash('danger', 'Enseignant introuvable.');
      redirect('professeur.php');
  }
?>

  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card shadow mb-4">
        <div class="card-header bg-warning text-dark py-3">
          <h5 class="m-0 font-weight-bold"><i class="fas fa-edit mr-2"></i>Modifier l'enseignant #<?= h($prof['IdProf']) ?></h5>
        </div>
        <div class="card-body p-4">
          <form action="majprof.php" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="idprof" value="<?= h($prof['IdProf']) ?>">

            <div class="form-row">
              <div class="form-group col-md-6">
                <label class="font-weight-bold">Nom <span class="text-danger">*</span> :</label>
                <input type="text" class="form-control" name="nomprof" value="<?= h($prof['NomProf']) ?>" required autofocus>
              </div>
              <div class="form-group col-md-6">
                <label class="font-weight-bold">Prénoms <span class="text-danger">*</span> :</label>
                <input type="text" class="form-control" name="prenomprof" value="<?= h($prof['PrenomProf']) ?>" required>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-6">
                <label class="font-weight-bold">Grade académique :</label>
                <select class="form-control" name="gradeprof">
                  <?php
                    $grades = ['Enseignant', 'Assistant', 'Docteur', 'Maître de Conférences', 'Professeur Titulaire', 'Intervenant Professionnel'];
                    foreach ($grades as $g) {
                      $sel = ($prof['GradeProf'] === $g) ? 'selected' : '';
                      echo "<option value=\"" . h($g) . "\" $sel>" . h($g) . "</option>";
                    }
                  ?>
                </select>
              </div>
              <div class="form-group col-md-6">
                <label class="font-weight-bold">Spécialité :</label>
                <input type="text" class="form-control" name="specialiteprof" value="<?= h($prof['SpecialiteProf']) ?>">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-6">
                <label class="font-weight-bold">Email :</label>
                <input type="email" class="form-control" name="emailprof" value="<?= h($prof['EmailProf']) ?>">
              </div>
              <div class="form-group col-md-6">
                <label class="font-weight-bold">Téléphone :</label>
                <input type="text" class="form-control" name="telprof" value="<?= h($prof['TelProf']) ?>">
              </div>
            </div>

            <div class="mt-4 pt-3 border-top text-right">
              <a href="professeur.php" class="btn btn-secondary mr-2"><i class="fas fa-times mr-1"></i>Annuler</a>
              <button type="submit" class="btn btn-warning font-weight-bold"><i class="fas fa-save mr-1"></i>Mettre à jour</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

<?php
  include __DIR__ . '/inclus/modal.php';
?>
