<?php
/**
  * @var \App\View\AppView $this
  */
?>


<?= $this->start('top_nav') ?>
    <span class="navbar-brand"><a href="<?= $this->Url->build(['action' => 'index'])?>">客户列表</a></span>
    <i class="fa fa-search" id="show-search"></i>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        <li><?= $this->Html->link(__('新增客戶'), ['action' => 'add']) ?></li>
        <!-- <?php if (strpos($_privileges, 'i')): ?>            
        <li><?= $this->Html->link(__('导入客戶'), ['action' => 'import']) ?></li>
        <?php endif ?>
        <?php if (strpos($_privileges, 'o')): ?>
        <li><?= $this->Html->link(__('导出客戶'), ['action' => 'export']) ?></li>
        <?php endif ?> -->
<?= $this->end() ?>
<div class="customers index columns content ">
    
    <div class="search_box  ui segment" style="display:none">
        <form action="<?= $this->Url->build(['action' => 'index'])?>" role="form">
        <div class="row form-group">
            <label class="col-md-1 col-xs-4">客戶名稱</label>
            <div class="col-md-2 col-xs-8">
                <input type="text" name="name" value="<?= h(isset($name) ? $name : '') ?>" class="form-control">
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
           <!--  <label class="col-md-1 col-xs-4">业务员</label>
            <div class="col-md-2 col-xs-8">
                <input type="hidden" name="user_id" value="<?= h(isset($user_id) ? $user_id : '') ?>" class="user_id">
                <input type="text" name="user_name" value="<?= h(isset($user_name) ? $user_name : '') ?>" class="form-control user_name">
            </div>   -->
        </div>
        <div class="row form-group">
            <label class="col-md-1 col-xs-4 col-md-offset-1">状态</label>
            <div class="col-md-5 col-xs-8">
                <div class="ui form">
                <div class="inline fields">
                    <div class="ui checkbox hidden"></div>
                <?php foreach ($stateArr as $key => $value): ?>
                   <div class="ui checkbox">
                     <input type="checkbox" name="state[]" value="<?= $value?>" <?php if (isset($state) && in_array($value, $state)): ?>checked <?php endif ?>>
                     <label><?= $value?></label>
                   </div>
                <?php endforeach ?>
                </div>
                </div>
            </div>
        </div>
        <div class="row form-group">
            <label class="col-md-1 col-xs-4 col-md-offset-1">來源</label>
            <div class="col-md-8 col-xs-8">
                <div class="ui form">
                <div class="inline fields">
                    <div class="ui checkbox hidden"></div>
                <?php foreach ($sourceArr as $key => $value): ?>
                   <div class="ui checkbox">
                     <input type="checkbox" name="source[]" value="<?= $value?>" <?php if (isset($state) && in_array($key, $source)): ?>checked <?php endif ?>>
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
                <!-- <?php if (strpos($_privileges, 'o') !== false): ?>
                <button class="btn btn-primary" name="submit" value="export">导出</button>    
                <?php endif ?>
                 -->
            </div>
        </div>
        </form>
    </div>
    <form action="<?= $this->Url->build(['action' => 'bulk'])?>" method="post" id="bulk">
    <div class="segment">
        <?php if (strpos($_privileges, 'd') !== false): ?>
            <button type="submit" class="btn btn-danger del" name="submit" value="del">删除选中</button>
        <?php endif ?>
        <span class="btn btn-primary add_status_btn">更新进展</span>
       <!--  <?php if (strpos($_privileges, 'o') !== false): ?>
            <button type="submit" class="btn btn-primary export" name="submit">导出选中</button>
        <?php endif ?>
        <?php if (strpos($_privileges, 't') !== false): ?>
            <div class="ui labeled input">
              <button class="ui label transfer" name="submit" value="transfer">转移至</button>
              <input type="text" class="user_name" placeholder="业务员">
              <input type="hidden" class="user_id" name="to_user_id">
            </div>
        <?php endif ?> -->
        
    </div>   

    <div class="display-none ui segment input-filed" id="add_status"> 
        
            <?php
                echo $this->Form->control('status',['label' => '进展','type' => 'textarea']);
                echo $this->Form->control('next_contact_time',['label' => '下次联系时间','class' => 'timepicker','readonly' => true]);
                echo $this->Form->control('next_note',['label' => '下次联系备注','type' => 'textarea']);
            ?>
        <button class="btn btn-primary add_status_btn" name="submit" value="status">更新</button>
    </div>

    <table cellpadding="0" cellspacing="0" class="ui table celled striped compact checkbox_container">
        <thead>
            <tr>
                <th scope="col"><input type="checkbox" class="flat" id="check-all"><label><?= __('編號') ?></label></th>
                <th scope="col"><?= __('姓名') ?></th>
                <th scope="col"><?= __('電話') ?></th>
                <th scope="col"><?= __('業務員') ?></th>
                <th scope="col"><?= __('進展') ?></th>
                <th scope="col"><?= __('更新時間') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody id="main" data-controller='customers'>
        </tbody>
    </table>
    <div id="message" class="ui segment message text-center">加载中...</div>
    <div class="segment">
        <?php if (strpos($_privileges, 'd') !== false): ?>
            <button type="submit" class="btn btn-danger del" name="submit" value="del">删除选中</button>
        <?php endif ?>
        <span class="btn btn-primary add_status_btn">更新进展</span>
       <!--  <?php if (strpos($_privileges, 'o') !== false): ?>
            <button type="submit" class="btn btn-primary" name="submit" value="export">导出选中</button>
        <?php endif ?>
        <?php if (strpos($_privileges, 't') !== false): ?>
            <div class="ui labeled input">
              <button class="ui label transfer" name="submit" value="transfer">
                转移至
              </button>
              <input type="text" class="user_name" placeholder="业务员">
              <input type="hidden" class="user_id" name="to_user_id">
            </div>
        <?php endif ?> -->
    </div>
    </form>
</div>
<div class="clearfix"></div>

<?= $this->start('script') ?>

<script type="text/javascript">
    $(function(){
        var url = window.location.origin + '/users/autocompelete/',
            userId = $('.user_id'),
            user = $('.user_name');
        user.autocomplete({
          serviceUrl: url,
          minChars : 0,
          onSelect: function(suggestion) {
            $('.user_id').val(suggestion.data.id);
          },
          onInvalidateSelection: function() {
              $(this).val('');
              $('.user_id').val('');
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