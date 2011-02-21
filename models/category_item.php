<?php
class CategoryItem extends AppModel {

    var $name = 'CategoryItem';

    var $actsAs = array(
        'Containable'
    );

    var $belongsTo = array('Category', 'Item');

}