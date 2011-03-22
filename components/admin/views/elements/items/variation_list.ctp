<h2><?php echo __('Variations'); ?></h2>
<table>
        <tr>
            <th><?php echo __('name'); ?></th>
            <th><?php echo __('price'); ?></th>
            <th><?php echo __('actions'); ?></th>
        </tr>
    <?php foreach ($variations['Variation'] as $variation): ?>
        <tr>
            <td><?php echo $variation['name']; ?></td>
            <td><?php echo $this->Format->currency($variation['price']); ?></td>
            <td><?php echo __('edit'); ?>, <?php echo __('delete'); ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<?php echo __('add new %s', __('variation')); ?>