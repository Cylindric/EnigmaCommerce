<?php echo $this->element('categorymenu'); ?>
<div id="body" class="view">
<div id="bodycontent">

<?php if (!empty($title)): ?>
    <h1><?php echo($title); ?></h1>
<?php endif; ?>

<?php echo $this->Session->flash(); ?>

<?php if (count($subCategories)>0): ?>
    <div class="x-list">
        <?php foreach ($subCategories as $category): ?>
        <div class="x-item category">
            <?php echo $this->Html->link($category['Category']['name'], array('controller' => 'categories', 'action' => 'view', $category['Category']['id'])); ?>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (count($relatedItems)>0): ?>
    <div class="x-list">
        <?php 
            $leaders = min(6, count($relatedItems));
            $list = count($relatedItems) - $leaders;
        ?>
        <?php for ($itemNum = 0; $itemNum < $leaders; $itemNum++): ?>
        <div class="x-item item">
            <?php echo $this->Html->link($relatedItems[$itemNum]['Item']['name'], array('controller' => 'items', 'action' => 'view', $relatedItems[$itemNum]['Item']['id'])); ?>
        </div>        
        <?php endfor;?>
    </div>
    
    <?php if ($list > 0): ?>
    <table class="list">
        <tr>
            <th><?php echo __('Items'); ?></th>
        </tr>
        <?php for ($itemNum = $leaders; $itemNum < count($relatedItems); $itemNum++): ?>
        <tr>
            <td>
                <?php
                    echo $this->Html->image('icons/item.png', array('alt' => __('Edit')));
                    echo $this->Html->link($relatedItems[$itemNum]['Item']['name'], array('controller' => 'items', 'action' => 'view', $relatedItems[$itemNum]['Item']['id']));
                ?>
            </td>
        </tr>
        <?php endfor; ?>
    </table>
    <?php endif; ?>
<?php endif; ?>

</div>
</div>
<div class="clear"></div>
