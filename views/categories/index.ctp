<?php echo $this->element('categorymenu'); ?>
<div id="body" class="view">
<div id="bodycontent">

<?php if (!empty($title)): ?>
		<h1><?php echo($title); ?></h1>
<?php endif; ?>

<?php echo $this->Session->flash(); ?>

	<table class="list">
		<tr>
			<th><?php echo __('Name'); ?></th>
		</tr>
		<?php	foreach ($categories as $category): ?>
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

</div>
</div>
<div class="clear"></div>
