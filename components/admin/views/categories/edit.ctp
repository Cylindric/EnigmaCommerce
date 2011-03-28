<div class="content-1">
    <h1><?php echo __('Category'); ?></h1>
    <?php 
        echo $this->Form->create('Category', array('inputDefaults' => array('label' => false)));
        
        echo $this->Form->input(
            'id');

        echo $this->Html->tag('label', __('Name'));
        echo $this->Form->input(
            'name', array(
            'error' => array('notEmpty'=>__('%s cannot be blank', __('Name')))));
        
        echo $this->Html->tag('label', __('Slug'));
        echo $this->Form->input(
            'slug', array(
            'error' => array('unique'=>__('%s must be unique', __('Slug')))));

        echo $this->Html->tag('label', __('Parent'));
        echo $this->Form->input(
            'parent_id');
        
        echo $this->Html->tag('label', __('Description'));
        echo $this->Form->input(
            'description');
        
        echo $this->Form->inputs(array(
            'legend' => __('Visbility'),
            'visible_on_web' => array('label' => __('Web')), 
            'visible_on_catalogue' => array('label' => __('Catalogue'))));
        
        echo $this->Form->submit(__('Save'));
        echo $this->Form->end();
    ?>

    <h1><?php echo __('Items'); ?></h1>
    <?php 
        foreach ($items as $item) {
            echo $this->element('items/compact_list', array('item' => $item));
        }
    ?>
</div>

<div class="content-2"> 
    <?php echo $this->element('categories/menu'); ?>
</div>