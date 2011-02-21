<div class="details form">
<?php echo $this->Form->create('Detail');?>
	<fieldset>
 		<legend><?php __('Add Detail'); ?></legend>
	<?php
		echo $this->Form->input('item_id');
		echo $this->Form->input('unit_id');
		echo $this->Form->input('size');
		echo $this->Form->input('name');
		echo $this->Form->input('tag');
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

		<li><?php echo $this->Html->link(__('List Details', true), array('action' => 'index'));?></li>
	</ul>
</div>