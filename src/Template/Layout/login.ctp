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
        <!-- BEGIN META -->
        <?= $this->Html->charset() ?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>
            crm
        </title>
        <?= $this->Html->meta('appicon.png', '/img/appicon.png', ['type' => 'icon']); ?>
        <!-- END META -->

        <!-- BEGIN STYLESHEETS -->
        <?= $this->Html->css('bootstrap.min.css') ?>
        <?= $this->Html->css('font-awesome.min.css') ?>
        <?= $this->Html->css('nprogress.css') ?>
        <?= $this->Html->css('animate.min.css') ?>
        <!-- PNotify -->
        <?= $this->Html->css('../js/vendors/pnotify/dist/pnotify.css') ?>
        <?= $this->Html->css('../js/vendors/pnotify/dist/pnotify.buttons.css') ?>
        <?= $this->Html->css('../js/vendors/pnotify/dist/pnotify.nonblock.css') ?>
        <?= $this->Html->css('../js/vendors/iCheck/skins/flat/green.css') ?>

        <?= $this->Html->css('custom.css') ?>
        <!-- END STYLESHEETS -->

        <?= $this->fetch('meta') ?>
        <?= $this->fetch('css') ?>
    </head>
    <body class="login">
        <?= $this->fetch('content') ?>
        <!-- BEGIN JAVASCRIPT -->
        <?= $this->Html->script('jquery.min.js') ?>
        <!-- PNotify -->
        <?= $this->Html->script('vendors/pnotify/dist/pnotify.js') ?>
        <?= $this->Html->script('vendors/pnotify/dist/pnotify.buttons.js') ?>
        <?= $this->Html->script('vendors/pnotify/dist/pnotify.nonblock.js') ?>
        <?= $this->fetch('script') ?>
        <?= $this->Flash->render() ?>
        <!-- END JAVASCRIPT -->
    </body>
</html>
