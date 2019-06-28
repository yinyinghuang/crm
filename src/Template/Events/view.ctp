<?php
/**
  * @var \App\View\AppView $this
  */
?>

<?= $this->start('top_nav') ?>
    <span class="navbar-brand"><?= $event->name ?>资料</span>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        <li><?= $this->Html->link(__('活动列表'), ['action' => 'index']) ?> </li>  
        <?php if (strpos($_privileges, 'e') !== false): ?>
        <li><?= $this->Html->link(__('編輯活动'), ['action' => 'edit', $event->id]) ?> </li>
        <?php endif ?>  
        <?php if (strpos($_privileges, 'd') !== false): ?>
            <li><?= $this->Form->postLink(__('刪除活动'), ['action' => 'delete', $event->id], ['confirm' => __('Are you sure you want to delete # {0}?', $event->id)]) ?> </li>
        <?php endif ?>    
        <?php if (strpos($_privileges, 'e') !== false): ?>  
        <li><?= $this->Html->link(__('新增活动'), ['action' => 'add']) ?> </li>
        <?php endif ?>
<?= $this->end() ?>

<div class="events view columns content">
    <h3 class="ui segment">
        <span class="ui huge header"><?= h($event->name) ?></span>
        <?php if (strpos($_module_privileges['Businesses'], 'i') !== false): ?>
        <span class="label add_import_btn">导入订单</span>    
        <?php endif ?>
        <?php if (strpos($_module_privileges['Businesses'], 'o') !== false): ?>
        <a href="<?= $this->Url->build(['controller' => 'Businesses','action' => 'export',$event->id])?>" class="label">导出订单</a> 
        <?php endif ?>        
    </h3>



    <div class="display-none ui segment input-filed" id="add_import"> 
        <h4 class="ui horizontal divider header add_images_btn"><i class="users icon"></i>导入客户</h4>        
        <?= $this->Form->create(null, ['url' => ['controller' => 'Businesses','action' => 'import'],'type' => 'file','class' => 'form-horizontal group-border-dashed','id' => 'form']) ?>
        <input type="hidden" value="<?= $event->id?>" name="event_id">
        <div class="form-group" id="example">
          <label class="control-label col-md-3 col-sm-3 col-xs-12">下载范本</label>
          <div class="col-md-6 col-sm-6 col-xs-12">
              <?php echo $this->Html->link('下载 import_businesses.xlsx', '/files/import_businesses.xlsx', array('class' => 'form-control'));?>
          </div>
        </div>
        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12">上传 Excel 文件 (.xls or .xlsx)</label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <?php echo $this->Form->input("File.import_file", array('label' => false, 'div' => false, 'class' => "form-control", 'type' => 'file', 'accept' => '.xls,.xlsx','id' => 'file'));?>
            </div>
        </div>
        <?= $this->Form->button(__('提交'),['class' => ['btn','btn-primary'],'id' => 'submit']) ?>
        <?= $this->Form->end() ?>
    </div>

    <div class="related col-md-6 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h4><?= __('数据统计') ?></h4>
                <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <table class="ui table celled striped compact unstackable ">
                    <tr>
                        <th scope="row"><?= __('昨日新增客户') ?></th>
                        <td><?=  $event->new_customer_count?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?= __('参加总人数') ?></th>
                        <td><?= h($event->total) ?>
                        <?php if (array_key_exists('Campaigns', $_module_privileges) && $event->total): ?>           
                                                                        
                            <?= $this->Form->postLink(__('群发短信'), ['action' => 'campaign', 'sms','?'=> ['event_id'=>$event->id]],['class' => 'label']) ?>
                            <?= $this->Form->postLink(__('群发电邮'), ['action' => 'campaign', 'email','?'=> ['event_id'=>$event->id]],['class' => 'label']) ?>
                            <?= $this->Form->postLink(__('群发彩信'), ['action' => 'campaign', 'mms','?'=> ['event_id'=>$event->id]],['class' => 'label']) ?>
                        <?php endif ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?= __('进行中') ?></th>
                        <td><?= $this->Number->format($event->ing) ?>  
                        <?php if (array_key_exists('Campaigns', $_module_privileges) && $event->ing): ?>          
                                                                            
                            <?= $this->Form->postLink(__('群发短信'), ['action' => 'campaign', 'sms','?'=> ['event_id'=>$event->id,'state[]' => 0]],['class' => 'label']) ?>
                            <?= $this->Form->postLink(__('群发电邮'), ['action' => 'campaign', 'email','?'=> ['event_id'=>$event->id,'state[]' => 0]],['class' => 'label']) ?>
                            <?= $this->Form->postLink(__('群发彩信'), ['action' => 'campaign', 'mms','?'=> ['event_id'=>$event->id,'state[]' => 0]],['class' => 'label']) ?>
                        <?php endif ?>   
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?= __('已成交') ?></th>
                        <td><?= h($event->signed) ?>
                        <?php if (array_key_exists('Campaigns', $_module_privileges) && $event->signed): ?>                                
                            
                            <?= $this->Form->postLink(__('群发短信'), ['action' => 'campaign', 'sms','?'=> ['event_id'=>$event->id,'state[]' => 2]],['class' => 'label']) ?>
                            <?= $this->Form->postLink(__('群发电邮'), ['action' => 'campaign', 'email','?'=> ['event_id'=>$event->id,'state[]' => 2]],['class' => 'label']) ?>
                            <?= $this->Form->postLink(__('群发彩信'), ['action' => 'campaign', 'mms','?'=> ['event_id'=>$event->id,'state[]' => 2]],['class' => 'label']) ?>
                        <?php endif ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?= __('失败') ?></th>
                        <td><?= h($event->closed) ?>
                        <?php if (array_key_exists('Campaigns', $_module_privileges) && $event->closed): ?>             
                            <?= $this->Form->postLink(__('群发短信'), ['action' => 'campaign', 'sms','?'=> ['event_id'=>$event->id,'state[]' => 1]],['class' => 'label']) ?>
                            <?= $this->Form->postLink(__('群发电邮'), ['action' => 'campaign', 'email','?'=> ['event_id'=>$event->id,'state[]' => 1]],['class' => 'label']) ?>
                            <?= $this->Form->postLink(__('群发彩信'), ['action' => 'campaign', 'mms','?'=> ['event_id'=>$event->id,'state[]' => 1]],['class' => 'label']) ?>
                        <?php endif ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="related col-md-6 col-xs-12">
        <div class="x_panel">
            <div class="x_title">
                <h4><?= __('活动情况') ?></h4>
                <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content" >
                <table class="ui table celled striped compact unstackable ">
                    <tr>
                        <th scope="row"><?= __('活动类型') ?></th>
                        <td><?= $event->has('event_type') ? $this->Html->link($event->event_type->name, ['controller' => 'EventTypes', 'action' => 'view', $event->event_type->id]) : '' ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?= __('活动名称') ?></th>
                        <td><?= h($event->name) ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?= __('活动内容') ?></th>
                        <td><?= h($event->content) ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?= __('Id') ?></th>
                        <td><?= $this->Number->format($event->id) ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?= __('开始时间') ?></th>
                        <td><?= h($event->start_time) ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?= __('结束时间') ?></th>
                        <td><?= h($event->end_time) ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?= __('创建时间') ?></th>
                        <td><?= h($event->created) ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?= __('更新时间') ?></th>
                        <td><?= h($event->modified) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="related">
        <div class="x_panel">
            <div class="x_title">
                <h4><?= __('订单列表') ?></h4>
                <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <?php if (!empty($event->businesses)): ?>

                <table cellpadding="0" cellspacing="0" class="ui table celled striped compact ">
                    <thead>
                    <tr>
                        <th scope="col"><?= __('Id') ?></th>
                        <th scope="col"><?= __('客户') ?></th>
                        <th scope="col"><?= __('状态') ?></th>
                        <th scope="col"><?= __('业务员') ?></th>
                        <th scope="col"><?= __('最新进展') ?></th>
                        <th scope="col"><?= __('更新时间') ?></th>
                        <th scope="col" class="actions"><?= __('Actions') ?></th>
                    </tr>
                    </thead>
                    <tbody id="main_related" data-controller='events' data-model="Businesses">
                        <?php foreach ($event->businesses as $businesses): ?>
                        <tr>
                            <td><?= h($businesses->id) ?></td>
                            <td><?= $this->Html->link($businesses->customer['name'], ['controller' => 'Customers', 'action' => 'view', $businesses->customer['id']]) ?></td>
                            <td><?= h($stateArr[$businesses->state]) ?></td>
                            <td><?= $this->Html->link($businesses->user['username'], ['controller' => 'Users', 'action' => 'view', $businesses->user['id']]) ?></td>
                            <td><?= isset($businesses->business_statuses[0])? $businesses->business_statuses[0]['status']:'' ?></td>                        
                            <td><?= h($businesses->modified) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Businesses', 'action' => 'view', $businesses->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Businesses', 'action' => 'edit', $businesses->id]) ?>
                                <?php if (strpos($_privileges, 'e') !== false): ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Businesses', 'action' => 'delete', $businesses->id], ['confirm' => __('Are you sure you want to delete # {0}?', $businesses->id)]) ?>    
                                <?php endif ?>
                                
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
        
                </table>
                
                    <a href="<?= $this->Url->build([
                            'controller' => 'Businesses',
                            'action' => 'index',
                            '?' =>['event_id' => $event->id]
                        ])?>" class="ui button primary">查看更多</a>
               
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->start('script') ?>
<script type="text/javascript">
    $(function(){
        $add = {};
        ['import'].forEach(function(val){
            $add[val] = $('#add_'+val);
            $(".add_"+val+"_btn").on('click',function () {
                $add[val].is(":visible") ? $add[val].slideUp() : $add[val].slideDown();
                $add[val].siblings('.input-filed').slideUp();
            });
        });
        $('#form').on('submit', function(){
          $('#submit').hide();
          new PNotify({
              text: '處理中，請不要關閉頁面',
              type: 'info',
              styling: 'bootstrap3',
              delay: 300000,
              width:'280px'
          });
          return true;
        });

          $('#file').on('click', function(){
            $('#submit').show();
          });
    })
</script>
<?= $this->end() ?>

