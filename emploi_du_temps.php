<?php
  require_once __DIR__ . '/inclus/barrenav.php';

  $codeNiv = $_GET['codeniv'] ?? 'L1';
  $codeMen = $_GET['codemen'] ?? 'IG';
  $anneeActive = get_active_academic_year($db);

  try {
    $stmtEdt = $db->prepare(
      'SELECT s.*, m.NomMatiere, p.NomProf, p.PrenomProf ' .
      'FROM emploi_du_temps s ' .
      'JOIN matiere m ON s.CodeMatiere = m.CodeMatiere ' .
      'LEFT JOIN professeur p ON s.IdProf = p.IdProf ' .
      'WHERE s.CodeNiveau = ? AND s.CodeMention = ? ' .
      'ORDER BY FIELD(s.JourSemaine, "Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi"), s.HeureDebut'
    );
    $stmtEdt->execute([$codeNiv, $codeMen]);
    $seances = $stmtEdt->fetchAll();

    // Matières disponibles pour cette classe
    $stmtMat = $db->prepare('SELECT * FROM matiere WHERE CodeNiveau = ? AND CodeMention = ? ORDER BY NomMatiere');
    $stmtMat->execute([$codeNiv, $codeMen]);
    $matieres = $stmtMat->fetchAll();

    // Tous les professeurs
    $profs = $db->query('SELECT * FROM professeur ORDER BY NomProf')->fetchAll();

  } catch (Exception $e) {
    $seances = [];
    $matieres = [];
    $profs = [];
  }
