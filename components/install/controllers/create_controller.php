<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 */

App::import('model', 'connection_manager');

/**
 * Manages the installation of new database structures and the migration of
 * data from previous versions.
 */
class CreateController extends AppController {
    var $uses = array();
    var $db;

    function beforeFilter() {
        parent::beforeFilter();

        // Make sure we disable Auth for the installer, otherwise we get lots
        // of complaints about missing tables etc
        $this->Auth->enabled = false;
        $this->Auth->authorize = false;

        $this->Auth->allow('*');
        $this->db = ConnectionManager::getDataSource('default');
    }

    function index() {
    }

    function blank() {
        $this->createDatabase();
        $this->render('index');
    }

    function sample() {
        $this->createDatabase();
        $this->insertSampleData();
        $this->render('index');
    }

    private function createDatabase() {
        $count = 0;
        $count += $this->executeSQL(CONFIGS . 'schema' . DS . 'schema.sql');

        $count += $this->createCoreStatuses();        
        $count += $this->createCoreGroups();
        $count += $this->createCoreUsers();
        $count += $this->createCoreCategories();
        $count += $this->createCoreUnits();
        $count += $this->createCorePictures();
        
        $this->Session->setFlash(__("Executed %d installation statements.", $count));
    }

    private function createCoreStatuses() {
        App::import('Model', 'Status');
        $status = new Status();
        $data = array();
        $data[] = $status->create(array('id'=>0, 'name'=>'disabled'));
        $data[] = $status->create(array('id'=>1, 'name'=>'active'));
        $status->saveAll($data);
        return count($data);
    }

    private function createCoreUsers() {
        App::import('Model', 'User');
        $user = new User();
        $data = array();
        $data[] = $user->create(array('group_id'=>1, 'username'=>'admin', 'password'=>$this->Auth->password('admin')));
        $user->saveAll($data);
        return count($data);
    }

    private function createCoreCategories() {
        App::import('Model', 'Category');
        $category = new Category();
        $data = array();
        $data[] = $category->create(array('parent_id'=>0, 'status'=>1, 'name'=>'CatRootNode', 'description'=>'System root node'));
        $category->saveAll($data);
        return count($data);
    }
    
    private function createCoreGroups() {
        App::import('Model', 'Group');
        $group = new Group();
        $data = array();
        $data[] = $group->create(array('id'=>1, 'access_admin'=>true,  'name'=>__('Administrators')));
        $data[] = $group->create(array('id'=>2, 'access_admin'=>true,  'name'=>__('Managers')));
        $data[] = $group->create(array('id'=>3, 'access_admin'=>false, 'name'=>__('Users')));
        $group->saveAll($data);
        return count($data);
    }
    
