<?php
/**
  * @var \App\View\AppView $this
  */
?>
<?= $this->start('top_nav') ?>
    <span class="navbar-brand">員工资料</span>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        <?php if (strpos($_privileges, 'e') !== false): ?>
            <li><?= $this->Html->link(__('編輯員工'), ['action' => 'edit', $user->id]) ?> </li>
        <?php endif ?>        
        <?php if (strpos($_privileges, 'd') !== false): ?>
            <li><?= $this->Form->postLink(__('刪除員工'), ['action' => 'delete', $user->id], ['confirm' => __('Are you sure you want to delete {0}?', $user->username)]) ?> </li>
        <?php endif ?>        
        <li><?= $this->Html->link(__('員工列表'), ['action' => 'index']) ?> </li>
        <?php if (strpos($_privileges, 'a') !== false): ?>
            <li><?= $this->Html->link(__('新增員工'), ['action' => 'add']) ?> </li>
        <?php endif ?>  
        <?php if (strpos($_privileges, 'i')): ?>            
        <li><?= $this->Html->link(__('导入客戶'), ['action' => 'import']) ?></li>
        <?php endif ?>
        <?php if (strpos($_privileges, 'o')): ?>            
        <li><?= $this->Html->link(__('导出客戶'), ['action' => 'export']) ?></li>
        <?php endif ?>