?>

  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
      <h3 class="font-weight-bold text-primary mb-1">
        <i class="fas fa-calendar-alt mr-2"></i>Emploi du Temps : <?= h($codeNiv) ?> <?= h($codeMen) ?>
      </h3>
      <p class="text-muted small mb-0">Année universitaire active : <strong><?= h($anneeActive) ?></strong></p>
    </div>
    <div>
      <!-- Class Selector Dropdown -->
      <div class="btn-group mr-2">
        <button type="button" class="btn btn-outline-primary dropdown-toggle shadow-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fas fa-filter mr-1"></i>Changer de classe : <strong><?= h($codeNiv) ?> <?= h($codeMen) ?></strong>
        </button>
        <div class="dropdown-menu dropdown-menu-right" style="max-height: 280px; overflow-y: auto;">
          <?php foreach ($allClasses as $c): ?>
            <a class="dropdown-item <?= ($c['CodeNiveau'] === $codeNiv && $c['CodeMention'] === $codeMen) ? 'active' : '' ?>"
               href="emploi_du_temps.php?codeniv=<?= h($c['CodeNiveau']) ?>&codemen=<?= h($c['CodeMention']) ?>">
              <?= h($c['CodeNiveau']) ?> <?= h($c['CodeMention']) ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
      <button class="btn btn-primary shadow-sm font-weight-bold" data-toggle="modal" data-target="#modalAjoutSeance">
        <i class="fas fa-plus mr-1"></i>Ajouter une séance
      </button>
      <button class="btn btn-secondary shadow-sm font-weight-bold ml-1" onclick="window.print();">
        <i class="fas fa-print mr-1"></i>Imprimer
      </button>
    </div>
  </div>

  <!-- Weekly Timetable Table -->
  <div class="card shadow mb-4">
    <div class="card-header bg-white py-3">
      <h6 class="m-0 font-weight-bold text-primary">
        <i class="fas fa-clock mr-1"></i>Planning hebdomadaire des cours
      </h6>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-bordered table-hover mb-0">
          <thead class="thead-dark text-center">
            <tr>
              <th style="width: 140px;">Jour</th>
              <th style="width: 160px;">Horaires</th>
              <th>Matière</th>
              <th>Enseignant</th>
              <th>Salle</th>
              <th style="width: 100px;">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($seances)): ?>
              <tr>
                <td colspan="6" class="text-center py-4 text-muted">
                  <i class="fas fa-info-circle mr-1"></i>Aucune séance programmée pour cette classe. Cliquez sur <strong>Ajouter une séance</strong> pour débuter.
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($seances as $s): ?>
                <tr>
                  <td class="font-weight-bold text-center align-middle bg-light">
                    <span class="badge badge-primary px-3 py-2"><?= h($s['JourSemaine']) ?></span>
                  </td>
                  <td class="text-center align-middle font-weight-bold text-dark">
                    <?= substr(h($s['HeureDebut']), 0, 5) ?> - <?= substr(h($s['HeureFin']), 0, 5) ?>
                  </td>
                  <td class="align-middle">
                    <strong><?= h($s['NomMatiere']) ?></strong>
                    <small class="text-muted d-block"><?= h($s['CodeMatiere']) ?></small>
                  </td>
                  <td class="align-middle">
                    <?php if (!empty($s['NomProf'])): ?>
                      <i class="fas fa-user-tie text-muted mr-1"></i><?= h($s['NomProf']) ?> <?= h($s['PrenomProf']) ?>
                    <?php else: ?>
                      <span class="text-muted font-italic">Non assigné</span>
                    <?php endif; ?>
                  </td>
                  <td class="align-middle font-weight-bold text-info">
                    <i class="fas fa-door-open mr-1"></i><?= h($s['Salle'] ?: 'Non spécifiée') ?>
                  </td>
                  <td class="text-center align-middle">
                    <form action="suppseance.php" method="post" onsubmit="return confirm('Supprimer cette séance ?');">
                      <?= csrf_field() ?>
                      <input type="hidden" name="idseance" value="<?= h($s['IdSeance']) ?>">
                      <input type="hidden" name="codeniv" value="<?= h($codeNiv) ?>">
                      <input type="hidden" name="codemen" value="<?= h($codeMen) ?>">
                      <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal Ajout Séance -->
  <div class="modal fade" id="modalAjoutSeance" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content border-0 shadow">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title font-weight-bold"><i class="fas fa-plus mr-2"></i>Programmer un cours (<?= h($codeNiv) ?> <?= h($codeMen) ?>)</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="ajoutseance.php" method="post">
          <?= csrf_field() ?>
          <input type="hidden" name="codeniv" value="<?= h($codeNiv) ?>">
          <input type="hidden" name="codemen" value="<?= h($codeMen) ?>">
          <input type="hidden" name="anneeuniv" value="<?= h($anneeActive) ?>">

          <div class="modal-body py-3">
            <div class="form-group">
              <label class="font-weight-bold">Matière <span class="text-danger">*</span> :</label>
              <select class="form-control" name="codematiere" required>
                <option value="" disabled selected>Sélectionner la matière</option>
                <?php foreach ($matieres as $m): ?>
                  <option value="<?= h($m['CodeMatiere']) ?>"><?= h($m['NomMatiere']) ?> (<?= h($m['CodeMatiere']) ?>)</option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label class="font-weight-bold">Enseignant responsable :</label>
              <select class="form-control" name="idprof">
                <option value="">-- Aucun enseignant assigné --</option>
                <?php foreach ($profs as $p): ?>
                  <option value="<?= h($p['IdProf']) ?>"><?= h($p['NomProf']) ?> <?= h($p['PrenomProf']) ?> (<?= h($p['GradeProf']) ?>)</option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-row">
              <div class="form-group col-md-4">
                <label class="font-weight-bold">Jour :</label>
                <select class="form-control" name="joursemaine" required>
                  <option value="Lundi">Lundi</option>
                  <option value="Mardi">Mardi</option>
                  <option value="Mercredi">Mercredi</option>
                  <option value="Jeudi">Jeudi</option>
                  <option value="Vendredi">Vendredi</option>
                  <option value="Samedi">Samedi</option>
                </select>
              </div>
              <div class="form-group col-md-4">
                <label class="font-weight-bold">Début :</label>
                <input type="time" class="form-control" name="heuredebut" required value="08:00">
              </div>
              <div class="form-group col-md-4">
                <label class="font-weight-bold">Fin :</label>
                <input type="time" class="form-control" name="heurefin" required value="10:00">
              </div>
            </div>

            <div class="form-group mb-0">
              <label class="font-weight-bold">Salle / Laboratoire :</label>
              <input type="text" class="form-control" name="salle" placeholder="ex: Salle 14, Labo Info, Amphi A" required>
            </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="submit" class="btn btn-primary font-weight-bold"><i class="fas fa-check mr-1"></i>Enregistrer la séance</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
          </div>
        </form>
      </div>
    </div>
  </div>

<?php
  include __DIR__ . '/inclus/modal.php';
?>
