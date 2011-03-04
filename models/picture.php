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
    
    public function productPath() {
        return WWW_ROOT.'img'.DS.'products'.DS;        
    }

    public function import($img) {
        $file = pathinfo($img);
        $imageInfo = getimagesize($img);
        
        $filename = basename($img);
        $name = $file['filename'];
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
            throw new CakeException(__('File exists %s', $destination));
        }
        
        if (!@copy($img, $destination)) {
            $this->delete();
            throw new CakeException(__('Could not copy file'));
        }

        $picture['Picture']['filename'] = basename($destination);
        $picture =  $this->save($picture);
        
        $picture['Picture']['id'] = $this->id;
        return $picture;
    }
    
}
