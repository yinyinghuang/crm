<?php foreach ($businessStatuses as $businessStatus): ?>
<tr>
    <td><?= $this->Number->format($businessStatus->id) ?></td>
    <td><span class="mobile only ui grid"><i class="icon list"></i></span><?= h($businessStatus->status) ?>
    <?php if (count($businessStatus->customer_images)): ?>
        <label class="customer_images_button"><i class="icon image "></i></label> 
    <?php endif ?></td>
    <td><span class="mobile only ui grid"><i class="icon user"></i></span><?= $businessStatus->has('user') ? $this->Html->link($businessStatus->user->username, ['controller' => 'Users', 'action' => 'view', $businessStatus->user->id]) : '' ?></td>
    <td><span class="mobile only ui grid"><i class="icon calendar"></i></span><?= h($businessStatus->next_contact_time) ?></td>
    <td><?= h($businessStatus->next_note) ?></td>
    <td><span class="mobile only ui grid"><i class="icon sync"></i></span><?= h($businessStatus->modified) ?></td>
    <td class="actions">
        <?php if (strpos($_privileges, 'e') !== false): ?>
        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $businessStatus->id,'?' =>['business_id' => $business->id]]) ?>    
        <?php endif ?>
        <?php if (strpos($_privileges, 'd') !== false): ?>
        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $businessStatus->id,'?' =>['business_id' => $business->id]], ['confirm' => __('Are you sure you want to delete # {0}?', $businessStatus->id)]) ?>    
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