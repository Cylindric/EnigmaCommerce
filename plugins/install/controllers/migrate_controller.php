<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 */

App::import('model', 'connection_manager');

class MigrateController extends AppController
{
    var $components = array();
    var $uses = array('Category', 'CategoryItem', 'Item', 'Variation');
    var $db;
    var $old_db;

    private $sequence = array(
        'CreateBlank',
        'Categories',
        'CategoryParents',
        'CategoryTreeRepair',
        'Items',
        'CategoryItems',
//        'ItemDetails'
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
            'limit' => 100,
            'count' => array(),
            'source' => 'upgrade',
            'status' => 'start',
            'messages' => array()
        ), $this->settings);

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
            'SELECT COUNT(*) count FROM ' . $this->old_db->config['prefix'] . 'category ');

        if ($count == 0) {
            $this->settings['status'] = 'done';
            $this->msg($name, 'none found');
            return;
        }

        $msg = __('Processing %d %s...', $count, __(Inflector::pluralize($name)));

        // Prevent the MPTT behavior from making the import take a long time
        // Tree will need rebuilding afterwards
        $this->Category->Behaviors->disable('Tree');
        
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
        $this->Category->Behaviors->enable('Tree');

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
        $this->Category->Behaviors->disable('Tree');
        
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
        $this->Category->Behaviors->enable('Tree');

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
            $parentId = $this->Category->field('id', array('conditions'=>array('Category.slug' => 'catrootnode')));
            $this->Category->recover('parent', $parentId);
            $msg .= $this->progressBar(1, 1);
            $this->settings['status'] = 'done';
        }
        $this->msg($name, $msg);
    }

    private function migrateItems() {
        $name = 'Items';     
        $count = $this->getCount($name, 
            'SELECT COUNT(*) count FROM ' . $this->old_db->config['prefix'] . 'item ');

        if ($count == 0) {
            $this->settings['status'] = 'done';
            $this->msg($name, 'none found');
            return;
        }

        $msg = __('Processing %d %s...', $count, __('Items'));
        
        $query  = 'SELECT * FROM ' . $this->old_db->config['prefix'] . 'item ';
        $query .= 'LIMIT ' . $this->settings['offset'] . ', ' . $this->settings['limit'];

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
            'SELECT COUNT(*) count FROM ' . $this->old_db->config['prefix'] . 'itemcategory ');

        if ($count == 0) {
            $this->settings['status'] = 'done';
            $this->msg($name, 'none found');
            return;
        }

        $msg = __('Processing %d %s-%s links...', $count, _('Category'), _('Item'));
        
        $query  = 'SELECT * FROM ' . $this->old_db->config['prefix'] . 'itemcategory ';
        $query .= 'LIMIT ' . $this->settings['offset'] . ', ' . $this->settings['limit'];

        $rows = $this->old_db->query($query);
        foreach ($rows as $row) {
            $oldObject = $row[$this->old_db->config['prefix'] . 'itemcategory'];
            $item = $this->Item->find('first', array('conditions'=>array('Item.legacy_id'=>$oldObject['ItemID'])));
            $cat = $this->Category->find('first', array('conditions'=>array('Category.legacy_id'=>$oldObject['CategoryID'])));
            
            $newObject = $this->CategoryItem->create();
            $newObject['CategoryItem']['item_id'] = $oldObject['ItemID'];
            $newObject['CategoryItem']['category_id'] = html_entity_decode($oldObject['CategoryID']);
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
        $this->msg('ItemDetails', 'done');
        $this->settings['status'] = 'done';
    }
    
    private function msg($section, $message) {
        $this->settings['messages'][$section] = $message;
    }

    private function __migrateItemCategories()
    {
        $this->loadModel('Category');
        $this->loadModel('Item');
        $this->loadModel('CategoryItem');

        $new = new CategoryItem();
        $msg = sprintf(__('Processing %s-%s links...'), __('item'), __('category'));

        $q = 'SELECT * FROM ' . $this->old_db->config['prefix'] . 'itemcategory ';
        if($this->Session->check('MigrationItemCategoryCount')) {
            $itemCatCount = $this->Session->read();
        } else {
            $this->old_db->query($q);
            $itemCatCount = $this->old_db->lastNumRows();
            $this->Session->write('MigrationItemCategoryCount', $itemCatCount);
        }

        $q .= 'LIMIT ' . $this->migrate_offset . ', ' . $this->migrate_chunk;
        $this->migrate_offset += $this->migrate_chunk;
        $msg .= sprintf(__('Processing %d to %d of %d...'), $this->migrate_offset, ($this->migrate_offset + $this->migrate_chunk), $itemCatCount);

        $olds = $this->old_db->query($q);
        $num = 0;
        $msg .= sprintf(__('found %d...'), count($olds));
        if (count($olds) < $this->migrate_chunk) {
            $this->migrate_section = 'done';
            $this->migrate_offset = 0;
        }
        foreach ($olds as $old) {
            $old = $old[$this->old_db->config['prefix'] . 'itemcategory'];
            $item = $this->Item->find('first', array('conditions'=>array('Item.legacy_id'=>$old['ItemID'])));
            $cat = $this->Category->find('first', array('conditions'=>array('Category.legacy_id'=>$old['CategoryID'])));
            $newdata = $new->create();
            $newdata['CategoryItem']['item_id'] = $item['Item']['id'];
            $newdata['CategoryItem']['category_id'] = $cat['Category']['id'];
            $newdata['CategoryItem']['is_primary'] = $old['IsPrimary'];
            $newdata['CategoryItem']['created'] = $old['CreateDate'];
            $newdata['CategoryItem']['modified'] = $old['ModifyDate'];

            if ($old['DeleteDate'] == 0) {
                $new->save($newdata);
            } else {
//                 $newdata['status'] = 0;
            }
            $num += 1;
        }

        $msg .= sprintf(__('processed %d...'), $num);
        $this->migrate_messages[] = array('text' => $msg);
    }

    private function __migrateDetails()
    {
        $this->loadModel('Item');
        $this->loadModel('Unit');
        $this->loadModel('Variation');
        $new = new Variation();
        $msg = sprintf(__('Processing %s...'), __('variations'));

        $q = 'SELECT * ';
        $q .= 'FROM ' . $this->old_db->config['prefix'] . 'detail ';
        $q .= 'LEFT JOIN ' . $this->old_db->config['prefix'] . 'unit ON (' . $this->old_db->config['prefix'] . 'variation.UnitID=' . $this->old_db->config['prefix'] . 'unit.UnitID) ';
        if($this->Session->check('MigrationVariationCount')) {
            $variationCount = $this->Session->read('MigrationVariationCount');
        } else {
            $this->old_db->query($q);
            $variationCount = $this->old_db->lastNumRows();
            $this->Session->write('MigrationVariationCount', $variationCount);
        }
        $q .= 'LIMIT ' . $this->migrate_offset . ', ' . $this->migrate_chunk;
        $this->migrate_offset += $this->migrate_chunk;
        $msg .= sprintf(__('Processing %d to %d of %d...'), $this->migrate_offset, ($this->migrate_offset + $this->migrate_chunk), $variationCount);

        $olds = $this->old_db->query($q);
        $num = 0;
        $msg .= sprintf(__('found %d...'), count($olds));
        if (count($olds) < $this->migrate_chunk) {
            $this->migrate_section = 'itemcats';
            $this->migrate_offset = 0;
        }
        foreach ($olds as $old) {
            $old_unit = $old[$this->old_db->config['prefix'] . 'unit'];
            $old = $old[$this->old_db->config['prefix'] . 'detail'];
            $newdata = array();
            $newdata['legacy_id'] = $old['DetailID'];
            $newdata['name'] = $old['DetailName'];
            $newdata['price'] = $old['RetailPrice'];
            $newdata['rrp'] = $old['RecommendedPrice'];
            $newdata['stockcode'] = $old['StockCode'];
            $newdata['size'] = $old['Size'];
            $newdata['created'] = $old['CreateDate'];
            $newdata['modified'] = $old['ModifyDate'];
            if ($old['DeleteDate'] == 0) {
                $newdata['status'] = 1;
            } else {
                $newdata['status'] = 0;
            }

            $newdata['item_id'] = 0;
            $parentitem = $this->Item->find('first', array('conditions'=>array('Item.legacy_id'=>$old['ItemID'])));
            if ($parentitem) {
                $newdata['item_id'] = $parentitem['Item']['id'];
            }

            $newdata['unit_id'] = 0;
            $parentunit = $this->Unit->find('first', array('conditions'=>array('Unit.unit'=>$old_unit['Code'])));
            if ($parentunit) {
                $newdata['unit_id'] = $parentunit['Unit']['id'];
            }

            $new->create();
            $new->save($newdata);
            $num += 1;
        }

        $msg .= sprintf(__('processed %d...'), $num);
        $this->migrate_messages[] = array('text' => $msg);
    }

    private function getCount($name, $query) {
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