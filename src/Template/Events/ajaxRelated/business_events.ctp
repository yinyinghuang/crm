<?php foreach ($event->businesses as $businesses): ?>
<tr>
    <td><?= h($businesses->id) ?></td>
    <td><?= $this->Html->link($businesses->customer['name'], ['controller' => 'Customers', 'action' => 'view', $businesses->customer['id']]) ?></td>
    <td><?= h($stateArr[$businesses->state]) ?></td>
    <td><?= $this->Html->link($businesses->user['username'], ['controller' => 'Customers', 'action' => 'view', $businesses->user['id']]) ?></td>
    <td><?= isset($businesses->business_statuses[0])? $businesses->business_statuses[0]['status']:'' ?></td>                        
    <td><?= h($businesses->modified) ?></td>
    <td class="actions">
        <?= $this->Html->link(__('View'), ['controller' => 'Businesses', 'action' => 'view', $businesses->id]) ?>
        <?= $this->Html->link(__('Edit'), ['controller' => 'Businesses', 'action' => 'edit', $businesses->id]) ?>
        <?= $this->Form->postLink(__('Delete'), ['controller' => 'Businesses', 'action' => 'delete', $businesses->id], ['confirm' => __('Are you sure you want to delete # {0}?', $businesses->id)]) ?>
    </td>
</tr>
<?php endforeach; ?>