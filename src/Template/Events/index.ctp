<?php
/**
  * @var \App\View\AppView $this
  */
?>

<?= $this->start('top_nav') ?>
    <span class="navbar-brand"><a href="<?= $this->Url->build(['action' => 'index'])?>">活动列表</a></span>
    <i class="fa fa-search" id="show-search"></i>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        <?php if (strpos($_privileges, 'a') !== false): ?>
        <li><?= $this->Html->link(__('新增活动'), ['action' => 'add']) ?></li>
        <?php endif ?>
        
<?= $this->end() ?>
<div class="events index columns content">
    <div class="search_box  ui segment" style="display:none">
        <form action="<?= $this->Url->build(['action' => 'index'])?>" role="form">
        <div class="row form-group">
            <label class="col-md-1 col-xs-4">活动名稱</label>
            <div class="col-md-3 col-xs-8">
                <input type="text" name="name" value="<?= h(isset($name) ? $name : '') ?>" class="form-control">
            </div>
            <label class="col-md-1 col-xs-4">活动类型</label>
            <div class="col-md-3 col-xs-8">
                <select name="event_type_id">
                    <option value="">请选择</option>
                    <?php foreach ($eventTypes as $key => $value): ?>
                    <option value="<?= $key?>" <?php if ( isset($event_type_id) && $key == $event_type_id): ?>selected<?php endif ?>><?= $value?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
        <div class="row form-group">
            <label class="col-md-1 col-xs-12">活动日期</label>
            <div class="col-md-6 col-xs-12">
                <div class="input-group">
                    <input type="text" name="startTime" value="<?= h(isset($startTime) ? $startTime : '') ?>" class="form-control datetimepicker" readonly>
                    <span class="input-group-addon">至</span>
                    <input type="text" name="endTime" value="<?= h(isset($endTime) ? $endTime : '') ?>" class="form-control datetimepicker" readonly>
                </div>
            </div>           
        </div>
        <div class="row form-group">
            <label class="col-md-1 col-xs-4">排序</label>
            <div class="col-md-2 col-xs-8">
                <select name="order">
                    <option value="">请选择</option>
                    <?php foreach ($orderOptions as $key => $value): ?>
                    <option value="<?= $key?>" <?php if ( isset($order) && $key == $order): ?>selected<?php endif ?>><?= $value?></option>
                    <?php endforeach ?>
                </select>
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
    <table cellpadding="0" cellspacing="0"  class="ui table celled striped compact ">
        <thead>
            <tr>
                <th scope="col"><input type="checkbox" class="flat" id="check-all"><label><?= $this->Paginator->sort('id') ?></label></th>
                <th scope="col"><?= $this->Paginator->sort('event_type_id',['类型']) ?></th>
                <th scope="col"><?= $this->Paginator->sort('name',['名称']) ?></th>
                <th scope="col">总数</th>
                <th scope="col">进行中</th>
                <th scope="col">成交</th>
                <th scope="col">失败</th>
                <th scope="col"><?= $this->Paginator->sort('modified',['创建时间']) ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody id="main" data-controller='events'>
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
