<?php
/**
  * @var \App\View\AppView $this
  */
?>
<?= $this->start('top_nav') ?>
    <span class="navbar-brand">新增開發商</span>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        <li><?= $this->Html->link(__('開發商列表'), ['action' => 'index']) ?></li>
<?= $this->end() ?> 

<div class="developers form columns content">
    <?= $this->Form->create($developer) ?>
    <fieldset>
        <?php
            echo $this->Form->control('user_id', ['type' => 'hidden', 'default' => $_user['id']]);
            echo $this->Form->control('name', ['label' => '名稱']);
            echo $this->Form->control('description', ['type' => 'textarea', 'label' => '描述']);
            echo $this->Form->control('state',['type' => 'radio','options' => [1 => '在售',0 => '售完'], 'default' => 1, 'label' => '狀態']);
        ?>
    </fieldset>
    <?= $this->Form->button(__('提交'),['class' => ['btn','btn-primary']]) ?>
    <?= $this->Form->end() ?>
</div>
<div class="clearfix"></div>
