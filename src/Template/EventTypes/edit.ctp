<?php
/**
  * @var \App\View\AppView $this
  */
?>
<?= $this->start('top_nav') ?>
    <span class="navbar-brand">編輯活动类型</span>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        <li><?= $this->Html->link(__('活动类型列表'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('活动列表'), ['controller' => 'Events','action' => 'index']) ?></li>
<?= $this->end() ?>

<div class="eventTypes columns content ui segment">
    <?= $this->Form->create($eventType) ?>
    <fieldset>
        <legend><?= __('编辑') ?></legend>
        <?php
            echo $this->Form->control('name',['label' => '名称']);
        ?>
    </fieldset>
    <?= $this->Form->button(__('提交'),['class' => ['btn','btn-primary']]) ?>
    <?= $this->Form->end() ?>
</div>
