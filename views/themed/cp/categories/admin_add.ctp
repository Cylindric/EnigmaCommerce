<?php echo $this->element('category_header', array('title'=>sprintf(__('New %s', true), __('category', true)))); ?>

<?php echo $form->create(); ?>

	<h2><?php sprintf(__('New %s', true), __('category', true));?></h2>

    <table>
        <tr>
            <th><?php __('Parent'); ?></th>
            <td><?php echo $form->input('parent_id', array('label'=>false)); ?></td>
        </tr>
        <tr>
            <th><label for="CategoryName" ><?php __('Name'); ?></label></th>
            <td><?php echo $form->input('name', array('label'=>false)); ?></td>
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
            <th><label for="CategoryDescription"><?php __('Stock Code Prefix'); ?></label></th>
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

<?php echo $this->element('general_footer'); ?>
