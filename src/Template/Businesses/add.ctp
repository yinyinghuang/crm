<?php
/**
  * @var \App\View\AppView $this
  */
?>

<?= $this->start('top_nav') ?>
    <span class="navbar-brand">新增订单</span>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        <li><?= $this->Html->link(__('订单列表'), ['action' => 'index']) ?></li>
<?= $this->end() ?>

<div class="businesses form columns content">
    <?= $this->Form->create($business,['class' => 'ui segment','type' =>'file']) ?>
    <fieldset>
        <?php
            echo $this->Form->control('event_id', ['type' => 'hidden']);
            echo $this->Form->control('customer_id', ['type' => 'hidden']);
            echo $this->Form->control('event_type_id', ['type' => 'hidden']);
            echo $this->Form->control('user_id', ['type' => 'hidden', 'default' => $_user['id']]);
            echo $this->Form->control('event', ['label' => '活动','type' => 'text','required' => true]);
            echo $this->Form->control('customer', ['label' => '客户','type' => 'text','required' => true]);
            echo $this->Form->control('state',['label' => '状态','options' => $stateArr,'empty' => '请选择','default' => 0]);
            echo $this->Form->control('parted',['type' => 'text','class' => 'datetimepicker','label' => '参与时间','readonly' =>true,'required' => false]);
            echo $this->Form->control('status',['type' => 'textarea','label' => '进展','required' => true]);
            echo $this->Form->control('next_contact_time',['type' => 'text','class' => 'timepicker','readonly' =>true,'label' => '下次联系时间']);
            echo $this->Form->control('next_note',['type' => 'textarea','label' => '下次联系备注']);
                echo $this->Form->control('images[]', ['label' => '图片', 'type' => 'file','class' => 'images','multiple' => true,]); 
        ?>
    </fieldset>
    <?= $this->Form->button(__('提交'),['class' => ['btn','btn-primary']]) ?>
    <?= $this->Form->end() ?>
</div>

<?= $this->start('css') ?>
<?= $this->Html->css('../js/vendors/bootstrap-fileinput-master/css/fileinput.min.css') ?>
<?= $this->end() ?>
<?= $this->start('script') ?>
<?= $this->Html->script('vendors/bootstrap-fileinput-master/js/fileinput.min.js') ?>
<script type="text/javascript">
    $(function(){
        $(".images").fileinput({
            maxFileCount: 10,
            allowedFileTypes: ["image"],
             maxFileCount:10,
            showPreview :false,
        }); 
        $userId = $('#user-id');
        $customerId = $('#customer-id');
        $eventId = $('#event-id'); 
        $eventTypeId = $('#event-type-id'); 
        
        $('#event').autocomplete({
          serviceUrl: window.location.origin + '/events/autocompelete/',
          minChars : 0,
          params:{
            customer_id:function(){
              return $customerId.val();
            }
          },
          onSelect: function(suggestion) {
            $eventId.val(suggestion.data.event_id);
            $eventTypeId.val(suggestion.data.event_type_id);
          },
          onInvalidateSelection: function() {
              $(this).val('');
              $eventId.val('');
              $eventTypeId.val('');
          }
        });  
        $('#customer').autocomplete({
          serviceUrl: window.location.origin + '/customers/autocompelete/',
          minChars : 0,
          params:{
            event_id:function(){
              return $eventId.val();
            }
          },
          onSelect: function(suggestion) {
            $customerId.val(suggestion.data.customer_id);
            $userId.val(suggestion.data.user_id);
          },
          onInvalidateSelection: function() {
              $(this).val('');
              $customerId.val('');
              $userId.val('');
          }
        }); 

    })
</script>
<?= $this->end() ?>

