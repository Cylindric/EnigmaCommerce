<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package core
 * @subpackage config
 */

App::build(array(
    'plugins' => array(ROOT.DS.APP_DIR.DS.'components'.DS),
));

class FileExistsException extends CakeException {
    protected $_messageTemplate = 'File already exists. %s';
}

class AccessDeniedException extends CakeException {
    protected $_messageTemplate = 'Cannot write file. %s';
}