    private function createCoreUnits() {
        App::import('Model', 'Unit');
        $unit = new Unit();
        $data = array();

        // Other
        $data[] = $unit->create(array('id'=> 1, 'parent_id'=> 0, 'scalefactor'=>1, 'unit'=>'-', 'name'=>__('-none-'), 'pluralname'=>__('-none-')));
        $data[] = $unit->create(array('id'=> 2, 'parent_id'=> 0, 'scalefactor'=>1, 'unit'=>'gph', 'name'=>__('gallons per hour'), 'pluralname'=>__('gallons per hour')));
        // Power
        $data[] = $unit->create(array('id'=> 3, 'parent_id'=> 0, 'scalefactor'=>1.0, 'unit'=>'W', 'name'=>__('watt'), 'pluralname'=>__('watts')));
        $data[] = $unit->create(array('id'=> 4, 'parent_id'=> 3, 'scalefactor'=>746, 'unit'=>'HP', 'name'=>__('horsepower'), 'pluralname'=>__('horsepower')));
        // Length
        $data[] = $unit->create(array('id'=> 5, 'parent_id'=> 0, 'scalefactor'=>1.0000, 'unit'=>'m', 'name'=>__('metre'), 'pluralname'=>__('metres')));
        $data[] = $unit->create(array('id'=> 6, 'parent_id'=> 5, 'scalefactor'=>0.0010, 'unit'=>'mm', 'name'=>__('millimetre'), 'pluralname'=>__('millimetres')));
        $data[] = $unit->create(array('id'=> 7, 'parent_id'=> 5, 'scalefactor'=>0.0100, 'unit'=>'cm', 'name'=>__('centimetre'), 'pluralname'=>__('centimetres')));
        $data[] = $unit->create(array('id'=> 8, 'parent_id'=> 5, 'scalefactor'=>0.0254, 'unit'=>'in', 'name'=>__('inch'), 'pluralname'=>__('inches')));
        $data[] = $unit->create(array('id'=> 9, 'parent_id'=> 5, 'scalefactor'=>0.3048, 'unit'=>'ft', 'name'=>__('foot'), 'pluralname'=>__('feet')));
        $data[] = $unit->create(array('id'=>10, 'parent_id'=> 5, 'scalefactor'=>0.9144, 'unit'=>'yd', 'name'=>__('yard'), 'pluralname'=>__('yards')));
        // Mass
        $data[] = $unit->create(array('id'=>11, 'parent_id'=> 0, 'scalefactor'=>1.000, 'unit'=>'kg', 'name'=>__('kilogramme'), 'pluralname'=>__('kilogrammes')));
        $data[] = $unit->create(array('id'=>12, 'parent_id'=>11, 'scalefactor'=>0.001, 'unit'=>'g', 'name'=>__('gram'), 'pluralname'=>__('grammes')));
        // Volume
        $data[] = $unit->create(array('id'=>13, 'parent_id'=> 0, 'scalefactor'=>1.00000, 'unit'=>'L', 'name'=>__('litre'), 'pluralname'=>__('litres')));
        $data[] = $unit->create(array('id'=>14, 'parent_id'=>13, 'scalefactor'=>0.00100, 'unit'=>'ml', 'name'=>__('millilitre'), 'pluralname'=>__('millilitres')));
        $data[] = $unit->create(array('id'=>15, 'parent_id'=>13, 'scalefactor'=>4.54609, 'unit'=>'gal', 'name'=>__('gallon'), 'pluralname'=>__('gallons')));

        $unit->saveAll($data);
        return count($data);
    }
    
    private function createCorePictures() {
        App::import('Model', 'Picture');
        $picture = new Picture();
        $data = array();
        $data[] = $picture->create(array('id'=>1, 'filename'=>'blank', 'name'=>__('Blank')));
        $picture->saveAll($data);
        return count($data);        
    }
    
