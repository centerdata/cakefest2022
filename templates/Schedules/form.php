<?php
/**
 * Very basic Schedule upload form
 *
 * @var \App\View\AppView $this
 * @var \Cake\Datasource\EntityInterface $schedule
 */
?>

<h1><?= __('Please upload schedule'); ?></h1>

<?= $this->Form->create($schedule, ['type' => 'file']); ?>
<?= $this->Form->control('file_name', ['type' => 'file']); ?>
<?= $this->Form->submit(__('Upload')); ?>
<?= $this->Form->end(); ?>
