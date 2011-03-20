<?php

/* ----------------------------------------------------------------
  Filename: login.php

  Requirements:

  Notes:
  ------------------------------------------------------------------- */
require_https();

if (count($_POST) > 0) {
    if ($_POST['hidFormSubmitted'] == 'login') {
        $subUsername = $_POST['txtusername'];
        $subPassword = $_POST['txtpassword'];

        if (!empty($subUsername) && !empty($subPassword)) {
            //username and password supplied, so check against db
            $rs = view_User($subUsername, $subPassword);
            if (db_num_rows($rs) > 0) {
                $r = db_fetch_array($rs);
                login($r['UserID'], $r['UserName'], $r['FirstName'], $r['LastName']);
                reload_page();
            }
        }
    } else {
        logout();
        reload_page();
    }
}

if (authorised()) {
    spIntegrityCheck();
    $html->assign('USERNAME', $_SESSION['username']);
    $html->assign('FIRSTNAME', $_SESSION['userfirstname']);
    $html->assign('LASTNAME', $_SESSION['userlastname']);
    $html->parse('main.loggedin');
} else {
    logout();
    $html->parse('main.loggedout');
    $setFocusForm = 'frmLogin';
    $setFocusField = 'txtusername';
}
?>