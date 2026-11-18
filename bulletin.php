<?php
  require_once __DIR__ . '/inclus/barrenav.php';

  $numEtu = (int)($_GET['numetu'] ?? 0);

  // Si aucun étudiant sélectionné, proposer la recherche ou le sélecteur
  if ($numEtu === 0) {
      $stmtEtus = $db->query('SELECT NumEtu, NomEtu, PrenomEtu, CodeNiveau, CodeMention FROM etudiant ORDER BY CodeNiveau, CodeMention, NomEtu');
      $listeEtudiants = $stmtEtus->fetchAll();
  } else {
      $stmtEtu = $db->prepare('SELECT * FROM etudiant WHERE NumEtu = ?');
      $stmtEtu->execute([$numEtu]);
      $etudiant = $stmtEtu->fetch();

      if (!$etudiant) {
          set_flash('danger', 'Étudiant introuvable.');
          redirect('bulletin.php');
      }

      // Notes et matières
      $stmtNotes = $db->prepare(
        'SELECT m.CodeMatiere, m.NomMatiere, m.CoeffMatiere, ex.CodeExam, ex.DateExam, c.Note ' .
        'FROM matiere m ' .
        'LEFT JOIN examen ex ON m.CodeMatiere = ex.CodeMatiere AND ex.CodeNiveau = ? AND ex.CodeMention = ? ' .
        'LEFT JOIN controle c ON ex.CodeExam = c.CodeExam AND c.NumEtu = ? ' .
        'WHERE m.CodeNiveau = ? AND m.CodeMention = ? ' .
        'ORDER BY m.NomMatiere'
      );
      $stmtNotes->execute([$etudiant['CodeNiveau'], $etudiant['CodeMention'], $numEtu, $etudiant['CodeNiveau'], $etudiant['CodeMention']]);
      $notes = $stmtNotes->fetchAll();

      $totalPoints = 0;
      $totalCoeff = 0;
      $incomplete = false;

      foreach ($notes as $n) {
          if ($n['Note'] === null) {
              $incomplete = true;
          } else {
              $totalPoints += ((float)$n['Note'] * (int)$n['CoeffMatiere']);
          }
          $totalCoeff += (int)$n['CoeffMatiere'];
      }

      $moyenne = ($totalCoeff > 0 && !$incomplete) ? round($totalPoints / $totalCoeff, 2) : 0;
  }
