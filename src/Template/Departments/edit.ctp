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
                    __('删除部门'),
                    ['action' => 'delete', $department->id],
                    ['confirm' => __('Are you sure you want to delete {0}?', $department->name)]
                )
            ?></li>
        <?php endif ?>        
        <li><?= $this->Html->link(__('部门详情'), ['action' => 'view',$department->id]) ?></li>
        <li><?= $this->Html->link(__('部门列表'), ['action' => 'index']) ?></li>
<?= $this->end() ?> 

<div class="departments columns content ui segment">
    <?= $this->Form->create($department) ?>
    <fieldset>
        <legend><?= __('編輯部门') ?></legend>
        <?php
            echo $this->Form->control('name', ['label' => '名稱']);
        ?>
    </fieldset>
    <?= $this->Form->button(__('提交'),['class' => ['btn','btn-primary']]) ?>
    <?= $this->Form->end() ?>
</div>
<div class="clearfix"></div>