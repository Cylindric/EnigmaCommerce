<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 */

App::import('model', 'connection_manager');

class MigrateController extends InstallAppController
{
    var $components = array();
    var $uses = array('Category', 'CategoryItem', 'Item', 'Picture', 'Unit', 'Variation');
    var $db;
    var $old_db;

    private $sequence = array(
        'CreateBlank',
        'Categories',
        'CategoryParents',
        'CategoryTreeRepair',
        'Items',
        'CategoryItems',
        'ItemDetails',
        'Pictures'
    );
    
    private $settings = array();
    
    function beforeFilter()
    {
        parent::beforeFilter();
        $this->Auth->allow('index', 'start');
        $this->db = ConnectionManager::getDataSource('default');
    }

    function index()
    {
        $this->Session->delete('migration_settings');
        $this->set('sequence', array('Sequence'=>$this->sequence));
    }

    function start()
    {
        $this->settings = array();
        if ($this->Session->check('migration_settings')) {
            $this->settings = $this->Session->read('migration_settings');
        } 

        $this->settings = array_merge(array(
            'queue' => $this->sequence,
            'offset' => 0,
            'limit' => 50,
            'count' => array(),
            'source' => 'upgrade',
            'status' => 'start',
            'messages' => array(),
            'disableTrees' => false,
        ), $this->settings);

        if ($this->data) {
            $this->settings['queue'] = array();
            foreach ($this->data['sequence'] as $item) {
                $this->settings['queue'][] = $this->sequence[$item];
            }
        }

        @$this->old_db = ConnectionManager::getDataSource($this->settings['source']);
        if (is_null($this->old_db)) {
            $this->Session->setFlash(__("Error - you need to define a '%s' db connection to migrate.", $this->settings['source']));
            $this->redirect(array('action'=>'index'));
        }
        
        if ($this->settings['status'] == 'done') {
            $this->settings['offset'] = 0;
            $this->settings['status'] = 'migrating';
            array_shift($this->settings['queue']);
        } 
        
        if(count($this->settings['queue']) == 0) {
            $this->settings['status'] = 'finished';
            $this->msg('Migration', 'Complete!');
            $this->Session->delete('migration_settings');
        } else {
            $method = 'migrate'.$this->settings['queue'][0];
            $this->{$method}();
        }

        $this->Session->write('migration_settings', $this->settings);

        $this->set('status', $this->settings['status']);
        $this->set('messages', $this->settings['messages']);
    }

    private function migrateCreateBlank() {
        $name = 'CreateBlank';
        $msg = __('Creating blank database...');
        if ($this->settings['offset'] == 0) {
            $msg .= $this->progressBar(0, 1);
            $this->settings['offset'] = 1;
        } else {
            $this->requestAction('/install/create/blank');
            $msg .= $this->progressBar(1, 1);
            $this->settings['status'] = 'done';
        }
        $this->msg($name, $msg);
    }
    
    private function migrateCategories() {
        $name = 'Categories';     
        $count = $this->getCount($name, 
            'SELECT COUNT(*) count FROM #__category ');

        if ($count == 0) {
            $this->settings['status'] = 'done';
            $this->msg($name, 'none found');
            return;
        }

        $msg = __('Processing %d %s...', $count, __(Inflector::pluralize($name)));

        // Prevent the MPTT behavior from making the import take a long time
        // Tree will need rebuilding afterwards
        if ($this->settings['disableTrees']) {
            $this->Category->Behaviors->disable('Tree');
        }
        
        $query  = 'SELECT * FROM ' . $this->old_db->config['prefix'] . 'category ';
        $query .= 'LIMIT ' . $this->settings['offset'] . ', ' . $this->settings['limit'];

        $rows = $this->old_db->query($query);
        foreach ($rows as $row) {
            $oldObject = $row[$this->old_db->config['prefix'] . 'category'];
            $newObject = $this->Category->create();
            $newObject['Category']['legacy_id'] = $oldObject['CategoryID'];
            $newObject['Category']['legacy_parent_id'] = $oldObject['ParentID'];
            $newObject['Category']['name'] = html_entity_decode($oldObject['CategoryName']);
            $newObject['Category']['description'] = $oldObject['Description'];
            $newObject['Category']['visible_on_web'] = $oldObject['WebView'];
            $newObject['Category']['visible_on_catalogue'] = $oldObject['CatalogueView'];
            $newObject['Category']['stockcodeprefix'] = $oldObject['StockCodePrefix'];
            $newObject['Category']['created'] = $oldObject['CreateDate'];
            $newObject['Category']['modified'] = $oldObject['ModifyDate'];
            if ($oldObject['DeleteDate'] == 0) {
                $newObject['Category']['status'] = 1;
            } else {
                $newObject['Category']['status'] = 0;
            }
            $this->Category->save($newObject);
        }
        $msg .= $this->progressBar($this->settings['offset']+count($rows), $count);

        // Reenable the MPTT behavior
        if ($this->settings['disableTrees']) {
            $this->Category->Behaviors->enable('Tree');
        }

        if (count($rows) < $this->settings['limit']) {
            $this->settings['status'] = 'done';
        } else {
            $this->settings['offset'] += $this->settings['limit'];
        }

        $this->msg($name, $msg);
    }

