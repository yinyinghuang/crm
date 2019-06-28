<?php
/**
  * @var \App\View\AppView $this
  */
?>
<?= $this->start('top_nav') ?>
    <span class="navbar-brand">群发详情</span>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        <li><?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $campaign->id], ['confirm' => __('Are you sure you want to delete # {0}?', $campaign->id)]) ?> </li>
        <li><?= $this->Html->link(__('群發列表'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('群发郵件'), ['action' => 'addEmail']) ?> </li>
        <li><?= $this->Html->link(__('群发短信'), ['action' => 'addSms']) ?> </li>
        <li><?= $this->Html->link(__('群发彩信'), ['action' => 'addMms']) ?> </li>
<?= $this->end() ?>

<div class="campaigns view columns content ui segment">
    <h3><?= h($campaign->id) ?></h3>
    <table class="ui table celled striped compact unstackable">
        <tr>
            <th scope="row"><?= __('類型') ?></th>
            <td><?= $typeArr[$campaign->type] ?></td>
        </tr>
        <?php if (in_array($campaign->type, [2,3,5,6])): ?>
        <tr>
            <th scope="row"><?= __('主題') ?></th>
            <td><?= h($campaign->subject) ?></td>
        </tr>    
        <?php endif ?>
        <tr>
            <th scope="row"><?= __('內容') ?></th>
            <td><?= str_replace("\n","<br>",$campaign->content) ?></td>
        </tr>
        <?php if (!in_array($campaign->type, [3,6])): ?>        
        <tr>
            <th scope="row"><?= __('成功數') ?></th>
            <td><?= h($campaign->success) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('失敗數') ?></th>
            <td><?= h($campaign->fail) ?></td>
        </tr>
        <?php else: ?>  
         <tr>
             <th scope="row"><?= __('图片') ?></th>
             <td><a href="<?= $campaign->image ?>"><img src="<?= $campaign->image ?>" width="200"></a></td>
         </tr>
        <?php endif ?>
        <tr>
            <th scope="row"><?= __('發送總數') ?></th>
            <td><?= h($campaign->total) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('發件人') ?></th>
            <td><?= $campaign->has('user') ? $this->Html->link($campaign->user->username, ['controller' => 'Users', 'action' => 'view', $campaign->user->id]) : '' ?></td>
        </tr>
        <?php if (in_array($campaign->type, [4,5,6])): ?>
        <tr>
            <th scope="row"><?= __('收件人') ?></th>
            <td><?= $campaign->remark ?></td>
        </tr>    
        <?php endif ?>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($campaign->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('發送時間') ?></th>
            <td><?= h($campaign->created) ?>
            <?php if ($campaign->campaign_records && $campaign->type): ?>
                
            <?php endif ?></td>
        </tr>
    </table>
    <?php if ($campaign->campaign_records && !in_array($campaign->type, [3,6])): ?>
    <div class="related">
        <div class="x_panel">
            <div class="x_title">
                <h4><?= __('失敗記錄') ?></h4>
                <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <table cellpadding="0" cellspacing="0" class="table">
                    <tr>
                        <th scope="col" width="80%"><?= __('姓名') ?></th>
                        <th scope="col"><?= __('失敗原因') ?></th>
                    </tr>                    
                    <?php foreach ($campaign->campaign_records as $campaignRecords): ?>
                    <tr width="80%">
                        <td><?php foreach ($campaignRecords->customers as $value): ?>
                            <?= $value['name']?>(<?= $value['idtf']?>);
                        <?php endforeach ?></td>
                        <td><?= h($campaignRecords->result) ?><?= $this->Form->postLink(__('重發'), ['action' => 'resend', $campaignRecords->id], ['confirm' => __('确认重发?'),'class' => 'label inverse', 'id' => 'send','style' => 'display:inline-block']) ?>
  
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>    
    <?php endif ?>
    
</div>
<div class="clearfix"></div>
<?= $this->start('script') ?>
<script>
    $(function () {
        $('#send').on('sum', function(){     
            new PNotify({
                text: '發送中，請耐心等候',
                type: 'info',
                styling: 'bootstrap3',
                delay: 3000,
                width:'280px'
            });
            this.style.display = 'none';
            
        });   
    });
</script>
<?= $this->end() ?>