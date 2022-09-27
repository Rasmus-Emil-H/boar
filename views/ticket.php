<h2>Submit ticket</h2>

<?php $form = \app\core\form\Form::begin('', 'POST'); ?>
  <?= $form->field($model, 'email')->emailField(); ?>
  <?= $form->field($model, 'message'); ?>
  <button type="submit" class="btn btn-primary">Submit</button>
<?= app\core\form\Form::end(); ?>