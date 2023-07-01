<?php 
  require_once('../views/includes/header.php');
  Use \app\core\Application;
?>
<div>
  <?php if (Application::$app->session->getFlashMessage('success')): ?>
    <div class="alert alert-success">
      <?= Application::$app->session->getFlashMessage('success'); ?>
    </div>
  <?php endif; ?>
  {{content}}
</div>
<?php require_once('../views/includes/footer.php'); ?>