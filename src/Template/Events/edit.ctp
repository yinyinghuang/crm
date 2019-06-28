<?php
/**
  * @var \App\View\AppView $this
  */
?>

<?= $this->start('top_nav') ?>
    <span class="navbar-brand">编辑活动</span>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        <li><?= $this->Html->link(__('活动列表'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('活动详情'), ['action' => 'view',$event->id]) ?></li>
        <li><?= $this->Html->link(__('新建活动'), ['action' => 'add']) ?></li>
<?= $this->end() ?>
<div class="events form columns content">
    <?= $this->Form->create($event) ?>
    <fieldset>
        <?php
            echo $this->Form->control('event_type_id', ['label' => '活动类型','options' => $eventTypes]);
            echo $this->Form->control('name',['label' => '活动名称']);
            echo $this->Form->control('content',['label' => '活动内容','type' => 'textarea']);
            echo $this->Form->control('start_time',['type' => 'text','class' => 'datetimepicker','readonly' =>true,'label' => '开始时间']);
            echo $this->Form->control('end_time',['type' => 'text','class' => 'datetimepicker','readonly' =>true,'label' => '结束时间']);
        ?>
    </fieldset>
    <?= $this->Form->button(__('提交'),['class' => ['btn','btn-primary']]) ?>
    <?= $this->Form->end() ?>
</div>
