<?php
  require_once __DIR__ . '/inclus/barrenav.php';

  $userId = $_SESSION['IdUGU'] ?? 1;

  // Marquer toutes comme lues si demandé
  if (isset($_GET['mark_all_read'])) {
      $stmtMark = $db->prepare('UPDATE notification SET Lu = 1 WHERE IdUGU = ?');
      $stmtMark->execute([$userId]);
      set_flash('success', 'Toutes les notifications ont été marquées comme lues.');
      redirect('notifications.php');
  }

  // Suppression d'une notification
  if (isset($_POST['delete_notif'])) {
      if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
          $idNotif = (int)$_POST['id_notif'];
          $stmtDel = $db->prepare('DELETE FROM notification WHERE IdNotif = ? AND IdUGU = ?');
          $stmtDel->execute([$idNotif, $userId]);
          set_flash('success', 'Notification supprimée.');
      }
      redirect('notifications.php');
  }

  // Récupérer toutes les notifications de l'utilisateur
  try {
      $stmtNotifs = $db->prepare('SELECT * FROM notification WHERE IdUGU = ? ORDER BY DateCreation DESC');
      $stmtNotifs->execute([$userId]);
      $notifs = $stmtNotifs->fetchAll();
  } catch (Exception $e) {
      $notifs = [];
  }
?>

  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
      <h3 class="font-weight-bold text-primary mb-1">
        <i class="fas fa-bell mr-2"></i>Centre de Notifications
      </h3>
      <p class="text-muted small mb-0">Consultez les alertes, rappels académiques et notifications système.</p>
    </div>
    <div>
      <?php if (!empty($notifs)): ?>
        <a href="notifications.php?mark_all_read=1" class="btn btn-sm btn-outline-primary shadow-sm font-weight-bold">
          <i class="fas fa-check-double mr-1"></i>Tout marquer comme lu
        </a>
      <?php endif; ?>
    </div>
  </div>

  <div class="card shadow mb-4">
    <div class="card-header bg-white py-3">
      <h6 class="m-0 font-weight-bold text-primary">
        <i class="fas fa-inbox mr-1"></i>Messages récents (<?= count($notifs) ?>)
      </h6>
    </div>
    <div class="card-body p-0">
      <?php if (empty($notifs)): ?>
        <div class="text-center py-5 text-muted">
          <i class="fas fa-bell-slash fa-3x mb-3 text-gray-300"></i>
          <h5>Aucune notification</h5>
          <p class="small mb-0">Vous êtes à jour, aucune alerte en attente.</p>
        </div>
      <?php else: ?>
        <div class="list-group list-group-flush">
          <?php foreach ($notifs as $n):
            $typeClass = 'border-left-primary';
            $icon = 'info-circle text-primary';
            if ($n['TypeNotif'] === 'success') { $typeClass = 'border-left-success'; $icon = 'check-circle text-success'; }
            if ($n['TypeNotif'] === 'warning') { $typeClass = 'border-left-warning'; $icon = 'exclamation-triangle text-warning'; }
            if ($n['TypeNotif'] === 'danger')  { $typeClass = 'border-left-danger'; $icon = 'exclamation-circle text-danger'; }
          ?>
            <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?= $typeClass ?> <?= $n['Lu'] ? 'bg-light' : 'bg-white' ?>" style="border-left-width: 4px;">
              <div class="d-flex align-items-center">
                <i class="fas fa-<?= $icon ?> fa-2x mr-3"></i>
                <div>
                  <p class="mb-1 text-dark <?= $n['Lu'] ? '' : 'font-weight-bold' ?>">
                    <?= h($n['Message']) ?>
                  </p>
                  <small class="text-muted">
                    <i class="fas fa-clock mr-1"></i><?= h($n['DateCreation']) ?>
                    <?php if (!$n['Lu']): ?>
                      <span class="badge badge-primary ml-2">Nouveau</span>
                    <?php endif; ?>
                  </small>
                </div>
              </div>
              <form action="notifications.php" method="post" class="mb-0">
                <?= csrf_field() ?>
                <input type="hidden" name="id_notif" value="<?= h($n['IdNotif']) ?>">
                <button type="submit" name="delete_notif" class="btn btn-sm btn-link text-danger" title="Supprimer">
                  <i class="fas fa-times"></i>
                </button>
              </form>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

<?php
  include __DIR__ . '/inclus/modal.php';
?>
