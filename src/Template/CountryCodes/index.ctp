<?php
/**
  * @var \App\View\AppView $this
  */
?>
<?= $this->start('top_nav') ?>
    <span class="navbar-brand">區號列表</span>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        <?php if (strpos($_privileges, 'a') !== false): ?>
            <li><?= $this->Html->link(__('新增區號'), ['action' => 'add']) ?></li>
        <?php endif ?>    
<?= $this->end() ?>
<div class="countryCodes index columns content">
    <table cellpadding="0" cellspacing="0"  class="ui table celled striped compact ">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('country','國家/地區') ?></th>
                <th scope="col"><?= $this->Paginator->sort('country_code','區號') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($countryCodes as $countryCode): ?>
            <tr>
                <td><?= $this->Number->format($countryCode->id) ?></td>
                <td><?= h($countryCode->country) ?></td>
                <td><?= $this->Number->format($countryCode->country_code) ?></td>
                <td class="actions">
                <?php if (strpos($_privileges, 'v') !== false): ?>
                    <?= $this->Html->link(__('View'), ['action' => 'view', $countryCode->id]) ?>
                <?php endif ?>
                <?php if (strpos($_privileges, 'e') !== false): ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $countryCode->id]) ?>
                <?php endif ?>
                <?php if (strpos($_privileges, 'd') !== false): ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $countryCode->id], ['confirm' => __('Are you sure you want to delete # {0}?', $countryCode->id)]) ?>
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
<div class="clearfix"></div>