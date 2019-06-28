<?php
/**
  * @var \App\View\AppView $this
  */
?>
<?= $this->start('top_nav') ?>
    <span class="navbar-brand">開發商列表</span>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        <li><?= $this->Html->link(__('新增開發商'), ['action' => 'add']) ?></li>
<?= $this->end() ?> 

<div class="developers index columns content">
    <table cellpadding="0" cellspacing="0" class="ui table celled striped compact ">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('name','名稱') ?></th>
                <th scope="col"><?= __('客戶數') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($developers as $developer): ?>
            <tr>
                <td><?= $this->Number->format($developer->id) ?></td>
                <td><?= h($developer->name) ?></td>
                <td><?= h(count($developer->customers)) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $developer->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $developer->id]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
    </div>
</div>
<div class="clearfix"></div>
