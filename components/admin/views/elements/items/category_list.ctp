<h2><?php echo __('Categories'); ?></h2>
<table>
    <tr>
        <th><?php echo __('name'); ?></th>
        <th><?php echo __('primary'); ?></th>
        <th><?php echo __('actions'); ?></th>
    </tr>
    <?php foreach ($categories['CategoryItem'] as $categoryItem): ?>
        <tr>
            <td><?php echo $categoryItem['Category']['name']; ?></td>
            <td><?php echo ($categoryItem['is_primary'] ? __('Yes') : __('No')); ?></td>
            <td><?php echo __('remove'); ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<?php 
    echo $this->Form->create('CategoryItems', array('action' => 'add'));
    echo $this->Form->input('item_id', array('type' => 'hidden', 'value'=>$categories['Item']['id']));
    echo $this->Form->input('category_id', array('label' => false));
    echo $this->Form->end('Add');
?>
