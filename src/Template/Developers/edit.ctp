<?php
/**
  * @var \App\View\AppView $this
  */
?>
<?= $this->start('top_nav') ?>
    <span class="navbar-brand">編輯部门</span>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        <?php if (strpos($_privileges, 'd') !== false): ?>
            <li><?= $this->Form->postLink(
                    __('删除開發商'),
                    ['action' => 'delete', $developer->id],
                    ['confirm' => __('Are you sure you want to delete {0}?', $developer->name)]
                )
            ?></li>
        <?php endif ?>        
        <li><?= $this->Html->link(__('開發商详情'), ['action' => 'view',$developer->id]) ?></li>
        <li><?= $this->Html->link(__('開發商列表'), ['action' => 'index']) ?></li>
<?= $this->end() ?> 

<div class="developers columns content">
    <?= $this->Form->create($developer) ?>
    <fieldset>
        <legend><?= __('編輯開發商') ?></legend>
        <?php
            echo $this->Form->control('user_id', ['type' => 'hidden', 'default' => $_user['id']]);
            echo $this->Form->control('name', ['label' => '名稱']);
            echo $this->Form->control('description', ['label' => '描述']);
            echo $this->Form->control('state',['type' => 'radio','options' => [1 => '在售',0 => '售完'] ,'label' => '狀態']);
        ?>
    </fieldset>
    <?= $this->Form->button(__('提交'),['class' => ['btn','btn-primary']]) ?>
    <?= $this->Form->end() ?>
</div>
<div class="clearfix"></div>