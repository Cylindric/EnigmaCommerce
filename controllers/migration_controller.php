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
class MigrationController extends AppController
{
    var $components = array();
    var $uses = array();
    var $db;
    var $old_db;
    var $migrate_chunk = 50;
    var $migrate_offset = 0;
    var $migrate_section = 'start';
    var $migrate_messages = array();

    /**
     * Before every action, allow access and define default database.
     *
     * @access public
     * @return void
     */
    function beforeFilter()
    {
        parent::beforeFilter();
        $this->Auth->allow('index', 'migrate');
        $this->db = ConnectionManager::getDataSource('default');
    }


    /**
     * Index page
     *
     * @access public
     * @return void
     */
    function index()
    {
    }


    /**
     * Migrate data from a previous version of Enigma.
     *
     * Currently this only supports migrating data from Enigma 2.0.    As that's
     * the only version out there "in the wild", that shouldn't be a problem
     * yet...
     *
     * All data structures need to exist already, so should be run after
     * create().
     * The Data Source 'upgrade' must be defined in the database.php config.
     *
     * @access public
     * @return void
     */
    function migrate($section, $offset)
    {
        @$this->old_db = ConnectionManager::getDataSource('upgrade');
        if (is_null($this->old_db)) {
            $this->Session->setFlash(__("Error - you need to define an 'upgrade' db connection to migrate."));
            $this->redirect(array('action'=>'index'));
        }

        $this->migrate_messages = $this->Session->read('migrate_messages');

        if ($section == 'start') {
            $section = 'categories';
            $this->migrate_messages = array();
        }
        $this->migrate_section = $section;
        $this->migrate_offset = $offset;

        // categories
        switch ($this->migrate_section) {
            case ('categories'):
                $this->__migrateCategories();
                break;

            case ('catparents'):
                $this->__migrateCategoryParents();
                break;

            case ('items'):
                $this->__migrateItems();
                break;

            case ('itemcats'):
                $this->__migrateItemCategories();
                break;

            case ('details'):
                $this->__migrateDetails();
                break;

            case ('done'):
                $this->migrate_messages[] = array('text' => __('Done'));
                break;
        }

        $next_page = '/migration/migrate/' . $this->migrate_section . '/' . $this->migrate_offset;
        if ($this->migrate_section != 'done') {
            $this->Session->write('migrate_messages', $this->migrate_messages);
        }

        $this->set('next_page', $next_page);
        $this->set('section', $this->migrate_section);
        $this->set('msgs', $this->migrate_messages);
    }

    /**
     * Migrate all Categories.
     *
     * @access private
     * @return void
     */
    private function __migrateCategories()
    {
        App::import('Model', 'Category');
        $newcat = new Category();
        $msg = sprintf(__('Processing %s...'), __('categories'));

        $q = 'SELECT * FROM ' . $this->old_db->config['prefix'] . 'category ';
        if($this->Session->check('MigrationCategoryCount')) {
            $catCount = $this->Session->read();
        } else {
            $this->old_db->query($q);
            $catCount = $this->old_db->lastNumRows();
            $this->Session->write('MigrationCategoryCount', $catCount);
        }

        $q .= 'LIMIT ' . $this->migrate_offset . ', ' . $this->migrate_chunk;
        $this->migrate_offset += $this->migrate_chunk;
        $msg .= sprintf(__('Processing %d to %d of %d...'), $this->migrate_offset, ($this->migrate_offset + $this->migrate_chunk), $catCount);

        $oldcats = $this->old_db->query($q);
        $num = 0;
        $msg .= sprintf(__('found %d...'), count($oldcats));
        if (count($oldcats) < $this->migrate_chunk) {
            $this->migrate_section = 'catparents';
            $this->migrate_offset = 0;
        }
        foreach ($oldcats as $oldcat) {
            $oldcat = $oldcat[$this->old_db->config['prefix'] . 'category'];
            $newdata = array();
            $newdata['legacy_id'] = $oldcat['CategoryID'];
            $newdata['legacy_parent_id'] = $oldcat['ParentID'];
            $newdata['name'] = html_entity_decode($oldcat['CategoryName']);
            $newdata['description'] = $oldcat['Description'];
            $newdata['stockcodeprefix'] = $oldcat['StockCodePrefix'];
            $newdata['created'] = $oldcat['CreateDate'];
            $newdata['modified'] = $oldcat['ModifyDate'];
            if ($oldcat['DeleteDate'] == 0) {
                $newdata['status'] = 1;
            } else {
                $newdata['status'] = 0;
            }
            $newcat->create();
            $newcat->save($newdata);
            $num += 1;
        }

        $msg .= sprintf(__('processed %d...'), $num);
        $this->migrate_messages[] = array('text' => $msg);
    }

