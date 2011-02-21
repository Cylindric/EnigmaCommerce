<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 **/
class CategoriesController extends AppController {

    var $name = 'Categories';
    var $uses = array('Category', 'Item');

    function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('index', 'view', 'menu');
    }

    function index() {
        $parentid = $this->Category->field('id', array('Category.tag'=>'catrootnode'));
        $this->Category->recursive = 0;
        $this->paginate = array(
            'fields'=>array('Category.id', 'Category.name', 'Category.tag'),
            'contain'=>array(),
            'conditions'=>array('Category.parent_id'=>$parentid)
        );
        $this->set('categories', $this->paginate());
    }

    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid category', true));
            $this->redirect(array('action' => 'index'));
        }

        if (is_numeric($id)) {
            $category = $this->Category->findById($id);
        } else {
            $category = $this->Category->findByTag($id);
        }

        $lft = $category['Category']['lft'];
        $rght = $category['Category']['rght'];
        $items = $this->Item->find(
            'all', array(
                'joins'=>array(
                    array(
                        'table'=>$this->Category->tablePrefix.'category_items',
                        'alias'=>'CategoryItems',
                        'type'=>'inner',
                        'foreignKey'=>false,
                        'conditions'=>array('CategoryItems.item_id = Item.id'),
                    ),
                    array(
                        'table'=>$this->Category->tablePrefix.'categories',
                        'alias'=>'Category',
                        'type'=>'inner',
                        'foreignKey'=>false,
                        'conditions'=>array(
                            'Category.id = CategoryItems.category_id',
                            'Category.lft >=' => $lft,
                            'Category.rght <=' => $rght
                        ),
                    )
                ),
            )
        );

        $category['RelatedItems'] = $items;
        
        $this->set('items', $items);
        $this->set('category', $category);
    }

    function admin_view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid category', true));
            $this->redirect(array('action' => 'index'));
        }
        $this->set('category', $this->Category->read(null, $id));
    }

    function admin_index() {
        $this->Category->recursive = 0;
        $this->set('categories', $this->paginate());
    }

    function admin_add() {
        if (!empty($this->data)) {
            $this->Category->create();
            if ($this->Category->save($this->data)) {
                $this->Session->setFlash(__('The category has been saved', true));
                $this->redirect(array('action' => 'view', $this->Category->id));
            } else {
                $this->Session->setFlash(__('The category could not be saved. Please, try again.', true));
            }
        }
        $this->set('parents', $this->Category->find('list'));
        $this->set('items', $this->Category->Item->find('list'));
    }

    function admin_edit($id = null) {
        if (!$id && empty($this->data)) {
            $this->Session->setFlash(__('Invalid category', true));
            $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->Category->save($this->data)) {
                $this->Session->setFlash(__('The category has been saved', true));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The category could not be saved. Please, try again.', true));
            }
        }
        if (empty($this->data)) {
            $this->data = $this->Category->read(null, $id);
        }

        $this->set('parents', $this->Category->find('list'));
        $this->set('items', $this->Category->Item->find('list'));
    }

    function admin_delete($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid id for category', true));
            $this->redirect(array('action'=>'index'));
        }
        if ($this->Category->delete($id)) {
            $this->Session->setFlash(__('Category deleted', true));
            $this->redirect(array('action'=>'index'));
        }
        $this->Session->setFlash(__('Category was not deleted', true));
        $this->redirect(array('action' => 'index'));
    }

    function admin_menuitems() {
        $root = 0;
        if(isset($this->request->params['form']['node'])) {
            $root = (int)$this->request->params['form']['node'];
        }
        if ($root == 0) {
            $root = $this->Category->field('id', array('Tag'=>'catrootnode'));
        }

        $categories = $this->Category->find('all', array(
            'conditions' => array('Category.parent_id' => $root),
            'fields' => array('Category.id', 'Category.name', 'Category.parent_id')
        ));

        $nodes = array();
        foreach ($categories as $category) {
            $node = array();
            $node['id'] = $category['Category']['id'];
            $node['text'] = $category['Category']['name'];
            $node['cls'] = 'category';
            $node['iconCls'] = 'category';
            $count = $this->Category->find('count', array(
                'conditions' => array('Category.parent_id' => $category['Category']['id'])
            ));
            if ($count>0) {
                $node['leaf'] = false;
            } else {
                $node['leaf'] = true;
            }
            $nodes[] = $node;
        }

        $this->set('data', $nodes);
        $this->viewPath = 'elements';
        $this->render('js_data');
    }

}