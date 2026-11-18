<?php
/**
 * GestUniv - Composant de notifications Toast Bootstrap 4
 */
$flash = get_flash();
if ($flash):
    $type = $flash['type'] ?? 'info';
    $message = $flash['message'] ?? '';

    $badgeClass = 'bg-primary';
    $icon = 'info-circle';
    $title = 'Information';

    switch ($type) {
        case 'success':
            $badgeClass = 'bg-success text-white';
            $icon = 'check-circle';
            $title = 'Succès';
            break;
        case 'danger':
            $badgeClass = 'bg-danger text-white';
            $icon = 'exclamation-circle';
            $title = 'Erreur';
            break;
        case 'warning':
            $badgeClass = 'bg-warning text-dark';
            $icon = 'exclamation-triangle';
            $title = 'Avertissement';
            break;
        default:
            $badgeClass = 'bg-info text-white';
            $icon = 'info-circle';
            $title = 'Information';
            break;
    }
?>
<div aria-live="polite" aria-atomic="true" class="position-relative">
  <div class="toast-container position-fixed" style="top: 80px; right: 25px; z-index: 99999; min-width: 320px;">
    <div class="toast shadow-lg border-0 show" role="alert" aria-live="assertive" aria-atomic="true" data-delay="5000" data-autohide="true">
      <div class="toast-header <?= $badgeClass ?>">
        <i class="fas fa-fw fa-<?= $icon ?> mr-2"></i>
        <strong class="mr-auto font-weight-bold"><?= h($title) ?></strong>
        <small class="text-white-50">à l'instant</small>
        <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast" aria-label="Close" style="opacity: 0.9;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="toast-body bg-white text-dark font-weight-normal py-3">
        <?= h($message) ?>
      </div>
    </div>
  </div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ !== 'undefined' && $.fn.toast) {
      $('.toast').toast({ delay: 5000 }).toast('show');
    } else {
      setTimeout(function() {
        var el = document.querySelector('.toast');
        if (el) el.classList.remove('show');
      }, 5000);
    }
  });
</script>
<?php endif; ?>
