<?php echo $this->element('category_header'); ?>

    <table class="list">
        <tr>
            <th><?php echo $this->Paginator->sort(__('Name'), 'name'); ?></th>
            <th class="actions"><?php __('Actions');?></th>
        </tr>
        <?php	foreach ($categories as $category): ?>
        <tr>
            <td>
                <?php
					echo $this->Html->image('icons/category.png', array('alt' => __('Edit')));
                    echo $category['Category']['name'];
                ?>
            </td>
            <td class="actions">
            <?php echo $this->Html->image('icons/delete.png', array('alt' => __('Delete'), 'url' => array('controller' => 'categories', 'action' => 'delete', $category['Category']['id']))); ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

<?php echo $this->element('general_footer'); ?>
