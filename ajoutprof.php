<?php
  require_once __DIR__ . '/inclus/barrenav.php';

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
          set_flash('danger', 'Jeton CSRF invalide.');
          redirect('professeur.php');
      }

      $nom = trim($_POST['nomprof'] ?? '');
      $prenom = trim($_POST['prenomprof'] ?? '');
      $email = trim($_POST['emailprof'] ?? '');
      $tel = trim($_POST['telprof'] ?? '');
      $grade = trim($_POST['gradeprof'] ?? '');
      $specialite = trim($_POST['specialiteprof'] ?? '');

      if (!empty($nom) && !empty($prenom)) {
          try {
              $stmt = $db->prepare('INSERT INTO professeur (NomProf, PrenomProf, EmailProf, TelProf, GradeProf, SpecialiteProf, DateRecrutement) VALUES (?, ?, ?, ?, ?, ?, NOW())');
              $stmt->execute([$nom, $prenom, $email, $tel, $grade, $specialite]);

              log_audit($db, 'AJOUT_PROFESSEUR', 'Professeur', "Ajout de $nom $prenom");
              set_flash('success', "Le professeur $nom $prenom a été ajouté avec succès !");
              redirect('professeur.php');
          } catch (Exception $e) {
              set_flash('danger', 'Erreur lors de l\'enregistrement : ' . $e->getMessage());
          }
      } else {
          set_flash('warning', 'Le nom et le prénom sont obligatoires.');
      }
  }
?>

  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white py-3">
          <h5 class="m-0 font-weight-bold"><i class="fas fa-user-plus mr-2"></i>Ajouter un nouvel enseignant</h5>
        </div>
        <div class="card-body p-4">
          <form action="ajoutprof.php" method="post">
            <?= csrf_field() ?>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label class="font-weight-bold">Nom de famille <span class="text-danger">*</span> :</label>
                <input type="text" class="form-control" name="nomprof" placeholder="ex: RAKOTO" required autofocus>
              </div>
              <div class="form-group col-md-6">
                <label class="font-weight-bold">Prénoms <span class="text-danger">*</span> :</label>
                <input type="text" class="form-control" name="prenomprof" placeholder="ex: Jean Luc" required>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-6">
                <label class="font-weight-bold">Grade académique :</label>
                <select class="form-control" name="gradeprof">
                  <option value="Enseignant">Enseignant</option>
                  <option value="Assistant">Assistant d'Enseignement</option>
                  <option value="Docteur">Docteur</option>
                  <option value="Maître de Conférences">Maître de Conférences</option>
                  <option value="Professeur Titulaire">Professeur Titulaire</option>
                  <option value="Intervenant Professionnel">Intervenant Professionnel</option>
                </select>
              </div>
              <div class="form-group col-md-6">
                <label class="font-weight-bold">Spécialité / Discipline :</label>
                <input type="text" class="form-control" name="specialiteprof" placeholder="ex: Génie Logiciel, Comptabilité">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-6">
                <label class="font-weight-bold">Adresse Email :</label>
                <input type="email" class="form-control" name="emailprof" placeholder="enseignant@emit.mg">
              </div>
              <div class="form-group col-md-6">
                <label class="font-weight-bold">Téléphone :</label>
                <input type="text" class="form-control" name="telprof" placeholder="+261 34 00 000 00">
              </div>
            </div>

            <div class="mt-4 pt-3 border-top text-right">
              <a href="professeur.php" class="btn btn-secondary mr-2"><i class="fas fa-times mr-1"></i>Annuler</a>
              <button type="submit" class="btn btn-primary font-weight-bold"><i class="fas fa-check mr-1"></i>Enregistrer l'enseignant</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

<?php
  include __DIR__ . '/inclus/modal.php';
?>
