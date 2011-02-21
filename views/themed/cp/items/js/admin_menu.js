/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 **/

var MenuTree = function(){
    var Tree = Ext.tree;

    return {
        init : function(){
            var tree = new Tree.TreePanel({
                animate: true,
                autoScroll: true,
                loader: new Tree.TreeLoader({
                    dataUrl:'<?php echo $this->Html->url("/admin/categories/menuitems");?>'
                }),
                containerScroll: true,
                border: false,
                autoHeight: true,
                rootVisible: false
            });

            // add a tree sorter in folder mode
            new Tree.TreeSorter(tree, {folderSort:true});

            // set the root node
            var root = new Tree.AsyncTreeNode({
                text: '<?php echo __("Items"); ?>',
                draggable: false,
                id: '0'
            });
            tree.setRootNode(root);

            // render the tree
            tree.render('left-navigation-tree');
            root.expand(false, false);
        }
    };
}();

Ext.EventManager.onDocumentReady(MenuTree.init, MenuTree, true);