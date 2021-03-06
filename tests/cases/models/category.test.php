<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 */

App::import('Model', 'Category');
App::import('Model', 'CategoryItem');

class CategoryTestCase extends CakeTestCase {
    var $fixtures = array(
        'app.category', 
        'app.category_item',
        'app.status',
    );
    
    public function setup() {
        parent::setup();
        $this->Category = ClassRegistry::init('Category');
//        $this->Category->recover();
    }

    public function testMenuNodes() {
        $nodes = $this->Category->menuNodes(0);
        
        $this->assertTrue(count($nodes)==1);
        $this->assertEqual('catrootnode', $nodes[0]['Category']['name']);
    }
    
    public function testMenuNodesContainsChildren() {
        $rootNode = $this->getCategory('catrootnode');
        $rootId = $rootNode['Category']['id'];

        $expectedChildren = $this->Category->find('count', array(
            'conditions' => array(
                'Category.parent_id' => $rootId)));
        if ($expectedChildren == 0) {
            $this->markTestSkipped('Test data is missing required categories.');
        }

        $nodes = $this->Category->menuNodes($rootId);
        $this->assertTrue(array_key_exists('children', $nodes[0]), 'Results don\'t include children');
        $this->assertEqual($expectedChildren, count($nodes[0]['children']));
    }

    public function testMenuNodesContainsChildrensChildren() {
        $rootNode = $this->getCategory('catrootnode');
        $rootId = $rootNode['Category']['id'];

        $nodes = $this->Category->menuNodes($rootId);
        $this->assertTrue(array_key_exists('children', $nodes[0]), 'Results don\'t include children');
        $this->assertGreaterThan(0, count($nodes[0]['children']));
        $firstChild = $nodes[0]['children'][0];
        $this->assertTrue(array_key_exists('children', $firstChild), 'Results don\'t include subchildren');
        $this->assertGreaterThan(0, count($firstChild['children']));
    }
    
    public function testSubCatgories() {
        $rootNode = $this->getCategory('catrootnode');
        $rootId = $rootNode['Category']['id'];
        
        $expectedChildren = $this->Category->find('count', array(
            'conditions' => array(
                'Category.parent_id' => $rootId)));
        if ($expectedChildren == 0) {
            $this->markTestSkipped('Test data is missing required categories.');
        }
        
        $nodes = $this->Category->subCategories($rootId);
        $this->assertEquals($expectedChildren, count($nodes));
    }
    
    private function getCategory($id) {
        $node = $this->Category->findById($id);
        if ($node === false) {
            $this->markTestSkipped('Test data is missing required root category.');
        }
        return $node;
    }
    
}