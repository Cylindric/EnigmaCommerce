<?php
if ($ajaxFrame == 'body') :
    echo $this->Html->script('/admin/categories/menu.js', array('inline'=>false));
?>

<div id="left-navigation">
    <div id="left-navigation-tree"></div>
</div>

<?php endif; ?>