    private function migrateCategoryParents() {
        $name = 'CategoryParents';     
        $count = $this->Category->find('count');

        if ($count == 0) {
            $this->settings['status'] = 'done';
            $this->msg($name, 'none found');
            return;
        }

        $msg = __('Processing %d %s cross-links...', $count, __('Category'));

        // Prevent the MPTT behavior from making the import take a long time
        // Tree will need rebuilding afterwards
        if ($this->settings['disableTrees']) {
            $this->Category->Behaviors->disable('Tree');
        }
        
        $params = array(
            'conditions' => array('Category.legacy_id !=' => 0),
            'limit' => $this->settings['limit'],
            'offset' => $this->settings['offset']
        );
        $rootnode = $this->Category->find('first', array('conditions'=>array('Category.slug' => 'catrootnode')));

        $rows = $this->Category->find('all', $params);

        if (!count($rows) == 0) {
            foreach ($rows as $row) {
                if ($row['Category']['legacy_parent_id'] == 0) {
                    $parentcat = $rootnode;
                } else {
                    $parentcat = $this->Category->find('first', array('conditions'=>array('Category.legacy_id' => $row['Category']['legacy_parent_id'])));
                }
                $this->Category->id = $row['Category']['id'];
                $this->Category->saveField('parent_id', $parentcat['Category']['id']);
            }
        }

        // Reenable the MPTT behavior
        if ($this->settings['disableTrees']) {
            $this->Category->Behaviors->enable('Tree');
        }

        if (count($rows) < $this->settings['limit']) {
            $this->settings['status'] = 'done';
            $msg .= $this->progressBar($count, $count);
        } else {
            $msg .= $this->progressBar($this->settings['offset'] + count($rows), $count);
            $this->settings['offset'] += $this->settings['limit'];
        }

        $this->msg($name, $msg);
    }
    
    private function migrateCategoryTreeRepair() {
        $name = 'CategoryTreeRepair';
        $msg = __('Repairing %s tree...', __('Category'));
        if ($this->settings['offset'] == 0) {
            $msg .= $this->progressBar(0, 1);
            $this->settings['offset'] = 1;
        } else {
            $parentId = $this->Category->field('id', array('Category.slug' => 'catrootnode'));
            if ($this->settings['disableTrees']) {
                $this->Category->recover('parent', $parentId);
            }
            $msg .= $this->progressBar(1, 1);
            $this->settings['status'] = 'done';
        }
        $this->msg($name, $msg);
    }

    private function migrateItems() {
        $name = 'Items';     
        $count = $this->getCount($name, 
            'SELECT COUNT(*) count FROM #__item ');

        if ($count == 0) {
            $this->settings['status'] = 'done';
            $this->msg($name, 'none found');
            return;
        }

        $msg = __('Processing %d %s...', $count, __('Items'));
        
        $query  = 'SELECT * '
                . 'FROM ' . $this->old_db->config['prefix'] . 'item '
                . 'ORDER BY ItemID '
                . 'LIMIT ' . $this->settings['offset'] . ', ' . $this->settings['limit'];

        $rows = $this->old_db->query($query);
        foreach ($rows as $row) {
            $oldObject = $row[$this->old_db->config['prefix'] . 'item'];
            $newObject = $this->Item->create();
            $newObject['Item']['legacy_id'] = $oldObject['ItemID'];
            $newObject['Item']['name'] = html_entity_decode($oldObject['ItemName']);
            $newObject['Item']['description'] = $oldObject['Description'];
            $newObject['Item']['created'] = $oldObject['CreateDate'];
            $newObject['Item']['modified'] = $oldObject['ModifyDate'];
            if ($oldObject['DeleteDate'] == 0) {
                $newObject['Item']['status'] = 1;
            } else {
                $newObject['Item']['status'] = 0;
            }
            $this->Item->save($newObject);
        }
        $msg .= $this->progressBar($this->settings['offset']+count($rows), $count);

        if (count($rows) < $this->settings['limit']) {
            $this->settings['status'] = 'done';
        } else {
            $this->settings['offset'] += $this->settings['limit'];
        }

        $this->msg($name, $msg);
    }

