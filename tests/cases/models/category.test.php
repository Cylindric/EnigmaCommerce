<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 */

App::import('Model', 'Category');
App::import('Model', 'CategoryItem');

class CategoryTestCase extends CakeTestCase {
    var $fixtures = array('app.category', 'app.category_item');

    function testTest() {
        $this->Category =& ClassRegistry::init('Category');
        var_dump($this->Category->create());
    }

}