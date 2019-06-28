<?php foreach ($campaigns as $campaign): ?>
<tr>
    <td><input type="checkbox" class="flat" name="ids[]" value="<?= $campaign->id?>"><label><?= $this->Number->format($campaign->id) ?></label></td></td>
    <td><?= $typeArr[$campaign->type] ?></td>
    <td><?= $campaign->has('user') ? $this->Html->link($campaign->user->username, ['controller' => 'Users', 'action' => 'view', $campaign->user->id]) : '' ?></td>
    <td><?= h($campaign->created) ?></td>
    <td class="actions">
        <?= $this->Html->link(__('View'), ['action' => 'view', $campaign->id]) ?>
        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $campaign->id], ['confirm' => __('Are you sure you want to delete # {0}?', $campaign->id)]) ?>
    </td>
</tr>
<?php endforeach; ?>