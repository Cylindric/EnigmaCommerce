<?php
App::import('Model', 'Unit');

class TreenodesTestCase extends CakeTestCase {
	var $fixtures = array('app.unit');

    function testInitialise() {
		$this->Tree = new Unit();

        $nodes = $this->Tree->treeNodes(0);
        //var_dump($nodes);
    }

    function testRootedNodeSearch() {
		$this->Tree = new Unit();

        $nodes = $this->Tree->treeNodes($this->Tree->field('id', array('unit' => 'm')));
        var_dump($nodes);
    }

}