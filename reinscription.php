<?php
  require_once __DIR__ . '/inclus/barrenav.php';

  $numetu = (int)($_GET['numetu'] ?? 0);
  $anneeActive = get_active_academic_year($db);

  $stmt = $db->prepare('SELECT e.*, n.NumNiveau FROM etudiant e JOIN niveau n ON e.CodeNiveau = n.CodeNiveau WHERE e.NumEtu = ?');
  $stmt->execute([$numetu]);
  $etudiant = $stmt->fetch();

  if (!$etudiant) {
      set_flash('danger', 'Étudiant introuvable.');
      redirect('accueil.php');
  }

  // Niveaux éligibles pour la réinscription (niveau actuel ou suivant)
  $stmtNiv = $db->prepare('SELECT * FROM niveau WHERE NumNiveau >= ? ORDER BY NumNiveau LIMIT 2');
  $stmtNiv->execute([$etudiant['NumNiveau']]);
  $niveauxEligibles = $stmtNiv->fetchAll();
?>

  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card shadow mb-4">
        <div class="card-header bg-success text-white py-3">
          <h5 class="m-0 font-weight-bold"><i class="fas fa-redo mr-2"></i>Réinscription / Passage de Niveau</h5>
        </div>
        <div class="card-body p-4">
          <form action="reinscrire.php" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="numetu" value="<?= h($etudiant['NumEtu']) ?>">
            <input type="hidden" name="mentionetu" value="<?= h($etudiant['CodeMention']) ?>">
            <input type="hidden" name="nometu" value="<?= h($etudiant['NomEtu']) ?>">
            <input type="hidden" name="prenometu" value="<?= h($etudiant['PrenomEtu']) ?>">

            <div class="p-3 bg-light rounded mb-4" style="border: 1px solid #e3e6f0;">
              <h6 class="font-weight-bold text-dark mb-2">Étudiant sélectionné :</h6>
              <div class="row">
                <div class="col-sm-6">
                  <p class="mb-1"><strong>Matricule :</strong> #<?= h($etudiant['NumEtu']) ?></p>
                  <p class="mb-0"><strong>Nom & Prénoms :</strong> <?= h($etudiant['NomEtu']) ?> <?= h($etudiant['PrenomEtu']) ?></p>
                </div>
                <div class="col-sm-6">
                  <p class="mb-1"><strong>Classe actuelle :</strong> <?= h($etudiant['CodeNiveau']) ?> <?= h($etudiant['CodeMention']) ?></p>
                  <p class="mb-0"><strong>Dernière année :</strong> <?= h($etudiant['AnneeUniv'] ?: '-') ?></p>
                </div>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-6">
                <label class="font-weight-bold">Nouvelle année universitaire :</label>
                <input class="form-control bg-white font-weight-bold text-success text-center" name="anneeuniv" value="<?= h($anneeActive) ?>" readonly>
              </div>
              <div class="form-group col-md-6">
                <label class="font-weight-bold">Niveau académique d'affectation :</label>
                <select class="form-control" name="niveauetu" required>
                  <?php foreach ($niveauxEligibles as $n): ?>
                    <option value="<?= h($n['CodeNiveau']) ?>" <?= ($n['CodeNiveau'] === $etudiant['CodeNiveau']) ? 'selected' : '' ?>>
                      <?= h($n['CodeNiveau']) ?> (<?= h($n['NomNiveau']) ?>)
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="font-weight-bold">Date de réinscription :</label>
              <input type="date" class="form-control" name="dateinscr" value="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="mt-4 pt-3 border-top text-right">
              <a href="etudiant.php?codeniv=<?= h($etudiant['CodeNiveau']) ?>&codemen=<?= h($etudiant['CodeMention']) ?>" class="btn btn-secondary mr-2">Annuler</a>
              <button type="submit" class="btn btn-success font-weight-bold px-4 shadow-sm">
                <i class="fas fa-check mr-1"></i>Confirmer la réinscription
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
