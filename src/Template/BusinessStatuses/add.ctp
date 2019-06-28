<?php
/**
  * @var \App\View\AppView $this
  */
?>

<?= $this->start('top_nav') ?>
    <span class="navbar-brand">新增进展</span>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        <li><?= $this->Html->link(__('订单详情'), ['controller' => 'Businesses','action' => 'view',$business->id]) ?></li>
        <li><?= $this->Html->link(__('进展列表'), ['action' => 'index','?' =>['business_id' => $business->id]]) ?></li>
<?= $this->end() ?>

<div class="businessStatuses form columns content">
    <table class="ui table celled striped compact  striped compact unstackable">
        <tr>
            <th>id</th>
            <td><?= $this->Html->link($business['id'], ['controller' => 'Businesses','action' => 'view',$business['id']]) ?>
            </td>
        </tr>
        <tr>
            <th>活动</th>
            <td><?= $this->Html->link($business['event']['name'], ['controller' => 'Events','action' => 'view',$business['event']['id']]) ?></td>
        </tr>
        <tr>
            <th>客户</th>
            <td><?= $this->Html->link($business['customer']['name'], ['controller' => 'Customers','action' => 'view',$business['customer']['id']]) ?></td>
        </tr>
        <tr>
            <th>业务员</th>
            <td><?= $this->Html->link($business['user']['username'], ['controller' => 'Users','action' => 'view',$business['user']['id']]) ?></td>
        </tr>
        <tr>
            <th>状态</th>
            <td class="<?= $stateColorArr[$business['state']]?>"><?= $stateArr[$business['state']]?></td>
        </tr>
    </table>
    <?= $this->Form->create($businessStatus,['class' => 'ui segment']) ?>
    <fieldset>
        <?php            
            echo $this->Form->control('status',['label' => '进展','type' => 'textarea','required' => true]);
            echo $this->Form->control('next_contact_time',['label' => '下次联系时间','class' => 'timepicker','readonly' => true,'type' => 'text']);
            echo $this->Form->control('next_note',['label' => '下次联系备注','type' => 'textarea']);
            echo $this->Form->control('state',['type' => 'radio','options' => $stateArr,'label' => '状态','default' => $business->state]); 
        ?>
    </fieldset>
    <?= $this->Form->button(__('提交'),['class' => ['btn','btn-primary']]) ?>
    <?= $this->Form->end() ?>
</div>
