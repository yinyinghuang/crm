<?php
/**
  * @var \App\View\AppView $this
  */
?>

<?= $this->start('top_nav') ?>
    <span class="navbar-brand">权限列表</span>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        
<?= $this->end() ?>
<div class="privileges index columns content">
    <form action="<?= $this->Url->build(['action' => 'edit'])?>" method="post">
    <table cellpadding="0" cellspacing="0" class="ui table celled striped compact ">
        <thead>
            <tr>
                <th scope="col"></th>
                <?php foreach ($roles as $role): ?>
                <th scope="col"><?= $role?></th>
                <?php endforeach ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($modules as $module_key => $module): ?>
            <tr>
                <td><span class="mobile only ui grid"><i class="icon cubes"></i></span><?= $module['name']?></td>
                <?php foreach ($roles as $role_id => $role_name): ?>
                <td>
                    <span class="mobile only ui grid"><i class="icon user"><?= $role_name?></i></span>                    
                    <?php foreach ($module['full_op'] as $op_key): ?>
                        <label for=""><input type="checkbox" name="p[<?= $role_id?>][<?= $module_key?>][]" value="<?= $op_key?>" <?php if (isset($privileges[$role_id][$module_key]) && in_array($op_key, $privileges[$role_id][$module_key])): ?>checked<?php endif ?>><?= $operations[$op_key]?></label>
                    <?php endforeach ?>    
                
                </td>
                <?php endforeach; ?>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>
    <?= $this->Form->button(__('提交'),['class' => ['btn','btn-primary']]) ?>
    </form>
</div>
