<div id="cat_menu">
</div>
<div id="body" class="view">
<div id="bodycontent">


<?php if (!empty($title)): ?>
		<h1><?php echo($title); ?></h1>
<?php endif; ?>

<?php echo $this->Session->flash(); ?>

	<table class="list">
		<tr>
			<th><?php echo $this->Paginator->sort(__('Name', true), 'name'); ?></th>
		</tr>
		<?php	foreach ($categories as $category): ?>
		<tr>
			<td>
				<?php
					echo $this->Html->image('icons/category.png', array('alt' => __('Edit', true)));
					echo $category['Category']['name'];
				?>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>

	<?php
	if($this->Paginator->hasPage(2)) {
		echo $this->Paginator->first($this->Html->image('icons/resultset_first.png', array('alt' => __('First', true))), array('escape' => false));
		echo $this->Paginator->prev($this->Html->image('icons/resultset_previous.png', array('alt' => __('Previous', true))), array('escape' => false));
		echo $this->Paginator->numbers();
		echo $this->Paginator->next($this->Html->image('icons/resultset_next.png', array('alt' => __('Next', true))), array('escape' => false));
		echo $this->Paginator->last($this->Html->image('icons/resultset_last.png', array('alt' => __('Last', true))), array('escape' => false));
		echo $this->Paginator->counter();
	}
	?>

</div>
</div>
<div class="clear"></div>
