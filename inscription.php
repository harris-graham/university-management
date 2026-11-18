<?php
  require_once __DIR__ . '/inclus/barrenav.php';

  $anneeActive = get_active_academic_year($db);

  try {
    $niveauxInscr = $db->query('SELECT * FROM niveau WHERE NumNiveau IN (1, 4) ORDER BY NumNiveau')->fetchAll();
    $mentionsInscr = $db->query('SELECT * FROM mention ORDER BY CodeMention')->fetchAll();
  } catch (Exception $e) {
    $niveauxInscr = [];
    $mentionsInscr = [];
  }
?>

  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white py-3">
          <h5 class="m-0 font-weight-bold"><i class="fas fa-user-plus mr-2"></i>Nouvelle Inscription Étudiant</h5>
        </div>
        <div class="card-body p-4">
          <form action="inscrire.php" method="post">
            <?= csrf_field() ?>

            <!-- Informations académiques -->
            <div class="p-3 bg-light rounded mb-4" style="border: 1px solid #e3e6f0;">
              <h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-university mr-1"></i>Filière & Année d'admission</h6>
              <div class="form-row">
                <div class="form-group col-md-4">
                  <label class="font-weight-bold">Année universitaire :</label>
                  <input class="form-control bg-white font-weight-bold text-primary text-center" name="anneeuniv" value="<?= h($anneeActive) ?>" readonly>
                </div>
                <div class="form-group col-md-4">
                  <label class="font-weight-bold">Niveau d'entrée <span class="text-danger">*</span> :</label>
                  <select class="form-control" name="niveauetu" required autofocus>
                    <option value="" disabled selected>Sélectionner le niveau</option>
                    <?php foreach ($niveauxInscr as $niv): ?>
                      <option value="<?= h($niv['CodeNiveau']) ?>"><?= h($niv['CodeNiveau']) ?> (<?= h($niv['NomNiveau']) ?>)</option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="form-group col-md-4">
                  <label class="font-weight-bold">Mention choisie <span class="text-danger">*</span> :</label>
                  <select class="form-control" name="mentionetu" required>
                    <option value="" disabled selected>Sélectionner la mention</option>
                    <?php foreach ($mentionsInscr as $men): ?>
                      <option value="<?= h($men['CodeMention']) ?>"><?= h($men['CodeMention']) ?> - <?= h($men['NomMention']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
            </div>

            <!-- État Civil -->
            <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-id-card mr-1"></i>Informations Personnelles</h6>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label class="font-weight-bold">Nom <span class="text-danger">*</span> :</label>
                <input class="form-control" name="nometu" placeholder="ex: RAKOTO" maxlength="50" required>
              </div>
              <div class="form-group col-md-6">
                <label class="font-weight-bold">Prénoms <span class="text-danger">*</span> :</label>
                <input class="form-control" name="prenometu" placeholder="ex: Jean Luc" maxlength="60" required>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-4">
                <label class="font-weight-bold">Date de naissance :</label>
                <input type="date" class="form-control" name="datenaiss">
              </div>
              <div class="form-group col-md-4">
                <label class="font-weight-bold">N° CIN / Passeport :</label>
                <input class="form-control" name="cinetu" placeholder="ex: 101234567890" maxlength="20">
              </div>
              <div class="form-group col-md-4">
                <label class="font-weight-bold">Genre <span class="text-danger">*</span> :</label>
                <select class="form-control" name="sexeetu" required>
                  <option value="Masculin">Masculin</option>
                  <option value="Feminin">Féminin</option>
                </select>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-4">
                <label class="font-weight-bold">Nationalité :</label>
                <select class="form-control" name="nationetu">
                  <option value="Malagasy" selected>Malagasy</option>
                  <option value="Autre">Autre</option>
                </select>
              </div>
              <div class="form-group col-md-4">
                <label class="font-weight-bold">Téléphone :</label>
                <input class="form-control" name="teletu" placeholder="+261 34 00 000 00" maxlength="20">
              </div>
              <div class="form-group col-md-4">
                <label class="font-weight-bold">Date d'inscription :</label>
                <input type="date" class="form-control" name="dateinscr" value="<?= date('Y-m-d') ?>">
              </div>
            </div>

            <div class="form-group">
              <label class="font-weight-bold">Adresse physique :</label>
              <input class="form-control" name="adresseetu" placeholder="Lot / Quartier / Ville" maxlength="100">
            </div>

            <div class="mt-4 pt-3 border-top text-right">
              <a href="accueil.php" class="btn btn-secondary mr-2"><i class="fas fa-times mr-1"></i>Annuler</a>
              <button type="submit" class="btn btn-primary font-weight-bold px-4 shadow-sm">
                <i class="fas fa-check mr-1"></i>Valider l'inscription
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
