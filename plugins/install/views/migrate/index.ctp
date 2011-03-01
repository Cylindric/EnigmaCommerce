<div id="body" class="view">
    <div id="bodycontent">
<?php

    echo $this->Form->create(false, array('action'=>'start'));
    echo $this->Form->input('sequence', array(
        'label' => __('Select desired actions:'),
        'type' => 'select', 
        'multiple'=>'checkbox', 
        'options' => $sequence['Sequence']));
    echo $this->Form->end('Migrate!');

?>
    </div>
</div>
<div class="clear"></div>
