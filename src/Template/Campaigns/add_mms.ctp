<?php
/**
  * @var \App\View\AppView $this
  */
?>

<?= $this->start('top_nav') ?>
    <span class="navbar-brand">发送彩信</span>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        <li><?= $this->Html->link(__('群發推廣列表'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('群发邮件'), ['action' => 'add-email']) ?></li>
        <li><?= $this->Html->link(__('群发短信'), ['action' => 'add-sms']) ?></li>
<?= $this->end() ?>

<div class="campaigns columns content">
    <div class="ui top attached tabular menu">
      <a class="<?php if (!isset($type) || $type== 'normal'): ?>active<?php endif ?> item" data-tab="normal">彩信群发</a>
      <a class="<?php if (isset($type) && $type== 'test'): ?>active<?php endif ?> item" data-tab="test">彩信测试</a>
    </div>
    <div class="ui bottom attached active tab segment" data-tab="normal">
        <?= $this->Form->create($campaign,['type' => 'file']) ?>
        <input type="hidden" value="normal" name="type">
        <fieldset>
            <div class="row form-group">
                <label class="col-md-1 col-xs-4">客戶名稱</label>
                <div class="col-md-2 col-xs-8">
                    <input type="text" name="name" value="<?= h(isset($name) ? $name : '') ?>" class="form-control">
                </div>
                <label class="col-md-1 col-xs-4">來源</label>
                <div class="col-md-2 col-xs-8">
                   <input type="text" name="source" value="<?= h(isset($source) ? $source : '') ?>" class="form-control">
                </div>
                <label class="col-md-1 col-xs-4">電話</label>
                <div class="col-md-2 col-xs-8">
                    <input type="text" name="mobile" value="<?= h(isset($mobile) ? $mobile : '') ?>" class="form-control">
                </div>
                <label class="col-md-1 col-xs-4">電郵</label>
                <div class="col-md-2 col-xs-8">
                    <input type="text" name="email" value="<?= h(isset($email) ? $email : '') ?>" class="form-control">
                </div>
            </div>
            <div class="row form-group">
                <label class="col-md-1 col-xs-12">更新日期</label>
                <div class="col-md-5 col-xs-12">
                    <div class="input-group">
                        <input type="text" name="startTime" value="<?= h(isset($startTime) ? $startTime : '') ?>" class="form-control datetimepicker" readonly>
                        <span class="input-group-addon">至</span>
                        <input type="text" name="endTime" value="<?= h(isset($endTime) ? $endTime : '') ?>" class="form-control datetimepicker" readonly>
                    </div>
                </div> 
                <label class="col-md-1 col-xs-4">业务员</label>
                <div class="col-md-2 col-xs-8">
                    <input type="hidden" name="user_id" value="<?= h(isset($user_id) ? $user_id : '') ?>" class="user_id">
                    <input type="text" name="user_name" value="<?= h(isset($user_name) ? $user_name : '') ?>" class="form-control user_name">
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
                <label class="col-md-1 col-xs-4">高级筛选</label>
                <div class="col-md-11 col-xs-12 form-inline">
                    

                    <?php foreach ($eventTypes as $key => $value): ?>
                    <div class="form-group">                    
                    <label class="text-bottom"><?= $value?></label>
                    <select name="advanced[<?= $key?>][rel]">
                        <option value="gt" <?php if (!isset($advanced[$key]['rel']) ||(isset($advanced[$key]['rel']) && $advanced[$key]['rel'] == 'gt')): ?> selected <?php endif ?>>大于</option>
                        <option value="eq" <?php if (isset($advanced[$key]['rel']) && $advanced[$key]['rel'] == 'eq'): ?> selected <?php endif ?>>等于</option>
                        <option value="lt" <?php if (isset($advanced[$key]['rel']) && $advanced[$key]['rel'] == 'lt'): ?> selected <?php endif ?>>小于</option>
                    </select>
                    <input type="number" name="advanced[<?= $key?>][num]" class="form-control" value="<?= h(isset($advanced[$key]['num']) ? $advanced[$key]['num'] : '') ?>">   
                    </div> 
                    <?php endforeach ?>
                </div>
                <label class="col-md-1 col-xs-12 col-md-offset-1">参与日期</label>
                <div class="col-md-5 col-xs-12">
                    <div class="input-group">
                        <input type="text" name="partedStartTime" value="<?= h(isset($partedStartTime) ? $partedStartTime : '') ?>" class="form-control datetimepicker" readonly>
                        <span class="input-group-addon">至</span>
                        <input type="text" name="partedEndTime" value="<?= h(isset($partedEndTime) ? $partedEndTime : '') ?>" class="form-control datetimepicker" readonly>
                    </div>
                </div> 
            </div>

            <div class="row form-group">
                <label class="col-md-1 col-xs-4 col-md-offset-1">状态</label>
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

            <?php
                echo $this->Form->control('subject', ['label' => '主题', 'required' => true, 'type' => 'text']);
                echo $this->Form->control('content', ['label' => '內容', 'required' => true,'type' => 'textarea','rows' => '15']);
                echo $this->Form->control('image', ['label' => '图片(图片长宽均不可超过1000px,大小不可超过250KB,图片名称不可包含中文)', 'required' => true,'type' => 'file','class' => 'image','multiple' => false,'placeholder' => '图片长宽均不可超过1000px,大小不可超过250KB,图片名称不可包含中文']);
            ?>
        </fieldset>
        <?= $this->Form->button(__('提交'),['class' => ['btn','btn-primary'], 'id' => 'submit']) ?>
        <?= $this->Form->end() ?>       
    </div>
    <div class="ui bottom attached tab segment" data-tab="test">
        <?= $this->Form->create($campaign,['type' => 'file']) ?>
        <fieldset>
            <input type="hidden" value="test" name="type">
            <?php
                echo $this->Form->control('code', ['type' => 'number','label' => '區號','default' => 852, 'required' =>true,'readonly' => true]);
                echo $this->Form->control('number', ['type' => 'number','label' => '收件人', 'required' =>true]);
                echo $this->Form->control('subject', ['label' => '主题', 'required' => true, 'type' => 'text']);
                echo $this->Form->control('content', ['label' => '內容1', 'required' => true, 'type' => 'textarea','rows' => '15']);
                echo $this->Form->control('image', ['label' => '图片(图片长宽均不可超过1000px,大小不可超过250KB,图片名称不可包含中文)', 'required' => true,'type' => 'file','class' => 'image','multiple' => false,'placeholder' => '图片长宽均不可超过1000px,大小不可超过250KB,图片名称不可包含中文']);
            ?>
        </fieldset>
        <?= $this->Form->button(__('提交'),['class' => ['btn','btn-primary'], 'id' => 'submit']) ?>
        <?= $this->Form->end() ?>
    </div>


</div>
<div class="clearfix"></div>
<?php $this->start('css'); ?>
<?= $this->Html->css('../js/vendors/bootstrap-fileinput-master/css/fileinput.min.css') ?>
<?php $this->end(); ?>
<?= $this->start('script') ?>
<?= $this->Html->script('vendors/bootstrap-fileinput-master/js/fileinput.min.js') ?>
<script>
    $(function () {
        $('.menu .item').tab();
        $(".image").fileinput({
            allowedFileTypes: ["image"],
            maxImageWidth: 1000,
            maxImageHeight: 1000,
            maxFileCount:1,
            maxFileSize:250,
            showPreview :false,
            msgPlaceholder:'图片长宽均不可超过1000px,大小不可超过250KB,图片名称不可包含中文'
        });
        $(document.forms[0]).on('submit', function(){
            if (confirm('確認發送？')) {
                new PNotify({
                    text: '發送中，請耐心等候',
                    type: 'info',
                    styling: 'bootstrap3',
                    delay: 3000,
                    width:'280px'
                });
                document.getElementById('submit').style.display = 'none';
            }
            
        }); 
        var url = window.location.origin + '/users/autocompelete-users/';

        $('#customer_name').autocomplete({
          serviceUrl: url,
          minChars : 0,
          onSelect: function(suggestion) {
            $('#customer_id').val(suggestion.data.id);
          },
          onInvalidateSelection: function() {
              $(this).val('');
              $('#customer_id').val('');
          }
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


    });
</script>
<?= $this->end() ?>