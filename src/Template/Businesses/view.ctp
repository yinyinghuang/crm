<?php
/**
  * @var \App\View\AppView $this
  */
?>
<?= $this->start('top_nav') ?>
    <span class="navbar-brand"><?= $business->name ?>订单资料</span>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        <li><?= $this->Html->link(__('編輯订单'), ['action' => 'edit', $business->id]) ?> </li>
        <?php if (strpos($_privileges, 'd') !== false): ?>
            <li><?= $this->Form->postLink(__('刪除订单'), ['action' => 'delete', $business->id], ['confirm' => __('Are you sure you want to delete # {0}?', $business->id)]) ?> </li>
        <?php endif ?>        
        <li><?= $this->Html->link(__('订单列表'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('新增订单'), ['action' => 'add']) ?> </li>
<?= $this->end() ?>

<div class="businesses view columns content">
    <h3 class="ui segment">
        <span class="ui huge header">订单<?= h($business->id) ?> </span>
        <span class="label add_status_btn">更新進展</span>            
    </h3>

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
    <div class="related">
        <div class="x_panel">
            <div class="x_title">
                <h4><?= __('基本信息') ?></h4>
                <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
               <table class="ui table celled striped compact unstackable">
                   <tr>
                       <th scope="row"><?= __('活动') ?></th>
                       <td><?= $business->has('event') ? $this->Html->link($business->event->name, ['controller' => 'Events', 'action' => 'view', $business->event->id]) : '' ?></td>
                   </tr>
                   <tr>
                       <th scope="row"><?= __('客户') ?></th>
                       <td><?= $business->has('customer') ? $this->Html->link($business->customer->name, ['controller' => 'Customers', 'action' => 'view', $business->customer->id]) : '' ?>
                         <a href="tel:<?= '+' . $business->customer['customer_mobiles'][0]['country_code']['country_code'] . '-' . $business->customer['customer_mobiles'][0]['mobile'] ?>">
                             (<?= '+' . $business->customer['customer_mobiles'][0]['country_code']['country_code']  . '-' . $business->customer['customer_mobiles'][0]['mobile'] ?>)                                 
                         </a>
                         <a href="https://api.whatsapp.com/send?phone=<?=  $business->customer['customer_mobiles'][0]['country_code']['country_code'] . $business->customer['customer_mobiles'][0]['mobile'] ?>"><i class="whatsapp icon"></i></a>

                       </td>
                   </tr>
                   <tr>
                       <th scope="row"><?= __('业务员') ?></th>
                       <td><?= $business->has('user') ? $this->Html->link($business->user->username, ['controller' => 'Users', 'action' => 'view', $business->user->id]) : '' ?></td>
                   </tr>
                   <tr>
                       <th scope="row"><?= __('Id') ?></th>
                       <td><?= $this->Number->format($business->id) ?></td>
                   </tr>
                   <tr>
                       <th scope="row"><?= __('状态') ?></th>
                       <td class="<?= $stateColorArr[$business->state] ?>"><?= $stateArr[$business->state] ?></td>
                   </tr>
                   <tr>
                       <th scope="row"><?= __('参与时间') ?></th>
                       <td><?= h($business->parted) ?></td>
                   </tr>
                   <tr>
                       <th scope="row"><?= __('创建时间') ?></th>
                       <td><?= h($business->created) ?></td>
                   </tr>
                   <tr>
                       <th scope="row"><?= __('更新时间') ?></th>
                       <td><?= h($business->modified) ?></td>
                   </tr>
               </table>
            </div>
        </div>
    </div>


    <div class="related">
        <?php if (!empty($business->business_statuses)): ?>
        <div class="x_panel">
            <div class="x_title">
                <h4><?= __('跟进列表') ?></h4>
                <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
            <?php if ($business->business_status_count > 20): ?>
                <a href="<?= $this->Url->build([
                        'controller' => 'BusinessStatuses',
                        'action' => 'index',
                        '?' => ['business_id'=>$business->id]
                    ])?>" class="ui button primary">查看更多</a>
            <?php endif ?>
                <table cellpadding="0" cellspacing="0" class="ui table celled striped compact ">
                    <tr>
                        <th scope="col"><?= __('Id') ?></th>
                        <th scope="col"><?= __('进展') ?></th>
                        <th scope="col"><?= __('更新时间') ?></th>
                        <th scope="col"><?= __('业务员') ?></th>
                        <th scope="col"><?= __('下次联系') ?></th>
                        <th scope="col"><?= __('备注') ?></th>
                        <th scope="col" class="actions"><?= __('Actions') ?></th>
                    </tr>
                    <?php foreach ($business->business_statuses as $businessStatus): ?>
                    <tr>
                        <td><?= h($businessStatus->id) ?></td>
                        <td><span class="mobile only ui grid"><i class="icon list"></i></span><?= h($businessStatus->status) ?>
                          <?php if (count($businessStatus->customer_images)): ?>
                             <label class="customer_images_button"><i class="icon image large"></i></label> 
                          <?php endif ?>
                        </td>
                        <td><span class="mobile only ui grid"><i class="sync icon"></i></span><?= h($businessStatus->modified) ?></td>
                        <td><span class="mobile only ui grid"><i class="user icon"></i></span><?= h($businessStatus->user['username']) ?></td>
                        <td><?php if ($businessStatus->next_contact_time): ?>
                          <span class="mobile only ui grid"><i class="calendar icon"></i></span><?= h($businessStatus->next_contact_time) ?>
                        <?php endif ?></td>
                        <td><?= h($businessStatus->next_note) ?></td>
                        <td class="actions">
                        <?php if (strpos($_privileges, 'e') !== false): ?>
                          <?= $this->Html->link(__('Edit'), ['controller' => 'BusinessStatuses', 'action' => 'edit', $businessStatus->id,'?' =>['business_id' => $business->id]]) ?>
                        <?php endif ?>
                        <?php if (strpos($_privileges, 'd') !== false): ?>
                          <?= $this->Form->postLink(__('Delete'), ['controller' => 'BusinessStatuses', 'action' => 'delete', $businessStatus->id,'?' =>['business_id' => $business->id]], ['confirm' => __('Are you sure you want to delete # {0}?', $businessStatus->id)]) ?>
                        <?php endif ?>
                            
                        </td>
                    </tr>

                    <?php if (count($businessStatus->customer_images)): ?>
                      
                    <tr style="display: none!important;">
                        <td colspan="7">
                            <table class="ui table celled striped compact green">
                                <thead>
                                    <tr>
                                        <th scope="col"><?= __('图片編號') ?></th>
                                        <th scope="col"><?= __('图片名称') ?></th>
                                        <th scope="col"><?= __('上传时间') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($businessStatus->customer_images as $customerImage): ?>  
                                <tr>
                                    <td><?= $customerImage->id ?></td>
                                    <td><span class="mobile only ui grid"><i class="image icon"></i></span><a href="<?= $customerImage->path.$customerImage->name.'.'.$customerImage->ext?>"><?= $customerImage->name ?></a></td>                
                                    <td><span class="mobile only ui grid"><i class="sync icon"></i></span><?= h($customerImage->created)?></td>
                                </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </td>
                        
                    </tr>

                    <?php endif ?>
                    <?php endforeach; ?>
                </table>
            <?php if ($business->business_status_count > 20): ?>
                <a href="<?= $this->Url->build([
                        'controller' => 'BusinessStatuses',
                        'action' => 'index',
                        '?' => ['business_id'=>$business->id]
                    ])?>" class="ui button primary">查看更多</a>
            <?php endif ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
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