    private function migrateCategoryItems() {
        $name = 'CategoryItems';     
        $count = $this->getCount($name, 
            'SELECT COUNT(*) count FROM #__itemcategory ');

        if ($count == 0) {
            $this->settings['status'] = 'done';
            $this->msg($name, 'none found');
            return;
        }

        $msg = __('Processing %d %s-%s links...', $count, _('Category'), _('Item'));
        
        $query  = 'SELECT * '
                . 'FROM ' . $this->old_db->config['prefix'] . 'itemcategory '
                . 'ORDER BY CategoryID, ItemID '
                . 'LIMIT ' . $this->settings['offset'] . ', ' . $this->settings['limit'];

        $rows = $this->old_db->query($query);
        foreach ($rows as $row) {
            $oldObject = $row[$this->old_db->config['prefix'] . 'itemcategory'];
            $item = $this->Item->find('first', array('conditions'=>array('Item.legacy_id'=>$oldObject['ItemID'])));
            $cat = $this->Category->find('first', array('conditions'=>array('Category.legacy_id'=>$oldObject['CategoryID'])));
            
            $newObject = $this->CategoryItem->create();
            $newObject['CategoryItem']['item_id'] = $oldObject['ItemID'];
            $newObject['CategoryItem']['category_id'] = htmlentities($oldObject['CategoryID']);
            $newObject['CategoryItem']['is_primary'] = $oldObject['IsPrimary'];
            $newObject['CategoryItem']['created'] = $oldObject['CreateDate'];
            $newObject['CategoryItem']['modified'] = $oldObject['ModifyDate'];
            if ($oldObject['DeleteDate'] == 0) {
                $this->CategoryItem->save($newObject);
            }
        }
        $msg .= $this->progressBar($this->settings['offset']+count($rows), $count);

        if (count($rows) < $this->settings['limit']) {
            $this->settings['status'] = 'done';
        } else {
            $this->settings['offset'] += $this->settings['limit'];
        }

        $this->msg($name, $msg);
    }

    private function migrateItemDetails() {
        $name = 'Variations';     
        $count = $this->getCount($name, 
            'SELECT COUNT(*) count FROM #__detail ');

        if ($count == 0) {
            $this->settings['status'] = 'done';
            $this->msg($name, 'none found');
            return;
        }

        $msg = __('Processing %d %s...', $count, __('Variations'));
        
        $query  = 'SELECT d.*, u.Code FROM ' . $this->old_db->config['prefix'] . 'detail d ';
        $query .= 'LEFT JOIN ' . $this->old_db->config['prefix'] . 'unit u ON (d.UnitID=u.UnitID) ';
        $query .= 'LIMIT ' . $this->settings['offset'] . ', ' . $this->settings['limit'];

        $rows = $this->old_db->query($query);
        foreach ($rows as $row) {
            $oldObject = $row['d'];
            $oldUnit = $row['u'];
            $item = $this->Item->find('first', array('conditions'=>array('Item.legacy_id'=>$oldObject['ItemID'])));
            $unit = $this->Unit->find('first', array('conditions'=>array('Unit.unit'=>$oldUnit['Code'])));
            $newObject = $this->Variation->create();
            $newObject['Variation']['item_id'] = $item['Item']['id'];
            $newObject['Variation']['legacy_id'] = $oldObject['DetailID'];
            $newObject['Variation']['unit_id'] = $unit['Unit']['id'];
            $newObject['Variation']['name'] = $this->cleanString($oldObject['DetailName']);
            $newObject['Variation']['price'] = $oldObject['WebPrice'];
            $newObject['Variation']['price_rrp'] = $oldObject['RecommendedPrice'];
            $newObject['Variation']['created'] = $oldObject['CreateDate'];
            $newObject['Variation']['size'] = $oldObject['Size'];
            $newObject['Variation']['modified'] = $oldObject['ModifyDate'];            
            if ($oldObject['DeleteDate'] == 0) {
                $newObject['Variation']['status'] = 1;
            } else {
                $newObject['Variation']['status'] = 0;
            }
            $this->Variation->save($newObject);
        }
        $msg .= $this->progressBar($this->settings['offset']+count($rows), $count);

        if (count($rows) < $this->settings['limit']) {
            $this->settings['status'] = 'done';
        } else {
            $this->settings['offset'] += $this->settings['limit'];
        }

        $this->msg($name, $msg);
    }
    
