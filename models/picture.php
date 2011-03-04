<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 */

class Picture extends AppModel {

    var $name = 'Picture';

    public function import($img) {
        var_dump(WEB_ROOT);
        die($img);
    }
    
}
