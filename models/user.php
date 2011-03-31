<?php

/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package core
 */

/**
 * Model for manipulating Users.
 * 
 * @package core
 * @subpackage models
 */
class User extends AppModel {

    var $name = 'User';
    var $displayField = 'username';
    var $belongsTo = 'Group';
    var $validate = array(
        'username' => array(
            'rule' => 'alphaNumeric',
            'required' => true,
            'allowEmpty' => false,
            'message' => 'Please enter your username'
        ),
        'password' => array(
            'rule' => 'notEmpty',
            'required' => true,
            'allowEmpty' => false,
            'message' => 'Please enter your password'
        )
    );

    /**
     * Checks wether the current user (if $id is null) or the user specified by
     * $id has access to administrative functions.
     * 
     * @param int $id
     * @return boolean
     */
    function isAdmin($id=null) {
        if (!empty($id)) {
            $this->read('*', $id);
        }

        if ($this->data['Group']['access_admin'] == 1) {
            return true;
        } else {
            return false;
        }
    }

}