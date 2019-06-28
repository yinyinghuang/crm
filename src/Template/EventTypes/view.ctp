<?php
/**
  * @var \App\View\AppView $this
  */
?>
<?= $this->start('top_nav') ?>
    <span class="navbar-brand">活动类型资料</span>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        <li><?= $this->Html->link(__('編輯活动类型'), ['action' => 'edit', $eventType->id]) ?> </li>
        <?php if (strpos($_privileges, 'd') !== false): ?>
            <li><?= $this->Form->postLink(__('刪除活动类型'), ['action' => 'delete', $eventType->id], ['confirm' => __('Are you sure you want to delete # {0}?', $eventType->id)]) ?> </li>
        <?php endif ?>        
        <li><?= $this->Html->link(__('活动类型列表'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('新增活动类型'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('活动列表'), ['controller' => 'Events','action' => 'add']) ?> </li>
<?= $this->end() ?>


<div class="eventTypes view columns content">
    <h3 class="ui segment"><?= h($eventType->name) ?></h3>
    <div class="related">
        <div class="x_panel">
            <div class="x_title">
                <h4><?= __('基本信息') ?></h4>
                <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content" style="display: none;">
                <table class="ui table celled striped compact unstackable">
                    <tr>
                        <th scope="row"><?= __('名称') ?></th>
                        <td><?= h($eventType->name) ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?= __('Id') ?></th>
                        <td><?= $this->Number->format($eventType->id) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="related">
        <div class="x_panel">
            <div class="x_title">
                <h4><?= __('相关活动列表') ?>
                    <a href="<?= $this->Url->build([
                        'controller' => 'Events',
                        'action' => 'index',
                        "?" => ['event_type_id' => $eventType->id]
                        ])?>"><i class="label "><?= count($eventType->events)?></i></a>
                </h4>
                <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content" >
                <?php if (!empty($eventType->events)): ?>
                <table cellpadding="0" cellspacing="0" class="ui table celled striped compact ">
                    <tr>
                        <th scope="col"><?= __('Id') ?></th>
                        <th scope="col"><?= __('活动名称') ?></th>
                        <th scope="col"><?= __('开始时间') ?></th>
                        <th scope="col"><?= __('结束时间') ?></th>
                        <th scope="col"><?= __('参与人数') ?></th>
                        <th scope="col"><?= __('更新时间') ?></th>
                    </tr>
                    <?php foreach ($eventType->events as $events): ?>
                    <tr>
                        <td><?= h($events->id) ?></td>
                        <td><?= h($events->name) ?></td>
                        <td><?= h($events->start_time) ?></td>
                        <td><?= h($events->end_time) ?></td>
                        <td>
                            <?= $this->Html->link(count($events->businesses), ['controller' => 'Events', 'action' => 'view', $events->id]) ?>
                        <td><?= h($events->modified) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
