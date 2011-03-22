<h2><?php echo __('Categories'); ?></h2>
<table>
        <tr>
            <th><?php echo __('name'); ?></th>
            <th><?php echo __('primary'); ?></th>
            <th><?php echo __('actions'); ?></th>
        </tr>
    <?php foreach ($categories['CategoryItem'] as $category): ?>
        <tr>
            <td><?php //echo $category['name']; ?></td>
            <td><?php echo $category['is_primary']; ?></td>
            <td><?php echo __('edit'); ?>, <?php echo __('delete'); ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<?php echo __('add new %s', __('Category')); ?>