<?php
/**
  * @var \App\View\AppView $this
  */
?>

<?= $this->start('top_nav') ?>
    <span class="navbar-brand"><a href="<?= $this->Url->build(['action' => 'index'])?>">订单列表</a></span>
    <i class="fa fa-search" id="show-search"></i>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        <li><?= $this->Html->link(__('新增订单'), ['action' => 'add']) ?></li>
<?= $this->end() ?>

<div class="businesses index columns content">
    <div class="search_box  ui segment" style="display:none">
        <form action="<?= $this->Url->build(['action' => 'index'])?>" role="form">
        <div class="row form-group">
            <label class="col-md-1 col-xs-4">客户名稱</label>
            <div class="col-md-3 col-xs-8">
                <input type="text" name="customer" value="<?= h(isset($customer) ? $customer : '') ?>" class="form-control">
            </div>
            <label class="col-md-1 col-xs-4">活动名稱</label>
            <div class="col-md-3 col-xs-8">
                <input type="text" name="event" value="<?= h(isset($event) ? $event : '') ?>" class="form-control">
            </div>
            <label class="col-md-1 col-xs-4">业务员</label>
            <div class="col-md-3 col-xs-8">
                <input type="text" name="username" value="<?= h(isset($username) ? $username : '') ?>" class="form-control">
            </div>
        </div>
        <div class="row form-group">
            <label class="col-md-1 col-xs-12">更新日期</label>
            <div class="col-md-6 col-xs-12">
                <div class="input-group">
                    <input type="text" name="startTime" value="<?= h(isset($startTime) ? $startTime : '') ?>" class="form-control datetimepicker" readonly>
                    <span class="input-group-addon">至</span>
                    <input type="text" name="endTime" value="<?= h(isset($endTime) ? $endTime : '') ?>" class="form-control datetimepicker" readonly>
                </div>
            </div>           
        </div>
        <div class="row form-group">
           <label class="col-md-1 col-xs-12">参与日期</label>
           <div class="col-md-5 col-xs-12">
               <div class="input-group">
                   <input type="text" name="partedStartTime" value="<?= h(isset($partedStartTime) ? $partedStartTime : '') ?>" class="form-control datetimepicker" readonly>
                   <span class="input-group-addon">至</span>
                   <input type="text" name="partedEndTime" value="<?= h(isset($partedEndTime) ? $partedEndTime : '') ?>" class="form-control datetimepicker" readonly>
               </div>
           </div>  
        </div>
            
        <div class="row form-group">
            <label class="col-md-1 col-xs-4">状态</label>
            <div class="col-md-5 col-xs-8">
                <div class="ui form">
                <div class="inline fields">
                    <div class="ui checkbox hidden"></div>
                <?php foreach ($stateArr as $key => $value): ?>
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
            
            <div class="col-md-2">
                <button class="btn btn-primary" name="submit" value="search">搜索</button>
                <?php if (strpos($_privileges, 'o') !== false): ?>
                <button class="btn btn-primary" name="submit" value="export">导出</button>    
                <?php endif ?>
            </div>
        </div>
        </form>
    </div>
    <form action="<?= $this->Url->build(['action' => 'bulk'])?>" method="post" id="bulk">
    <div class="segment">
        <?php if (strpos($_privileges, 'd') !== false): ?>
            <button type="submit" class="btn btn-danger del" name="submit" value="del">删除选中</button>
        <?php endif ?>
        <?php if (strpos($_privileges, 'o') !== false): ?>
            <button type="submit" class="btn btn-primary export" name="submit" value="export">导出选中</button>
        <?php endif ?>
        <button type="submit" class="btn btn-primary export" name="submit" value="signed">成交</button>
        <button type="submit" class="btn btn-danger export" name="submit" value="closed">失败</button>
        <span class="btn btn-primary add_status_btn">更新进展</span>
    </div>

    <div class="display-none ui segment input-filed" id="add_status"> 
        
            <?php
                echo $this->Form->control('status',['label' => '进展','type' => 'textarea']);
                echo $this->Form->control('next_contact_time',['label' => '下次联系时间','class' => 'timepicker','readonly' => true]);
                echo $this->Form->control('next_note',['label' => '下次联系备注','type' => 'textarea']);
            ?>
        <button class="btn btn-primary add_status_btn" name="submit" value="status">更新</button>
    </div>

    <table cellpadding="0" cellspacing="0" class="ui table celled striped compact ">
        <thead>
            <tr>
                <th scope="col"><input type="checkbox" class="flat" id="check-all"><label><?= $this->Paginator->sort('id') ?></label></th>
                <th scope="col"><?= $this->Paginator->sort('customer_id',['客户']) ?></th>
                <th scope="col"><?= $this->Paginator->sort('event_id',['活动']) ?></th>
                <th scope="col">最新跟进</th>
                <th scope="col"><?= $this->Paginator->sort('state',['状态']) ?></th>
                <th scope="col"><?= $this->Paginator->sort('user_id',['业务员']) ?></th>
                <th scope="col">更新时间</th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody id="main" data-controller="businesses">
            
        </tbody>
    </table>
    <div id="message" class="ui segment message text-center">加载中...</div>
    <div class="segment">
        <?php if (strpos($_privileges, 'd') !== false): ?>
            <button type="submit" class="btn btn-danger del" name="submit" value="del">删除选中</button>
        <?php endif ?>
        <button type="submit" class="btn btn-primary export" name="submit" value="signed">成交</button>
        <button type="submit" class="btn btn-danger export" name="submit" value="closed">失败</button>
        <span class="btn btn-primary add_status_btn">更新进展</span>
    </div>
    </form>
</div>
<?= $this->start('script') ?>
<script type="text/javascript">
    $(function(){
        $add = {};
        ['status'].forEach(function(val){
            $add[val] = $('#add_'+val);
            $(".add_"+val+"_btn").on('click',function () {
                $add[val].is(":visible") ? $add[val].slideUp() : $add[val].slideDown();
                $add[val].siblings('.input-filed').slideUp();
                $('html,body').animate({ scrollTop: 0 }, 500);
            });
        });
    })
</script>
<?= $this->end() ?>
