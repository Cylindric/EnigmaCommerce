<?php
/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 **/

class CategoriesController extends AppController {

    var $name = 'Categories';
    var $uses = array('Category', 'Item');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('*');
    }

    public function index() {
        $parentid = $this->Category->field('id', array('Category.slug'=>'catrootnode'));
        
        $categories = $this->Category->find('all', array(
            'fields'=>array('Category.id', 'Category.name', 'Category.slug'),
            'contain'=>array(),
            'conditions'=>array('Category.parent_id'=>$parentid)
        ));

        $this->set('categories', $categories);
    }

    public function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid category'));
            $this->redirect(array('action' => 'index'));
        }

        $category = $this->Category->findById($id);
        $subCategories = $this->Category->subCategories($category['Category']['id']);
        $relatedItems = $this->Category->subItems($category['Category']['id']);

        $this->set('category', $category);
        $this->set('subCategories', $subCategories);
        $this->set('relatedItems', $relatedItems);
    }

    public function menu() {
        $this->ext = '.js';
    }
    
    /**
     * Retrieves a list of Categories suitable for use in the menus.
     * Note that this always returns it's data using the json encoding of the
     * js_data element.
     */
    public function menu_nodes() {
        $root = 0;
        if (isset($this->request->params['form']['node'])) {
            $root = (int)$this->request->params['form']['node'];
        }
        if ($root == 0) {
            $root = $this->Category->field('id', array('slug' => 'catrootnode'));
        }
        $data = $this->Category->menuNodes($root);
        $this->set('data', $data);
        $this->viewPath = 'elements';
        $this->render('js_data');
    }

}