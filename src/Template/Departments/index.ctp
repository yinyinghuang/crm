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
        <?php if (strpos($_privileges, 'a') !== false): ?>
            <li><?= $this->Html->link(__('同步'), ['action' => 'sync']) ?></li>
        <?php endif ?>
<?= $this->end() ?> 



<div class="departments index columns content">
    <h3><?= __('部门') ?></h3>
    <table cellpadding="0" cellspacing="0" class="ui table celled striped compact ">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('name','名稱') ?></th>
                <!-- <th scope="col">员工数</th>
                <th scope="col">客户数</th>
                <th scope="col">订单数</th> -->
                <th scope="col"><?= $this->Paginator->sort('created','創建時間') ?></th>
                <th scope="col"><?= $this->Paginator->sort('modified','修改時間') ?></th>
                <!-- <th scope="col" class="actions"><?= __('Actions') ?></th> -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($departments as $department): ?>
            <tr>
                <td><?= $this->Number->format($department->id) ?></td>
                <td><?= h(str_repeat('——', $department->level).$department->name) ?></td>
                <!-- <td><span class="mobile only ui grid"><i>员工数量：</i></span><?= h($department->user_count) ?></td>
                <td><span class="mobile only ui grid"><i>客户数量：</i></span><?= h($department->customer_count) ?></td>
                <td><span class="mobile only ui grid"><i>订单数量：</i></span><?= h($department->business_count) ?></td> -->
                <td><?= h($department->created) ?></td>
                <td><?= h($department->modified) ?></td>
                <!-- <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $department->id]) ?>
                    <?php if (strpos($_privileges, 'e')): ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $department->id]) ?>
                    <?php endif ?>
                    <?php if (strpos($_privileges, 'd')): ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $department->id], ['confirm' => __('Are you sure you want to delete {0}?', $department->name)]) ?>
                    <?php endif ?>
                </td> -->
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="clearfix"></div>