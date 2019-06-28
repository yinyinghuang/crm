<?php
/**
  * @var \App\View\AppView $this
  */
?>
<?= $this->start('top_nav') ?>
    <span class="navbar-brand">新增部门</span>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        <li><?= $this->Html->link(__('部门列表'), ['action' => 'index']) ?></li>
<?= $this->end() ?> 
<div class="departments columns content ui segment">
    <?= $this->Form->create($department) ?>
    <fieldset>
        <legend><?= __('新增部门') ?></legend>
        <?php
            echo $this->Form->control('name', ['label' => '名稱']);
        ?>
    </fieldset>
    <?= $this->Form->button(__('提交'),['class' => ['btn','btn-primary']]) ?>
    <?= $this->Form->end() ?>
</div>
<div class="clearfix"></div>
