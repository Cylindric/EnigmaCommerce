<?php

/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package install_component
 */

/**
 * Handles the installation of Sample data
 * @package install_component
 * @subpackage controllers
 */
class SampleController extends InstallAppController {

    var $components = array();
    var $uses = array('Category', 'CategoryItem', 'Item', 'ItemPicture', 'Picture', 'Unit', 'Variation');
    var $cats = array();
    var $pics = array();

    function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('index', 'start');
    }

    function index() {
        
    }

    function start() {
        $this->requestAction('/install/create/blank');
        $this->createCategories();
        $this->createPictures();
        $this->createItems();
    }

    private function createCategories() {
        $cats = array('root' => 1);

        // pond
        $this->Category->save($this->Category->Create(array('Category' => array(
                        'name' => 'Pond', 'parent_id' => $cats['root']
                        ))));
        $cats['pond'] = $this->Category->id;

        $this->Category->save($this->Category->Create(array('Category' => array(
                        'name' => 'Pumps', 'parent_id' => $cats['pond']
                        ))));
        $cats['pond/pumps'] = $this->Category->id;

        $this->Category->save($this->Category->Create(array('Category' => array(
                        'name' => 'Sump Pumps', 'parent_id' => $cats['pond/pumps']
                        ))));
        $cats['pond/pumps/sump'] = $this->Category->id;

        $this->Category->save($this->Category->Create(array('Category' => array(
                        'name' => 'External Pumps', 'parent_id' => $cats['pond/pumps']
                        ))));
        $cats['pond/pumps/external'] = $this->Category->id;

        $this->Category->save($this->Category->Create(array('Category' => array(
                        'name' => 'Filters', 'parent_id' => $cats['pond']
                        ))));
        $cats['pond/filters'] = $this->Category->id;

        $this->Category->save($this->Category->Create(array('Category' => array(
                        'name' => 'Filter Equipment', 'parent_id' => $cats['pond/filters']
                        ))));
        $cats['pond/filters/equipment'] = $this->Category->id;

        $this->cats = $cats;
    }

    private function createPictures() {
        $pics = array('blank' => 1);

        $imgbase = App::pluginPath('install') . 'webroot' . DS . 'img' . DS . 'products' . DS;

        $dir = opendir($imgbase);
        if ($dir !== false) {
            while (($file = readdir($dir)) !== false) {
                if (($file != '..') && ($file != '.')) {
                    $file = pathinfo($imgbase . $file);
                    $this->Picture->import(
                            $imgbase . $file['basename'], array('overwrite' => true, 'create' => true)
                    );
                    $pics[$file['filename']] = (int) $this->Picture->id;
                }
            }
        }
        $this->pics = $pics;
    }

    private function createItems() {
        $cats = $this->cats;
        $pics = $this->pics;

        $this->Item->saveAll($this->Item->create(array(
                    'Item' => array(
                        'name' => 'Mega Sump Pump - Dirty Water',
                        'description' => 'A fairly standard item, with a couple of variants.',
                    ),
                    'Variation' => array(array(
                            'name' => 'Q400 with float', 'price' => 99.95), array(
                            'name' => 'Q700', 'price' => 129.99),
                    ),
                    'CategoryItem' => array(array(
                            'is_primary' => true, 'category_id' => $cats['pond/pumps/sump']),
                    ),
                    'ItemPicture' => array(array(
                            'is_primary' => true, 'picture_id' => $pics['mega-sump-pump']),
                    ),
                )));

        $this->Item->saveAll($this->Item->create(array(
                    'Item' => array(
                        'name' => 'Mega Sump Pump - Clean Water',
                        'description' => 'A fairly standard item, with a couple of variants.',
                    ),
                    'Variation' => array(array(
                            'name' => 'Q2501 with float', 'price' => 82.49), array(
                            'name' => 'Q2501 no float', 'price' => 65.95),
                    ),
                    'CategoryItem' => array(array(
                            'is_primary' => true, 'category_id' => $cats['pond/pumps/sump']),
                    ),
                    'ItemPicture' => array(array(
                            'is_primary' => true, 'picture_id' => $pics['mega-sump-pump']),
                    ),
                )));

        $this->Item->saveAll($this->Item->create(array(
                    'Item' => array(
                        'name' => 'ITT Argonaut AG Series Pump',
                        'description' => 'A fairly standard item, with several variants and multiple pictures.',
                    ),
                    'Variation' => array(array(
                            'name' => 'ITT Series 8', 'price' => 214.99), array(
                            'name' => 'ITT Series 10', 'price' => 224.99), array(
                            'name' => 'ITT Series 14', 'price' => 234.99), array(
                            'name' => 'ITT Series 16', 'price' => 245.99),
                    ),
                    'CategoryItem' => array(array(
                            'is_primary' => true, 'category_id' => $cats['pond/pumps/external']),
                    ),
                    'ItemPicture' => array(array(
                            'is_primary' => true, 'picture_id' => $pics['itt-argonaut']), array(
                            'is_primary' => false, 'picture_id' => $pics['itt-argonaut-2'])
                    ),
                )));

        $this->Item->saveAll($this->Item->create(array(
                    'Item' => array(
                        'name' => 'Badu Magic Pond Pumps',
                        'description' => 'A fairly standard item, with a couple of variants.',
                    ),
                    'Variation' => array(array(
                            'name' => 'Magic 4', 'price' => 219.95), array(
                            'name' => 'Magic 6', 'price' => 229.95), array(
                            'name' => 'Magic 8', 'price' => 239.95),
                    ),
                    'CategoryItem' => array(array(
                            'is_primary' => true, 'category_id' => $cats['pond/pumps/external']),
                    ),
                    'ItemPicture' => array(array(
                            'is_primary' => true, 'picture_id' => $pics['speck-badu']),
                    ),
                )));

        $this->Item->saveAll($this->Item->create(array(
                    'Item' => array(
                        'name' => 'Filter Media Grid',
                        'description' => 'A fairly standard item, with a couple of variants.',
                    ),
                    'Variation' => array(array(
                            'name' => 'each', 'price' => 5.50),
                    ),
                    'CategoryItem' => array(array(
                            'is_primary' => true, 'category_id' => $cats['pond/filters/equipment']),
                    ),
                    'ItemPicture' => array(array(
                            'is_primary' => true, 'picture_id' => $pics['filter-media-grid']),
                    ),
                )));

        $this->Item->saveAll($this->Item->create(array(
                    'Item' => array(
                        'name' => 'Poly Bead',
                        'description' => 'This item h as multiple pictures, but no primary one!',
                    ),
                    'Variation' => array(array(
                            'name' => 'each', 'price' => 5.50),
                    ),
                    'CategoryItem' => array(array(
                            'is_primary' => true, 'category_id' => $cats['pond/filters/equipment']),
                    ),
                    'ItemPicture' => array(
                        array('is_primary' => false, 'picture_id' => $pics['poly-bead']),
                        array('is_primary' => false, 'picture_id' => $pics['poly-bead-2']),
                    ),
                )));

//        
//        $this->createItem(
//                'filter-equipment',
//                'Filter Media Grid - Stainless Steel',
//                array('each'=>85.50, '<strong>BULK BUY Filter Grids x 10</strong>'=>500.00));
//
//        $this->createCategory('filters', 'Bead Filters');
//        $this->createItem(
//                'bead-filters',
//                'Poly-Bead',
//                array(
//                    'Poly-Bead 2'=>350.50, 
//                    'Poly-Bead 4'=>455.50, 
//                    'Ploy-Bead 6'=>650.00));
//
//        $this->createCategory('filters', 'Filtration Accessories');
//        $this->createItem(
//                'filtration-accessories',
//                'Filter Media Sacks - Coarse Net',
//                array(
//                    'Standard Quality'=>1.50, 
//                    'High Quality'=>2.50));
//        $this->createItem(
//                'filtration-accessories',
//                'Oase Filtoclear Spare Foam Set',
//                array(
//                    '3000 Foam Set'=>21.50, 
//                    '6000 Foam Set'=>29.50, 
//                    '11000 Foam Set'=>41.50, 
//                    '15000 Foam Set'=>50.50));
//
//        $this->createCategory('pond', 'Medication');
//        $this->createCategory('medication', 'Surgical Equipment');
//        $this->createCategory('medication', 'Pond Salt');
//        $this->createCategory('pond', 'Food');
//        $this->createCategory('food', 'Tetra');
//        $this->createCategory('food', 'Hikari');
//        $this->createCategory('food', 'Nishikoi');
//        $this->createCategory('food', 'Ogata');
//        $this->createCategory('pond', 'Fittings');
//        $this->createCategory('fittings', 'Pipes');
//        $this->createCategory('fittings', 'Fittings');
//        $this->createCategory('fittings', 'Hose &amp; Fittings');
//        $this->createCategory('fittings', 'Glue &amp; Silicone');
    }

}