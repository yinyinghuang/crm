<div class="">
  <div class="clearfix"></div>
  <div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h2>设置<small></small></h2>
          <ul class="nav navbar-right panel_toolbox">
          </ul>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <br />
          <form action="/configs/edit" method="post" class="form-horizontal group-border-dashed" >
            <div id="fileds">                
                <div class="wrap">
                    <div class="form-group">
                      <label class="control-label col-md-3 col-sm-3 col-xs-12">状态设置(多个请用英文竖线隔开)</label>
                      <div class="col-md-6 col-sm-6 col-xs-12">
                        <input name="state" class="form-control" value="<?= isset($state) ? $state : ''?>" type="text">
                      </div>
                    </div>
                </div>  
                <div class="wrap">
                    <div class="form-group">
                      <label class="control-label col-md-3 col-sm-3 col-xs-12">来源设置(多个请用英文竖线隔开)</label>
                      <div class="col-md-6 col-sm-6 col-xs-12">
                        <input name="source" class="form-control" value="<?= isset($source) ? $source : ''?>" type="text">
                      </div>
                    </div>
                </div>    
                <div class="wrap">
                    <div class="form-group">
                      <label class="control-label col-md-3 col-sm-3 col-xs-12">提醒往后几天代办事件</label>
                      <div class="col-md-6 col-sm-6 col-xs-12">
                        <input name="ahead" class="form-control" value="<?= isset($ahead) ? $ahead : ''?>" type="number">
                      </div>
                    </div>
                </div>                               
                 
            </div>
              
            
            <div class="clearfix"></div>                    
            <div class="ln_solid"></div>
            <div class="form-group">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <button class="btn btn-primary" type="submit">儲存</button>
                </div>
            </div>             
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $this->start('css'); ?>
  <?php echo $this->Html->css("summernote/summernote.css"); ?>
<?php $this->end(); ?>
<?= $this->start('script') ?>
<!-- summernote-->
<?php echo $this->Html->script("summernote/dist/summernote.min.js"); ?>
<script>
    $(function () {
        
        $('#add').on('click',function(){
            key = $('#count').val();
            $('#fileds').append('<div class="wrap"><div class="form-group">  <label class="control-label col-md-3 col-sm-3 col-xs-12"><span class="ui header">警告设置'+ ((key-0)+1) +'</span><i class="icon trash red del"></i></label></div>   <div class="form-group">  <label class="control-label col-md-3 col-sm-3 col-xs-12">警告类型</label>  <div class="col-md-6 col-sm-6 col-xs-12">    <input name="warnings['+ key +'][level]" class="form-control" type="text">  </div></div><div class="form-group">  <label class="control-label col-md-3 col-sm-3 col-xs-12">未更新时间</label>  <div class="col-md-6 col-sm-6 col-xs-12">    <input name="warnings['+ key +'][time]" class="form-control" type="number">  </div></div><div class="form-group">  <label class="control-label col-md-3 col-sm-3 col-xs-12">警告语</label>  <div class="col-md-6 col-sm-6 col-xs-12">    <input name="warnings['+ key +'][syntax]" class="form-control" type="text">  </div></div> </div> ');

            key++;
        });

        $('body').on('click','.del',function(){
            if (confirm('确认删除？')) {
                $(this).parents('.wrap').remove();
            }
        });
    });
</script>
<?= $this->end() ?>