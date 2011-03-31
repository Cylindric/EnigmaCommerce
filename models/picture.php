<?php

/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package core
 */

/**
 * Model for manipulating Pictures.
 * 
 * @package core
 * @subpackage models
 */
class Picture extends AppModel {

    var $name = 'Picture';
    var $actsAs = array(
        'Sluggable' => array(
            'label' => 'name'
        ),
    );
    var $hasMany = array('ItemPicture');

    const ASPECT_SQUARE = 0;
    const ASPECT_LANDSCAPE = 1;
    const ASPECT_PORTRAIT = 2;

    public function beforeDelete() {
        $picture = $this->read();
        $filename = $this->productPath() . $picture['Picture']['filename'];
        if (file_exists($filename) && is_file($filename)) {
            unlink($filename);
        }
        return true;
    }

    public function productPath() {
        return WWW_ROOT . 'img' . DS . 'products' . DS;
    }

    /**
     * Takes the specified image filename, imports it into the standard product
     * path, and creates a record for it.
     * If $name is specified, that will be used for the destination file name,
     * otherwise the existing name will be used.  Sluggable is used to create a 
     * unique name in either case.
     * @param string $$filename The full filesystem path to the image to import.
     * @param string $name The name to give the file, or null to use the existing name.
     * @return array The created Picture array.
     */
    public function import($filename, $settings = array()) {
        $defaultSettings = array(
            'filename' => null,
            'overwrite' => false,
            'create' => false,
            'name' => null,
            'imgPath' => $this->productPath(),
        );

        if (is_array($filename)) {
            $settings = array_merge($defaultSettings, $filename);
        } else {
            $settings = array_merge($defaultSettings, $settings);
            $settings['filename'] = $filename;
        }
        extract($settings);

        if (!file_exists($filename)) {
            throw new InvalidArgumentException('Source file missing');
        }

        $file = pathinfo($filename);
        $imageInfo = getimagesize($filename);
        if ($imageInfo !== false) {
            $width = $imageInfo[0];
            $height = $imageInfo[1];
        }

        if (($create) || (!$this->id)) {
            if (!$create) {
                throw new InvalidArgumentException('No Picture specified');
            } else {
                // Create a new Picture object
                if (empty($name)) {
                    $name = $file['filename'];
                }
                $picture = $this->create();
                $picture['Picture']['name'] = $name;
                $picture = $this->save($picture);
            }
        }
        $picture = $this->read();

        // Move the image to the correct location
        $destination = $imgPath . $this->data['Picture']['slug'] . '.' . strtolower($file['extension']);

        $tmpName = '';
        if (file_exists($destination)) {
            if ($overwrite) {
                $tmpName = $destination . '.old';
                rename($destination, $tmpName);
            } else {
                $this->delete();
                throw new FileExistsException($destination);
            }
        }

        // If the copy fails, delete the just-created record, and if a backup was made 
        // of a clobbered destination-file, put it back.
        try {
            copy($filename, $destination);
        } catch (Exception $e) {
            if ((strlen($tmpName) > 0) && (file_exists($tmpName))) {
                rename($tmpName, $destination);
            }
            $this->delete();
            throw new AccessDeniedException($destination);
        }

        // Update the image info
        $picture['Picture']['filename'] = basename($destination);
        $picture['Picture']['width'] = $width;
        $picture['Picture']['height'] = $height;
        if ($width > $height) {
            $picture['Picture']['aspect'] = Picture::ASPECT_LANDSCAPE;
        } elseif ($width < $height) {
            $picture['Picture']['aspect'] = Picture::ASPECT_PORTRAIT;
        } else {
            $picture['Picture']['aspect'] = Picture::ASPECT_SQUARE;
        }

        $picture = $this->save($picture);

        if ((strlen($tmpName) > 0) && (file_exists($tmpName)) && (file_exists($destination))) {
            unlink($tmpName);
        }

        return $picture;
    }

}
