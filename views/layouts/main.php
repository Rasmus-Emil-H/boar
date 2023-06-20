<?php
  use app\core\Application;
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="resources/css/bootstrap.min.css">
    <link rel="stylesheet" href="resources/css/main.css">
  </head>
  <body>
    <div class="container">
      <?php if (Application::$app->session->getFlashMessage('success')): ?>
        <div class="alert alert-success">
          <?= Application::$app->session->getFlashMessage('success'); ?>
        </div>
      <?php endif; ?>
      {{content}}
    </div>
    <script src="resources/js/jquery-3.5.1.min.slim.js"></script>
    <script src="resources/js/bootstrap.bundle.min.js"></script>
  </body>
</html>