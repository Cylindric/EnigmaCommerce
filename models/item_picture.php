<?php
class ItemPicture extends AppModel {

    var $name = 'ItemPicture';

    var $belongsTo = array('Item', 'Picture');

}