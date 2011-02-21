/**
 * Enigma : Online Sales Management. (http://www.enigmagen.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 **/

Ext.onReady(function(){

    Ext.QuickTips.init();

    // turn on validation errors beside the field globally
    Ext.form.Field.prototype.msgTarget = 'side';

    var editForm = new Ext.FormPanel({
        labelWidth: 75,
        url: '<?php echo $this->Html->url(); ?>',
        frame: true,
        title: '<?php echo __("Editing \'%s\'", $data["Item"]["name"]);?>',
        bodyStyle: 'padding:5px 5px 0',
        width: 600,
        onSubmit: Ext.emptyFn,
        submit: function(){
            this.getForm().getEl().dom.submit();
        },

        items: [{
                xtype: 'hidden',
                id: 'data[Item][id]',
                name: 'data[Item][id]',
                value: '<?php echo $data["Item"]["id"];?>'
            },{
                xtype: 'textfield',
                id: 'data[Item][name]',
                name: 'data[Item][name]',
                fieldLabel: '<?php echo __("Name");?>',
                value: '<?php echo $data["Item"]["name"];?>',
                allowBlank: false
            },{
                xtype: 'htmleditor',
                id: 'data[Item][description]',
                name: 'data[Item][description]',
                fieldLabel: '<?php echo __("Description");?>',
                value: '<?php echo $data["Item"]["description"];?>'
            }
        ],
        
        buttons: [{ 
            text: 'Save',
            handler: function(){ editForm.submit(); }
        }]
    });

    editForm.render('edit-form');

});