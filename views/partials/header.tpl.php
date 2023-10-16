<!DOCTYPE html>
<html lang="en">
  <head>
    <title><?= app()->config->get('appName'); ?></title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Language" content="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta content="width=device-width, initial-scale=1, user-scalable=1, minimum-scale=1, maximum-scale=5" name="viewport" />
    <meta name="description" content="boar application">
    <meta name="msapplication-tap-highlight" content="no">
    <link rel="stylesheet" href="/resources/css/bootstrap.min.css">
    <link rel="stylesheet" href="/resources/css/main.css">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <meta name="theme-color" content="#03045e"/>
  </head>
<body>

<?php if(!app()::isGuest()): ?>
  <?php require_once $navbar; ?>
<?php endif; ?>