<div id="left_menu">
</div>
<div id="body" class="view">
<div id="bodycontent">

<?php echo $this->Session->flash(); ?>

<?php 
    $delete_text = __('Delete %s', __('category'));
    $delete_url = array('controller' => 'categories', 'action' => 'delete', $this->data['Category']['id']);
    
    $create_child_text = __('Create %s %s', __('child'),  __('category'));
    $create_child_url = array('controller' => 'categories', 'action' => 'add', 'parent_id' => $this->data['Category']['id']);
?>

<?php echo $this->Form->create(); ?>

    <h2><?php  __('Category');?></h2>
    <table>
        <tr>
            <th><label for="CategoryName" ><?php echo __('Name'); ?></label></th>
            <td><?php echo $this->Form->input('name', array('label'=>false)); ?></td>
        </tr>
        <tr>
            <th><label for="CategoryParentId"><?php echo __('Parent'); ?></label></th>
            <td><?php echo $this->Form->input('parent_id', array('label'=>false)); ?></td>
        </tr>
        <tr>
            <th><label for="CategoryTag" ><?php echo __('Tag'); ?></label></th>
            <td><?php echo $this->Form->input('tag', array('label'=>false)); ?></td>
        </tr>
        <tr>
            <th><label for="CategoryDescription"><?php echo __('Description'); ?></label></th>
            <td><?php echo $this->Form->input('description', array('label'=>false)); ?></td>
        </tr>
        <tr>
            <th><label for="CategoryStockcodeprefix"><?php echo __('Stock Code Prefix'); ?></label></th>
            <td><?php echo $this->Form->input('stockcodeprefix', array('label'=>false)); ?></td>
        </tr>
        <tr>
            <th></th>
            <td><?php echo $this->Form->submit(__('Save'));?></td>
        </tr>
    </table>


    <div class="actions">
		<h3><?php echo __('Actions'); ?></h3>
		<ul>
			<li class="delete">
				<?php echo $this->Html->link($delete_text, $delete_url); ?>
			</li>
			<li class="add">
				<?php echo $this->Html->link($create_child_text, $create_child_url); ?>
			</li>
		</ul>
	</div>
<?php echo $this->Form->end(); ?>

</div>
</div>
<div class="clear"></div>
