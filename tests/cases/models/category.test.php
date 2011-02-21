<?php
App::import('Model', 'Category');
App::import('Model', 'CategoryItem');

class CategoryTestCase extends CakeTestCase {
    var $fixtures = array('app.category', 'app.category_item');

    function testTest() {
        $this->Category =& ClassRegistry::init('Category');
        var_dump($this->Category->create());
    }

}