<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package core
 * @subpackage config
 */

/**
 * Defines the locations of various system elements, such as plugins
 */
App::build(array(
    'plugins' => array(ROOT.DS.APP_DIR.DS.'components'.DS),
));

/**
 * @package core
 * @subpackage config
 */
class FileExistsException extends CakeException {
    protected $_messageTemplate = 'File already exists. %s';
}

/**
 * @package core
 * @subpackage config
 */
class AccessDeniedException extends CakeException {
    protected $_messageTemplate = 'Cannot write file. %s';
}

