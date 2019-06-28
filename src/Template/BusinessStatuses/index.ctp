<?php
/**
  * @var \App\View\AppView $this
  */
?>

<?= $this->start('top_nav') ?>
    <span class="navbar-brand">进展列表</span>
    <i class="fa fa-search" id="show-search"></i>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        <li><?= $this->Html->link(__('订单详情'), ['controller' => 'Businesses','action' => 'view',$business->id]) ?></li>
        <?php if (strpos($_privileges, 'd') !== false): ?>
            <li><?= $this->Form->postLink(__('刪除订单'), ['controller' => 'Businesses','action' => 'delete', $business->id], ['confirm' => __('Are you sure you want to delete # {0}?', $business->id)]) ?> </li>
        <?php endif ?>        
        <li><?= $this->Html->link(__('新增进展'), ['action' => 'add','?' =>['business_id' => $business->id]]) ?></li>
<?= $this->end() ?>

<div class="businessStatuses index columns content">
    <div class="search_box  ui segment" style="display:none">
        <form action="<?= $this->Url->build(['action' => 'index'])?>" role="form">
        <input type="hidden" name="business_id" value="<?= $business->id?>">
        <div class="row form-group">
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
            
            <div class="col-md-2">
                <button class="btn btn-primary">搜索</button>
            </div>
        </div>
        </form>
    </div>
    <table class="ui table celled striped compact  striped compact unstackable">
        <tr>
            <th>订单id</th>
            <td><?= $this->Html->link($business['id'], ['controller' => 'Businesses','action' => 'view',$business['id']]) ?>
            </td>
        </tr>
        <tr>
            <th>活动</th>
            <td><?= $this->Html->link($business['event']['name'], ['controller' => 'Events','action' => 'view',$business['event']['id']]) ?></td>
        </tr>
        <tr>
            <th>客户</th>
            <td><?= $this->Html->link($business['customer']['name'], ['controller' => 'Customers','action' => 'view',$business['customer']['id']]) ?></td>
        </tr>
        <tr>
            <th>业务员</th>
            <td><?= $this->Html->link($business['user']['username'], ['controller' => 'Users','action' => 'view',$business['user']['id']]) ?></td>
        </tr>
        <tr>
            <th>状态</th>
            <td class="<?= $stateColorArr[$business['state']]?>"><?= $stateArr[$business['state']]?>
        <span class="label add_status_btn">更新進展</span>  </td>
        </tr>
    </table>

    <div class="display-none ui segment input-filed" id="add_status"> 
        <h4 class="ui horizontal divider header add_status_btn"><i class="icon list"></i>更新進展</h4>
        <?= $this->Form->create(null, ['url' => ['controller' => 'BusinessStatuses','action' => 'add'],'type' => 'file']) ?>
        <fieldset>
            <?php
                echo $this->Form->control('customer_id',['type' => 'hidden','value' => $business->customer_id]);
                echo $this->Form->control('user_id',['type' => 'hidden','value' => $_user['id']]);
                echo $this->Form->control('business_id',['type' => 'hidden','value' => $business->id]);
                echo $this->Form->control('status',['label' => '进展','type' => 'textarea','required' => true]);
                echo $this->Form->control('next_contact_time',['label' => '下次联系时间','class' => 'timepicker','readonly' => true]);
                echo $this->Form->control('next_note',['label' => '下次联系备注','type' => 'textarea']);
                echo $this->Form->control('state',['type' => 'radio','options' => $stateArr,'label' => '状态','default' => $business->state]); 
                echo $this->Form->control('images[]', ['label' => '图片', 'type' => 'file','class' => 'images','multiple' => true,]); 
            ?>
        </fieldset>
        <?= $this->Form->button(__('提交'),['class' => ['btn','btn-primary']]) ?>
        <?= $this->Form->end() ?>
    </div>
    <table cellpadding="0" cellspacing="0" class="ui table celled striped compact ">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col">进展</th>
                <th scope="col"><?= $this->Paginator->sort('user_id',['业务员']) ?></th>
                <th scope="col"><?= $this->Paginator->sort('next_contact_time',['下次联系']) ?></th>
                <th scope="col"><?= $this->Paginator->sort('next_note',['备注']) ?></th>
                <th scope="col"><?= $this->Paginator->sort('modified',['更新时间']) ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody id="main" data-controller="business-statuses">
        </tbody>
    </table>
    <div id="message" class="ui segment message text-center">加载中...</div>
</div>

<?= $this->start('css') ?>
<?= $this->Html->css('../js/vendors/bootstrap-fileinput-master/css/fileinput.min.css') ?>
<?= $this->end() ?>
<?= $this->start('script') ?>
<?= $this->Html->script('vendors/bootstrap-fileinput-master/js/fileinput.min.js') ?>
<script type="text/javascript">
    $(function(){
        $add = {};
        ['status'].forEach(function(val){
            $add[val] = $('#add_'+val);
            $(".add_"+val+"_btn").on('click',function () {
                $add[val].is(":visible") ? $add[val].slideUp() : $add[val].slideDown();
                $add[val].siblings('.input-filed').slideUp();
            });
        });
        

        $(".images").fileinput({
            maxFileCount: 10,
            allowedFileTypes: ["image"],
             maxFileCount:10,
            showPreview :false,
        }); 

        $('body').on('click','.customer_images_button',function(){
            $tr = $(this).parent().parent().next('tr');
            $tr.is(':visible') ? $tr[0].style.cssText = 'display:none!important' :$tr[0].style.cssText = '';
        })
    })
</script>
<?= $this->end() ?>
