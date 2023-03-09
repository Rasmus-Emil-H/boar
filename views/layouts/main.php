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
        <div class="logo p-2 d-flex">
          <img class="mr-2" src="/images/logo.png" height="60" width="60">
          <div class="d-flex justify-content-between w-100">
            <span class="pl-4 d-inline-block text-font-12 align-center">
              <p class="m-0 "><?= nl2br(htmlspecialchars($title)); ?></p>
            </span>
            <div class="dropdown align-center">
              <?php foreach ( Application::$app->getLanguages() as $languageSplitKey => $languageSplitArray ): ?>
                <?php if ( $languageSplitArray[0]['language'] === Application::$app->session->get('language') ): ?>
                  <span class="dropdown-toggle mb-0" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= $languageSplitArray[0]['language']; ?>
                  </span>
                <?php else: ?>
                  <?php $otherLangs[] = $languageSplitArray; ?>
                <?php endif; ?>
              <?php endforeach; ?>
              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton p-2">
                <?php foreach ( $otherLangs as $languageSplitKey => $languageSplitArray ): ?>
                  <span id="<?=$languageSplitArray[0]['languageID'];?>" class="language-changer d-flex justify-content-center pt-4 pb-4"><?= $languageSplitArray[0]['language']; ?></span>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
      {{content}}
    </div>
    <script src="resources/js/jquery-3.5.1.min.slim.js"></script>
    <script src="resources/js/bootstrap.bundle.min.js"></script>
  </body>
</html>