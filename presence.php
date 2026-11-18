<?php
  require_once __DIR__ . '/inclus/barrenav.php';

  $codeNiv = $_GET['codeniv'] ?? 'L1';
  $codeMen = $_GET['codemen'] ?? 'IG';
  $idSeance = (int)($_GET['idseance'] ?? 0);
  $datePresence = $_GET['date'] ?? date('Y-m-d');

  try {
    // Liste des séances programmées pour cette classe
    $stmtSeances = $db->prepare(
      'SELECT s.IdSeance, s.JourSemaine, s.HeureDebut, s.HeureFin, s.Salle, m.NomMatiere ' .
      'FROM emploi_du_temps s ' .
      'JOIN matiere m ON s.CodeMatiere = m.CodeMatiere ' .
      'WHERE s.CodeNiveau = ? AND s.CodeMention = ? ' .
      'ORDER BY FIELD(s.JourSemaine, "Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi")'
    );
    $stmtSeances->execute([$codeNiv, $codeMen]);
    $seances = $stmtSeances->fetchAll();

    if ($idSeance === 0 && !empty($seances)) {
        $idSeance = (int)$seances[0]['IdSeance'];
    }

    // Liste des étudiants de la classe
    $stmtEtus = $db->prepare('SELECT * FROM etudiant WHERE CodeNiveau = ? AND CodeMention = ? ORDER BY NomEtu, PrenomEtu');
    $stmtEtus->execute([$codeNiv, $codeMen]);
    $etudiants = $stmtEtus->fetchAll();

    // État actuel des présences pour cette séance et cette date
    $presencesActuelles = [];
    if ($idSeance > 0) {
        $stmtP = $db->prepare('SELECT NumEtu, Statut, Motif FROM presence WHERE IdSeance = ? AND DatePresence = ?');
        $stmtP->execute([$idSeance, $datePresence]);
        while ($row = $stmtP->fetch()) {
            $presencesActuelles[$row['NumEtu']] = $row;
        }
    }

  } catch (Exception $e) {
    $seances = [];
    $etudiants = [];
    $presencesActuelles = [];
  }
