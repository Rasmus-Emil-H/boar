<!DOCTYPE html>
<html lang="en">
  <head>
    <title><?= $appName; ?></title>
    <?php \app\core\src\miscellaneous\CoreFunctions::loopAndEcho($metaTags); ?> 
    <?php \app\core\src\miscellaneous\CoreFunctions::loopAndEcho($stylesheets); ?>
    <link rel="icon" type="image/x-icon" href="<?= hs($icon); ?>">
  </head>
<body data-bs-theme="dark">
  
<?php if(!app()::isGuest()): ?>
  <?php require_once $navbar; ?>
<?php endif; ?>