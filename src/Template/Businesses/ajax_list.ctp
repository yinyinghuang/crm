<?php foreach ($businesses as $business): ?>
<tr>
    <td><input type="checkbox" class="flat" name="ids[]" value="<?= $business->id?>"><label><?= $business->id ?></label></td>
    <td><span class="mobile only ui grid"><i class="icon users"></i></span><?= $business->has('customer') ? $this->Html->link($business->customer->name, ['controller' => 'Customers', 'action' => 'view', $business->customer->id]) : '' ?></td>
    <td><span class="mobile only ui grid"><i class="icon cube"></i></span><?= $business->has('event') ? $this->Html->link($business->event->name, ['controller' => 'Events', 'action' => 'view', $business->event->id]) : '' ?></td>
    <td><?php if (isset($business->business_statuses[0]['status'])): ?>
        <span class="mobile only ui grid"><i class="icon list"></i></span><?= $business->business_statuses[0]['status']?>
    <?php endif ?></td>
    <td class="<?= $stateColorArr[$business->state] ?>"><?= $stateArr[$business->state] ?></td>
    <td><span class="mobile only ui grid"><i class="icon user"></i></span><?= $business->has('user') ? $this->Html->link($business->user->username, ['controller' => 'Users', 'action' => 'view', $business->user->id]) : '' ?></td>
    <td><span class="mobile only ui grid"><i class="icon calendar"></i></span><?= h($business->modified) ?></td>
    <td class="actions">
        <?= $this->Html->link(__('进展列表'), ['action' => 'view', $business->id]) ?>
        <?php if (strpos($_privileges, 'd') !== false): ?>
        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $business->id], ['confirm' => __('Are you sure you want to delete # {0}?', $business->id)]) ?>
        <?php endif ?>
    </td>
</tr>
<?php endforeach; ?>