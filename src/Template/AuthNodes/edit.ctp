<?php
/**
  * @var \App\View\AppView $this
  */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $authNode->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $authNode->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Auth Nodes'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Parent Auth Nodes'), ['controller' => 'AuthNodes', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Parent Auth Node'), ['controller' => 'AuthNodes', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="authNodes form large-9 medium-8 columns content">
    <?= $this->Form->create($authNode) ?>
    <fieldset>
        <legend><?= __('Edit Auth Node') ?></legend>
        <?php
            echo $this->Form->control('title');
            echo $this->Form->control('routing_address');
            echo $this->Form->control('routing_param');
            echo $this->Form->control('routing_method');
            echo $this->Form->control('mark');
            echo $this->Form->control('is_nav');
            echo $this->Form->control('nav_icon');
            echo $this->Form->control('state');
            echo $this->Form->control('parent_id', ['options' => $parentAuthNodes]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
