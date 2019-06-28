<?php
/**
  * @var \App\View\AppView $this
  */
?>
<div class="customerCommissions form large-10 medium-9 columns content">
    <?= $this->Form->create($customerCommission) ?>
    <fieldset>
        <legend><?= __('添加業績') ?></legend>
        <?php
            echo $this->Form->control('customer_id',['type' => 'hidden', 'value' => $id]);         
            echo $this->Form->control('department_id',['label' => '分組', 'options' => $departments, 'empty' => '請選擇','required' => true,'id' => 'department']);
            echo $this->Form->control('user_id',['options' => $users, 'empty' => '請選擇','label' => '姓名','required' => true, 'id' => 'users']);         
            echo $this->Form->control('commission',['label' => '業績','required' => true]);         
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit'),['class' => ['btn','btn-primary']]) ?>
    <?= $this->Form->end() ?>
</div>
<div class="clearfix"></div>
<?= $this->start('script') ?>
<script type="text/javascript">
$(function(){
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
    })
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
    })
})
</script>
<?= $this->end() ?>