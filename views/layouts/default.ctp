<?php echo $this->Html->docType('xhtml-strict'); ?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-GB" lang="en-GB">
    <head>
        <?php 
            echo $this->Html->charset(); 
            echo $this->Html->meta('icon');
            echo $this->Html->css('style');
            echo $scripts_for_layout; 
        ?>
        <title><?php echo __('Enigma: '); ?><?php echo $title_for_layout; ?></title>
    </head>
    <body>

        <div id="header">
            <?php echo __('EnigmaGen Commerce'); ?>
        </div>
        <div id="flash">
            <?php echo $this->Session->flash(); ?>
        </div>
        
        <div class="col-mask three-col"> 
            <div class="col-mid"> 
                <div class="col-left">
                    <?php echo ($title_for_layout=='Errors') ? '<div class="content-1">' : '';?>
                    <?php echo $content_for_layout; ?>
                    <?php echo ($title_for_layout=='Errors') ? '</div>' : '';?>
                </div> 
            </div> 
        </div> 

        <div id="footer">
            <div id="menu">
                <?php echo $this->element('footer_menu'); ?>
            </div>
            Copyright © 2011 Mark Hanford
        </div>
                
<?php //echo $this->Js->writeBuffer(); ?>
<?php echo $this->element('sql_dump'); ?>

    </body>
</html>
