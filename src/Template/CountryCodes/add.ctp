<?php
/**
  * @var \App\View\AppView $this
  */
?>
<?= $this->start('top_nav') ?>
    <span class="navbar-brand">新增區號</span>
</div>
<div class="collapse navbar-collapse" id="example-navbar-collapse">
    <ul class="nav navbar-nav">
        <?php if (strpos($_privileges, 'v') !== false): ?>
            <li><?= $this->Html->link(__('區號列表'), ['action' => 'index']) ?></li>
        <?php endif ?>    
<?= $this->end() ?>

<div class="countryCodes columns content ui segment">
    <?= $this->Form->create($countryCode) ?>
    <fieldset>
        <?php
            echo $this->Form->control('country',['label' => '國家/地區']);
            echo $this->Form->control('country_code',['label' => '區號','placeholder' => '格式如：852']);
        ?>
    </fieldset>
    <?= $this->Form->button(__('提交'),['class' => ['btn','btn-primary']]) ?>
    <?= $this->Form->end() ?>
</div>
