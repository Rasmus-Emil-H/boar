<?php 
  require_once(dirname(__DIR__, 1).'/includes/header.php');
?>
<?php if (\app\core\Application::$app->session->getFlashMessage('success')): ?>
  <div class="alert alert-success">
    <?= \app\core\Application::$app->session->getFlashMessage('success'); ?>
  </div>
<?php endif; ?>
  {{content}}
</div>
<?php require_once(dirname(__DIR__, 1).'/includes/footer.php'); ?>