    private function insertSampleData() {
        $this->loadModel('Category');
        $this->loadModel('Item');
        $this->loadModel('CategoryItem');

        // Add some categories and items
        // aquarium
//        $this->createCategory('catrootnode', 'Aquarium');
//        $this->createCategory('aquarium', 'Heaters');
//        $this->createCategory('aquarium', 'Air Pumps');


        // pond
        $this->createCategory('catrootnode', 'Pond');
        $this->createCategory('pond', 'Pumps');
        $this->createCategory('pumps', 'Sump Pumps');
        $this->createItem(
                'sump-pumps', 
                'Mega Sump Pump - Dirty Water', 
                array(
                    'Q400 with float'=>99.95, 
                    'Q700'=>129.99));
        $this->createItem(
                'sump-pumps', 
                'Mega Sump Pump - Clean Water', 
                array(
                    'Q2501 with float'=>82.49, 
                    'Q2501 no float'=>65.95));

        $this->createCategory('pumps', 'External Pumps');
        $this->createItem(
                'external-pumps', 
                'ITT Argonaut AG Series Pumps',
                array(
                    'ITT AG Series 8 Pump'=>214.99,
                    'ITT AG Series 10 Pump'=>224.99,
                    'ITT AG Series 14 Pump'=>234.99,
                    'ITT AG Series 16 Pump'=>245.99));
        $this->createItem(
                'external-pumps',
                'Speck - Badu Magic Pond Pumps',
                array(
                    'Speck Magic 4 Pump'=>219.95,
                    'Speck Magic 6 Pump'=>229.95,
                    'Speck Magic 8 Pump'=>239.95));

        $this->createCategory('pond', 'Filters');
        $this->createCategory('filters', 'Filter Equipment');
        $this->createItem(
                'filter-equipment',
                'Filter Media Grid',
                array('each'=>5.50));
        $this->createItem(
                'filter-equipment',
                'Filter Media Grid - Stainless Steel',
                array('each'=>85.50, '<strong>BULK BUY Filter Grids x 10</strong>'=>500.00));

        $this->createCategory('filters', 'Bead Filters');
        $this->createItem(
                'bead-filters',
                'Poly-Bead',
                array(
                    'Poly-Bead 2'=>350.50, 
                    'Poly-Bead 4'=>455.50, 
                    'Ploy-Bead 6'=>650.00));

        $this->createCategory('filters', 'Filtration Accessories');
        $this->createItem(
                'filtration-accessories',
                'Filter Media Sacks - Coarse Net',
                array(
                    'Standard Quality'=>1.50, 
                    'High Quality'=>2.50));
        $this->createItem(
                'filtration-accessories',
                'Oase Filtoclear Spare Foam Set',
                array(
                    '3000 Foam Set'=>21.50, 
                    '6000 Foam Set'=>29.50, 
                    '11000 Foam Set'=>41.50, 
                    '15000 Foam Set'=>50.50));

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


        
        // books.fiction.science-fiction
//        $this->createCategory('Science-Fiction', 'fiction');
//        $this->createItem('Foundation', 'science-fiction', array('Hardback'=>19.99, 'Paperback'=>6.99));
//        $this->createItem('Contact', 'science-fiction', array('Hardback'=>19.99, 'Paperback'=>6.99));
//        $this->createItem('2001: A Space Odyssey', 'science-fiction', array('Hardback'=>19.99, 'Paperback'=>6.99));
//
//        // books.fiction.fantasy
//        $this->createCategory('Fantasy', 'fiction');
//        $this->createItem('Lord of the Rings', 'fantasy', array('Hardback'=>19.99, 'Paperback'=>6.99));
//        $this->createItem('Druss the Legend', 'fantasy', array('Hardback'=>19.99, 'Paperback'=>6.99));
//
//        // books.fiction.horror
//        $this->createCategory('Horror', 'fiction');
//        $this->createItem('Dracula', 'horror', array('Hardback'=>19.99, 'Paperback'=>6.99));
//        $this->createItem('Frankenstein', 'horror', array('Hardback'=>19.99, 'Paperback'=>6.99));
//        $this->createItem('Dr. Jekyl & Mr. Hyde', 'horror', array('Hardback'=>19.99, 'Paperback'=>6.99));
//
//        // books.non-fiction
//        $this->createCategory('Non-Fiction', 'books');
//
//        // books.non-fiction.science
//        $this->createCategory('Science', 'non-fiction');
//        $this->createItem('Fabric of the Cosmos', 'science-1', array('Hardback'=>19.99, 'Paperback'=>6.99));
//        $this->createItem('Black Holes and Baby Universes', 'science-1', array('Hardback'=>19.99, 'Paperback'=>6.99));
//
//        // books.non-fiction.science
//        $this->createCategory('Computing', 'non-fiction');
//        $this->createItem('Code Complete', 'computing', array('Hardback'=>19.99, 'Paperback'=>6.99));
//        $this->createItem('GoF', 'computing', array('Hardback'=>19.99, 'Paperback'=>6.99));
//        $this->createItem('CakePHP Cookbook', 'computing', array('Hardback'=>19.99, 'Paperback'=>6.99));
//
//        // books.non-fiction.travel
//        $this->createCategory('Travel', 'non-fiction');
//        $this->createItem('Rome', 'travel', array('Hardback'=>19.99, 'Paperback'=>6.99));
//        $this->createItem('Paris', 'travel', array('Hardback'=>19.99, 'Paperback'=>6.99));
//        $this->createItem('London', 'travel', array('Hardback'=>19.99, 'Paperback'=>6.99));
//        $this->createItem('Barcelona', 'travel', array('Hardback'=>19.99, 'Paperback'=>6.99));
//
//        // films
//        $this->createCategory('Films', 'catrootnode');
//
//        // films.science-fiction
//        $this->createCategory('Science-Fiction', 'films');
//        $this->createItem('Star Wars IV', 'science-fiction-1', array('DVD'=>12.99, 'Blu-Ray'=>19.99, 'VHS'=>15.99));
//        $this->createItem('Star Wars V', 'science-fiction-1', array('DVD'=>12.99, 'Blu-Ray'=>19.99, 'VHS'=>15.99));
//        $this->createItem('Star Wars VI', 'science-fiction-1', array('DVD'=>12.99, 'Blu-Ray'=>19.99, 'VHS'=>15.99));
//        $this->createItem('Star Wars I', 'science-fiction-1', array('DVD'=>12.99, 'Blu-Ray'=>19.99, 'VHS'=>15.99));
//        $this->createItem('Star Wars II', 'science-fiction-1', array('DVD'=>12.99, 'Blu-Ray'=>19.99, 'VHS'=>15.99));
//        $this->createItem('Star Wars III', 'science-fiction-1', array('DVD'=>12.99, 'Blu-Ray'=>19.99, 'VHS'=>15.99));
//        $this->createItem('Tron', 'science-fiction-1', array('DVD'=>12.99, 'Blu-Ray'=>19.99, 'VHS'=>15.99));
//        $this->createItem('Tron: Legacy', 'science-fiction-1', array('DVD'=>12.99, 'Blu-Ray'=>19.99, 'VHS'=>15.99));
//
//        // films.horror
//        $this->createCategory('Horror', 'films');
//        $this->createItem('Carrie', 'horror-1', array('DVD'=>12.99, 'Blu-Ray'=>19.99, 'VHS'=>15.99));
//
//        // films.drama
//        $this->createCategory('Drama', 'films');
//        $this->createItem('The Shawshank Redemption', 'drama', array('DVD'=>12.99, 'Blu-Ray'=>19.99, 'VHS'=>15.99));
//
//        // games
//        $this->createCategory('Games', 'catrootnode');
//
//        // games.fps
//        $this->createCategory('FPS', 'games');
//        $this->createItem('Left for Dead', 'fps', array('PC'=>39.99, 'Xbox360'=>39.99));
//
//        // games.rts
//        $this->createCategory('RTS', 'games');
//        $this->createItem('Dawn of War', 'rts', array('PC'=>39.99));
//
//        // games.mmorpg
//        $this->createCategory('MMORPG', 'games');
//        $this->createItem('World of Warcraft', 'mmorpg', array('PC'=>39.99));
//        $this->createItem('Star Trek: Online', 'mmorpg', array('PC'=>39.99));
//        $this->createItem('Warhammer Online', 'mmorpg', array('PC'=>39.99));
//
//        // games.strategy
//        $this->createCategory('Strategy', 'games');
//        $this->createItem('Civilisation 4', 'strategy', array('PC'=>39.99));

        $this->Session->setFlash(__("Inserted sample data."));
    }

