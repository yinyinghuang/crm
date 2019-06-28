<?php
/**
  * @var \App\View\AppView $this
  */
?>
<?= $this->start('top_nav') ?>
    <span class="navbar-brand">編輯订单</span>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        <li><?= $this->Html->link(__('订单详情'), ['action' => 'view',$business->id]) ?></li>
        <li><?= $this->Html->link(__('订单進展'), ['controller' => 'BusinessStatuses','action' => 'index', '?' => ['business_id' =>$business->id]]) ?></li>
        <?php if (strpos($_privileges, 'd') !== false): ?>
            <li><?= $this->Form->postLink(__('刪除订单'), ['action' => 'delete', $business->id], ['confirm' => __('Are you sure you want to delete # {0}?', $business->id)]) ?> </li>
        <?php endif ?>        
        <li><?= $this->Html->link(__('订单列表'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('新增订单'), ['action' => 'add']) ?></li>
<?= $this->end() ?>
<div class="businesses form columns content">      
    <table class="ui table celled striped compact unstackable">
        <tr>
            <th>id</th>
            <td><?= $this->Html->link($business->id, ['action' => 'view',$business->id]) ?></td>
        </tr>
        <tr>
            <th>活动</th>
            <td><?= $this->Html->link($business->event->name, ['controller' => 'Events','action' => 'view',$business->event->id]) ?></td>
        </tr>
        <tr>
            <th>客户</th>
            <td><?= $this->Html->link($business->customer->name, ['controller' => 'Customers','action' => 'view',$business->customer->id]) ?></td>
        </tr>
        <tr>
            <th>业务员</th>
            <td><?= $this->Html->link($business->user->username, ['controller' => 'Users','action' => 'view',$business->user->id]) ?></td>
        </tr>
    </table>
    <?= $this->Form->create($business,['class' => 'ui segment']) ?> 
    <fieldset> 
        <?php
            echo $this->Form->control('state',['label' => '状态','options' => $stateArr,'type' => 'radio','default' => 0]);
            echo $this->Form->control('parted',['type' => 'text','class' => 'datetimepicker','label' => '参与时间','readonly' =>true,]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('提交'),['class' => ['btn','btn-primary']]) ?>
    <?= $this->Form->end() ?>
</div>

