/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 **/

Ext.onReady(function(){
    Ext.QuickTips.init();

    var tb = new Ext.Toolbar();
    tb.render('toolbar');

    // Categories button
    tb.add({
        action: 'admin/categories/index',
        text: '<?php echo __("Categories"); ?>',
        iconCls: 'category',
        handler: onButtonClick
    }, '-');

    // Items button
    tb.add({
        action: 'admin/items/index',
        text: '<?php echo __("Items"); ?>',
        iconCls: 'item',
        handler: onButtonClick
    }, '-');
    
    // Users button
    tb.add({
        action: 'admin/users/index',
        text: '<?php echo __("Users"); ?>',
        iconCls: 'user',
        handler: onButtonClick
    }, '-');
    
    // Units button
    tb.add({
        action: 'admin/units/index',
        text: '<?php echo __("Units"); ?>',
        iconCls: 'unit',
        handler: onButtonClick
    }, '-');
    
    
    tb.doLayout();

    // functions to display feedback
    function onButtonClick(btn){
        var content = Ext.get('viewcontent');
        content.load({
            url: '<?php echo $webRoot;?>'+btn.action+'?resetFrame=body',
            params: '',
            test: 'Updating...',
            scripts: true
        });
        content.show();
    }

});