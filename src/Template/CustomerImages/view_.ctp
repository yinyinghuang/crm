<?php
/**
  * @var \App\View\AppView $this
  */
?>
<?= $this->start('top_nav') ?>
    <span class="navbar-brand">客户图片</span>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        <li><?= $this->Html->link(__('客户详情'), ['controller' => 'Customers', 'action' => 'view',$customer_id]) ?></li>
<?= $this->end() ?>

<div class="customerImages view columns content ">
    <div class="ui segment header">
        <h3 class="header inline"><?= __('客户相关图片') ?><span id="add_images_btn" class="label nav-label-span">上传图片</span></h3>
         <i class="fa fa-search pull-right" id="show-search"></i>
    </div>
    <div class="ui segment search_box">
        <form action="<?= $this->Url->build(['action' => 'search'])?>" role="form">
            <input type="hidden" name="customer_id" value="<?= $customer_id?>">
        <div class="row form-group">
            <label class="col-md-1 col-xs-4">图片名稱</label>
            <div class="col-md-3 col-xs-8">
                <input type="text" name="name" value="<?= h(isset($name) ? $name : '') ?>" class="form-control">
            </div>
        </div>
        <div class="row form-group">
            <label class="col-md-1 col-xs-12">上传日期</label>
            <div class="col-md-6 col-xs-12">
                <div class="input-group">
                    <input type="text" name="startTime" value="<?= h(isset($startTime) ? $startTime : '') ?>" class="form-control datetimepicker" readonly>
                    <span class="input-group-addon">至</span>
                    <input type="text" name="endTime" value="<?= h(isset($endTime) ? $endTime : '') ?>" class="form-control datetimepicker" readonly>
                </div>
            </div>           
        </div>
        <div class="row form-group">            
            <div class="col-md-2">
                <button class="btn btn-primary">搜索</button>
            </div>
        </div>
        </form>
    </div>
    <div class="display-none ui segment" id="add_images"> 
        <?= $this->Form->create(null, ['url' => ['controller' => 'Customers','action' => 'addImages', $customer_id],'type' => 'file']) ?>
        <h4 class="ui horizontal divider header"><i class="cloud upload icon"></i>上传图片</h4>
        <input id="input" type="file" multiple name="images[]">
        
        <?= $this->Form->button(__('提交'),['class' => ['btn','btn-primary']]) ?>
        <?= $this->Form->end() ?>
    </div>
    <?php if ($customerImages->count()!==0): ?>
    <form action="/customer-images/delete-batch/" method="post" class="segment ui" id="del">
        <input type="hidden" name="customer_id" value="<?= $customer_id ?>">
        <div class="segment ui checkbox">
            <input type="checkbox" id="all">
            <label for="all" id="all_label" class="display-inline">全选</label>
            <button type="submit" class="btn btn-danger">删除选中</button>

        </div>
        <div class="baguetteBox ui  four stackable  cards"> 
            <?php foreach ($customerImages as $customerImage): ?>
            
            <a href="<?= $customerImage->path.$customerImage->name.'.'.$customerImage->ext?>" data-caption="<?= $customerImage->name?>" class="card">
                <div class="content">
                    <div  class="description ui checkbox">                        
                        <input type="checkbox" name="ids[]" value="<?= $customerImage->id?>" class="check">
                        <label class="name display-inline" ><?= $customerImage->name ?> </label>
                        <i class="trash icon red" data-id="<?= $customerImage->id ?>"></i>
                    </div>
                </div>
                <div class=" image">
                   <img src="<?= $customerImage->path.$customerImage->name.'.thumb.'.$customerImage->ext?>"> 
                </div>
            </a>
           
            <?php endforeach; ?>
        </div>
    </form> 
    <?php else: ?>
        <div class="ui segment">
            <p>暂无图片</p>
        </div>
    <?php endif ?>
</div>

<?= $this->start('css') ?>
    <?= $this->HTML->css('../js/baguetteBox/baguetteBox.min.css') ?>
<?= $this->Html->css('../js/vendors/bootstrap-fileinput-master/css/fileinput.min.css') ?>
<?= $this->end() ?>
<?= $this->start('script') ?>
<?= $this->Html->script('vendors/bootstrap-fileinput-master/js/fileinput.min.js') ?>
<?= $this->Html->script('jquery-lazyload/jquery.lazyload.js') ?>
<?= $this->Html->script('vendors/bootstrap-fileinput-master/js/locales/zh-TW.js') ?>
<?= $this->HTML->script('baguetteBox/baguetteBox.min.js') ?>
    <script type="text/javascript">
        $(function(){
            $('img').lazyload();
            baguetteBox.run('.baguetteBox');
            $('.trash').on('click',function () {
                if(confirm('确定删除此图片？')){
                    var that = this;
                    $.ajax({
                        type:'post',
                        url:'/customer-images/delete/',
                        data:{
                            id:$(this).data('id'),
                            customer_id:"<?= $customer_id ?>"
                        },
                        success:function (res) {
                            res = JSON.parse(res);
                            if (res.success) {
                                $(that).parents('.card').remove();
                            }else{
                                alert(res.error);
                            }
                        },
                        error:function () {
                            alert('系统错误');
                        }
                    });
                    return false;
                }else{
                    return false;
                }
            });


            $('.name').on('click',function(e){
                var input = $(this).siblings('input')[0];
                $(input).prop('checked',!$(input).prop('checked'));
                e.stopPropagation();
                e.preventDefault()
            });

            $add_images = $('#add_images');
            $("#add_images_btn").on('click',function () {
                $add_images.is(":visible") ? $add_images.slideUp() : $add_images.slideDown()
            });



            $("#input").fileinput({
                maxFileCount: 10,
                allowedFileTypes: ["image"],
                showPreview :false,

            });
        })
    </script>
<?= $this->end() ?>
