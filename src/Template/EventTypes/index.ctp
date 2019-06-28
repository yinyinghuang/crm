<?php
/**
  * @var \App\View\AppView $this
  */
?>
<?= $this->start('top_nav') ?>
    <span class="navbar-brand">活动类型</span>
    <i class="fa fa-search" id="show-search"></i>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        <li><?= $this->Html->link(__('新增活动类型'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('活动列表'), ['controller' => 'Events','action' => 'index']) ?></li>
<?= $this->end() ?>
<div class="eventTypes index columns content">
    <h3 class="ui segment"><?= __('活动类型') ?></h3>
    <table cellpadding="0" cellspacing="0"  class="ui table celled striped compact ">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('名称') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($eventTypes as $eventType): ?>
            <tr>
                <td><?= $this->Number->format($eventType->id) ?></td>
                <td><?= h($eventType->name) ?></td>

                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $eventType->id]) ?>
                    <?php if (strpos($_privileges, 'e') !== false): ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $eventType->id]) ?>
                    <?php endif ?>
                    <?php if (strpos($_privileges, 'd') !== false): ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $eventType->id], ['confirm' => __('Are you sure you want to delete # {0}?', $eventType->id)]) ?>
                    <?php endif ?>  
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
