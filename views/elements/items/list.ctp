<table class="list">
    <?php foreach ($items as $item):  ?>
    <tr>
        <td><?php echo $this->element('items/list_item', array('item' => $item)); ?></td>
    </tr>
    <?php endforeach; ?>

</table>
