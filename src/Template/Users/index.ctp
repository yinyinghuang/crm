<?php
/**
  * @var \App\View\AppView $this
  */
?>
<?= $this->start('top_nav') ?>
    <span class="navbar-brand"><a href="<?= $this->Url->build(['action' => 'index'])?>">員工列表</a></span>
    <i class="fa fa-search" id="show-search"></i>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        <?php if (strpos($_privileges, 'a') !== false): ?>
            <li><?= $this->Html->link(__('同步'), ['action' => 'sync']) ?></li>
        <?php endif ?>
<?= $this->end() ?>

<div class="users index columns content">
    <div class="search_box  ui segment" style="display:none">
        <form action="<?= $this->Url->build(['action' => 'index'])?>" role="form">
        <div class="row form-group">
            <label class="col-md-1 col-xs-4">业务员</label>
            <div class="col-md-2 col-xs-8">
                <input type="text" name="username" value="<?= h(isset($username) ? $username : '') ?>" class="form-control">
            </div>
            <label class="col-md-1 col-xs-4">分组</label>
            <div class="col-md-2 col-xs-8">
                <select name="department_id">
                    <option value="">请选择</option>
                    <?php foreach ($departments as $key => $value): ?>
                    <option value="<?= $key?>" <?php if ( isset($department_id) && $key == $department_id): ?>selected<?php endif ?>><?= $value?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <label class="col-md-1 col-xs-4">電話</label>
            <div class="col-md-2 col-xs-8">
                <input type="text" name="mobile" value="<?= h(isset($mobile) ? $mobile : '') ?>" class="form-control">
            </div>
            <label class="col-md-1 col-xs-4">性别</label>
            <div class="col-md-2 col-xs-8">
                <select name="gender">
                    <option value="">请选择</option>
                    <option value="0" <?php if ( isset($gender) && 0 === $gender): ?>selected<?php endif ?>>男</option>
                    <option value="1" <?php if ( isset($gender) && 1 === $gender): ?>selected<?php endif ?>>女</option>
                </select>
            </div>

        </div>
        <div class="row form-group">
            <label class="col-md-1 col-xs-4 col-md-offset-1">状态</label>
            <div class="col-md-5 col-xs-8">
                <div class="ui form">
                <div class="inline fields">
                    <div class="ui checkbox hidden"></div>
                <?php foreach ($userStateArr as $key => $value): ?>
                   <div class="ui checkbox">
                     <input type="checkbox" name="state[]" value="<?= $key?>" <?php if (isset($state) && in_array($key, $state)): ?>checked <?php endif ?>>
                     <label><?= $value?></label>
                   </div>
                <?php endforeach ?>
                </div>
                </div>
            </div>
        </div>
        <div class="row form-group">            
            <div class="col-md-12">
                <button class="btn btn-primary" name="submit" value="search">搜索</button>
                <?php if (strpos($_privileges, 'o') !== false): ?>
                <button class="btn btn-primary" name="submit" value="export">导出</button>    
                <?php endif ?>                
            </div>
        </div>
        </form>
    </div>

    <form action="<?= $this->Url->build(['action' => 'bulk'])?>" method="post">
    <div class="segment">
        <?php if (strpos($_privileges, 'd') !== false): ?>
            <button type="submit" class="btn btn-danger" name="submit" value="del">删除选中</button>
        <?php endif ?>
        <?php if (strpos($_privileges, 'o') !== false): ?>
            <button type="submit" class="btn btn-primary" name="submit" value="export">导出选中</button>
        <?php endif ?>        
    </div>
    <table cellpadding="0" cellspacing="0" class="ui table celled striped compact checkbox_container">
        <thead>
            <tr>
                <th scope="col"><input type="checkbox" class="flat" id="check-all"><label><?= $this->Paginator->sort('id') ?></label></th>
                <th scope="col"><?= $this->Paginator->sort('username','姓名') ?></th>
                <th scope="col"><?= $this->Paginator->sort('department_id','分組') ?></th>
                <th scope="col"><?= $this->Paginator->sort('role_id','職位') ?></th>
                <th scope="col" ><?= $this->Paginator->sort('mobile','電話') ?></th>
                <th scope="col"><?= $this->Paginator->sort('last_op','上次更新') ?></th>
                <th scope="col" ><?= $this->Paginator->sort('state','状态') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody id="main" data-controller='users'>
            
        </tbody>
    </table>
    <div id="message" class="ui segment message text-center">加载中...</div>
    <div class="segment">
        <?php if (strpos($_privileges, 'd') !== false): ?>
            <button type="submit" class="btn btn-danger" name="submit" value="del">删除选中</button>
        <?php endif ?>
        <?php if (strpos($_privileges, 'o') !== false): ?>
            <button type="submit" class="btn btn-primary" name="submit" value="export">导出选中</button>
        <?php endif ?>
    </div>
    </form>
</div>
<div class="clearfix"></div>
