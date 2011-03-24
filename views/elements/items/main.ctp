<table>
    <tr>
        <td>
            <?php echo $this->Image->itemThumb($item, array('link' => false, 'blank' => false)); ?>    
        </td>        
        <td>
            <h1><?php echo $item['Item']['name']; ?></h1>
            <?php if ($item['Item']['recommended']): ?>
                <?php echo __('Recommended'); ?>
            <?php endif; ?>
            <p><?php echo $item['Item']['description']; ?></p>
            <?php if (count($item['Variation']) > 0): ?>
                <?php echo $this->element('variations/item_list', array('data' => $item)); ?>
            <?php endif; ?>
        </td>        
    </tr>
</table>
