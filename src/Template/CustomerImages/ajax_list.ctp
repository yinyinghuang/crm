<?php foreach ($customerImages as $customerImage): ?>    
<tr>
	<td><input type="checkbox" class="flat" name="ids[]" value="<?= $customerImage['id']?>"><label><?= $customerImage['id'] ?></label></td>
	<td><span class="mobile only ui grid"><i class="image icon"></i></span><a href="<?= $customerImage['path'].$customerImage['name'].'.'.$customerImage['ext']?>"><?= $customerImage['name'] ?></a></td>
	<td><?php if (isset($businesses) && $customerImage['business_id']): ?><span class="mobile only ui grid"><i class="cubes icon"></i></span>
		<a href="<?= $this->Url->build(['controller' => 'businesses','action' => 'view',$customerImage['business_id']])?>"><?= $businesses[$customerImage['business_id']]?></a>
	<?php endif ?></td>
	<td><span class="mobile only ui grid"><i class="sync icon"></i></span><?= h($customerImage['created'])?></td>
	<td class="actions">
	    <?php if (strpos($_privileges, 'd') !== false): ?>
	        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $customerImage['id']], ['confirm' => __('Are you sure you want to delete # {0}?', $customerImage['name'])]) ?>
	    <?php endif ?>                    
	</td>
</tr>
<?php endforeach; ?>