<div class="content-1">
    <?php echo $this->Session->flash(); ?>                    
    <?php 
        echo $this->Form->create('Category');
        
        echo $this->Form->input(
            'id');
        
        echo $this->Form->input(
            'name', array(
            'error' => array('notEmpty'=>__('%s cannot be blank', 'Name'))));
        
        echo $this->Form->input(
            'slug', array(
            'error' => array('unique'=>__('%s must be unique', 'Slug'))));
        
        echo $this->Form->input(
            'parent_id');
        
        echo $this->Form->input(
            'description');
        
        echo $this->Form->inputs(array(
            'legend' => __('Visbility'),
            'visible_on_web', 
            'visible_on_catalogue'));

        echo $this->Form->submit(__('Create'));
        echo $this->Form->end();
    ?>
</div>

<div class="content-2"> 
    <?php echo $this->element('categories/menu'); ?>
</div>