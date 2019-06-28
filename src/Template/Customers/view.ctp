<?php
/**
  * @var \App\View\AppView $this
  */
?>
<?= $this->start('top_nav') ?>
    <span class="navbar-brand"><?= $customer->name ?>客户资料</span>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        <?php if (strpos($_privileges, 'e') !== false): ?>
        <li><?= $this->Html->link(__('編輯客戶'), ['action' => 'edit', $customer->id]) ?> </li>
        <?php endif ?>             
        <?php if (strpos($_privileges, 'd') !== false): ?>
            <li><?= $this->Form->postLink(__('刪除客戶'), ['action' => 'delete', $customer->id], ['confirm' => __('Are you sure you want to delete # {0}?', $customer->id)]) ?> </li>
        <?php endif ?>        
        <li><?= $this->Html->link(__('客戶列表'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('新增客戶'), ['action' => 'add']) ?> </li>
<?= $this->end() ?>

<div class="customers view columns content">
    <h3 class="ui segment">
        <span class="ui huge header"><?= h($customer->name) ?></span>   
        <span class="label add_status_btn">更新進展</span>
    </h3>
    
    
    <div class="display-none ui segment input-filed" id="add_status"> 
        <h4 class="ui horizontal divider header add_status_btn"><i class="icon list"></i>更新進展</h4>
        <?= $this->Form->create(null, ['url' => ['controller' => 'BusinessStatuses','action' => 'add'],'type' => 'file']) ?>
        <fieldset>
            <?php
                echo $this->Form->control('customer_id',['type' => 'hidden','value' => $customer->id]);
                echo $this->Form->control('user_id',['type' => 'hidden','value' => $_user['id']]);
                echo $this->Form->control('status',['label' => '進展','type' => 'textarea','required' => true]);
                echo $this->Form->control('next_contact_time',['label' => '下次联系时间','required' => false,'class' => 'timepicker']);
                echo $this->Form->control('next_note',['label' => '下次联系备注','required' => false,'type' => 'textarea']);
                echo $this->Form->control('state',['type' => 'radio','options' => $stateArr,'label' => '状态','class'=>'ui  radio','default' => 0]); 
            ?>
        </fieldset>
        <?= $this->Form->button(__('提交'),['class' => ['btn','btn-primary']]) ?>
        <?= $this->Form->end() ?>
    </div>

    <div class="related">
        <?php if (!empty($customer->business_statuses)): ?>
        <div class="x_panel">
            <div class="x_title">
                <h4><?= __('進展') ?></h4>
                <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
            <?php if ($customer->business_status_count > 20): ?>
                <a href="<?= $this->Url->build([
                        'controller' => 'BusinessStatuses',
                        'action' => 'index',
                        '?' => ['customer_id'=>$customer->id]
                    ])?>" class="ui button primary">查看更多</a>
            <?php endif ?>
                <table cellpadding="0" cellspacing="0" class="ui table celled striped compact ">
                    <tr>
                        <th scope="col"><?= __('Id') ?></th>
                        <th scope="col"><?= __('進展') ?></th>
                        <th scope="col"><?= __('下次联系') ?></th>
                        <th scope="col"><?= __('备注') ?></th>
                        <th scope="col"><?= __('更新时间') ?></th>
                        <th scope="col" class="actions"><?= __('Actions') ?></th>
                    </tr>
                    <?php foreach ($customer->business_statuses as $businessStatus): ?>
                    <tr>
                        <td><?= h($businessStatus->id) ?></td>
                        <td><span class="mobile only ui grid"><i class="icon list"></i></span><?= h($businessStatus->status) ?>
                          <?php if (count($businessStatus->customer_images)): ?>
                             <label class="customer_images_button"><i class="icon image large"></i></label> 
                          <?php endif ?>
                        </td>
                        <td><?php if ($businessStatus->next_contact_time): ?>
                          <span class="mobile only ui grid"><i class="calendar icon"></i></span><?= h($businessStatus->next_contact_time) ?>
                        <?php endif ?></td>
                        <td><?= h($businessStatus->next_note) ?></td>
                        <td><span class="mobile only ui grid"><i class="sync icon"></i></span><?= h($businessStatus->modified) ?></td>
                        <td class="actions">
                        <?php if (strpos($_privileges, 'e') !== false): ?>
                          <?= $this->Html->link(__('Edit'), ['controller' => 'BusinessStatuses', 'action' => 'edit', $businessStatus->id,'?' =>['business_id' => $customer->id]]) ?>
                        <?php endif ?>
                        <?php if (strpos($_privileges, 'd') !== false): ?>
                          <?= $this->Form->postLink(__('Delete'), ['controller' => 'BusinessStatuses', 'action' => 'delete', $businessStatus->id,'?' =>['business_id' => $customer->id]], ['confirm' => __('Are you sure you want to delete # {0}?', $businessStatus->id)]) ?>
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
            <?php if ($customer->business_status_count > 20): ?>
                <a href="<?= $this->Url->build([
                        'controller' => 'BusinessStatuses',
                        'action' => 'index',
                        '?' => ['customer_id'=>$customer->id]
                    ])?>" class="ui button primary">查看更多</a>
            <?php endif ?>
            </div>
        </div>
        <?php endif; ?>
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
                        <th scope="row"><?= __('客戶來源') ?></th>
                        <td><?= $customer->source; ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?= __('客戶來源detail') ?></th>
                        <td><?= $customer->source_detail; ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?= __('姓名') ?></th>
                        <td>
                            <?= h($customer->name) ?>
                            <?php if ($customer->images): ?>
                                
                                <?= $this->Html->link('相关图片', ['controller' => 'CustomerImages', 'action' => 'index', '?' => ['customer_id' => $customer->id]],['class' => ['m2em','label']]) ?>
                                <a class="ui red circular label"><?= $customer->images ?></a>
                            <?php endif ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?= __('聯絡方式') ?></th>
                        <td>
                            <table>
                                <?php foreach ($customer->customer_mobiles as $m): ?>
                                    <tr><td class="no-border">
                                        <a href="tel:<?= '+' . $m->country_code->country_code . '-' . $m->mobile ?>"><?= '+' . $m->country_code->country_code . '-' . $m->mobile ?></a>
                                        <a href="https://api.whatsapp.com/send?phone=<?=  $m->country_code->country_code . $m->mobile ?>"><i class="whatsapp icon"></i></a>
                                    </td></tr>
                                <?php endforeach ?>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?= __('郵件') ?></th>
                        <td>
                            <table>
                                <?php foreach ($customer->customer_emails as $e): ?>
                                    <tr><td class="no-border">
                                        <a href="mailto:<?= $e->email ?>"><?= $e->email ?></a>
                                    </td></tr>
                                <?php endforeach ?>
                            </table>
                        </td>
                    </tr>
                   <tr>
                       <th scope="row"><?= __('状态') ?></th>
                       <td><?= $customer->state ?></td>
                   </tr>
                    <tr>
                        <th scope="row"><?= __('Id') ?></th>
                        <td><?= $this->Number->format($customer->id) ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?= __('創建時間') ?></th>
                        <td><?= h($customer->created) ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?= __('更新时间') ?></th>
                        <td><?= h($customer->modified) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="clearfix"></div>
<?= $this->start('css') ?>
<?= $this->Html->css('../js/vendors/bootstrap-fileinput-master/css/fileinput.min.css') ?>
<?= $this->end() ?>
<?= $this->start('script') ?>
<?= $this->Html->script('vendors/bootstrap-fileinput-master/js/fileinput.min.js') ?>

<script type="text/javascript">
    $(function(){
        var $add = {};
        ['status','images','event'].forEach(function(val){
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
    })
</script>
<?= $this->end() ?>


