<div class="details form">
<?php echo $this->Form->create('Detail');?>
	<fieldset>
 		<legend><?php __('Edit Detail'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('item_id');
		echo $this->Form->input('unit_id');
		echo $this->Form->input('size');
		echo $this->Form->input('name');
		echo $this->Form->input('slug');
		echo $this->Form->input('price');
		echo $this->Form->input('rrp');
		echo $this->Form->input('stockcode');
		echo $this->Form->input('status');
		echo $this->Form->input('legacy_id');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('Detail.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('Detail.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Details', true), array('action' => 'index'));?></li>
	</ul>
</div>