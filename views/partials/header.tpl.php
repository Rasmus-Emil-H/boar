<!DOCTYPE html>
<html lang="en">
  <head>
    <title><?= $appName; ?></title>
    <?php loopAndEcho($metaTags); ?> 
    <?php loopAndEcho($stylesheets); ?>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
  </head>
<body>
  
<?php if(!app()::isGuest()): ?>
  <?php require_once $navbar; ?>
<?php endif; ?>