    /**
     * Update migrated Categories with their new Parent IDs.
     *
     * @access private
     * @return void
     */
    private function __migrateCategoryParents()
    {
        $this->loadModel('Category');
        $msg = sprintf(__('Processing %s...'), __('category') . ' ' . __('joins'));

        // Fix category hierarchy
        $num = 0;

        $conditions = array('Category.legacy_id !=' => 0);
        $params = array(
            'conditions' => $conditions,
            'limit' => $this->migrate_chunk,
            'offset' => $this->migrate_offset
        );
        $this->migrate_offset += $this->migrate_chunk;
        $msg .= sprintf(__('Processing %d to %d...'), $this->migrate_offset, ($this->migrate_offset + $this->migrate_chunk));

        $rootnode = $this->Category->find('first', array('conditions'=>array('Category.slug' => 'catrootnode')));

        $newcats = $this->Category->find('all', $params);
        if (count($newcats) < $this->migrate_chunk) {
            $this->migrate_section = 'items';
            $this->migrate_offset = 0;
        }
        if (!count($newcats) == 0) {
            foreach ($newcats as $cat) {
                if ($cat['Category']['legacy_parent_id'] == 0) {
                    $parentcat = $rootnode;
                } else {
                    $parentcat = $this->Category->find('first', array('conditions'=>array('Category.legacy_id' => $cat['Category']['legacy_parent_id'])));
                }
                $this->Category->id = $cat['Category']['id'];
                $this->Category->saveField('parent_id', $parentcat['Category']['id']);
                $num += 1;
            }

            $msg .= sprintf(__('processed %d...'), $num);
            $this->migrate_messages[] = array('text' => $msg);
        }
    }

    /**
     * Migrate the Items
     *
     * @access private
     * @return void
     */
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
            $this->migrate_section = 'details';
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
var_dump($old);
// var_dump($item);
// var_dump($cat);
            $newdata = $new->create();
            $newdata['item_id'] = $item['Item']['id'];
            $newdata['category_id'] = $cat['Category']['id'];
            $newdata['is_primary'] = $old['IsPrimary'];
            $newdata['created'] = $old['CreateDate'];
            $newdata['modified'] = $old['ModifyDate'];

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

    /**
     * Migrate the Item Details
     *
     * @access private
     * @return void
     */
    private function __migrateDetails()
    {
        $this->loadModel('Item');
        $this->loadModel('Unit');
        $this->loadModel('Detail');
        $new = new Detail();
        $msg = sprintf(__('Processing %s...'), __('details'));

        $q = 'SELECT * ';
        $q .= 'FROM ' . $this->old_db->config['prefix'] . 'detail ';
        $q .= 'LEFT JOIN ' . $this->old_db->config['prefix'] . 'unit ON (' . $this->old_db->config['prefix'] . 'detail.UnitID=' . $this->old_db->config['prefix'] . 'unit.UnitID) ';
        if($this->Session->check('MigrationDetailCount')) {
            $detailCount = $this->Session->read('MigrationDetailCount');
        } else {
            $this->old_db->query($q);
            $detailCount = $this->old_db->lastNumRows();
            $this->Session->write('MigrationDetailCount', $detailCount);
        }
        $q .= 'LIMIT ' . $this->migrate_offset . ', ' . $this->migrate_chunk;
        $this->migrate_offset += $this->migrate_chunk;
        $msg .= sprintf(__('Processing %d to %d of %d...'), $this->migrate_offset, ($this->migrate_offset + $this->migrate_chunk), $detailCount);

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

}