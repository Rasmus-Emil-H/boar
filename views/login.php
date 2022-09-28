<?php
  /** @var $model app\models\User */
?>

<h2>Login</h2>
<?php $form = app\core\form\Form::begin('', 'POST'); ?>
  <?= $form->field($model, 'email')->emailField(); ?>
  <?= $form->field($model, 'password')->passwordField(); ?>
<?= app\core\form\Form::end(); ?>