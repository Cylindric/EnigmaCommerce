<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 */

class PictureTestCase extends CakeTestCase {
    var $fixtures = array('app.picture');
    
    var $sampleSource = array();
    var $sampleDest = array();
    
    function setUp() {
        parent::setUp();
        $this->Picture =& ClassRegistry::init('Picture');
        
        $this->sampleSource[] = dirname(dirname(dirname(__FILE__))).DS.'resources'.DS.'sample0.jpg';
        $this->sampleDest[] = $this->Picture->productPath().'sample0.jpg';

        $this->sampleSource[] = dirname(dirname(dirname(__FILE__))).DS.'resources'.DS.'sample1.png';
        $this->sampleDest[] = $this->Picture->productPath().'sample1.png';
    }

    public function tearDown() {
        parent::tearDown();
        foreach ($this->sampleDest as $dest) {
            if (file_exists($dest)) {
                unlink($dest);
            }
        }
    }
    
    function testImport() {
        $picture = $this->Picture->import($this->sampleSource[0], array('create' => true));
        $this->assertFileExists($this->sampleDest[0]);
        $this->assertFileEquals($this->sampleSource[0], $this->sampleDest[0]);
    }

    function testImportWithoutRecordFails() {
        $this->expectException('InvalidArgumentException');
        $picture = $this->Picture->import(array('filename' => $this->sampleSource[0], 'create' => false));
    }
    
    function testImportWithOnlyArrayParameters() {
        $picture = $this->Picture->import(array('filename' => $this->sampleSource[0], 'create' => true));
        $this->assertFileExists($this->sampleDest[0]);
        $this->assertFileEquals($this->sampleSource[0], $this->sampleDest[0]);
    }
    
    function testImportSavesProperties() {
        $this->Picture->import($this->sampleSource[0], array('create' => true));
        $picture = $this->Picture->findById($this->Picture->id);
        $this->assertEqual(basename($this->sampleSource[0]), $picture['Picture']['filename']);
        $this->assertEqual(200, $picture['Picture']['width']);
        $this->assertEqual(150, $picture['Picture']['height']);
    }
    
    function testImportFailsIfTargetExists() {
        // put a blocking image in the way
        copy($this->sampleSource[0], $this->sampleDest[0]);

        // make sure that the copy fails
        $excepted = false;
        try {
            $picture = $this->Picture->import($this->sampleSource[0], array('create' => true));
        } catch (FileExistsException $expected) {
            $excepted = true;
        }
        $this->assertEqual(true, $excepted);
    }
    
    function testImportSucceedsIfOverwrite() {
        copy($this->sampleSource[1], $this->sampleDest[0]);
        $picture = $this->Picture->import($this->sampleSource[0], array('overwrite' => true, 'create' => true));
        $this->assertFileExists($this->sampleDest[0]);
        $this->assertFileEquals($this->sampleSource[0], $this->sampleDest[0]);
    }
    
    public function testDeleteRemovesImageFile() {
        $this->Picture->import($this->sampleSource[0], array('create' => true));
        $this->assertFileExists($this->sampleDest[0]);
        $this->Picture->delete();
        $this->assertFileNotExists($this->sampleDest[0]);
    }

    public function testSpecifiedNameDefaultsToOriginalName() {
        $picture = $this->Picture->import($this->sampleSource[0], array('create' => true));
        $this->assertFileExists($this->sampleDest[0]);
    }
    
    public function testSpecifiedName() {
        $rndname = uniqid();
        $expected = $this->Picture->productPath() . $rndname . '.jpg';
        $picture = $this->Picture->import($this->sampleSource[0], array('name' => $rndname, 'create' => true));
        $this->assertFileExists($expected);
        unlink($expected);
    }

    public function testImportToReadOnlyDestinationRaisesException() {
        $rndName = uniqid();
        $tmpPath = $this->Picture->productPath() . $rndName . DS;
        mkdir($tmpPath);
        chmod($tmpPath, 0500);
        $expected = $tmpPath . 'sample0.jpg';
        $excepted = false;
        try {
            $picture = $this->Picture->import($this->sampleSource[0], array('imgPath' => $tmpPath, 'create' => true));
        } catch (Exception $exception) {
            $excepted = true;
        }
        $this->assertEqual(true, $excepted);
        rmdir($tmpPath);
    }
    
}