<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

$title = 'Market Hotpot';
?>
<!DOCTYPE html>
<html>
    <head>
        <?= $this->Html->charset() ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=no">
        <title>
            CRM
        </title>
        <!-- <?= $this->Html->meta('appicon.png', '/img/appicon.png', ['type' => 'icon']); ?> -->
        <!-- <?= $this->Html->meta('icon') ?> -->

        <!-- BEGIN STYLESHEETS -->
        <?= $this->Html->css('base.css') ?>
        <?= $this->Html->css('bootstrap.min.css') ?>
        <?= $this->Html->css('font-awesome.min.css') ?>
        <?= $this->Html->css('nprogress.css') ?>
        <?= $this->Html->css('animate.min.css') ?>
        <?= $this->Html->css('custom.css') ?>
        <!-- PNotify -->
        <?= $this->Html->css('../js/vendors/pnotify/dist/pnotify.css') ?>
        <?= $this->Html->css('../js/vendors/pnotify/dist/pnotify.buttons.css') ?>
        <?= $this->Html->css('../js/vendors/pnotify/dist/pnotify.nonblock.css') ?>
        <?= $this->Html->css('../js/vendors/iCheck/skins/minimal/green.css') ?>
        <?= $this->Html->css('semantic-ui-css/semantic.min.css') ?>
        <!-- END STYLESHEETS -->

        <?= $this->fetch('meta') ?>
        <?= $this->fetch('css') ?>
    </head>
    <body class="nav-md footer_fixed">
        <div class="container body">
          <div class="main_container">          
            <div class="col-md-3 left_col">
              <div class="left_col scroll-view">
                <div class="nav_title" style="border: 0;">
                  
                </div>
                <br />
                <!-- sidebar menu -->
                <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                  <div class="menu_section">
                    <h3>General</h3>
                    <ul class="nav side-menu">
                    <?php if (array_key_exists('Customers', $_module_privileges)): ?>
                      <li><a><i class="fa fa-users"></i> 客戶 <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                          <li><a href="<?= $this->Url->build(['controller' => 'Customers', 'action' => 'index']) ?>">客戶列表</a></li>
                          <li><a href="<?= $this->Url->build(['controller' => 'Customers', 'action' => 'add']) ?>">新增客戶</a></li>
                          <!-- <?php if (strpos($_module_privileges['Customers'], 'i')): ?>
                          <li><a href="<?= $this->Url->build(['controller' => 'Customers', 'action' => 'import']) ?>">客户导入</a></li> 
                          <?php endif ?>
                          <?php if (strpos($_module_privileges['Customers'], 'o')): ?>
                          <li><a href="<?= $this->Url->build(['controller' => 'Customers', 'action' => 'export']) ?>">客户导出</a></li>        
                          <?php endif ?>      
                          <?php if (strpos($_module_privileges['Customers'], 't')): ?>
                          <li><a href="<?= $this->Url->build(['controller' => 'Customers', 'action' => 'transfer']) ?>">客户转移</a></li>  
                          <?php endif ?>  -->                        
                        </ul>
                      </li> 
                      <!-- <li><a><i class="fa fa-tasks"></i> 订单 <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                          <li><a href="<?= $this->Url->build(['controller' => 'Businesses', 'action' => 'index']) ?>">订单列表</a></li>
                          <li><a href="<?= $this->Url->build(['controller' => 'Businesses', 'action' => 'add']) ?>">新增订单</a></li>                    
                        </ul>
                      </li> -->
                      <!-- <li><a><i class="fa fa-cubes"></i> 活动 <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                          <li><a href="<?= $this->Url->build(['controller' => 'Events', 'action' => 'index']) ?>">活动列表</a></li>
                          <li><a href="<?= $this->Url->build(['controller' => 'Events', 'action' => 'add']) ?>">新增活动</a></li>
                          <?php if (in_array($_user['role_id'], [3,4])): ?>  
                          <li><a href="<?= $this->Url->build(['controller' => 'EventTypes', 'action' => 'index']) ?>">活动类型</a></li>  
                          <?php endif ?>     
                        </ul>
                      </li>  -->
                    <?php endif ?>
                    <!-- <?php if (array_key_exists('Campaigns', $_module_privileges)): ?>
                       <li><a><i class="fa fa-paper-plane"></i> 群發推廣 <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                          <li><a href="<?= $this->Url->build(['controller' => 'Campaigns', 'action' => 'index']) ?>">群發列表</a></li>
                          <li><a href="<?= $this->Url->build(['controller' => 'Campaigns', 'action' => 'addEmail']) ?>">群发郵件</a></li>
                          <li><a href="<?= $this->Url->build(['controller' => 'Campaigns', 'action' => 'addSms']) ?>">群发短信</a></li>
                          <li><a href="<?= $this->Url->build(['controller' => 'Campaigns', 'action' => 'addMms']) ?>">群发彩信</a></li>
                        </ul>
                      </li>                                     
                    <?php endif ?>  -->                                     
                    <?php if (array_key_exists('Users', $_module_privileges) || array_key_exists('Departments', $_module_privileges)): ?>
                      <li><a><i class="fa fa-user"></i> 分組 <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu"> 
                          <?php if (in_array('Users', $_modules)): ?>
                          <li><a href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'index']) ?>">員工</a></li>
                          <?php endif ?>
                          <?php if (in_array('Departments', $_modules)): ?>
                          <li><a href="<?= $this->Url->build(['controller' => 'Departments', 'action' => 'index']) ?>">部门</a></li>
                          <?php endif ?>    
                        </ul>
                      </li>     
                    <?php endif ?>  
                   <!--  <?php if (array_key_exists('Developers', $_module_privileges)): ?>
                      <li><a> <i class="fa fa-university"></i> 開發商 <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                          <li><a href="<?= $this->Url->build(['controller' => 'Developers', 'action' => 'index']) ?>">開發商列表</a></li>
                          <li><a href="<?= $this->Url->build(['controller' => 'Developers', 'action' => 'add']) ?>">新增開發商</a></li>
                        </ul>
                      </li>  
                    <?php endif ?>  -->  
                    <?php if (array_key_exists('Configs', $_module_privileges)): ?>                      
                      <li><a> <i class="fa fa-cog"></i> 设置 <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">                          
                          <li><a href="<?= $this->Url->build(['controller' => 'CountryCodes', 'action' => 'index']) ?>">区号列表</a></li>                          
                          <li><a href="<?= $this->Url->build(['controller' => 'Configs', 'action' => 'index']) ?>">其他设置</a></li>
                          <!-- <?php if (in_array('Privileges', $_modules)): ?>
                          <li><a href="<?= $this->Url->build(['controller' => 'Privileges', 'action' => 'index']) ?>">权限设置</a></li>
                          <?php endif ?>  -->
                        </ul>
                      </li>  
                    <?php endif ?>   
                    <!-- <?php if (array_key_exists('Statistics', $_module_privileges)): ?>                      
                      <li><a href="<?= $this->Url->build(['controller' => 'Statistics', 'action' => 'index']) ?>"> <i class="fa fa-pie-chart"></i> 数据统计</a>
                        
                      </li>  
                    <?php endif ?>  -->                      
                    </ul>
                  </div>
                </div>
                <!-- /sidebar menu -->

                <!-- /menu footer buttons -->
                <div class="sidebar-footer hidden-small">
                  <a data-toggle="tooltip" data-placement="top" title="Logout" href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'logout']) ?>">
                    <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
                  </a>
                </div>
                <!-- /menu footer buttons -->
              </div>
            </div>

            <!-- top navigation -->
            <div class="top_nav">
              <div class="nav_menu">
                <nav>
                 
                    <div class="clearfix"></div>
                </nav>
                <nav class="navbar navbar-default" role="navigation">
                    <div class="container-fluid"> 
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse"
                                data-target="#example-navbar-collapse">
                            <span class="sr-only">切换导航</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <div class="nav toggle">
                          <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                        </div>
                        <?= $this->fetch('top_nav')?>
                        </ul>

                        <ul class="nav navbar-nav navbar-right">
                          <li class="">
                            <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                              <?= $_user['name'] ?>
                              <span class=" fa fa-angle-down"></span>
                            </a>
                            <ul class="dropdown-menu dropdown-usermenu pull-right">
                              <li><a href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'logout']) ?>"><i class="fa fa-sign-out pull-right"></i> 登出</a></li>
                            </ul>
                          </li>
                        </ul>
                    </div>
                    </div>
                </nav>
              </div>
              <div class="clearfix"></div>
            </div>
            <!-- /top navigation -->

            <!-- page content -->

            <div class="right_col" role="main">

            <?php if ($this->request->controller !== 'Dashboard'): ?>
              <?php if (!empty($warning)): ?>
                <div class="ui negative message">
                  <i class="close icon"></i>
                  <div class="header"><?= $warning['level']?></div>
                  <p><?= $warning['syntax']?></p></div>
              <?php endif ?>
              <?php if (!empty($todolist)): ?>
                <div class="ui info message transition">
                  <i class="close icon"></i>
                  <div class="header">
                    待办事件
                  </div>
                  <ul class="list">
                    <?php foreach ($todolist as $todo): ?>
                      <li style="line-height: 2em" data-id="<?= $todo->id?>" data-c-id="<?= $todo->customer_id?>" class="clearfix">
                        客户：
                        <strong>
                          <a href="<?= $this->Url->build(['controller' => 'Customers','action' => 'view',$todo->customer_id])?>"><?= $todo->name?>(<?=  $todo->mobile ?>)</a>
                          <a href="https://api.whatsapp.com/send?phone=<?=  $todo->mobile ?>">
                            <i class="whatsapp icon"></i>
                          </a>
                        </strong>；
                        联系时间：<strong><?= $todo->next_contact_time?></strong>；
                        联系备注：<strong><?= $todo->next_note?></strong>；
                        上次跟进内容：<strong><?= $todo->status?></strong>
                        <button class="pull-right ui green label todo_done">已联系,填写进展</button>
                      </li>
                    <?php endforeach ?>                    
                  </ul>
                </div>
              <?php endif ?>
            <?php endif ?>
                
                <?= $this->fetch('content') ?>
            </div>
            <!-- /page content -->

            <!-- footer content -->
            <!-- <footer>
              
            </footer> -->
            <!-- /footer content -->
          </div>
          <div class="clearfix"></div>
        </div>
        <i class="icon chevron circle up" id="btn_top"></i>
        <!-- BEGIN JAVASCRIPT -->
        <?= $this->Html->script('jquery.min.js') ?>
        <?= $this->Html->script('bootstrap.min.js') ?>
        <?= $this->Html->script('jquery.autocomplete.js') ?>
        <?= $this->Html->script('custom.js') ?>
        <?= $this->Html->script('moment/moment.min.js') ?>
        <?= $this->Html->script('datepicker/daterangepicker.js') ?>
        <?= $this->Html->script('../css/semantic-ui-css/semantic.min.js') ?>
        <!-- iCheck -->
        <?= $this->Html->script('vendors/iCheck/icheck.min.js') ?>
        <!-- PNotify -->
        <?= $this->Html->script('vendors/pnotify/dist/pnotify.js') ?>
        <?= $this->Html->script('vendors/pnotify/dist/pnotify.buttons.js') ?>
        <?= $this->Html->script('vendors/pnotify/dist/pnotify.nonblock.js') ?>
        <?= $this->Flash->render() ?>
        <?= $this->fetch('script') ?>
        <!-- END JAVASCRIPT -->        
    </body>
</html>