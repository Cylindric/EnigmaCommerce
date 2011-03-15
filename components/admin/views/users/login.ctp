<div class="content-1"> 
<?php
$this->Session->flash('auth');
echo $this->Form->create('User', array('action' => 'login', 'inputDefaults' => array('label' => false)));
echo $this->Form->inputs(array(
	'legend' => __('Login', true),
	'username' => array('label' => __('Username')),
	'password' => array('label' => __('Password'))
));
echo $this->Form->end('Login');
?>
</div>