<?= $this->end() ?>
<div class="users view columns content">
    <div class="ui segment">
        <h3 class="header ui"><?= h($user->username) ?></h3>
    </div>
        
    <div class="related">
        
        <div class="x_panel">
            <div class="x_title">
                <h4><?= __('员工信息') ?></h4>
                <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <table class="ui table celled striped compact unstackable">
                    <tr>
                        <th scope="row"><?= __('编号') ?></th>
                        <td><?= h($user->id) ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?= __('分組') ?></th>
                        <td><?= $user->has('department') ? $this->Html->link($user->department->name, ['controller' => 'Departments', 'action' => 'view', $user->department->id]) : '' ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?= __('職位') ?></th>
                        <td><?= $user->has('role') ? $this->Html->link($user->role->name, ['controller' => 'Roles', 'action' => 'view', $user->role->id]) : '' ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?= __('性別') ?></th>
                        <td><?= h($user->gender ? __('女') : __('男')) ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?= __('電話') ?></th>
                        <td><?php if ($user->mobile): ?>
                            <a href="tel:<?= '+'.h($user->country_code['country_code'].'-'. $user->mobile) ?>"><?= '+'.h($user->country_code['country_code'].'-'. $user->mobile) ?></a>
                        <?php endif ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?= __('創建時間') ?></th>
                        <td><?= h($user->created) ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?= __('修改時間') ?></th>
                        <td><?= h($user->modified) ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?= __('狀態') ?></th>
                        <td><?= $user->state ? __('在職') : __('離職'); ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?= __('客戶數') ?></th>
                        <td><?= $count_customer; ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?= __('订单数') ?></th>
                        <td><?= $count_business; ?></td>
                    </tr>
                </table>
            </div>
        </div>        
        
    </div>


    <div class="related">
        <?php if (!empty($user->customer_commissions)): ?>
        <div class="x_panel">
            <div class="x_title">
                <h4><?= __('相關業績') ?></h4>
                <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">       
                <table cellpadding="0" cellspacing="0" class="ui table celled striped compact ">
                    <tr>
                        <th scope="col"><?= __('客戶') ?></th>
                        <th scope="col"><?= __('業績') ?></th>
                    </tr>
                    <?php foreach ($user->customer_commissions as $customerCommissions): ?>
                    <tr>
                        <td><?= h($customerCommissions->customer['name']) ?></td>
                        <td><?= h($customerCommissions->commission) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>            
        <?php endif; ?>
    </div>
    <div class="related">
        <?php if (!empty($user->customers)): ?>
        <div class="container">
            <div class="row">

                <div class="col-md-6 col-xs-12">
                    <canvas id="canvas-customer"></canvas>
                </div>
                <div class="col-md-6 col-xs-12">
                    <canvas id="canvas-business"></canvas>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <div class="related">
        <?php if (!empty($user->customers)): ?>
        <div class="x_panel">
            <div class="x_title">
                <h4><?= __('相關进行中订单') ?></h4>
                <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <table cellpadding="0" cellspacing="0" class="ui table celled striped compact ">
                    <tr>                        
                        <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                        <th scope="col"><?= $this->Paginator->sort('customer_id',['客户']) ?></th>
                        <th scope="col"><?= $this->Paginator->sort('event_id',['活动']) ?></th>
                        <th scope="col"><?= $this->Paginator->sort('state',['状态']) ?></th>
                        <th scope="col">更新时间</th>
                        <th scope="col" class="actions"><?= __('Actions') ?></th>
                    </tr><?php foreach ($user->businesses as $business): ?>
                    <tr>
                        <td><?= $business->id ?></td>
                        <td><span class="mobile only ui grid"><i class="icon users"></i></span><?= $business->has('customer') ? $this->Html->link($business->customer->name, ['controller' => 'Customers', 'action' => 'view', $business->customer->id]) : '' ?></td>
                        <td><span class="mobile only ui grid"><i class="icon cube"></i></span><?= $business->has('event') ? $this->Html->link($business->event->name, ['controller' => 'Events', 'action' => 'view', $business->event->id]) : '' ?></td>
                        <td class="<?= $stateColorArr[$business->state] ?>"><?= $stateArr[$business->state] ?></td>
                        <td><span class="mobile only ui grid"><i class="icon calendar"></i></span><?= h($business->modified) ?></td>
                        <td class="actions">
                            <?= $this->Html->link(__('进展列表'), ['controller' => 'Businesses','action' => 'view', $business->id]) ?>
                            <?php if (strpos($_privileges, 'd') !== false): ?>
                            <?= $this->Form->postLink(__('Delete'), ['controller' => 'Businesses','action' => 'delete', $business->id], ['confirm' => __('Are you sure you want to delete # {0}?', $business->id)]) ?>
                            <?php endif ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                </table>
                <div class="">
                    <a href="<?= $this->Url->build([
                        'controller' => 'Businesses',
                        'action' => 'index',
                        '?' =>['user_id' => $user->id]])?>" class="ui button blue">查看更多</a>
                </div>
            </div>
        </div>        
        <?php endif; ?>
    </div>
    <div class="related">
        <?php if (!empty($user->customers)): ?>
        <div class="x_panel">
            <div class="x_title">
                <h4><?= __('相關客戶') ?></h4>
                <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <table cellpadding="0" cellspacing="0" class="ui table celled striped compact ">
                    <tr>                        
                        <th scope="col"><?= __('編號') ?></th>
                        <th scope="col"><?= __('姓名') ?></th>
                        <th scope="col"><?= __('電話') ?></th>
                        <th scope="col"><?= __('進展') ?></th>
                        <th scope="col"><?= __('更新時間') ?></th>
                        <th scope="col" class="actions"><?= __('Actions') ?></th>
                    </tr>
                    <?php foreach ($user->customers as $customer): ?>
                    <tr>
                        <td><?= $customer->id ?></td>
                        <td><span class="mobile only ui grid"><i class="users icon"></i></span><?= h($customer->name) ?></td>
                        <td>
                            <span class="mobile only ui grid"><i class="mobile icon"></i></span>
                            <a href="tel:<?= '+' . $customer['customer_mobiles'][0]['country_code']['country_code'] . '-' . $customer['customer_mobiles'][0]['mobile'] ?>">
                                <?= '+' . $customer['customer_mobiles'][0]['country_code']['country_code']  . '-' . $customer['customer_mobiles'][0]['mobile'] ?>
                                    
                            </a>
                            <a href="https://api.whatsapp.com/send?phone=<?=  $customer['customer_mobiles'][0]['country_code']['country_code'] . $customer['customer_mobiles'][0]['mobile'] ?>"><i class="whatsapp icon"></i></a>
                        </td>
                        <td><span class="mobile only ui grid"><i class="list layout icon"></i></span><?= h(isset($customer->business_statuses[0]['status']) ? $customer->business_statuses[0]['status'] : '') ?></td>
                        <td><span class="mobile only ui grid"><i class="sync icon"></i></span><?= h($customer->modified) ?></td>
                        <td class="actions">
                            <?= $this->Html->link(__('View'), ['controller' => 'Customers', 'action' => 'view', $customer->id]) ?>
                            <?php if (strpos($_privileges, 'e') !== false): ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Customers', 'action' => 'edit', $customer->id]) ?>
                            <?php endif ?>
                            <?php if (strpos($_privileges, 'd') !== false): ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Customers', 'action' => 'delete', $customer->id], ['confirm' => __('Are you sure you want to delete # {0}?', $customer->id)]) ?>
                            <?php endif ?>                    
                        </td>
                    </tr>
                    <?php endforeach; ?>

                </table>
                <div class="">
                    <a href="<?= $this->Url->build([
                        'controller' => 'customers',
                        'action' => 'index',
                        '?' =>['user_id' => $user->id]])?>" class="ui button blue">查看更多</a>
                </div>
            </div>
        </div>        
        <?php endif; ?>
    </div>
