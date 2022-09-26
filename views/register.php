<h2>Register</h2>
<?php $form = app\core\form\Form::begin('', 'POST'); ?>
  <?php echo $form->field($model, 'email')->emailField(); ?>
  <?php echo $form->field($model, 'password')->passwordField(); ?>
  <button type="submit" class="btn btn-primary">Submit</button>
<?= app\core\form\Form::end(); ?>