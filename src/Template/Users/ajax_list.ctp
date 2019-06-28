<?php foreach ($users as $user): ?>
<tr>
    <td><input type="checkbox" class="flat" name="ids[]" value="<?= $user->id?>"><label><?= $user->id ?></label></td>
    <td><?= h($user->name) ?></td>
    <td><?= $user->has('department') ? $this->Html->link($user->department->name, ['controller' => 'Departments', 'action' => 'view', $user->department->id]) : '' ?></td>
    <td><?= $user->has('role') ? $user->role->name : '' ?></td>
    <td>
    <?php if ($user->mobile): ?>
    <a href="tel:<?= '+'.h($user->country_code['country_code'].'-'. $user->mobile) ?>"><?= '+'.h($user->country_code['country_code'].'-'. $user->mobile) ?></a>  
    <a href="https://api.whatsapp.com/send?phone=<?= '+'.h($user->country_code['country_code'].'-'. $user->mobile) ?>"><i class="whatsapp icon"></i></a>  
    <?php endif ?></td>

    <td>
        <?= h($user->last_op)?>
    </td>
    <td class="<?= $stateColorArr[$user->state+1] ?>">
        <?= $userStateArr[$user->status]?>
    </td>
    <td class="actions">
        <?= $this->Html->link(__('View'), ['action' => 'view', $user->id]) ?>
        <?php if (strpos($_privileges, 'e') !== false): ?>
            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $user->id]) ?>
        <?php endif ?>
        <?php if (strpos($_privileges, 'd') !== false): ?>
            <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $user->id], ['confirm' => __('Are you sure you want to delete # {0}?', $user->id)]) ?>
        <?php endif ?>
    </td>
</tr>
<?php endforeach; ?>
