<?php echo $this->element('categories/menu'); ?>
<div class="body-wide" id="body">

    <?php if (!empty($title)): ?>
            <h1><?php echo($title); ?></h1>
    <?php endif; ?>

    <?php echo $this->Session->flash(); ?>

    <table class="list">
        <tr>
            <th><?php echo __('Name'); ?></th>
        </tr>
        <?php    foreach ($categories as $category): ?>
        <tr>
            <td>
                <?php
                    echo $this->Html->image('icons/category.png', array('alt' => __('Edit')));
                    echo $this->Link->view('Category', $category['Category']);
                ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

</div>