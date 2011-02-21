	<div id="cat_menu">
	</div>
	<div id="body" class="view">
	<div id="bodycontent">


<?php if (!empty($title)): ?>
		<h1><?php echo($title); ?></h1>
<?php endif; ?>

<?php echo $this->Session->flash(); ?>

<h2><?php echo $item['Item']['name']; ?></h2>
<?php echo $item['Item']['description']; ?>

    <div class="related">
        <h3><?php __('Details');?></h3>
        <?php if (!empty($item['Detail'])):?>
        <table cellpadding = "0" cellspacing = "0">
        <tr>
            <th><?php __('Name'); ?></th>
            <th><?php __('Price'); ?></th>
            <th><?php __('Actions'); ?></th>
        </tr>
        <?php
            $i = 0;
            foreach ($item['Detail'] as $detail):
                $class = null;
                if ($i++ % 2 == 0) {
                    $class = ' class="altrow"';
                }
            ?>
            <tr<?php echo $class;?>>
                <td><?php echo $detail['name'];?></td>
                <td><?php echo $this->Number->currency($detail['price'], 'GBP');?></td>
                <td><?php echo $this->Html->link(__('Add to Basket'), array('controller' => 'basket', 'action' => 'basket', $detail['id'])); ?></td>
            </tr>
        <?php endforeach; ?>
        </table>
    <?php endif; ?>
    </div>

	</div>
	</div>
	<div class="clear"></div>
