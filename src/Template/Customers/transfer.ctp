<?php
/**
  * @var \App\View\AppView $this
  */
?>

<?= $this->start('top_nav') ?>
    <span class="navbar-brand">新增客戶</span>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        <li><?= $this->Html->link(__('客戶列表'), ['action' => 'index']) ?></li>
        <?php if (strpos($_privileges, 'i')): ?>          
        <li><?= $this->Html->link(__('导入客戶'), ['action' => 'import']) ?></li>
        <?php endif ?>
        <?php if (strpos($_privileges, 'o')): ?>
        <li><?= $this->Html->link(__('导出客戶'), ['action' => 'export']) ?></li>  
        <?php endif ?>        
<?= $this->end() ?>

<div class="customers columns content">
    <div class="ui top attached tabular menu">
      <a class="<?php if (!isset($filter) || $filter== false): ?>active<?php endif ?> item" data-tab="entire">全体</a>
      <a class="<?php if (isset($filter) && $filter): ?>active<?php endif ?> item" data-tab="filter">条件筛选</a>
    </div>
    <div class="ui bottom attached active tab segment" data-tab="entire">
        <form action="<?= $this->Url->build(['action' => 'transfer-entire'])?>" method="post" id="form-entire">
            <?php 
                echo $this->Form->control('from_user_id', ['type' => 'hidden','value' => ',']);
                echo $this->Form->control('from_user', ['type' => 'text','label' => '业务员']); ?>

            <div id="from_user_list"></div>
            <?php 
                echo $this->Form->control('to_user_id', ['type' => 'hidden']);
                echo $this->Form->control('to_user', ['type' => 'text','label' => '转移至']); ?>
        <?= $this->Form->button(__('确认转移'),['class' => ['btn','btn-primary']]) ?>
        <?= $this->Form->end() ?>
        </form>
    </div>
    <div class="ui bottom attached tab segment" data-tab="filter">
        <form action="<?= $this->Url->build(['action' => 'transfer-filter'])?>" role="form" id="form-filter" method="post">
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
                    <input type="hidden" name="user_id" value="<?= h(isset($user_id) ? $user_id : '') ?>" id="user_id">
                    <input type="text" name="user_name" value="<?= h(isset($user_name) ? $user_name : '') ?>" class="form-control" id="user_name">
                </div>  
            </div>       
            <div class="row form-group">
                <label class="col-md-1 col-xs-4">转移至</label>
                <div class="col-md-5 col-xs-8">
                    <input type="hidden" id="to-user-id-filter" name="to_user_id_filter" value="<?= h(isset($to_user_id_filter) ? $to_user_id_filter : '') ?>" >
                    <input type="text" id="to-user-filter" name="to_user_filter" value="<?= h(isset($to_user_filter) ? $to_user_filter : '') ?>" >
                </div>
            </div>
            <div class="row form-group">            
                <div class="col-md-2">
                    <button class="btn btn-primary">确认转移</button>
                </div>
            </div>
            </form>
    </div>

</div>
<div class="clearfix"></div>
<?= $this->start('css') ?>
<?= $this->end() ?>
<?= $this->start('script') ?>

<script type="text/javascript">
    $(function(){
        $('.menu .item').tab();

        var url = window.location.origin + '/users/autocompelete/',
            fromUserId = $('#from-user-id'),
            fromUser = $('#from-user'),
            fromUserList = $('#from_user_list'),
            toUserId = $('#to-user-id'),
            toUser = $('#to-user'),
            toUserIdFilter = $('#to-user-id-filter'),
            toUserFilter = $('#to-user-filter'),
            userId = $('#user_id'),
            user = $('#user_name');
        user.autocomplete({
          serviceUrl: url,
          minChars : 0,
          onSelect: function(suggestion) {
            userId.val(suggestion.data.id);
          },
          onInvalidateSelection: function() {
              $(this).val('');
              userId.val('');
          }
        });

        fromUser.autocomplete({
          serviceUrl: url,
          minChars : 0,
          params:{
            selected_id:function(){
              return fromUserId.val();
            }
          },
          onSelect: function(suggestion) {
            fromUserId.val(fromUserId.val() + suggestion.data.id +',');
            let user = '<div class="ui label user_list" data-user-id='+suggestion.data.id+'> '+suggestion.value+'<i class="delete icon"></i></div>';
            fromUserList.append(user);
            fromUser.val('');
          },
          onInvalidateSelection: function() {
              $(this).val('');
          }
        });


        toUser.autocomplete({
          serviceUrl: url,
          minChars : 0,
          params:{
            selected_id:function(){
              return fromUserId.val();
            }
          },
          onSelect: function(suggestion) {
            toUserId.val(suggestion.data.id);
          },
          onInvalidateSelection: function() {
              $(this).val('');
          }
        });



        toUserFilter.autocomplete({
          serviceUrl: url,
          minChars : 0,
          onSelect: function(suggestion) {
            toUserIdFilter.val(suggestion.data.id);
          },
          onInvalidateSelection: function() {
              $(this).val('');
              toUserIdFilter.val('');
          }
        });

        $('body').on('click','.user_list',function () {
            fromUserId.val(fromUserId.val().replace(','+$(this).data('user-id')+',',','));
            $(this).remove();
        });
        $('#form-entire').on('submit',function () {
            if (fromUserId.val()==',') {
                alert('请选择要转出的业务员');
                return false;
            } 
            if (toUserId.val()=='') {
                alert('请选择要转入的业务员');
                return false; 
            }
            if(confirm('确定要批量转移客户？')) return true;
            return false;
        });

        $('#form-filter').on('submit',function () {
            
            if (toUserIdFilter.val()=='') {
                alert('请选择要转入的业务员');
                return false; 
            }
            if(confirm('确定要批量转移客户？')) return true;
            return false;
        });
    })
</script>
<?= $this->end() ?>