<?php
/**
  * @var \App\View\AppView $this
  */
?>

<?= $this->start('top_nav') ?>
    <span class="navbar-brand">新增权限</span>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        <li><?= $this->Html->link(__('权限列表'), ['action' => 'index']) ?></li>
<?= $this->end() ?> 
<div class="privileges view columns content">
    <h3><?= h($privilege->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Role') ?></th>
            <td><?= $privilege->has('role') ? $this->Html->link($privilege->role->name, ['controller' => 'Roles', 'action' => 'view', $privilege->role->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('What') ?></th>
            <td><?= h($privilege->what) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('How') ?></th>
            <td><?= h($privilege->how) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($privilege->id) ?></td>
        </tr>
    </table>
</div>
