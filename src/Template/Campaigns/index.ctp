<?php
/**
  * @var \App\View\AppView $this
  */
?>
<?= $this->start('top_nav') ?>
    <span class="navbar-brand"><a href="<?= $this->Url->build(['action' => 'index'])?>">群發推廣</a></span>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        <?php if ($clear): ?>
            <li><?= $this->Form->postLink(__('清理記錄'), ['action' => 'clear'], ['confirm' => __('確認要清楚三個月前發送記錄?')]) ?> </li>
        <?php endif ?>
        <li><?= $this->Html->link(__('群发郵件'), ['action' => 'addEmail']) ?></li>
        <li><?= $this->Html->link(__('群发短信'), ['action' => 'addSms']) ?></li>
<?= $this->end() ?>


<div class="campaigns index columns content">
     <div class="search_box ui segment">
        <form action="" role="form">
        <div class="row form-group">
            <label class="col-md-1 col-xs-4">發件人</label>
            <div class="col-md-3 col-xs-8">
                <input type="text" name="name" value="<?= h(isset($name) ? $name : '') ?>" class="form-control">
            </div>
        </div>
        <div class="row form-group">
            <label class="col-md-1 col-xs-12">發送日期</label>
            <div class="col-md-6 col-xs-12">
                <div class="input-group">
                    <input type="text" name="startTime" value="<?= h(isset($startTime) ? $startTime : '') ?>" class="form-control datetimepicker" readonly>
                    <span class="input-group-addon">至</span>
                    <input type="text" name="endTime" value="<?= h(isset($endTime) ? $endTime : '') ?>" class="form-control datetimepicker" readonly>
                </div>
            </div>            
        </div>
        <div class="row form-group">
            <label class="col-md-1 col-xs-4">類型</label>
            <div class="col-md-11 col-xs-12">
                <div class="ui form">
                <div class="inline fields">
                    <div class="ui checkbox hidden"></div>
                <?php foreach ($typeArr as $key => $value): ?>
                   <div class="ui checkbox">
                     <input type="checkbox" name="type[]" value="<?= $key?>" <?php if (isset($type) && in_array($key, $type)): ?>checked <?php endif ?>>
                     <label><?= $value?></label>
                   </div>
                <?php endforeach ?>
                </div>
                </div>
            </div>
        </div>
        <div class="row form-group">
            
            <div class="col-md-2">
                <button class="btn btn-primary">搜索</button>
            </div>
        </div>
        </form>
    </div>
    <form action="<?= $this->Url->build(['action' => 'bulk'])?>" method="post" id="bulk">
    <div class="segment">
        <?php if (strpos($_privileges, 'd') !== false): ?>
            <button type="submit" class="btn btn-danger del" name="submit" value="del">删除选中</button>
        <?php endif ?>
        
    </div>
    <table cellpadding="0" cellspacing="0" class="ui table celled striped compact ">
        <thead>
            <tr>
                <th scope="col"><input type="checkbox" class="flat" id="check-all"><label><?= __('編號') ?></label></th>
                <th scope="col"><?= $this->Paginator->sort('type','類型') ?></th>
                <th scope="col"><?= $this->Paginator->sort('user_id','發件人') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created','發送時間') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody id="main" data-controller='campaigns'>
            
        </tbody>
    </table>
    <div id="message" class="ui segment message text-center">加载中...</div>
    <div class="segment">
        <?php if (strpos($_privileges, 'd') !== false): ?>
            <button type="submit" class="btn btn-danger del" name="submit" value="del">删除选中</button>
        <?php endif ?>
    </div>
    </form>
</div>
<div class="clearfix"></div>