<div id="left-navigation">
<?php
	echo $this->TreeMenu->generate(
		$this->requestAction('categories/menu'),
		array(
			'model'=>'Category',
			'controller'=>'categories',
			'action'=>'view',
			'ajaxtarget'=>'bodycontent'
		)
	);
?>
</div>