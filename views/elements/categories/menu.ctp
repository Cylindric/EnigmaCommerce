<?php
if ($ajaxFrame == 'body') :
    echo $this->Html->script('/categories/menu.js', array('inline'=>false));
?>
<div id="left-navigation">
    <div id="left-navigation-tree"></div>
    <p style="height:300px;"></p>
</div>

<?php endif; ?>