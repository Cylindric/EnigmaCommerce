<?php echo $this->element('category_header'); ?>

<?php
    $edit_text = sprintf(__('Edit %s', true), __('category', true));
    $edit_url = array('controller' => 'categories', 'action' => 'edit', $this->data['Category']['id']);
    $delete_text = sprintf(__('Delete %s', true), __('category', true));
    $delete_url = array('controller' => 'categories', 'action' => 'delete', $this->data['Category']['id']);
    $create_child_text = sprintf(__('Create %s %s', true), __('child', true),  __('category', true));
    $create_child_url = array('controller' => 'categories', 'action' => 'add', 'parent_id' => $this->data['Category']['id']);
    $create_item_text = sprintf(__('Create %s %s', true), __('child', true), __('item', true));
    $create_item_url = array('controller' => 'items', 'action' => 'add', 'category_id' => $this->data['Category']['id']);
?>

<?php echo $form->create(); ?>

    <h2><?php  __('Category');?></h2>
    <table>
        <tr>
            <th><label for="CategoryName" ><?php __('Name'); ?></label></th>
            <td><?php echo $form->input('name', array('label'=>false)); ?></td>
        </tr>
        <tr>
            <th><label for="CategoryParentId"><?php __('Parent'); ?></label></th>
            <td><?php echo $form->input('parent_id', array('label'=>false)); ?></td>
        </tr>
        <tr>
            <th><label for="CategoryTag" ><?php __('Tag'); ?></label></th>
            <td><?php echo $form->input('tag', array('label'=>false)); ?></td>
        </tr>
        <tr>
            <th><label for="CategoryDescription"><?php __('Description'); ?></label></th>
            <td><?php echo $form->input('description', array('label'=>false)); ?></td>
        </tr>
        <tr>
            <th><label for="CategoryStockcodeprefix"><?php __('Stock Code Prefix'); ?></label></th>
            <td><?php echo $form->input('stockcodeprefix', array('label'=>false)); ?></td>
        </tr>
        <tr>
            <th></th>
            <td><?php
            echo $this->Js->submit(
                    __('Save', true),
                    array(
                        'update' => '#bodycontent',
				        'before' => $this->Js->get('#ajax-save-message')->effect('fadeIn'),
					    'complete' => $this->Js->get('#ajax-save-message')->effect('fadeOut')
                    ));
            ?></td>
        </tr>
    </table>

    <div id="ajax-save-message">Saving...</div>
    
<?php echo $form->end(); ?>

    <div class="actions">
		<h3><?php __('Actions'); ?></h3>
		<ul>
			<li class="delete">
				<?php echo $this->Html->image('icons/delete.png', array('alt' => $delete_text, 'url' => $delete_url)); ?>
				<?php echo $this->Js->link($delete_text, $delete_url, array('update' => '#bodycontent')); ?>
			</li>
			<li class="add">
				<?php echo $this->Html->image('icons/category.png', array('alt' => $create_child_text, 'url' => $create_child_url)); ?>
				<?php echo $this->Js->link($create_child_text, $create_child_url, array('update' => '#bodycontent', 'before' => $this->Js->get('#bodycontent')->effect('fadeOut'), 'complete' => $this->Js->get('#bodycontent')->effect('fadeIn'))); ?>
			</li>
			<li class="add">
				<?php echo $this->Html->image('icons/item.png', array('alt' => $create_item_text, 'url' => $create_item_url)); ?>
				<?php echo $this->Js->link($create_item_text, $create_item_url, array('update' => '#bodycontent', 'before' => $this->Js->get('#bodycontent')->effect('fadeOut'), 'complete' => $this->Js->get('#bodycontent')->effect('fadeIn'))); ?>
			</li>
		</ul>
	</div>

<?php echo $this->element('general_footer'); ?>
