<div class="content-1">
    <?php echo $this->Session->flash(); ?>                    
    <h1><?php echo __('Item'); ?></h1>
    <?php
    echo $this->Form->create('Item', array('inputDefaults' => array('label' => false)));

    echo $this->Form->input(
            'id');

    echo $this->Html->tag('label', __('Name'));
    echo $this->Form->input(
            'name', array(
        'error' => array('notEmpty' => __('The %s cannot be blank', __('name')))));

    echo $this->Html->tag('label', __('Slug'));
    echo $this->Form->input(
            'slug', array(
        'error' => array('unique' => __('The %s must be unique', __('slug')))));

    echo $this->Html->tag('label', __('Description'));
    echo $this->Form->input(
            'description');

    echo $this->Form->submit(__('Save'));
    echo $this->Form->end();
    ?>
</div>

<div class="content-2"> 
    <?php echo $this->element('items/menu'); ?>
</div>