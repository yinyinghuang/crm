<?php
/**
  * @var \App\View\AppView $this
  */
?>
<?= $this->start('top_nav') ?>
    <span class="navbar-brand">编辑員工</span>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        <?php if (strpos($_privileges, 'd') !== false): ?>
            <li><?= $this->Form->postLink(
                    __('Delete'),
                    ['action' => 'delete', $user->id],
                    ['confirm' => __('Are you sure you want to delete  {0}?', $user->username)]
                )
            ?></li>
        <?php endif ?>
        <li><?= $this->Html->link(__('員工详情'), ['action' => 'view',$user->id]) ?></li>
        <li><?= $this->Html->link(__('員工列表'), ['action' => 'index']) ?></li>
        <?php if (strpos($_privileges, 'i')): ?>            
        <li><?= $this->Html->link(__('导入客戶'), ['action' => 'import']) ?></li>
        <?php endif ?>
        <?php if (strpos($_privileges, 'o')): ?>            
        <li><?= $this->Html->link(__('导出客戶'), ['action' => 'export']) ?></li>
        <?php endif ?>
<?= $this->end() ?>

<div class="users columns content ui segment">
    <?= $this->Form->create($user) ?>
    <fieldset>
        <legend><?= __('編輯員工信息') ?></legend>
        <?php
            echo $this->Form->control('username', ['label' => '姓名']);
            echo $this->Form->control('password', ['label' => '密碼']);  
            echo $this->Form->control('weixinid', ['label' => '微信id']); 
            echo $this->Form->control('department_id', ['options' => $departments, 'empty' => '請選擇','label' => '分組']);
            echo $this->Form->control('role_id', ['options' => $roles, 'empty' => '請選擇','label' => '職位']);
            echo $this->Form->control('gender', ['options' => [0 => '男',1 => '女'], 'empty' => '請選擇','label' => '性別']);
            echo $this->Form->control('country_code_id',['type' => 'radio','options' => $countrycodes,'label' => '地區','required' => true,'value' => $user->country_code_id]);
            echo $this->Form->control('mobile', ['label' => '電話','type' => 'tel','required' => true]);
            echo $this->Form->control('state',['type' => 'radio','options' => [1 => '在職',0 => '離職'],'label' => '']);
        ?>
    </fieldset>
    <?= $this->Form->button(__('提交'),['class' => ['btn','btn-primary']]) ?>
    <?= $this->Form->end() ?>
</div>
<div class="clearfix"></div>
