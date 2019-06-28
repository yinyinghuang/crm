<?php foreach ($events as $event): ?>
<tr>
    <td><input type="checkbox" class="flat" name="ids[]" value="<?= $event->id?>"><label><?= $event->id ?></label></td>
    <td><?= $event->has('event_type') ? $this->Html->link($event->event_type->name, ['controller' => 'EventTypes', 'action' => 'view', $event->event_type->id]) : '' ?></td>
    <td><span class="mobile only ui grid"><i class="icon cube"></i></span><?= h($event->name) ?></td>
    <td><span class="mobile only ui grid label purple">总共</span><?= h($event->total) ?></td>
    <td><span class="mobile only ui grid label blue">进行中</span><?= h($event->ing) ?></td>
    <td><span class="mobile only ui grid label green">成交</span><?= h($event->signed) ?></td>
    <td><span class="mobile only ui grid label red">失败</span><?= h($event->closed) ?></td>
    <td><span class="mobile only ui grid"><i class="icon calendar"></i></span><?= h($event->created) ?></td>
    <td class="actions">
        <?= $this->Html->link(__('View'), ['action' => 'view', $event->id]) ?>
        <?php if (strpos($_privileges, 'e') !== false): ?>
            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $event->id]) ?>
        <?php endif ?>
    </td>
</tr>
<?php endforeach; ?>