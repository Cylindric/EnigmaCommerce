<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 */

class PictureTestCase extends CakeTestCase {
    var $fixtures = array('app.picture');
    
    var $defaultImg;
    var $defaultDst;
    
    function setUp() {
        parent::setUp();
        $this->Picture =& ClassRegistry::init('Picture');
        $this->defaultImg = dirname(dirname(dirname(__FILE__))).DS.'resources'.DS.'picture_import.jpg';
        $this->defaultDst = $this->Picture->productPath().'picture_import.jpg';
    }

    public function tearDown() {
        parent::tearDown();
        if (file_exists($this->defaultDst)) {
            unlink($this->defaultDst);
        }
    }
    
    function testImport() {
        $picture = $this->Picture->import($this->defaultImg);
        $this->assertFileExists($this->defaultDst);
        $this->assertFileEquals($this->defaultImg, $this->defaultDst);
    }

    function testImportSavesProperties() {
        $this->Picture->import($this->defaultImg);
        $picture = $this->Picture->findById($this->Picture->id);
        $this->assertEqual(basename($this->defaultImg), $picture['Picture']['filename']);
        $this->assertEqual(200, $picture['Picture']['width']);
        $this->assertEqual(150, $picture['Picture']['height']);
    }
    
    function testImportFailsIfTargetExists() {
        $dst = $this->Picture->productPath().'picture_import.jpg';

        // put a blocking image in the way
        copy($this->defaultImg, $this->defaultDst);

        // make sure that the copy fails
        $excepted = false;
        try {
            $picture = $this->Picture->import($this->defaultImg);
        } catch (FileExistsException $expected) {
            $excepted = true;
        }
        $this->assertEqual(true, $excepted);
    }
    
    public function testDeleteRemovesImageFile() {
        $this->Picture->import($this->defaultImg);
        $this->assertFileExists($this->defaultDst);
        $this->Picture->delete();
        $this->assertFileNotExists($this->defaultDst);
    }

    public function testSpecifiedNameDefaultsToOriginalName() {
        $picture = $this->Picture->import($this->defaultImg);
        $this->assertFileExists($this->defaultDst);
    }
    
    public function testSpecifiedName() {
        $rndname = uniqid();
        $expected = $this->Picture->productPath() . $rndname . '.jpg';
        $picture = $this->Picture->import($this->defaultImg, $rndname);
        $this->assertFileExists($expected);
        unlink($expected);
    }
    
}