var MenuTree = function(){
    var Tree = Ext.tree;

    return {
        init : function(){
            var tree = new Tree.TreePanel({
                animate: true,
                autoScroll: true,
                loader: new Tree.TreeLoader({
                    dataUrl: '<?php echo $this->Html->url("/admin/units/menuitems.js");?>',
                    preloadChildren: true
                }),
                containerScroll: true,
                border: false,
                autoHeight: true,
                rootVisible: false,
                listeners: {
                    'click': onNodeClick
                }
            });

            // add a tree sorter in folder mode
            new Tree.TreeSorter(tree, {folderSort:true});

            // set the root node
            var root = new Tree.AsyncTreeNode({
                text: '<?php echo __("Units"); ?>',
                draggable: false,
                id: '0'
            });
            tree.setRootNode(root);

            // render the tree
            tree.render('left-navigation-tree');
            root.expand(false, false);
        }
    };
    
    function onNodeClick(node){
        var content = Ext.get('edit-form');
        content.load({
            url: node.attributes.editAction,
            params: '',
            test: 'Updating...',
            loadScripts: true,
            scripts: true
        });
        content.show();
    }

}();

Ext.EventManager.onDocumentReady(MenuTree.init, MenuTree, true);