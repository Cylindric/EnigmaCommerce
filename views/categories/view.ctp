<?php echo $this->element('categorymenu'); ?>
<div id="body" class="view">
    <div id="bodycontent">

    <?php if (!empty($title)): ?>
        <h1><?php echo($title); ?></h1>
    <?php endif; ?>

    <?php echo $this->Session->flash(); ?>

    <?php if (count($subCategories)>0): ?>
        <table class="list">
            <tr>
                <th><?php echo __('Sub-categories'); ?></th>
            </tr>
            <?php foreach ($subCategories as $category): ?>
            <tr>
                <td>
                    <?php
                        echo $this->Html->image('icons/category.png', array('alt' => __('View')));
                        echo $this->Link->view('Category', $category['Category']);
                    ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <?php if (count($relatedItems)>0): ?>

        <table class="list">
            <tr>
                <th><?php echo __('Items'); ?></th>
            </tr>
            <?php foreach ($relatedItems as $item): ?>
            <tr>
                <td>
                    <?php
                        echo $this->Html->image('icons/item.png', array('alt' => __('View')));
                        echo $this->Link->view('Item', $item['Item']);
                    ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    </div>
</div>
