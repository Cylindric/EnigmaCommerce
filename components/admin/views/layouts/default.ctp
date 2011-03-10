<?php echo $this->Html->docType('xhtml-strict'); ?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-GB" lang="en-GB">
    <head>
        <?php 
            echo $this->Html->charset(); 
            echo $this->Html->meta('icon');
            echo $this->Html->css('extjs/ext-all.css');
            echo $this->Html->css('/admin/css/style');
            echo $this->Html->script('extjs/adapter/ext/ext-base.js');
            echo $this->Html->script('extjs/ext-all-debug.js');
            echo $this->Html->scriptBlock("Ext.BLANK_IMAGE_URL = '".$this->Html->url('/img/extjs/default/s.gif')."';");
            echo $scripts_for_layout; 
        ?>
        <title><?php echo __('Enigma: '); ?><?php echo $title_for_layout; ?></title>

    </head>
    <body>

        <div id="wrapper">

            <div id="header">
                <ul class="menu">
                    <li><?php echo $this->Html->link(__('Home'), '/');?></li>
<?php if(!isset($user) || is_null($user)): ?>
                    <li><?php echo $this->Html->link(__('Login'), '/users/login');?></li>
<?php else: ?>
                    <li><?php echo $this->Html->link(__('Hello, %s', $user['username']), '/users/logout');?></li>
<?php endif; ?>
                </ul>
            </div>

            <div id="separator">
                <div id="title"><h1><?php echo $title_for_layout; ?></h1></div>
            </div>

            <div id="content">
                <div id="maincontent">
                    <?php echo $this->Session->flash(); ?>
                    
<?php echo $content_for_layout; ?>

                    <div class="clear">&nbsp;</div>
                </div>
            </div>

            <div id="footer">Copyright © 2011 Mark Hanford</div>

<?php echo $this->Js->writeBuffer(); ?>
<?php //echo $this->element('sql_dump'); ?>
        </div>

    </body>
</html>
