<?php
App::import('Model', 'User');

class DetailTestCase extends CakeTestCase {
    var $fixtures = array('app.user');

    // TODO test user admin checks - needs a Group mock
    function testIsAdmin() {
        $this->User =& ClassRegistry::init('User');
    }

}