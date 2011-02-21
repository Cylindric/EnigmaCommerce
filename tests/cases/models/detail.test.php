<?php
App::import('Model', 'Detail');

class DetailTestCase extends CakeTestCase {
    var $fixtures = array('app.detail', 'app.item');
    
    /**
     * testNewRecordGetsTag()
     * 
     * Tests that when a new Detail is inserted, it is given a tag derived from the name.
     */
    function testNewRecordGetsTag() {
        $this->Detail =& ClassRegistry::init('Detail');
        
        $this->Detail->save($this->Detail->create(array('name'=>'test')));
        $this->assertEqual('test', $this->Detail->field('tag', array('id'=>1)));       
    }

    /**
     * testNewRecordGetsSameTagOnDifferentItems()
     * 
     * Tests that the uniqueness of the generated tag is limited to the current item.
     * Basically for Details the tag doesn't uniquely identify it - the item_id is
     * required too.
     */
    function testNewRecordGetsSameTagOnDifferentItems() {
        $this->Detail =& ClassRegistry::init('Detail');
        
        $this->Detail->save($this->Detail->create(array('name'=>'test', 'item_id'=>1)));
        $idA = $this->Detail->id;
        
        $this->Detail->save($this->Detail->create(array('name'=>'test', 'item_id'=>2)));
        $idB = $this->Detail->id;
        
        $this->assertEqual('test', $this->Detail->field('tag', array('id'=>$idA)));       
        $this->assertEqual('test', $this->Detail->field('tag', array('id'=>$idB)));       
    }

    /**
     * testNewRecordGetsDifferentTagOnSameItem()
     * 
     * Tests that tags are unique for Details with the same item_id.
     */
    function testNewRecordGetsDifferentTagOnSameItem() {
        $this->Detail =& ClassRegistry::init('Detail');
        
        $this->Detail->save($this->Detail->create(array('name'=>'test', 'item_id'=>1)));
        $idA = $this->Detail->id;
        
        $this->Detail->save($this->Detail->create(array('name'=>'test', 'item_id'=>1)));
        $idB = $this->Detail->id;
        
        $this->assertNotEqual($this->Detail->field('tag', array('id'=>$idA)), $this->Detail->field('tag', array('id'=>$idB)));       
    }
   
}