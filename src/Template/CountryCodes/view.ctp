<?php
/**
  * @var \App\View\AppView $this
  */
?>

<?= $this->start('top_nav') ?>
    <span class="navbar-brand">地区详情</span>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        <?php if (strpos($_privileges, 'e') !== false): ?>
            <li><?= $this->Html->link(__('編輯區號'), ['action' => 'edit', $countryCode->id]) ?> </li>
        <?php endif ?>
        <?php if (strpos($_privileges, 'd') !== false): ?>
            <li><?= $this->Form->postLink(__('刪除區號'), ['action' => 'delete', $countryCode->id], ['confirm' => __('Are you sure you want to delete # {0}?', $countryCode->id)]) ?> </li>
        <?php endif ?>
        <?php if (strpos($_privileges, 'v') !== false): ?>
            <li><?= $this->Html->link(__('區號列表'), ['action' => 'index']) ?> </li>
        <?php endif ?>
        <?php if (strpos($_privileges, 'a') !== false): ?>
            <li><?= $this->Html->link(__('新增區號'), ['action' => 'add']) ?> </li>
        <?php endif ?>
<?= $this->end() ?>
<div class="countryCodes view columns content ui segment">
    <h3 class="ui segment"><?= h($countryCode->id) ?></h3>
    <table class="ui table celled striped compact unstackable">
        <tr>
            <th scope="row"><?= __('國家/地區') ?></th>
            <td><?= h($countryCode->country) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($countryCode->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('區號') ?></th>
            <td><?= $this->Number->format($countryCode->country_code) ?></td>
        </tr>
    </table>
</div>
<div class="clearfix"></div>