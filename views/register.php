<h2>Register</h2>
<?php $form = app\core\form\Form::begin('', 'POST'); ?>
  <?= $form->field($model, 'email')->emailField(); ?>
  <?= $form->field($model, 'firstname'); ?>
  <?= $form->field($model, 'lastname'); ?>
  <?= $form->field($model, 'password')->passwordField(); ?>
  <button type="submit" class="btn btn-primary">Submit</button>
<?= app\core\form\Form::end(); ?>