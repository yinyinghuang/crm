<?php
/**
  * @var \App\View\AppView $this
  */
?>
<?= $this->start('top_nav') ?>
    <span class="navbar-brand">部门列表</span>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        <?php if (strpos($_privileges, 'e') !== false): ?>
            <li><?= $this->Html->link(__('編輯部门'), ['action' => 'edit', $department->id]) ?> </li>        
        <?php endif ?> 
        <?php if (strpos($_privileges, 'd') !== false): ?>
            <li><?= $this->Form->postLink(__('刪除部门'), ['action' => 'delete', $department->id], ['confirm' => __('Are you sure you want to delete  {0}?', $department->name)]) ?> </li>
        <?php endif ?>
        <li><?= $this->Html->link(__('部门列表'), ['action' => 'index']) ?> </li>
        <?php if (strpos($_privileges, 'a') !== false): ?>
            <li><?= $this->Html->link(__('新增部门'), ['action' => 'add']) ?> </li>
        <?php endif ?>        
<?= $this->end() ?> 


<div class="departments view columns content">
    <h3><?= h($department->name) ?></h3>
    <table class="ui table celled striped compact ">
        <tr>
            <th scope="row"><?= __('名稱') ?></th>
            <td><?= h($department->name) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($department->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('员工数量') ?></th>
            <td><?= $this->Number->format($department->user_count) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('客户数量') ?></th>
            <td><?= $this->Number->format($department->customer_count) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('订单数量') ?></th>
            <td><?= $this->Number->format($department->business_count) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('創建時間') ?></th>
            <td><?= h($department->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('修改時間') ?></th>
            <td><?= h($department->modified) ?></td>
        </tr>
    </table>
    <div class="related">
        <?php if (!empty($department->users)): ?>
        <div class="x_panel">
            <div class="x_title">
               <h4><?= __('部门員工') ?></h4>
                <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <table cellpadding="0" cellspacing="0" class="ui table celled striped compact ">
                    <tr>
                        <th scope="col"><?= __('Id') ?></th>
                        <th scope="col"><?= __('姓名') ?></th>
                        <th scope="col"><?= __('職位') ?></th>
                        <th scope="col">客户数</th>
                        <th scope="col">订单数</th>
                        <th scope="col"><?= __('電話') ?></th>
                        <th scope="col" class="actions"><?= __('Actions') ?></th>
                    </tr>
                    <?php foreach ($department->users as $users): ?>
                    <tr>
                        <td><?= h($users->id) ?></td>
                        <td><?= h($users->username) ?></td>
                        <td><?= h($users->role->name) ?></td>
                        <td><span class="mobile only ui grid"><i>客户数量：</i></span><?= h($users->customer_count) ?></td>
                        <td><span class="mobile only ui grid"><i>订单数量：</i></span><?= h($users->business_count) ?></td>
                        <td><?= h($users->mobile) ?></td>
                        <td class="actions">
                            <?= $this->Html->link(__('View'), ['controller' => 'Users', 'action' => 'view', $users->id]) ?>
                            <?= $this->Html->link(__('Edit'), ['controller' => 'Users', 'action' => 'edit', $users->id]) ?>
                            <?php if (strpos($_privileges, 'd' !== false)): ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Users', 'action' => 'delete', $users->id], ['confirm' => __('Are you sure you want to delete {0}?', $users->username)]) ?>
                            <?php endif ?>
                            
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<div class="clearfix"></div>