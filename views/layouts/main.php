<?php if (\app\core\Application::$app->session->getFlashMessage('success')): ?>
  <div class="alert alert-success">
    <?= \app\core\Application::$app->session->getFlashMessage('success'); ?>
  </div>
<?php endif; ?>
  {{content}}
</div>