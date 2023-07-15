<?php 
  require_once(dirname(__DIR__, 1).'/includes/header.php');
  Use \app\core\Application;
?>
<?php if (Application::$app->session->getFlashMessage('success')): ?>
  <div class="alert alert-success">
    <?= Application::$app->session->getFlashMessage('success'); ?>
  </div>
<?php endif; ?>
{{content}}
<?php require_once(dirname(__DIR__, 1).'/includes/footer.php'); ?>