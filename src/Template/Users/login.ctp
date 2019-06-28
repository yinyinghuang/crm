<!-- File: src/Template/Users/login.ctp -->
<?php $this->layout = 'login';?>
<div class="container full-height">
    <div class="row full-height">
        <div class="col-xs-12 col-sm-12 white-backgroud full-height">
        	<div class="row full-height">
        		<div class="col-xs-10 col-md-4 col-lg-2 login-view white-backgroud vertical-center">
		            
		        	<?= $this->Form->create() ?>
                        <div class="form-group text-center">
                            <h3>登入管理系統</h3>
                        </div>
		              	<div class="form-group">
		              		<?= $this->Form->input('userid',['class' => 'form-control', 'label' => false, 'placeholder'=>'帳號']) ?>
		              	</div>
		             	<div class="form-group">
		              		<?= $this->Form->input('password',['class' => 'form-control', 'label' => false, 'placeholder'=>'密碼']) ?>
		              	</div>
		              	<div class="form-group">
		                	<?= $this->Form->button(__('登入'),['class' => 'btn btn-default btn-block']); ?>
		              	</div>
		            <?php echo $this->Form->end(); ?> 
        		</div>
        	</div>
        </div>
    </div>
</div>
<?= $this->start('css') ?>
<style type="text/css">
html, 
body {
    height: 100%;
    background: #fff;
}
.full-height{
    height: 100%;
}
.white-backgroud{
    background: #fff;
}
.login-view {
    margin-bottom: 40px;
}
@media (min-width:  992px){
    .mobile-img{
        display: none;
    }
}
@media (max-width: 991px){
    .desktop-img{
        display: none;
    }
}
@media (max-width: 1199px) and (min-width: 992px){
    body {
        background-color: lightgreen;
    }
}
div.message.error{display: none;}
</style>
<?= $this->end(); ?>