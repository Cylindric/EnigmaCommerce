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
class CreateController extends InstallAppController {
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
        $data[] = $picture->create(array('filename'=>'blank.png', 'name'=>__('Blank')));
        $picture->saveAll($data);
        return count($data);        
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