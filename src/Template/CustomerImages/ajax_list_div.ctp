<?php foreach ($customerImages as $customerImage): ?>            
<a href="<?= $customerImage->path.$customerImage->name.'.'.$customerImage->ext?>" data-caption="<?= $customerImage->name?>" class="card">
    <div class="content">
        <div  class="description ui">                        
            <input type="checkbox" class="flat" name="ids[]" value="<?= $customerImage->id?>"><label><?= $customerImage->id ?></label>
            <label class="name display-inline" ><?= $customerImage->name ?> </label>
        </div>
    </div>
    <div class=" image">
       <img src="<?= $customerImage->path.$customerImage->name.'.thumb.'.$customerImage->ext?>"> 
    </div>
</a>
<?php endforeach; ?>