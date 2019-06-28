<?php foreach ($customers as $customer): ?>
<tr>
    <td><input type="checkbox" class="flat" name="ids[]" value="<?= $customer->id?>"><label><?= $customer->id ?></label></td>
    <td><span class="mobile only ui grid"><i class="users icon"></i></span><?= h($customer->name) ?></td>
    <td>
        <span class="mobile only ui grid"><i class="mobile icon"></i></span>
        <a href="tel:<?= '+' . $customer['customer_mobiles'][0]['country_code']['country_code'] . '-' . $customer['customer_mobiles'][0]['mobile'] ?>">
            <?= '+' . $customer['customer_mobiles'][0]['country_code']['country_code']  . '-' . $customer['customer_mobiles'][0]['mobile'] ?>
                
        </a>
        <a href="https://api.whatsapp.com/send?phone=<?=  $customer['customer_mobiles'][0]['country_code']['country_code'] . $customer['customer_mobiles'][0]['mobile'] ?>"><i class="whatsapp icon"></i></a>
    </td>
    <td><span class="mobile only ui grid"><i class="user icon"></i></span><?= $customer->has('user') ? $this->Html->link($customer->user->username, ['controller' => 'Users', 'action' => 'view', $customer->user->id]) : '' ?></td>
    <td><span class="mobile only ui grid"><i class="list layout icon"></i></span><?= h(isset($customer->business_statuses[0]['status']) ? $customer->business_statuses[0]['status'] : '') ?></td>
    <td><span class="mobile only ui grid"><i class="sync icon"></i></span><?= h($customer->modified) ?></td>
    <td class="actions">
        <?= $this->Html->link(__('View'), ['action' => 'view', $customer->id]) ?>
        <?php if (strpos($_privileges, 'e') !== false): ?>
            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $customer->id]) ?>
        <?php endif ?>
        <?php if (strpos($_privileges, 'd') !== false): ?>
            <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $customer->id], ['confirm' => __('Are you sure you want to delete # {0}?', $customer->id)]) ?>
        <?php endif ?>                    
    </td>
</tr>
<?php endforeach; ?>
