<?php

/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 */
class CategoryFixture extends CakeTestFixture {

    var $name = 'Category';
    var $import = array('table' => 'categories');
    var $records = array(
        array('id' => 1, 'name' => 'catrootnode', 'slug' => 'catrootnode', 'parent_id' => 0),
        array('id' => 2, 'name' => 'Cat 1', 'slug' => 'cat-1', 'parent_id' => 1),
        array('id' => 3, 'name' => 'Cat 1-a', 'slug' => 'cat-1a', 'parent_id' => 2),
        array('id' => 4, 'name' => 'Cat 1-a-1', 'slug' => 'cat-1a1', 'parent_id' => 3),
        array('id' => 5, 'name' => 'Cat 1-a-2', 'slug' => 'cat-1a2', 'parent_id' => 3),
        array('id' => 6, 'name' => 'Cat 1-b', 'slug' => 'cat-1b', 'parent_id' => 2),
        array('id' => 7, 'name' => 'Cat 2', 'slug' => 'cat-2', 'parent_id' => 1),
        array('id' => 8, 'name' => 'Cat 2-a', 'slug' => 'cat-2a', 'parent_id' => 7),
        array('id' => 9, 'name' => 'Cat 2-b', 'slug' => 'cat-2b', 'parent_id' => 7),
        array('id' => 10, 'name' => 'Cat 2-b-1', 'slug' => 'cat-2b1', 'parent_id' => 9),
        array('id' => 11, 'name' => 'Cat 2-b-2', 'slug' => 'cat-2b2', 'parent_id' => 9),
    );

}