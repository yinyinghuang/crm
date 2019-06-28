<?php
/**
  * @var \App\View\AppView $this
  */
?>

<?= $this->start('top_nav') ?>
    <span class="navbar-brand">导入员工</span>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        <li><?= $this->Html->link(__('员工列表'), ['action' => 'index']) ?></li>
        <?php if (strpos($_privileges, 'o')): ?>
        <li><?= $this->Html->link(__('导出员工'), ['action' => 'export']) ?></li>  
        <?php endif ?>
        
<?= $this->end() ?>

<div class="users form columns content">
  <div class="clearfix"></div>
  <div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <!-- <h2>导入客户 <small></small></h2> -->
          <ul class="nav navbar-right panel_toolbox">
          </ul>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <br />
          <?php echo $this->Form->create('Application', array('class' => 'form-horizontal group-border-dashed', 'type' => 'file','id' => 'form')); ?>
            <div class="form-group" id="example">
              <label class="control-label col-md-3 col-sm-3 col-xs-12">下载范本</label>
              <div class="col-md-6 col-sm-6 col-xs-12">
                  <?php echo $this->Html->link('下载 import_users.xlsx', '/files/import_users.xlsx', array('class' => 'form-control'));?>
              </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">上传 Excel 文件 (.xls or .xlsx)</label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <?php echo $this->Form->input("File.import_file", array('label' => false, 'div' => false, 'class' => "form-control", 'type' => 'file', 'accept' => '.xls,.xlsx','id' => 'file'));?>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12"></label>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <small style="color: red">*系統新建員工默認密碼為123456</small>
                    <?php if (isset($this->request->session()->read('Flash')['flash'])): ?>
                      <div class="message negative ui"><?= $this->request->session()->read('Flash')['flash'][0]['message'] ?></div>
                    <?php endif ?>
                    
                </div>
            </div>
            <div class="ln_solid"></div>
            <div class="form-group">
                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                    <button class="btn btn-primary" type="submit" id="submit">导入</button>
                </div>
            </div>             
          <?php echo $this->Form->end(); ?>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="clearfix"></div>
<?= $this->start('script') ?>
<script type="text/javascript">
    $(function(){
        $('#form').on('submit', function(){
          $('#submit').hide();
          new PNotify({
              text: '處理中，請不要關閉頁面',
              type: 'info',
              styling: 'bootstrap3',
              delay: 300000,
              width:'280px'
          });
          return true;
        });

          $('#file').on('click', function(){
            $('#submit').show();
          });
    });
</script>
<?= $this->end() ?>