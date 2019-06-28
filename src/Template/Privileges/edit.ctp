<?php
/**
  * @var \App\View\AppView $this
  */
?>
<?= $this->start('top_nav') ?>
    <span class="navbar-brand">新增权限</span>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        <li><?= $this->Html->link(__('权限列表'), ['action' => 'index']) ?></li>
<?= $this->end() ?>
<div class="privileges form columns content">
    <?= $this->Form->create($privilege) ?>
    <fieldset>
        <legend><?= __('Edit Privilege') ?></legend>
        <?php
            echo $this->Form->control('role_id', ['options' => $roles]);
            echo $this->Form->control('what');
            echo $this->Form->control('how');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
