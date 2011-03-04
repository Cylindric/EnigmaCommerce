<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 */

class PictureTestCase extends CakeTestCase {
    var $fixtures = array('app.picture');

    function testImport() {
        $this->Picture =& ClassRegistry::init('Picture');
        
        $src = dirname(dirname(dirname(__FILE__))).DS.'resources'.DS.'picture_import.jpg';

        $picture = $this->Picture->import($src);

        $dst = $this->Picture->productPath().$picture['Picture']['filename'];
        $this->assertFileExists($dst);
        $this->assertFileEquals($src, $dst);
        
        @unlink($dst);
    }
 
    function testImportFailsIfTargetExists() {
        $this->Picture =& ClassRegistry::init('Picture');
        
        $src = dirname(dirname(dirname(__FILE__))).DS.'resources'.DS.'picture_import.jpg';
        $dst = $this->Picture->productPath().'picture_import.jpg';

        // put a blocking image in the way
        copy($src, $dst);

        // make sure that the copy fails
        $excepted = false;
        try {
            $picture = $this->Picture->import($src);
        } catch (CakeException $expected) {
            $excepted = true;
        }
        $this->assertEqual(true, $excepted);

        @unlink($dst);
    }
    
}