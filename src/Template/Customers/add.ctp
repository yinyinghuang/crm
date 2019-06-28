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

<div class="customers columns content ui segment">
    <?= $this->Form->create($customer,[
           'type'=>'file',]) ?>
    <fieldset>
        <input type="hidden" value="1" name="user_id">

        <?php 
            echo $this->Form->control('source',['label' => '客戶來源','options' => $sourceArr,'required' => true]);         
            echo $this->Form->control('source_detail',['type' => 'textarea','label' => '客戶來源detailed']);         
        ?>
        <?php
            echo $this->Form->control('name', ['label' => '客戶名稱']);
        ?>
        <div class='input tel required'>
            <label>聯絡方式</label>
            <div>
                <select name="country_code_id_1" id="country1" style="display: inline-block;width: 30%;vertical-align:middle" required>
                    <?php foreach ($countrycodes as $key => $value): ?>
                       <option value="<?= $key ?>" <?php if (isset($customer->country_1) && $customer->country_1 == $key): ?>selected<?php endif ?>><?= $value ?></option> 
                    <?php endforeach ?>
                </select>
                <input type="tel" name="mobile_1" style="width:68%;display:inline-block;vertical-align:middle" id="mobile1" pattern="^\d{6,11}$" oninvalid="setCustomValidity('請填寫6至11位數字');" oninput="setCustomValidity('')" maxlength="11" required class="mobile" <?php if (isset($customer->mobile_1)): ?>value="<?= $customer->mobile_1 ?>"<?php endif ?>>


            <div>          
        </div>
        <div class='input tel'>
            <label>郵件</label>
            <div>
                <input type="tel" name="email_1" style="width:68%;display:inline-block;vertical-align:middle" id="email1" pattern="^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$" oninvalid="setCustomValidity('邮箱格式有误');" oninput="setCustomValidity('')" class="email" <?php if (isset($customer->email_1)): ?>value="<?= $customer->email_1 ?>"<?php endif ?>>


            <div>          
        </div>
        <?php
            echo $this->Form->control('state',['label' => '狀態','options' => $stateArr,'empty' => '请选择','default' => 0]);
            echo $this->Form->control('status',['type' => 'textarea','label' => '進展','required' => true]);
            echo $this->Form->control('next_contact_time',['type' => 'text','class' => 'timepicker','readonly' =>true,'label' => '下次聯絡時間']);
            echo $this->Form->control('next_note',['type' => 'textarea','label' => '下次聯絡備註']);
        ?>
    </fieldset>
    <?= $this->Form->button(__('提交'),['class' => ['btn','btn-primary']]) ?>
    <?= $this->Form->end() ?>
</div>
<div class="clearfix"></div>
<?= $this->start('css') ?>
<?= $this->Html->css('../js/vendors/bootstrap-fileinput-master/css/fileinput.min.css') ?>
<?= $this->end() ?>
<?= $this->start('script') ?>
<?= $this->Html->script('vendors/bootstrap-fileinput-master/js/fileinput.min.js') ?>
<?= $this->Html->script('vendors/bootstrap-fileinput-master/js/locales/zh-TW.js') ?>

<script type="text/javascript">
    $(function(){

       $("#input").fileinput({
            maxFileCount: 10,
            allowedFileTypes: ["image"],
            showPreview :false,
       });
        $('.mobile').on('blur',function(){
            let that = this;
            if (this.value != '') {
                $.ajax({
                    type : 'get',
                    url : '<?= $this->Url->build(['action' => 'ajax']) ?>',
                    data : {
                        mobile : this.value,
                        country : $(this).siblings('select')[0].value,
                        'type' : 'add'
                    },
                    success : function (data) {
                        data = JSON.parse(data);console.log(data);
                        if (data != null) {
                            alert(data.customer.name + '(電話：' + data.mobile + ')現在是' + data.customer.user.username + '的客戶,請勿重復添加');
                            that.value = ''
                        }   
                    }

                })  
            }
            
        });
        $('.email').on('blur',function(){
            let that = this;
            if(this.value != ''){
                $.ajax({
                    type : 'get',
                    url : '<?= $this->Url->build(['action' => 'ajax']) ?>',
                    data : {
                        email : this.value
                    },
                    success : function (data) {
                        data = JSON.parse(data);
                        if (data != null) {
                            alert(data.customer.name+ '現在是' + data.customer.user.username + '的客戶,請勿重復添加');
                            that.value = ''
                        }

                    }

                })
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
    })
</script>
<?= $this->end() ?>