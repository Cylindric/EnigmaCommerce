<?php
class CategoryItem extends AppModel {

    var $name = 'CategoryItem';

    var $belongsTo = array('Category', 'Item');

}