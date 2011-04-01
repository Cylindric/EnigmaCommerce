<?php
$nodes = array(
    array(
        'action' => 'edit',
        'content' => __('Summary'),
    ),
    array(
        'action' => 'edit_items',
        'content' => __('Items'),
    ),
    array(
        'action' => 'edit_pictures',
        'content' => __('Pictures'),
    ),
);
?>
<div class="editmenu">
    <ul>
        <?php 
        foreach ($nodes as $node) {
            $link = $this->Link->link('Category', $data, array('action' => $node['action'], 'name' => false, 'content' => $node['content']));
            $options = array();
            if ($this->request->params['action'] == $node['action']) {
                $options['class'] = 'active';
            }
            echo $this->Html->tag('li', $link, $options);
        }
        ?>
    </ul>
</div>