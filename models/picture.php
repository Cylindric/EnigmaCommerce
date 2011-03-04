<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 */

class Picture extends AppModel {

    var $name = 'Picture';
    
    var $actsAs = array(
        'Sluggable' => array(
            'label' => 'name'
        ),
    );
    
    public function beforeDelete() {
        $picture = $this->read();
        $filename = $this->productPath() . $picture['Picture']['filename'];
        if (file_exists($filename) && is_file($filename) ) {
            unlink($filename);
        }
        return true;
    }
    
    public function productPath() {
        return WWW_ROOT.'img'.DS.'products'.DS;        
    }

    /**
     * Takes the specified image filename, imports it into the standard product
     * path, and creates a record for it.
     * If $name is specified, that will be used for the destination file name,
     * otherwise the existing name will be used.  Sluggable is used to create a 
     * unique name in either case.
     * @param string $img The full filesystem path to the image to import.
     * @param string $name The name to give the file, or null to use the existing name.
     * @return array The created Picture array.
     */
    public function import($img, $name = null) {
        $file = pathinfo($img);
        $imageInfo = getimagesize($img);

        if (empty($name)) {
            $name = $file['filename'];
        }
        $width = $imageInfo[0];
        $height = $imageInfo[1];
        
        // Create a new Picture object
        $picture = $this->create();
        $picture['name'] = $name;
        $picture['width'] = $width;
        $picture['height'] = $height;
        $picture = $this->save($picture);
        
        // Move the image to the correct location
        $destination = $this->productPath() . $picture['Picture']['slug'].'.'.$file['extension'];
        
        if (file_exists($destination)) {
            $this->delete();
            throw new FileExistsException($destination);
        }
        
        if (!@copy($img, $destination)) {
            $this->delete();
            throw new AccessDeniedException($destination);
        }

        $picture['Picture']['filename'] = basename($destination);
        $picture = $this->save($picture);
        return $picture;
    }
    
}
