<?php
/**
  * @var \App\View\AppView $this
  */
?>
<?= $this->start('top_nav') ?>
    <span class="navbar-brand">開發商详情</span>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        <li><?= $this->Html->link(__('編輯開發商'), ['action' => 'edit', $developer->id]) ?> </li>
        <li><?= $this->Html->link(__('開發商列表'), ['action' => 'index']) ?> </li>
<?= $this->end() ?> 

<div class="developers view columns content">
    <h3><?= h($developer->name) ?></h3>
    <table class="ui table celled striped compact ">
        <tr>
            <th scope="row"><?= __('名稱') ?></th>
            <td><?= h($developer->name) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('描述') ?></th>
            <td><?= h($developer->description) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($developer->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('創建時間') ?></th>
            <td><?= h($developer->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('更新時間') ?></th>
            <td><?= h($developer->modified) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('狀態') ?></th>
            <td><?= $developer->state ? __('在售') : __('售完'); ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('客戶數') ?></th>
            <td><?= count($developer->customers) ?></td>
        </tr>
    </table>
    <div class="related">
        <?php if (!empty($developer->customers)): ?>
        <div class="x_panel">
            <div class="x_title">
                <h4><?= __('相關客戶') ?></h4>
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
                        <th scope="col"><?= __('電話') ?></th>
                        <th scope="col"><?= __('電郵') ?></th>
                        <th scope="col"><?= __('地址') ?></th>
                        <th scope="col"><?= __('業務員') ?></th>
                        <th scope="col"><?= __('來源') ?></th>
                        <th scope="col"><?= __('狀態') ?></th>
                        <th scope="col" class="actions"><?= __('Actions') ?></th>
                    </tr>
                    <?php foreach ($developer->customers as $customers): ?>
                    <tr>
                        <td><?= h($customers->id) ?></td>
                        <td><?= h($customers->name) ?></td>
                        <td><?= h($customers->mobile) ?></td>
                        <td><?= h($customers->email) ?></td>
                        <td><?= h($customers->address) ?></td>
                        <td><?= $customers->has('user') ? $this->Html->link($customers->user->username, ['controller' => 'Users', 'action' => 'view', $customers->user->id]) : '' ?></td>
                        <td><?= h($customers->source) ?></td>
                        <td><?= h($stateArr[$customers->state]) ?></td>
                        <td class="actions">
                            <?= $this->Html->link(__('View'), ['controller' => 'Customers', 'action' => 'view', $customers->id]) ?>
                            <?= $this->Html->link(__('Edit'), ['controller' => 'Customers', 'action' => 'edit', $customers->id]) ?>
                            <?php if (strpos($_privileges, 'd') !== false): ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Customers', 'action' => 'delete', $customers->id], ['confirm' => __('Are you sure you want to delete # {0}?', $customers->id)]) ?>
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
