<?php
  require_once __DIR__ . '/inclus/barrenav.php';
  require_role(['CSCO', 'ADMIN', 'SCR']);

  try {
      $stmt = $db->query(
        'SELECT a.*, u.NomCGU, u.RoleUGU ' .
        'FROM audit_log a ' .
        'LEFT JOIN utilisateur u ON a.IdUGU = u.IdUGU ' .
        'ORDER BY a.DateLog DESC LIMIT 200'
      );
      $logs = $stmt->fetchAll();
  } catch (Exception $e) {
      $logs = [];
  }
?>

  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
      <h3 class="font-weight-bold text-primary mb-1">
        <i class="fas fa-shield-alt mr-2"></i>Journal d'Audit & Sécurité
      </h3>
      <p class="text-muted small mb-0">Traçabilité complète des opérations et actions sensibles effectuées dans le système.</p>
    </div>
    <button class="btn btn-sm btn-outline-secondary shadow-sm font-weight-bold" onclick="window.print();">
      <i class="fas fa-print mr-1"></i>Imprimer le journal
    </button>
  </div>

  <div class="card shadow mb-4">
    <div class="card-header bg-white py-3">
      <h6 class="m-0 font-weight-bold text-primary">
        <i class="fas fa-list-alt mr-1"></i>Historique des événements récents (200 derniers)
      </h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped table-bordered table-sm table-hover" id="dataTable" width="100%" cellspacing="0">
          <thead class="thead-dark">
            <tr>
              <th style="width: 140px;">Date & Heure</th>
              <th>Utilisateur</th>
              <th>Action</th>
              <th>Cible</th>
              <th>Détails</th>
              <th style="width: 110px;">Adresse IP</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($logs as $l):
              $badge = 'badge-secondary';
              if (strpos($l['Action'], 'AJOUT') !== false || strpos($l['Action'], 'INSCRI') !== false) $badge = 'badge-success';
              if (strpos($l['Action'], 'MODIF') !== false || strpos($l['Action'], 'MAJ') !== false) $badge = 'badge-warning text-dark';
              if (strpos($l['Action'], 'SUPP') !== false || strpos($l['Action'], 'RENVOI') !== false) $badge = 'badge-danger';
              if (strpos($l['Action'], 'CONNEXION') !== false) $badge = 'badge-info';
              if (strpos($l['Action'], 'ECHEC') !== false) $badge = 'badge-danger';
            ?>
            <tr>
              <td class="small align-middle"><?= h($l['DateLog']) ?></td>
              <td class="font-weight-bold align-middle">
                <?= h($l['NomCGU'] ?? 'Système') ?>
                <small class="text-muted d-block"><?= h($l['RoleUGU'] ?? '') ?></small>
              </td>
              <td class="align-middle">
                <span class="badge <?= $badge ?> px-2 py-1"><?= h($l['Action']) ?></span>
              </td>
              <td class="align-middle font-weight-bold text-dark"><?= h($l['Cible'] ?? '-') ?></td>
              <td class="small align-middle text-muted"><?= h($l['Details'] ?? '-') ?></td>
              <td class="small text-monospace align-middle"><?= h($l['IpAddress'] ?? '-') ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

<?php
  include __DIR__ . '/inclus/modal.php';
?>
