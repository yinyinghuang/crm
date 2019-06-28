<?php
/**
  * @var \App\View\AppView $this
  */
?>
<?= $this->start('top_nav') ?>
    <span class="navbar-brand">編輯进展</span>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        <li><?= $this->Html->link(__('客户详情'), ['controller' => 'Customers','action' => 'view',$customer->id]) ?></li>
        <li><?= $this->Html->link(__('进展列表'), ['action' => 'index','?' =>['customer_id' => $customer->id]]) ?></li>
        <li><?= $this->Html->link(__('新增进展'), ['action' => 'add','?' =>['customer_id' => $customer->id]]) ?></li>
<?= $this->end() ?>

<div class="businessStatuses form columns content">
    <table class="ui table celled striped compact  striped compact unstackable">
        <tr>
            <th>客户</th>
            <td><?= $this->Html->link($customer->name, ['controller' => 'Customers','action' => 'view',$customer->id]) ?></td>
        </tr>
        <tr>
            <th>状态</th>
            <td class="<?= $stateColorArr[$customer->state]?>"><?= $stateArr[$customer->state]?></td>
        </tr>
    </table>

    <?= $this->Form->create($businessStatus,['class' => 'ui segment']) ?>
    <fieldset>
        <?php        
            echo $this->Form->control('status',['label' => '進展','type' => 'textarea','required' => true]);
            echo $this->Form->control('next_contact_time',['label' => '下次聯絡時間','class' => 'timepicker','readonly' => true,'type' => 'text']);
            echo $this->Form->control('next_note',['label' => '下次聯絡備註','type' => 'textarea']);
            echo $this->Form->control('state',['type' => 'radio','options' => $stateArr,'label' => '狀態','default' => $customer->state]); 
        ?>
    </fieldset>
    <?= $this->Form->button(__('提交'),['class' => ['btn','btn-primary']]) ?>
    <?= $this->Form->end() ?>
</div>
