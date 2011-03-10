<?php echo $this->Html->docType('xhtml-strict'); ?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-GB" lang="en-GB">
    <head>
        <?php
            echo $this->Html->charset();
            echo $this->Html->meta('icon'); 
            echo $this->Html->css('extjs/ext-all.css');
            echo $this->Html->css('style');
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
                <h1><a href="/">Enigma</a></h1>
                <ul class="menu">
    <?php if(isset($user)): ?>
                    <li><?php echo __('Hello, %s', $user['username']);?></li>
    <?php endif; ?>
                    <li><?php echo $this->Html->link(__('Home', true), '/');?></li>
    <?php if(!isset($user) || is_null($user)): ?>
                    <li><?php echo $this->Html->link(__('Login', true), '/users/login');?></li>
    <?php else: ?>
                    <li><?php echo $this->Html->link(__('Logout', true), '/users/logout');?></li>
    <?php endif; ?>
                </ul>
            </div>

            <div id="separator_m">
                <div id="separator_l">
                    <div id="separator_r">
                        <div id="separator">
                            <div id="title"><h1><?php echo $title_for_layout; ?></h1></div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="content_l">
                <div id="content_r">
                    <div id="content_b">
                        <div id="content_bl">
                            <div id="content_br">
                                <div id="content">

                                        <?php echo $this->Session->flash(); ?>
                                        <?php echo $content_for_layout; ?>
                                        <div class="clear">&nbsp;</div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="footer">Copyright © 2011 Mark Hanford</div>

            <?php echo $this->Js->writeBuffer(); ?>
            <?php echo $this->element('sql_dump'); ?>

        </div>

    </body>
</html>
