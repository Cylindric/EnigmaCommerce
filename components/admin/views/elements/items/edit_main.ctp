<h1><?php echo __('Item'); ?></h1>
<?php
echo $this->Form->create('Item');

echo $this->Form->input(
        'id');

echo $this->Form->input(
        'name', array(
    'label' => __('Name'),
    'error' => array('notEmpty' => __('The %s cannot be blank', __('name')))));

echo $this->Form->input(
        'slug', array(
    'label' => __('Slug'),
    'error' => array('unique' => __('The %s must be unique', __('slug')))));

echo $this->Form->input(
        'description', array(
    'label' => __('Description')));

echo $this->Form->input(
        'recommended', array(
    'label' => __('Recommended')));

echo $this->Form->inputs(array(
    'legend' => __('Visbility'),
    'visible_on_web' => array('label' => __('Web')),
    'visible_on_catalogue' => array('label' => __('Catalogue'))));

echo $this->Form->submit(__('Save'));
echo $this->Form->end();
?>

<label><?php echo __('Created'); ?></label><?php echo $this->data['Item']['created']; ?>
<label><?php echo __('Modified'); ?></label><?php echo $this->data['Item']['modified']; ?>