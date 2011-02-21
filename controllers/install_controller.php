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
class InstallController extends AppController {
    var $uses = array();
    var $db;

    /**
     * Before every action, allow access and define default database.
     *
     * @access public
     * @return void
     */
    function beforeFilter() {
        // Make sure we disable Auth for the installer, otherwise we get lots
        // of complaints about missing tables etc
        $this->Auth->enabled = false;

        parent::beforeFilter();
        $this->Auth->allow('index', 'createBlank', 'createSample');
        $this->db = ConnectionManager::getDataSource('default');
    }


    /**
     * Index page
     *
     * @access public
     * @return void
     */
    function index() {
    }

    function createBlank() {
        $this->create();
        $this->render('index');
    }

    function createSample() {
        $this->create();
        $this->sample();
        $this->render('index');
    }

    private function create() {
        $count = 0;
        $count += $this->executeSQL(CONFIGS . 'schema' . DS . 'schema.sql');

        App::import('Model', 'Group');
        $group = new Group();
        $data = array();
        $data[] = $group->create(array('id'=>1, 'access_admin'=>true,  'name'=>__('Administrators', true)));
        $data[] = $group->create(array('id'=>2, 'access_admin'=>true,  'name'=>__('Managers', true)));
        $data[] = $group->create(array('id'=>3, 'access_admin'=>false, 'name'=>__('Users', true)));
        $group->saveAll($data);
        $count += count($data);
        
        App::import('Model', 'User');
        $user = new User();
        $data = array();
        $data[] = $user->create(array('group_id'=>1, 'username'=>'admin', 'password'=>$this->Auth->password('admin')));
        $user->saveAll($data);
        $count += count($data);

        App::import('Model', 'Category');
        $category = new Category();
        $data = array();
        $data[] = $category->create(array('parent_id'=>0, 'status'=>1, 'name'=>'CatRootNode', 'description'=>'System root node'));
        $category->saveAll($data);
        $count += count($data);
                
        App::import('Model', 'Unit');
        $unit = new Unit();
        $data = array();

        // Other
        $data[] = $unit->create(array('id'=> 1, 'parent_id'=> 0, 'scalefactor'=>1, 'unit'=>'-', 'name'=>__('-none-', true), 'pluralname'=>__('-none-', true)));
        $data[] = $unit->create(array('id'=> 2, 'parent_id'=> 0, 'scalefactor'=>1, 'unit'=>'gph', 'name'=>__('gallons per hour', true), 'pluralname'=>__('gallons per hour', true)));
        // Power
        $data[] = $unit->create(array('id'=> 3, 'parent_id'=> 0, 'scalefactor'=>1.0, 'unit'=>'W', 'name'=>__('watt', true), 'pluralname'=>__('watts', true)));
        $data[] = $unit->create(array('id'=> 4, 'parent_id'=> 3, 'scalefactor'=>746, 'unit'=>'HP', 'name'=>__('horsepower', true), 'pluralname'=>__('horsepower', true)));
        // Length
        $data[] = $unit->create(array('id'=> 5, 'parent_id'=> 0, 'scalefactor'=>1.0000, 'unit'=>'m', 'name'=>__('metre', true), 'pluralname'=>__('metres', true)));
        $data[] = $unit->create(array('id'=> 6, 'parent_id'=> 5, 'scalefactor'=>0.0010, 'unit'=>'mm', 'name'=>__('millimetre', true), 'pluralname'=>__('millimetres', true)));
        $data[] = $unit->create(array('id'=> 7, 'parent_id'=> 5, 'scalefactor'=>0.0100, 'unit'=>'cm', 'name'=>__('centimetre', true), 'pluralname'=>__('centimetres', true)));
        $data[] = $unit->create(array('id'=> 8, 'parent_id'=> 5, 'scalefactor'=>0.0254, 'unit'=>'in', 'name'=>__('inch', true), 'pluralname'=>__('inches', true)));
        $data[] = $unit->create(array('id'=> 9, 'parent_id'=> 5, 'scalefactor'=>0.3048, 'unit'=>'ft', 'name'=>__('foot', true), 'pluralname'=>__('feet', true)));
        $data[] = $unit->create(array('id'=>10, 'parent_id'=> 5, 'scalefactor'=>0.9144, 'unit'=>'yd', 'name'=>__('yard', true), 'pluralname'=>__('yards', true)));
        // Mass
        $data[] = $unit->create(array('id'=>11, 'parent_id'=> 0, 'scalefactor'=>1.000, 'unit'=>'kg', 'name'=>__('kilogramme', true), 'pluralname'=>__('kilogrammes', true)));
        $data[] = $unit->create(array('id'=>12, 'parent_id'=>11, 'scalefactor'=>0.001, 'unit'=>'g', 'name'=>__('gram', true), 'pluralname'=>__('grammes', true)));
        // Volume
        $data[] = $unit->create(array('id'=>13, 'parent_id'=> 0, 'scalefactor'=>1.00000, 'unit'=>'L', 'name'=>__('litre', true), 'pluralname'=>__('litres', true)));
        $data[] = $unit->create(array('id'=>14, 'parent_id'=>13, 'scalefactor'=>0.00100, 'unit'=>'ml', 'name'=>__('millilitre', true), 'pluralname'=>__('millilitres', true)));
        $data[] = $unit->create(array('id'=>15, 'parent_id'=>13, 'scalefactor'=>4.54609, 'unit'=>'gal', 'name'=>__('gallon', true), 'pluralname'=>__('gallons', true)));

        $unit->saveAll($data);
        $count += count($data);
        
        $this->Session->setFlash(__("Executed %d installation statements.", $count));
    }

