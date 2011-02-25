<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 */

App::import('model', 'connection_manager');

class MigrationController extends AppController
{
    var $components = array();
    var $uses = array('Category', 'CategoryItem', 'Item', 'Variation');
    var $db;
    var $old_db;

    private $sequence = array(
        'Categories',
        'CategoryParents',
        'Items',
        'CategoryItems',
        'ItemDetails'
    );
    
    private $settings = array();
    
    function beforeFilter()
    {
        parent::beforeFilter();
        $this->Auth->allow('index', 'migrate');
        $this->db = ConnectionManager::getDataSource('default');
    }

    function index()
    {
        $this->Session->delete('migration_settings');
    }

    function migrate()
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
            'messages' => array()
        ), $this->settings);

        @$this->old_db = ConnectionManager::getDataSource($this->settings['source']);
        if (is_null($this->old_db)) {
            $this->Session->setFlash(__("Error - you need to define a '%s' db connection to migrate.", $this->settings['source']));
            $this->redirect(array('action'=>'index'));
        }
        
        if ($this->settings['status'] == 'done') {
            $this->settings['offset'] = 0;
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

    private function migrateCategories() {
        $name = 'Categories';     
        $count = $this->getCount($name, 
            'SELECT COUNT(*) count FROM ' . $this->old_db->config['prefix'] . 'category ');

        $msg = __('Processing %d %s...', $count, __(Inflector::pluralize($name)));

        $query  = 'SELECT * FROM ' . $this->old_db->config['prefix'] . 'category ';
        $query .= 'LIMIT ' . $this->settings['offset'] . ', ' . $this->settings['limit'];
        
        $msg .= __('Processing %d to %d...', 
            $this->settings['offset'], 
            min($count, ($this->settings['offset'] + $this->settings['limit'])));

        $rows = $this->old_db->query($query);
        foreach ($rows as $row) {
            $oldObject = $row[$this->old_db->config['prefix'] . 'category'];
            $newObject = $this->Category->create();
            $newObject['legacy_id'] = $oldObject['CategoryID'];
            $newObject['legacy_parent_id'] = $oldObject['ParentID'];
            $newObject['name'] = html_entity_decode($oldObject['CategoryName']);
            $newObject['description'] = $oldObject['Description'];
            $newObject['stockcodeprefix'] = $oldObject['StockCodePrefix'];
            $newObject['created'] = $oldObject['CreateDate'];
            $newObject['modified'] = $oldObject['ModifyDate'];
            if ($oldObject['DeleteDate'] == 0) {
                $newObject['status'] = 1;
            } else {
                $newObject['status'] = 0;
            }
            $this->Category->save($newObject);
        }

        $this->settings['offset'] += $this->settings['limit'];

        if (count($rows) < $this->settings['limit']) {
            $this->settings['status'] = 'done';
            $msg .= 'done';
        }

        $this->msg($name, $msg);
    }

    private function migrateCategoryParents() {
        $name = 'CategoryParents';     
//        $count = $this->getCount($name, 
//            'SELECT COUNT(*) count FROM ' . $this->old_db->config['prefix'] . 'category ');
//
//        $msg = __('Processing %d %s...', $count, __(Inflector::pluralize($name)));
//
//        $query  = 'SELECT * FROM ' . $this->old_db->config['prefix'] . 'category ';
//        $query .= 'LIMIT ' . $this->settings['offset'] . ', ' . $this->settings['limit'];
//        
//        $msg .= __('Processing %d to %d...', 
//        
        // Fix category hierarchy
        $num = 0;

        $conditions = array('Category.legacy_id !=' => 0);
        $params = array(
            'conditions' => $conditions,
            'limit' => $this->settings['limit'],
            'offset' => $this->settings['offset']
        );
        $this->migrate_offset += $this->migrate_chunk;
//        $msg .= sprintf(__('Processing %d to %d...'), $this->migrate_offset, ($this->migrate_offset + $this->migrate_chunk));
//
//        $rootnode = $this->Category->find('first', array('conditions'=>array('Category.slug' => 'catrootnode')));
//
//        $newcats = $this->Category->find('all', $params);
//        if (count($newcats) < $this->migrate_chunk) {
//            $this->migrate_section = 'items';
//            $this->migrate_offset = 0;
//        }
//        if (!count($newcats) == 0) {
//            foreach ($newcats as $cat) {
//                if ($cat['Category']['legacy_parent_id'] == 0) {
//                    $parentcat = $rootnode;
//                } else {
//                    $parentcat = $this->Category->find('first', array('conditions'=>array('Category.legacy_id' => $cat['Category']['legacy_parent_id'])));
//                }
//                $this->Category->id = $cat['Category']['id'];
//                $this->Category->saveField('parent_id', $parentcat['Category']['id']);
//                $num += 1;
//            }
//
//            $msg .= sprintf(__('processed %d...'), $num);
//            $this->msg('Categories', $msg);
//        }

        $this->settings['offset'] += $this->settings['limit'];

        if (count($rows) < $this->settings['limit']) {
            $this->settings['status'] = 'done';
            $msg .= 'done';
        }

        $this->msg($name, $msg);
    }

    private function migrateItems() {
        $this->msg('Items', 'done');
        $this->settings['status'] = 'done';
    }

    private function migrateCategoryItems() {
        $this->msg('CategoryItems', 'done');
        $this->settings['status'] = 'done';
    }

    private function migrateItemDetails() {
        $this->msg('ItemDetails', 'done');
        $this->settings['status'] = 'done';
    }
    
    private function msg($section, $message) {
        $this->settings['messages'][$section] = $message;
    }

    private function __migrateItems()
    {
        $this->loadModel('Item');
        $new = new Item();
        $msg = sprintf(__('Processing %s...'), __('items'));

        $q = 'SELECT * FROM ' . $this->old_db->config['prefix'] . 'item ';
        $q .= 'LIMIT ' . $this->migrate_offset . ', ' . $this->migrate_chunk;
        $this->migrate_offset += $this->migrate_chunk;
        $msg .= sprintf(__('Processing %d to %d...'), $this->migrate_offset, ($this->migrate_offset + $this->migrate_chunk));

        $olds = $this->old_db->query($q);
        $num = 0;
        $msg .= sprintf(__('found %d...'), count($olds));
        if (count($olds) < $this->migrate_chunk) {
            $this->migrate_section = 'variations';
            $this->migrate_offset = 0;
        }
        foreach ($olds as $old) {
            $old = $old[$this->old_db->config['prefix'] . 'item'];
            $newdata = array();
            $newdata['legacy_id'] = $old['ItemID'];
            $newdata['name'] = $old['ItemName'];
            $newdata['description'] = $old['Description'];
            $newdata['created'] = $old['CreateDate'];
            $newdata['modified'] = $old['ModifyDate'];
            if ($old['DeleteDate'] == 0) {
                $newdata['status'] = 1;
            } else {
                $newdata['status'] = 0;
            }
            $new->create();
            $new->save($newdata);
            $num += 1;
        }

        $msg .= sprintf(__('processed %d...'), $num);
        $this->migrate_messages[] = array('text' => $msg);
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
}