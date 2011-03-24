<div class="x-list variation">
    <table>
        <?php foreach ($data['Variation'] as $variation): ?>
            <tr>
                <td><?php echo $variation['name']; ?></td>
                <td><?php echo $this->Format->currency($variation['price']); ?></td>
                <td><?php echo $this->Html->link(__('Buy'), array('controller' => 'basket', 'action' => 'add', $data['Item']['slug'], $variation['slug'])); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