    private function sample() {
        $this->loadModel('Category');
        $this->loadModel('Item');
        $this->loadModel('CategoryItem');

        // Add some categories and items
        // books
        $this->createCategory('Books', 'catrootnode');

        // books.fiction
        $this->createCategory('Fiction', 'books');

        // books.fiction.science-fiction
        $this->createCategory('Science-Fiction', 'fiction');
        $this->createItem('Foundation', 'science-fiction', array('Hardback'=>19.99, 'Paperback'=>6.99));
        $this->createItem('Contact', 'science-fiction', array('Hardback'=>19.99, 'Paperback'=>6.99));
        $this->createItem('2001: A Space Odyssey', 'science-fiction', array('Hardback'=>19.99, 'Paperback'=>6.99));

        // books.fiction.fantasy
        $this->createCategory('Fantasy', 'fiction');
        $this->createItem('Lord of the Rings', 'fantasy', array('Hardback'=>19.99, 'Paperback'=>6.99));
        $this->createItem('Druss the Legend', 'fantasy', array('Hardback'=>19.99, 'Paperback'=>6.99));

        // books.fiction.horror
        $this->createCategory('Horror', 'fiction');
        $this->createItem('Dracula', 'horror', array('Hardback'=>19.99, 'Paperback'=>6.99));
        $this->createItem('Frankenstein', 'horror', array('Hardback'=>19.99, 'Paperback'=>6.99));
        $this->createItem('Dr. Jekyl & Mr. Hyde', 'horror', array('Hardback'=>19.99, 'Paperback'=>6.99));

        // books.non-fiction
        $this->createCategory('Non-Fiction', 'books');

        // books.non-fiction.science
        $this->createCategory('Science', 'non-fiction');
        $this->createItem('Fabric of the Cosmos', 'science-1', array('Hardback'=>19.99, 'Paperback'=>6.99));
        $this->createItem('Black Holes and Baby Universes', 'science-1', array('Hardback'=>19.99, 'Paperback'=>6.99));

        // books.non-fiction.science
        $this->createCategory('Computing', 'non-fiction');
        $this->createItem('Code Complete', 'computing', array('Hardback'=>19.99, 'Paperback'=>6.99));
        $this->createItem('GoF', 'computing', array('Hardback'=>19.99, 'Paperback'=>6.99));
        $this->createItem('CakePHP Cookbook', 'computing', array('Hardback'=>19.99, 'Paperback'=>6.99));

        // books.non-fiction.travel
        $this->createCategory('Travel', 'non-fiction');
        $this->createItem('Rome', 'travel', array('Hardback'=>19.99, 'Paperback'=>6.99));
        $this->createItem('Paris', 'travel', array('Hardback'=>19.99, 'Paperback'=>6.99));
        $this->createItem('London', 'travel', array('Hardback'=>19.99, 'Paperback'=>6.99));
        $this->createItem('Barcelona', 'travel', array('Hardback'=>19.99, 'Paperback'=>6.99));

        // films
        $this->createCategory('Films', 'catrootnode');

        // films.science-fiction
        $this->createCategory('Science-Fiction', 'films');
        $this->createItem('Star Wars IV', 'science-fiction-1', array('DVD'=>12.99, 'Blu-Ray'=>19.99, 'VHS'=>15.99));
        $this->createItem('Star Wars V', 'science-fiction-1', array('DVD'=>12.99, 'Blu-Ray'=>19.99, 'VHS'=>15.99));
        $this->createItem('Star Wars VI', 'science-fiction-1', array('DVD'=>12.99, 'Blu-Ray'=>19.99, 'VHS'=>15.99));
        $this->createItem('Star Wars I', 'science-fiction-1', array('DVD'=>12.99, 'Blu-Ray'=>19.99, 'VHS'=>15.99));
        $this->createItem('Star Wars II', 'science-fiction-1', array('DVD'=>12.99, 'Blu-Ray'=>19.99, 'VHS'=>15.99));
        $this->createItem('Star Wars III', 'science-fiction-1', array('DVD'=>12.99, 'Blu-Ray'=>19.99, 'VHS'=>15.99));
        $this->createItem('Tron', 'science-fiction-1', array('DVD'=>12.99, 'Blu-Ray'=>19.99, 'VHS'=>15.99));
        $this->createItem('Tron: Legacy', 'science-fiction-1', array('DVD'=>12.99, 'Blu-Ray'=>19.99, 'VHS'=>15.99));

        // films.horror
        $this->createCategory('Horror', 'films');
        $this->createItem('Carrie', 'horror-1', array('DVD'=>12.99, 'Blu-Ray'=>19.99, 'VHS'=>15.99));

        // films.drama
        $this->createCategory('Drama', 'films');
        $this->createItem('The Shawshank Redemption', 'drama', array('DVD'=>12.99, 'Blu-Ray'=>19.99, 'VHS'=>15.99));

        // games
        $this->createCategory('Games', 'catrootnode');

        // games.fps
        $this->createCategory('FPS', 'games');
        $this->createItem('Left for Dead', 'fps', array('PC'=>39.99, 'Xbox360'=>39.99));

        // games.rts
        $this->createCategory('RTS', 'games');
        $this->createItem('Dawn of War', 'rts', array('PC'=>39.99));

        // games.mmorpg
        $this->createCategory('MMORPG', 'games');
        $this->createItem('World of Warcraft', 'mmorpg', array('PC'=>39.99));
        $this->createItem('Star Trek: Online', 'mmorpg', array('PC'=>39.99));
        $this->createItem('Warhammer Online', 'mmorpg', array('PC'=>39.99));

        // games.strategy
        $this->createCategory('Strategy', 'games');
        $this->createItem('Civilisation 4', 'strategy', array('PC'=>39.99));

        $this->Session->setFlash(__("Inserted sample data.", true));
    }

    private function createCategory($name, $parentTag) {
        $parentId = $this->Category->field('id', array('Category.tag'=>$parentTag));
        $this->Category->saveAll($this->Category->create(array('name'=>$name, 'parent_id'=>$parentId)));
    }

    private function createItem($name, $categoryTags, $details) {
        $newDetails = array();
        foreach($details as $detailkey=>$detailvalue) {
            $newDetails[] = array('name'=>$detailkey, 'price'=>$detailvalue);
        }
        
        $this->Item->saveAll(array(
            'Item' => array('name'=>$name),
            'Detail' =>$newDetails,
        ));
        
        $itemId = $this->Item->id;
        
        $categories = array();
        if (is_array($categoryTags)) {
            $categories = $categoryTags;
        } else {
            $categories[] = $categoryTags;
        }
        
        $catdata = array();
        $primary = true;
        foreach ($categories as $cat) {
            $ic = $this->CategoryItem->create();
            $ic['CategoryItem']['item_id'] = $itemId;
            $ic['CategoryItem']['category_id'] = $this->Category->field('id', array('Category.tag'=>$cat));
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