?>

  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
      <h3 class="font-weight-bold text-primary mb-1">
        <i class="fas fa-user-check mr-2"></i>Gestion de l'Assiduité et des Présences
      </h3>
      <p class="text-muted small mb-0">Classe : <strong><?= h($codeNiv) ?> <?= h($codeMen) ?></strong> | Date : <strong><?= h(format_date_fr($datePresence)) ?></strong></p>
    </div>
    <div class="btn-group">
      <button type="button" class="btn btn-outline-primary dropdown-toggle shadow-sm" data-toggle="dropdown">
        <i class="fas fa-filter mr-1"></i>Classe : <strong><?= h($codeNiv) ?> <?= h($codeMen) ?></strong>
      </button>
      <div class="dropdown-menu dropdown-menu-right" style="max-height: 280px; overflow-y: auto;">
        <?php foreach ($allClasses as $c): ?>
          <a class="dropdown-item <?= ($c['CodeNiveau'] === $codeNiv && $c['CodeMention'] === $codeMen) ? 'active' : '' ?>"
             href="presence.php?codeniv=<?= h($c['CodeNiveau']) ?>&codemen=<?= h($c['CodeMention']) ?>&date=<?= h($datePresence) ?>">
            <?= h($c['CodeNiveau']) ?> <?= h($c['CodeMention']) ?>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- Filters Row -->
  <div class="card shadow mb-4">
    <div class="card-body bg-light">
      <form method="get" action="presence.php" class="form-row align-items-end">
        <input type="hidden" name="codeniv" value="<?= h($codeNiv) ?>">
        <input type="hidden" name="codemen" value="<?= h($codeMen) ?>">

        <div class="form-group col-md-5 mb-0">
          <label class="font-weight-bold small text-dark">Séance de cours :</label>
          <select class="form-control" name="idseance" required>
            <?php if (empty($seances)): ?>
              <option value="">Aucune séance programmée dans l'emploi du temps</option>
            <?php else: ?>
              <?php foreach ($seances as $s): ?>
                <option value="<?= h($s['IdSeance']) ?>" <?= $s['IdSeance'] == $idSeance ? 'selected' : '' ?>>
                  <?= h($s['JourSemaine']) ?> (<?= substr(h($s['HeureDebut']), 0, 5) ?> - <?= substr(h($s['HeureFin']), 0, 5) ?>) : <?= h($s['NomMatiere']) ?> [<?= h($s['Salle']) ?>]
                </option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
        </div>

        <div class="form-group col-md-4 mb-0">
          <label class="font-weight-bold small text-dark">Date de la séance :</label>
          <input type="date" class="form-control" name="date" value="<?= h($datePresence) ?>" required>
        </div>

        <div class="form-group col-md-3 mb-0">
          <button type="submit" class="btn btn-primary btn-block font-weight-bold">
            <i class="fas fa-search mr-1"></i>Charger la feuille
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Attendance Register Form -->
  <?php if ($idSeance > 0 && !empty($etudiants)): ?>
  <div class="card shadow mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
      <h6 class="m-0 font-weight-bold text-primary">
        <i class="fas fa-clipboard-list mr-1"></i>Feuille de présence (<?= count($etudiants) ?> étudiants)
      </h6>
      <div>
        <button type="button" class="btn btn-sm btn-outline-success font-weight-bold" onclick="marquerTous('Present');">
          <i class="fas fa-check-double mr-1"></i>Tous Présents
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary font-weight-bold ml-1" onclick="window.print();">
          <i class="fas fa-print mr-1"></i>Imprimer la feuille
        </button>
      </div>
    </div>
    <form action="marquerpresence.php" method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="idseance" value="<?= h($idSeance) ?>">
      <input type="hidden" name="datepresence" value="<?= h($datePresence) ?>">
      <input type="hidden" name="codeniv" value="<?= h($codeNiv) ?>">
      <input type="hidden" name="codemen" value="<?= h($codeMen) ?>">

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-striped table-bordered table-hover mb-0">
            <thead class="thead-dark text-center">
              <tr>
                <th style="width: 100px;">N° Matr.</th>
                <th>Nom & Prénoms</th>
                <th style="width: 280px;">Statut de Présence</th>
                <th>Motif / Justificatif (si absence)</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($etudiants as $etu):
                $num = $etu['NumEtu'];
                $statut = $presencesActuelles[$num]['Statut'] ?? 'Present';
                $motif = $presencesActuelles[$num]['Motif'] ?? '';
              ?>
              <tr>
                <td class="text-center font-weight-bold">#<?= h($num) ?></td>
                <td class="font-weight-bold text-dark"><?= h($etu['NomEtu']) ?> <?= h($etu['PrenomEtu']) ?></td>
                <td class="text-center">
                  <div class="btn-group btn-group-toggle d-flex" data-toggle="buttons">
                    <label class="btn btn-sm btn-outline-success flex-fill <?= $statut === 'Present' ? 'active' : '' ?>">
                      <input type="radio" name="statut[<?= h($num) ?>]" value="Present" <?= $statut === 'Present' ? 'checked' : '' ?>> Présent
                    </label>
                    <label class="btn btn-sm btn-outline-danger flex-fill <?= $statut === 'Absent' ? 'active' : '' ?>">
                      <input type="radio" name="statut[<?= h($num) ?>]" value="Absent" <?= $statut === 'Absent' ? 'checked' : '' ?>> Absent
                    </label>
                    <label class="btn btn-sm btn-outline-warning flex-fill <?= $statut === 'Excuse' ? 'active' : '' ?>">
                      <input type="radio" name="statut[<?= h($num) ?>]" value="Excuse" <?= $statut === 'Excuse' ? 'checked' : '' ?>> Excusé
                    </label>
                  </div>
                </td>
                <td>
                  <input type="text" class="form-control form-control-sm" name="motif[<?= h($num) ?>]" value="<?= h($motif) ?>" placeholder="Optionnel...">
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="card-footer bg-light text-right py-3">
        <button type="submit" class="btn btn-primary font-weight-bold px-4 shadow-sm">
          <i class="fas fa-save mr-1"></i>Enregistrer l'appel
        </button>
      </div>
    </form>
  </div>
  <?php endif; ?>

  <script>
    function marquerTous(statut) {
      document.querySelectorAll('input[type="radio"][value="' + statut + '"]').forEach(function(radio) {
        radio.checked = true;
        radio.closest('label').classList.add('active');
        // Uncheck others in group
        radio.closest('.btn-group').querySelectorAll('label').forEach(function(lbl) {
          if (lbl !== radio.closest('label')) lbl.classList.remove('active');
        });
      });
    }
  </script>

<?php
  include __DIR__ . '/inclus/modal.php';
?>
