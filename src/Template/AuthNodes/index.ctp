<?php
/**
  * @var \App\View\AppView $this
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Auth Node'), ['action' => 'add']) ?></li>
    </ul>
</nav>
<div class="authNodes index large-9 medium-8 columns content">
    <h3><?= __('Auth Nodes') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('title') ?></th>
                <th scope="col"><?= $this->Paginator->sort('routing_address') ?></th>
                <th scope="col"><?= $this->Paginator->sort('routing_param') ?></th>
                <th scope="col"><?= $this->Paginator->sort('routing_method') ?></th>
                <th scope="col"><?= $this->Paginator->sort('mark') ?></th>
                <th scope="col"><?= $this->Paginator->sort('is_nav') ?></th>
                <th scope="col"><?= $this->Paginator->sort('nav_icon') ?></th>
                <th scope="col"><?= $this->Paginator->sort('state') ?></th>
                <th scope="col"><?= $this->Paginator->sort('parent_id') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($authNodes as $authNode): ?>
            <tr>
                <td><?= $this->Number->format($authNode->id) ?></td>
                <td><?= h($authNode->title) ?></td>
                <td><?= h($authNode->routing_address) ?></td>
                <td><?= h($authNode->routing_param) ?></td>
                <td><?= $this->Number->format($authNode->routing_method) ?></td>
                <td><?= h($authNode->mark) ?></td>
                <td><?= h($authNode->is_nav) ?></td>
                <td><?= h($authNode->nav_icon) ?></td>
                <td><?= $this->Number->format($authNode->state) ?></td>
                <td><?= $authNode->has('parent_auth_node') ? $this->Html->link($authNode->parent_auth_node->title, ['controller' => 'AuthNodes', 'action' => 'view', $authNode->parent_auth_node->id]) : '' ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $authNode->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $authNode->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $authNode->id], ['confirm' => __('Are you sure you want to delete # {0}?', $authNode->id)]) ?>
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