</div>
<div class="clearfix"></div>


<?= $this->start('script') ?>

<?= $this->Html->script('vendors/Chart.min.js') ?>
<script>
    $(function(){
        var obj = {};
        obj.config_customer = {
            type: 'line',
            data: {
                labels: [
                            <?php foreach ($week_lately_arr as $value): ?>
                                <?= $value.','?>
                            <?php endforeach ?>
                        ],
                datasets:[
                    {
                        label: '<?= $labelArr['total'][0]?>',
                        backgroundColor: '<?= $labelArr['total'][1]?>',
                        borderColor: '<?= $labelArr['total'][1]?>',
                        data: [
                            <?php foreach ($week_lately_arr as $week): ?>
                                <?= (isset($customerData[$week]) ? $customerData[$week]['total']:0).','?>
                            <?php endforeach ?>
                        ],
                        fill: false,
                    }
                ]
            },
            options: {
                responsive: true,
                title: {
                    display: true,
                    text:'客户数据'
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: '周数'
                        }
                    }],
                    yAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: '人数'
                        }
                    }]
                }
            }
        };
        obj.config_business = {
            type: 'line',
            data: {
                labels: [
                            <?php foreach ($week_lately_arr as $value): ?>
                                <?= $value.','?>
                            <?php endforeach ?>
                        ],
                datasets:[
                    <?php foreach ($labelArr as $key =>  $value): ?>
                    {
                        label: '<?= $value[0]?>',
                        backgroundColor: '<?= $value[1]?>',
                        borderColor: '<?= $value[1]?>',
                        data: [
                            <?php foreach ($week_lately_arr as $week): ?>
                                <?= (isset($businessData[$week][$key]) ? $businessData[$week][$key] :0).','?>
                            <?php endforeach ?>
                        ],
                        fill: false,
                    }, 
                    <?php endforeach ?>
                ]
            },
            options: {
                responsive: true,
                title: {
                    display: true,
                    text:'订单数据'
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: '时间'
                        }
                    }],
                    yAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: '人数'
                        }
                    }]
                }
            }
        };

        ['customer','business'].forEach(function(type){
            obj['ctx_' + type] = document.getElementById('canvas-'+type).getContext('2d');
            obj['myLine_'+type] = new Chart(obj['ctx_' + type], obj['config_' + type]);
        });
                
    });
</script>
<?= $this->end() ?>

