<?php
/**
  * @var \App\View\AppView $this
  */
?>

<?= $this->start('top_nav') ?>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        <li><?= $this->Html->link(__('客戶列表'), ['action' => 'index']) ?></li>
        <!-- <li><?= $this->Html->link(__('客户图片'), ['action' => 'view',$customer->id]) ?></li> -->
<?= $this->end() ?>
<div class="customers form  columns content">
    <?= $this->Form->create($customer) ?>
    <fieldset>
        <input type="hidden" value="1" name="user_id">

        <?php   
            echo $this->Form->control('source',['label' => '客戶來源','options' => $sourceArr,'required' => true]);
            echo $this->Form->control('source_detail',['type' => 'textarea','label' => '客戶來源detailed']);  
            echo $this->Form->control('state',['type' => 'radio','options' => $stateArr, 'default' => $customer->state, 'label' => '狀態']);            
        ?>
        <div class='input tel required'>
            <label>聯絡方式</label>
            <div>
                <input type="text" name="phone" style="width:54%;display:inline-block;vertical-align:middle" placeholder="多个用英文竖线'|'隔开" value="<?= $customer->phone ?>" >                
            <div>            
        </div>

        <div class='input email'>
            <label>郵件</label>
            <div>
                <input type="email" name="email_1" style="width:54%;display:inline-block;vertical-align:middle" placeholder="多个用英文竖线'|'隔开" value="<?= $customer->email ?>" >                
            <div>            
        </div>
    </fieldset>
    <?= $this->Form->button(__('提交'),['class' => ['btn','btn-primary']]) ?>
    <?= $this->Form->end() ?>
</div>
<div class="clearfix"></div>
<?= $this->start('script') ?>
<script type="text/javascript">
    $(function(){
        <?php if (strpos($_privileges, 'e')): ?>
        $('#department').on('change',function(){
            $.ajax({
                type:'get',
                url:'<?= $this->Url->build(['controller' => 'Users', 'action' => 'ajax']) ?>',
                data:{
                    department:this.value
                },
                success:function(data){
                    data = JSON.parse(data);
                    var html = '<option value>請選擇</option>';
                    $.each(data,function(key,value){
                        html += '<option value=' + value.id + '>' + value.username + '</option>'
                    });
                    $('#users').html(html);
                }

            })
        });
        $('#users').on('change',function(){
            $.ajax({
                type:'get',
                url:'<?= $this->Url->build(['controller' => 'Users', 'action' => 'ajax']) ?>',
                data:{
                    user:this.value
                },
                success:function(data){
                    data = JSON.parse(data);
                    if (data !== null) {
                        $('#department').find('option').each(function(){
                            if(this.value == data.department_id){
                                $(this).attr('selected', true);
                                $(this).siblings().attr('selected', false);
                            }
                        });
                    }                    
                }

            })
        });
        <?php endif ?>        
    })
</script>
<?= $this->end() ?>