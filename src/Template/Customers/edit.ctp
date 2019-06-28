<?php
/**
  * @var \App\View\AppView $this
  */
?>

<?= $this->start('top_nav') ?>
    <span class="navbar-brand">編輯客戶<?= $this->Html->link($customer->name, ['action' => 'edit', $customer->id]) ?></span>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        <li><?= $this->Html->link(__('客戶列表'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('客户详情'), ['action' => 'view',$customer->id]) ?></li>
        <li><?= $this->Html->link(__('客户進展'), ['controller' => '','action' => 'view',$customer->id]) ?></li>
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
                <input type="hidden" name="mobile_id_1" value="<?= $customer->customer_mobiles[0]->id ?>" class="mobile-id">
                <select name="country_code_id_1" style="display: inline-block;width: 30%;vertical-align:middle" required>
                    <?php foreach ($countrycodes as $k => $c): ?>
                       <option value="<?= $k ?>" <?php if ($customer->customer_mobiles[0]->country_code_id == $k): ?>selected<?php endif ?>><?= $c ?></option> 
                    <?php endforeach ?>
                </select>
                <input type="tel" name="mobile_1" style="width:54%;display:inline-block;vertical-align:middle" pattern="^\d{6,11}$" oninvalid="setCustomValidity('請填寫6至11位數字');" oninput="setCustomValidity('')" maxlength="11" required class="mobile" value="<?= $customer->customer_mobiles[0]->mobile ?>" >                
            <div>            
        </div>

        <div class='input email'>
            <label>郵件</label>
            <div>
                <input type="hidden" name="email_id_1" <?php if (isset($customer->customer_emails[0])): ?>value="<?= $customer->customer_emails[0]->id ?><?php endif ?>" class="email-id">
                <input type="email" name="email_1" style="width:54%;display:inline-block;vertical-align:middle" pattern="^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$" oninvalid="setCustomValidity('邮箱格式有误');" oninput="setCustomValidity('')" class="email" <?php if (isset($customer->customer_emails[0])): ?>value="<?= $customer->customer_emails[0]->email ?><?php endif ?>" >                
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
        $('.mobile').on('blur',function(){
            let that = this;
            if (this.value != '') {
                if ($('#users').val() !== '') {
                    $.ajax({
                        type : 'get',
                        url : '<?= $this->Url->build(['action' => 'ajax']) ?>',
                        data : {
                            mobile : this.value,
                            country : $(this).siblings('select')[0].value,
                            user_id: $('#users').val(),
                            type : 'edit'
                        },
                        success : function (data) {
                            data = JSON.parse(data);console.log(data);
                            if (data != null) {

                                alert(data.customer.name + '(電話：' + data.mobile + ')現在是' + data.customer.user.username + '的客戶,請勿重復添加');
                                 that.value ='';
                            }   
                        }

                    }) 
                } else {
                    alert('請先確定業務員');
                }
                 
            }
            
        });
        $('.email').on('blur',function(){
            let that = this;
            if(this.value != ''){
                if ($('#users').val() !== '') {
                    $.ajax({
                        type : 'get',
                        url : '<?= $this->Url->build(['action' => 'ajax']) ?>',
                        data : {
                            email : this.value,
                            user_id: $('#users').val(),
                            type : 'edit'
                        },
                        success : function (data) {
                            data = JSON.parse(data);
                            if (data != null) {
                                alert(data.customer.name+ '現在是' + data.customer.user.username + '的客戶,請勿重復添加');
                                that.value ='';
                            }   
                        }

                    })
                } else {
                    alert('請先確定業務員');
                }
                
            }
        });
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
        ['mobile','email'].forEach(function(type){
            $('.delete-'+type).on('click', function(){
                if(confirm('確定要刪除？')){
                    var that = this;
                    $.ajax({
                        type : 'post',
                        url: '/customers/delete-'+type,
                        data : {
                            id : $(this).siblings('.'+type+'-id')[0].value
                        },
                        success : function(data){
                            if (data == 1) {
                                $(that).siblings('.'+type+'-id')[0].value = '';
                                $(that).siblings('.'+type)[0].value = '';
                                $(that).remove();
                                 new PNotify({
                                    title: '成功',
                                    text: '刪除成功',
                                    type: 'success',
                                    styling: 'bootstrap3',
                                    delay: 3000,
                                    width:'280px'
                                });                            
                            } else {                            
                               new PNotify({
                                    title: '錯誤',
                                    text: '刪除失敗，請重試',
                                    type: 'error',
                                    styling: 'bootstrap3',
                                    delay: 3000,
                                    width:'280px'
                                });
                            }
                        }
                    });
                }
            });

        })
        
    })
</script>
<?= $this->end() ?>