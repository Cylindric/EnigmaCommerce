<?php if ($ajaxFrame == 'body'): ?>
    <?php echo $this->Html->script('/admin/users/menu.js'); ?>
	<div id="left-navigation"><div id="left-navigation-tree"></div></div>
    <div id="body">
    <div id="bodycontent">
<?php endif; ?>

    <?php echo $this->Session->flash(); ?>

    <div id="ajax-loading-message">
        Loading
    </div>
