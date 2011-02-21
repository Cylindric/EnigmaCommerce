<?php echo $this->element('categorymenu'); ?>
<div id="body" class="view">
<div id="bodycontent">

<?php if (!empty($title)): ?>
		<h1><?php echo($title); ?></h1>
<?php endif; ?>

<?php echo $this->Session->flash(); ?>

<?php if (count($categories)>0): ?>
	<table class="list">
		<tr>
			<th><?php echo __('Categories'); ?></th>
		</tr>
		<?php foreach ($categories as $category): ?>
		<tr>
			<td>
				<?php
					echo $this->Html->image('icons/category.png', array('alt' => __('Edit')));
					echo $category['Category']['name'];
				?>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
<?php endif; ?>

<?php if (count($items)>0): ?>
	<table class="list">
		<tr>
			<th><?php echo __('Items'); ?></th>
		</tr>
		<?php foreach ($items as $item): ?>
		<tr>
			<td>
				<?php
					echo $this->Html->image('icons/item.png', array('alt' => __('Edit')));
					echo $item['Item']['name'];
				?>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
<?php endif; ?>

</div>
</div>
<div class="clear"></div>