    private function createCategory($parentSlug, $name) {
        $parentId = $this->Category->field('id', array('Category.slug'=>$parentSlug));
        $category = $this->Category->create(array('name'=>$name, 'parent_id'=>$parentId));
        $this->Category->saveAll($category);
    }

    private function createItem($categorySlugs, $name, $variations) {
        $newVariations = array();
        foreach($variations as $variationkey=>$variationvalue) {
            $newVariations[] = array('name'=>$variationkey, 'price'=>$variationvalue);
        }
        
        $this->Item->saveAll(array(
            'Item' => array('name'=>$name),
            'Variation' =>$newVariations,
        ));
        
        $itemId = $this->Item->id;
        
        $categories = array();
        if (is_array($categorySlugs)) {
            $categories = $categorySlugs;
        } else {
            $categories[] = $categorySlugs;
        }
        
        $catdata = array();
        $primary = true;
        foreach ($categories as $cat) {
            $ic = $this->CategoryItem->create();
            $ic['CategoryItem']['item_id'] = $itemId;
            $ic['CategoryItem']['category_id'] = $this->Category->field('id', array('Category.slug'=>$cat));
            $ic['CategoryItem']['is_primary'] = $primary;
            $catdata[] = $ic;
            $primary = false;
        }
        $this->CategoryItem->saveAll($catdata);
    }

    private function executeSQL($filename) {
        $statements = file_get_contents($filename);
        $statements = explode(';', $statements);

        $count  = 0;
        foreach ($statements as $statement) {
            if (trim($statement) != '') {
                $statement = str_replace('store_', $this->db->config['prefix'] . 'sample_', $statement);
                $statement = str_replace('enigma3_', $this->db->config['prefix'], $statement);
                $statement = str_replace('#__SALT', Configure::read('Security.salt'), $statement);
                $this->db->query($statement);
                $count ++;
            }
        }
        return $count ;
    }

}