<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package core
 * @subpackage config
 */

Configure::write('sef', true);

Configure::write('migrate.productImages', ROOT.DS.'products'.DS);

/* Config.language sets the global system language for the application.
 * Set to your three-letter country-code, matching the language translation
 * files in /locale/.
 * Set to 'xxx' to use the test strings; useful for finding untranslated strings.
 */
//Configure::write('Config.language', 'xxx');
