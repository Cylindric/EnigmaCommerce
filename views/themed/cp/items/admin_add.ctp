<?php echo $this->element('item_header'); ?>

<?php echo $this->Form->create('Item');?>

    <table>
        <tr>
            <th><label for="ItemName" ><?php __('Name'); ?></label></th>
            <td><?php echo $form->input('name', array('label'=>false)); ?></td>
        </tr>
        <tr>
            <th><label for="ItemTag" ><?php __('Tag'); ?></label></th>
            <td><?php echo $form->input('tag', array('label'=>false)); ?></td>
        </tr>
        <tr>
            <th><label for="ItemDescription"><?php __('Description'); ?></label></th>
            <td><?php echo $form->input('description', array('label'=>false)); ?></td>
        </tr>
        <tr>
            <th><label for="CategoryCategory"><?php __('Categories'); ?></label></th>
            <td><?php echo $form->input('Category', array('label'=>false)); ?></td>
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

<?php echo $this->Form->end();?>

<?php echo $this->element('general_footer'); ?>
