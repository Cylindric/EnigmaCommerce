<?php echo $this->Html->docType('xhtml-strict'); ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-GB" lang="en-GB">
    <head>
        <title>
            <?php echo __('Enigma: '); ?>
            <?php echo $title_for_layout; ?>
        </title>
        <?php echo $this->Html->charset(); ?>
        <?php echo $this->Html->meta('icon'); ?>
        
        <?php echo $this->Html->css('extjs/ext-all.css'); ?>
        <?php echo $this->Html->css('style'); ?>

        <?php echo $this->Html->script('extjs/adapter/ext/ext-base.js'); ?>
        <?php echo $this->Html->script('extjs/ext-all-debug.js'); ?>
        <script type="text/javascript">Ext.BLANK_IMAGE_URL = '<?php echo $this->request->webroot;?>/img/extjs/default/s.gif';</script>
        
        <?php echo $scripts_for_layout; ?>
    </head>
    <body>

        <div id="global-navigation">
            <?php echo $this->Html->script('/admin/pages/mainmenu.js'); ?>
            <div id="toolbar"></div>
        </div>
        
        <div id="container">

            <div id="content">
                <div id="viewcontent">
                    <?php echo $content_for_layout; ?>
                </div>
                <div class="clear"></div>
            </div>

            <div id="footer">Copyright &copy; 2011 Mark Hanford</div>

        </div>

        <?php echo $this->Js->writeBuffer(); ?>
		<?php //echo $this->element('sql_dump'); ?>

    </body>
</html>