    private function migratePictures() {
        $name = 'Pictures';     
        $count = $this->getCount($name, 
            'SELECT COUNT(*) count FROM #__picture ');

        if ($count == 0) {
            $this->settings['status'] = 'done';
            $this->msg($name, 'none found');
            return;
        }

        $imgPath = Configure::Read('Migrate.productImages');

        $msg = __('Processing %d %s...', $count, __('Pictures'));
        
        $query  = 'SELECT * FROM ' . $this->old_db->config['prefix'] . 'picture picture ';
        $query .= 'LEFT JOIN ' . $this->old_db->config['prefix'] . 'itempicture itempicture ON (picture.PictureID=itempicture.PictureID) ';
        $query .= 'LEFT JOIN ' . $this->old_db->config['prefix'] . 'item item ON (itempicture.ItemID=item.ItemID) ';
        $query .= 'LIMIT ' . $this->settings['offset'] . ', ' . $this->settings['limit'];

        $rows = $this->old_db->query($query);
        foreach ($rows as $row) {
            $newObject = $this->Picture->create();
            $newObject['Picture']['picture_id'] = $row['picture']['PictureID'];
            $newObject['Picture']['name'] = $this->cleanString($row['picture']['PictureName']);
            $newObject['Picture']['legacy_id'] = $this->cleanString($row['picture']['PictureID']);
            $newObject['Picture']['created'] = $row['picture']['CreateDate'];
            $newObject['Picture']['modified'] = $row['picture']['ModifyDate'];
            
            if (strlen($newObject['Picture']['name']) == 0) {
                $itemName = $this->cleanString($row['item']['ItemName']);
                if (strlen($itemName) == 0) {
                    $newObject['Picture']['name'] = 'picture';
                } else {
                    $newObject['Picture']['name'] = $itemName;
                }
            }
            
            if ($row['picture']['DeleteDate'] == 0) {
                $this->Picture->save($newObject);
                try {
                    $this->Picture->import(array(
                        'filename' => $imgPath . $row['picture']['FileName'],
                        'overwrite' => true,
                    ));
                } catch (Exception $exception) {
                    // failed to import image
                }
            }
        }
        $msg .= $this->progressBar($this->settings['offset']+count($rows), $count);

        if (count($rows) < $this->settings['limit']) {
            $this->settings['status'] = 'done';
        } else {
            $this->settings['offset'] += $this->settings['limit'];
        }

        $this->msg($name, $msg);
    }
    
    private function msg($section, $message) {
        $this->settings['messages'][$section] = $message;
    }

    /**
     * Converts HTML strings to plain strings.
     * Many strings in Enigma2 are only partially correct HTML, as they were all
     * manually entered by users.  This means it's not uncommon to have correct uses
     * of &amp; mixed with non-ASCII characters.
     * @param type $string
     * @return type 
     */
    private function cleanString($string) {
        $out = html_entity_decode($string);
        $out = strip_tags($out);
        return $out;
    }
    
    private function getCount($name, $query) {
        $query = str_replace('#__', $this->old_db->config['prefix'], $query);
        if (!array_key_exists($name, $this->settings['count'])) {
            $result = $this->old_db->query($query);
            $count = $result[0][0]['count'];
            $this->settings['count'][$name] = $count;
        } else {
            $count = $this->settings['count'][$name];
        }
        return $count;
    }
    
    private function progressBar($current, $max) {
        $width = 20;
        $current = (float)$current;
        $max = (float)$max;
        $percent = ($current/$max);
 
        $left = max(0, floor($percent*$width));
        $right = min($width, $width-$left);
        
        $barLeft = str_repeat('=', $left);
        $barRight = str_repeat('-', $right);
        $bar = "[$barLeft$barRight]";
        
        $out  = '<span class="progress_bar">';
        $out .= '<span class="progress_a">' . $barLeft . '</span>';
        $out .= '<span class="progress_b">' . $barRight . '</span>';
        $out .= '</span>';
        return sprintf('%s%d/%d: %d%%', $out, $current, $max, ($percent*100));
    }
    
}