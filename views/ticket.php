<?php use \app\core\form\TextareaField; ?>

<h2>Submit ticket</h2>

<?php $form = \app\core\form\Form::begin('', 'POST'); ?>
  <?= $form->field($model, 'email')->emailField(); ?>
  <?php echo new TextareaField($model, 'message'); ?>
  <button type="submit" class="btn btn-primary">Submit</button>
<?= app\core\form\Form::end(); ?>