<div class="install">
	<ul class="menu">
		<li><?php echo $this->Html->link(__('Create Blank', true), '/install/create/blank', array(), __('Are you sure you want to recreate all tables?', true));?></li>
		<li><?php echo $this->Html->link(__('Create Sample', true), '/install/create/sample', array(), __('Are you sure you want to insert sample data?', true));?></li>
	</ul>
</div>