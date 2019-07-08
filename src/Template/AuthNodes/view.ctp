<?php
/**
  * @var \App\View\AppView $this
  */
?>
<nav class="large-2 medium-3 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Auth Node'), ['action' => 'edit', $authNode->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Auth Node'), ['action' => 'delete', $authNode->id], ['confirm' => __('Are you sure you want to delete # {0}?', $authNode->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Auth Nodes'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Auth Node'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Parent Auth Nodes'), ['controller' => 'AuthNodes', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Parent Auth Node'), ['controller' => 'AuthNodes', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="authNodes view large-9 medium-8 columns content">
    <h3><?= h($authNode->title) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Title') ?></th>
            <td><?= h($authNode->title) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Routing Address') ?></th>
            <td><?= h($authNode->routing_address) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Routing Param') ?></th>
            <td><?= h($authNode->routing_param) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Mark') ?></th>
            <td><?= h($authNode->mark) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Nav Icon') ?></th>
            <td><?= h($authNode->nav_icon) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Parent Auth Node') ?></th>
            <td><?= $authNode->has('parent_auth_node') ? $this->Html->link($authNode->parent_auth_node->title, ['controller' => 'AuthNodes', 'action' => 'view', $authNode->parent_auth_node->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($authNode->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Routing Method') ?></th>
            <td><?= $this->Number->format($authNode->routing_method) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('State') ?></th>
            <td><?= $this->Number->format($authNode->state) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Lft') ?></th>
            <td><?= $this->Number->format($authNode->lft) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Rght') ?></th>
            <td><?= $this->Number->format($authNode->rght) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Is Nav') ?></th>
            <td><?= $authNode->is_nav ? __('Yes') : __('No'); ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Auth Nodes') ?></h4>
        <?php if (!empty($authNode->child_auth_nodes)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th scope="col"><?= __('Id') ?></th>
                <th scope="col"><?= __('Title') ?></th>
                <th scope="col"><?= __('Routing Address') ?></th>
                <th scope="col"><?= __('Routing Param') ?></th>
                <th scope="col"><?= __('Routing Method') ?></th>
                <th scope="col"><?= __('Mark') ?></th>
                <th scope="col"><?= __('Is Nav') ?></th>
                <th scope="col"><?= __('Nav Icon') ?></th>
                <th scope="col"><?= __('State') ?></th>
                <th scope="col"><?= __('Parent Id') ?></th>
                <th scope="col"><?= __('Lft') ?></th>
                <th scope="col"><?= __('Rght') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($authNode->child_auth_nodes as $childAuthNodes): ?>
            <tr>
                <td><?= h($childAuthNodes->id) ?></td>
                <td><?= h($childAuthNodes->title) ?></td>
                <td><?= h($childAuthNodes->routing_address) ?></td>
                <td><?= h($childAuthNodes->routing_param) ?></td>
                <td><?= h($childAuthNodes->routing_method) ?></td>
                <td><?= h($childAuthNodes->mark) ?></td>
                <td><?= h($childAuthNodes->is_nav) ?></td>
                <td><?= h($childAuthNodes->nav_icon) ?></td>
                <td><?= h($childAuthNodes->state) ?></td>
                <td><?= h($childAuthNodes->parent_id) ?></td>
                <td><?= h($childAuthNodes->lft) ?></td>
                <td><?= h($childAuthNodes->rght) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'AuthNodes', 'action' => 'view', $childAuthNodes->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['controller' => 'AuthNodes', 'action' => 'edit', $childAuthNodes->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'AuthNodes', 'action' => 'delete', $childAuthNodes->id], ['confirm' => __('Are you sure you want to delete # {0}?', $childAuthNodes->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
</div>
