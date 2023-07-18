<?php use \app\core\form\TextareaField; ?>

<h2>Submit ticket</h2>

<?php $form = \app\core\form\Form::begin('', 'POST'); ?>
  <?= $form->field($model, 'email')->emailField(); ?>
  <?= new TextareaField($model, 'message'); ?>
<?= app\core\form\Form::end(); ?>