?>

  <?php if ($numEtu === 0): ?>
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card shadow mb-4">
          <div class="card-header bg-primary text-white py-3">
            <h5 class="m-0 font-weight-bold"><i class="fas fa-file-pdf mr-2"></i>Générateur de Relevés de Notes & Bulletins</h5>
          </div>
          <div class="card-body p-4 text-center">
            <i class="fas fa-graduation-cap fa-4x text-primary mb-3"></i>
            <h4>Édition de Bulletin Officiel</h4>
            <p class="text-muted mb-4">Sélectionnez un étudiant pour générer son relevé officiel avec calcul automatique des moyennes pondérées.</p>

            <form method="get" action="bulletin.php" class="text-left">
              <div class="form-group">
                <label class="font-weight-bold">Choisir l'étudiant :</label>
                <select name="numetu" class="form-control form-control-lg" required>
                  <option value="" disabled selected>-- Rechercher par nom ou matricule --</option>
                  <?php foreach ($listeEtudiants as $e): ?>
                    <option value="<?= h($e['NumEtu']) ?>">
                      #<?= h($e['NumEtu']) ?> - <?= h($e['NomEtu']) ?> <?= h($e['PrenomEtu']) ?> (<?= h($e['CodeNiveau']) ?> <?= h($e['CodeMention']) ?>)
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <button type="submit" class="btn btn-primary btn-block font-weight-bold py-2 mt-3 shadow-sm">
                <i class="fas fa-print mr-2"></i>Afficher le relevé de notes
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>

  <?php else: ?>

    <!-- Actions buttons (hidden on print) -->
    <div class="d-print-none d-flex justify-content-between align-items-center mb-4">
      <a href="bulletin.php" class="btn btn-secondary font-weight-bold">
        <i class="fas fa-arrow-left mr-1"></i>Choisir un autre étudiant
      </a>
      <div>
        <button class="btn btn-primary font-weight-bold shadow-sm" onclick="window.print();">
          <i class="fas fa-print mr-1"></i>Imprimer / Exporter en PDF
        </button>
      </div>
    </div>

    <!-- Official Report Card Card -->
    <div class="card shadow p-5 bg-white text-dark" id="bulletinPrintArea" style="border: 2px solid #333; max-width: 900px; margin: 0 auto;">
      <!-- University Header -->
      <div class="row align-items-center border-bottom pb-3 mb-4">
        <div class="col-2 text-center">
          <i class="fas fa-university fa-4x text-primary"></i>
        </div>
        <div class="col-7 text-center">
          <h6 class="text-uppercase font-weight-bold mb-0">Repoblikan'i Madagasikara</h6>
          <small class="font-italic text-muted">Fitiavana - Tanindrazana - Fandrosoana</small>
          <hr class="my-1" style="width: 60px; border-top: 2px solid #333; margin: auto;">
          <h5 class="font-weight-bold text-primary mb-0 mt-2">UNIVERSITÉ DE FIANARANTSOA</h5>
          <h6 class="font-weight-bold mb-0">ÉCOLE DE MANAGEMENT ET D'INNOVATION TECHNOLOGIQUE</h6>
          <small class="text-muted">BP 1487 - Andrainjato Fianarantsoa | Scolarité Générale</small>
        </div>
        <div class="col-3 text-right">
          <span class="badge badge-secondary p-2 d-block">ANNEE UNIV.</span>
          <strong class="h5"><?= h($etudiant['AnneeUniv'] ?: '2024-2025') ?></strong>
        </div>
      </div>

      <div class="text-center my-4">
        <h3 class="font-weight-bold text-uppercase text-dark" style="letter-spacing: 1px; text-decoration: underline;">
          RELEVÉ DE NOTES OFFICIEL
        </h3>
        <span class="text-muted">Semestre Académique Ordinaire</span>
      </div>

      <!-- Student info box -->
      <div class="p-3 bg-light rounded mb-4" style="border: 1px solid #ddd;">
        <div class="row">
          <div class="col-sm-6">
            <p class="mb-1"><strong>Matricule :</strong> #<?= h($etudiant['NumEtu']) ?></p>
            <p class="mb-1"><strong>Nom & Prénoms :</strong> <?= h($etudiant['NomEtu']) ?> <?= h($etudiant['PrenomEtu']) ?></p>
            <p class="mb-1"><strong>Date de naissance :</strong> <?= h(format_date_fr($etudiant['DateNaiss'])) ?></p>
            <p class="mb-0"><strong>CIN / Passeport :</strong> <?= h($etudiant['CinEtu'] ?: '-') ?></p>
          </div>
          <div class="col-sm-6">
            <p class="mb-1"><strong>Niveau & Mention :</strong> <?= h($etudiant['CodeNiveau']) ?> - <?= h($etudiant['CodeMention']) ?></p>
            <p class="mb-1"><strong>Nationalité :</strong> <?= h($etudiant['NationEtu']) ?></p>
            <p class="mb-1"><strong>Genre :</strong> <?= h($etudiant['SexeEtu']) ?></p>
            <p class="mb-0"><strong>Date d'inscription :</strong> <?= h(format_date_fr($etudiant['DateInscr'])) ?></p>
          </div>
        </div>
      </div>

      <!-- Grades Table -->
      <div class="table-responsive mb-4">
        <table class="table table-bordered table-sm text-center">
          <thead class="thead-dark">
            <tr>
              <th class="text-left" style="width: 45%;">Matière / Épreuve</th>
              <th style="width: 15%;">Coeff</th>
              <th style="width: 20%;">Note / 20</th>
              <th style="width: 20%;">Total Coeff</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($notes as $n):
              $pt = ($n['Note'] !== null) ? ((float)$n['Note'] * (int)$n['CoeffMatiere']) : '-';
            ?>
            <tr>
              <td class="text-left font-weight-bold"><?= h($n['NomMatiere']) ?></td>
              <td><?= h($n['CoeffMatiere']) ?></td>
              <td class="font-weight-bold <?= ($n['Note'] !== null && $n['Note'] < 10) ? 'text-danger' : '' ?>">
                <?= ($n['Note'] !== null) ? number_format((float)$n['Note'], 2) : '<span class="badge badge-warning">En attente</span>' ?>
              </td>
              <td class="font-weight-bold">
                <?= ($pt !== '-') ? number_format($pt, 2) : '-' ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot class="bg-light font-weight-bold">
            <tr>
              <td class="text-right">TOTAL PONDÉRÉ :</td>
              <td><?= $totalCoeff ?></td>
              <td colspan="2" class="text-right h5 mb-0 text-primary">
                <?= number_format($totalPoints, 2) ?> / <?= $totalCoeff * 20 ?>
              </td>
            </tr>
          </tfoot>
        </table>
      </div>

      <!-- Decision Box -->
      <div class="row align-items-center p-3 mb-5 rounded" style="background:#f4f6f9; border: 2px dashed #4e73df;">
        <div class="col-md-6">
          <h5 class="mb-1">Moyenne Générale : <span class="badge badge-primary h4 mb-0"><?= number_format($moyenne, 2) ?> / 20</span></h5>
          <p class="small text-muted mb-0">Base de calcul pondérée selon les crédits d'enseignements.</p>
        </div>
        <div class="col-md-6 text-right">
          <h6>DÉCISION DU JURY :</h6>
          <?php if ($incomplete): ?>
            <span class="badge badge-warning p-2 h5 font-weight-bold">DOSSIER INCOMPLET (RATTRAPAGE)</span>
          <?php elseif ($moyenne >= 10): ?>
            <span class="badge badge-success p-2 h5 font-weight-bold">ADMIS(E) AVEC SUCCÈS</span>
          <?php else: ?>
            <span class="badge badge-danger p-2 h5 font-weight-bold">NON ADMIS(E) / AJOURNÉ(E)</span>
          <?php endif; ?>
        </div>
      </div>

      <!-- Signatures Footer -->
      <div class="row pt-4 text-center">
        <div class="col-4">
          <p class="font-weight-bold mb-5">L'Étudiant(e)</p>
          <p class="text-muted small mt-4">(Signature)</p>
        </div>
        <div class="col-4">
          <p class="font-weight-bold mb-5">Le Responsable de Mention</p>
          <p class="text-muted small mt-4">(Cachet & Signature)</p>
        </div>
        <div class="col-4">
          <p class="font-weight-bold mb-1">Fait à Fianarantsoa, le <?= date('d/m/Y') ?></p>
          <p class="font-weight-bold mb-5">Le Chef de Scolarité</p>
          <p class="text-muted small mt-2">[Sceau de l'Établissement]</p>
        </div>
      </div>
    </div>

  <?php endif; ?>

<?php
  include __DIR__ . '/inclus/modal.php';
?>
