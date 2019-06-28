<?php
/**
  * @var \App\View\AppView $this
  */
?>
<?= $this->start('top_nav') ?>
    <span class="navbar-brand"><a href="<?= $this->Url->build(['action' => 'index','?' => ['customer_id' => $customer->id]])?>">客户图片</a></span><i class="fa fa-search pull-right" id="show-search"></i>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        <li><?= $this->Html->link(__('客户详情'), ['controller' => 'Customers', 'action' => 'view',$customer_id]) ?></li>
<?= $this->end() ?>

<div class="customerImages view columns content ">
    <div class="ui segment ">
        <h3 class="header inline">客户名称(<?= $customer->name ?>)<span id="add_images_btn" class="label nav-label-span">上传图片</span></h3>
         
    </div>
    <div class="ui segment search_box" style="display: none;">
        <form action="" role="form">
            <input type="hidden" name="customer_id" value="<?= $customer_id?>">
        <div class="row form-group">
            <label class="col-md-1 col-xs-4">图片名稱</label>
            <div class="col-md-3 col-xs-8">
                <input type="text" name="name" value="<?= h(isset($name) ? $name : '') ?>" class="form-control">
            </div>
            <label class="col-md-1 col-xs-4">无关联活动</label>
            <div class="col-md-3 col-xs-8">
                <input type="checkbox" name="non_involved_business" value="on" <?php if (isset($non_involved_business)): ?>checked <?php endif ?>>
            </div>
        </div>
        <div class="row form-group">
            <label class="col-md-1 col-xs-12">上传日期</label>
            <div class="col-md-6 col-xs-12">
                <div class="input-group">
                    <input type="text" name="startTime" value="<?= h(isset($startTime) ? $startTime : '') ?>" class="form-control datetimepicker" readonly>
                    <span class="input-group-addon">至</span>
                    <input type="text" name="endTime" value="<?= h(isset($endTime) ? $endTime : '') ?>" class="form-control datetimepicker" readonly>
                </div>
            </div>           
        </div>

        <div class="row form-group">
            <label class="col-md-1 col-xs-12">活动</label>
            <div class="col-md-5 col-xs-12">
                <input type="hidden" name="event_id" value="<?= h(isset($event_id) ? $event_id : ',') ?>" class="event_id" id="event_id" >
                <input type="text" name="event_name" class="form-control event_name" id="event_name">                
            </div>
            <div class="col-md-6 col-xs-12" id="event_name_list">
                <?php if (isset($event_names)): ?>
                    <?php foreach ($event_names as $value): ?>
                        <span class="ui label red event_label"><?= $value?></span>
                    <?php endforeach ?>
                <?php endif ?>
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
                <button class="btn btn-primary">搜索</button>
            </div>
        </div>
        </form>
    </div>
    <div class="display-none ui segment" id="add_images"> 
        <?= $this->Form->create(null, ['url' => ['controller' => 'CustomerImages','action' => 'add', $customer_id],'type' => 'file']) ?>
        <input type="hidden" name="customer_id" value="<?= $customer->id?>">
        <h4 class="ui horizontal divider header"><i class="cloud upload icon"></i>上传图片</h4>
        <input id="input" type="file" multiple name="images[]" required>
        
        <?= $this->Form->button(__('提交'),['class' => ['btn','btn-primary']]) ?>
        <?= $this->Form->end() ?>
    </div>
    
    <form action="<?= $this->Url->build(['action' => 'bulk'])?>" method="post" id="bulk">
        <input type="hidden" name="customer_id" value="<?= $customer->id?>">
    <div class="segment" style="margin-bottom: 10px;">
        <?php if (strpos($_privileges, 'd') !== false): ?>
            <button type="submit" class="btn btn-danger del" name="submit" value="del">删除选中</button>
        <?php endif ?>        
    </div>
    
    <table cellpadding="0" cellspacing="0" class="ui table celled striped compact checkbox_container">
        <thead>
            <tr>
                <th scope="col"><input type="checkbox" class="flat" id="check-all"><label><?= __('編號') ?></label></th>
                <th scope="col"><?= __('图片名称') ?></th>
                <th scope="col"><?= __('关联活动') ?></th>
                <th scope="col"><?= __('上传时间') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody id="main" data-controller='customer-images'>
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

<?= $this->start('css') ?>
<?= $this->Html->css('../js/vendors/bootstrap-fileinput-master/css/fileinput.min.css') ?>
<?= $this->end() ?>
<?= $this->start('script') ?>
<?= $this->Html->script('vendors/bootstrap-fileinput-master/js/fileinput.min.js') ?>
<?= $this->Html->script('jquery-lazyload/jquery.lazyload.js') ?>
    <script type="text/javascript">
        $(function(){

            $add_images = $('#add_images');
            $("#add_images_btn").on('click',function () {
                $add_images.is(":visible") ? $add_images.slideUp() : $add_images.slideDown()
            });

            $('body').on('click','.flat',function(e){
                var input = $(this).siblings('input')[0];
                $(input).prop('checked',!$(input).prop('checked'));
                e.stopPropagation();
                e.preventDefault()
            });
            $("#input").fileinput({
                maxFileCount: 10,
                allowedFileTypes: ["image"],
                showPreview :false,

            });

            $eventId = $('#event_id'); 
            $eventNameList = $('#event_name_list'); 
            
            $('#event_name').autocomplete({
              serviceUrl: window.location.origin + '/events/autocompelete/',
              minChars : 0,
              params:{
                non_event_id:function(){
                  return $eventId.val();
                }
              },
              onSelect: function(suggestion) {
                $eventId.val($eventId.val() + suggestion.data.event_id + ',');
                $eventNameList.append('<span class="ui label event_label red" data-id='+suggestion.data.event_id +'>'+ suggestion.value+'</span>');
                $(this).val('');
              },
              onInvalidateSelection: function() {
                  $(this).val('');
              }
            }); 

            $('body').on('click','.event_label',function(){
                var id = $(this).data('id');
                $eventId.val($eventId.val().replace(',' + id+',',','));
                $(this).remove();
            })
        })
    </script>
<?= $this->end() ?>
