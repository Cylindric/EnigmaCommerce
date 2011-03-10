Ext.onReady(function(){

    Ext.QuickTips.init();

    // turn on validation errors beside the field globally
    Ext.form.Field.prototype.msgTarget = 'side';

    var editForm = new Ext.FormPanel({
        labelWidth: 75,
        url: '<?php echo $this->Html->url(); ?>',
        frame: true,
        title: '<?php echo __("Editing \'%s\'", $data["Category"]["name"]);?>',
        bodyStyle: 'padding:5px 5px 0',
        width: 600,
        onSubmit: Ext.emptyFn,
        submit: function(){
            this.getForm().getEl().dom.submit();
        },

        items: [{
                xtype: 'hidden',
                id: 'data[Category][id]',
                name: 'data[Category][id]',
                value: '<?php echo $data["Category"]["id"];?>'
            },{
                xtype: 'textfield',
                id: 'data[Category][name]',
                name: 'data[Category][name]',
                fieldLabel: '<?php echo __("Name");?>',
                value: '<?php echo $data["Category"]["name"];?>',
                allowBlank: false
            }
        ],

        buttons: [{ 
            text: 'Save',
            handler: function(){ editForm.submit(); }
        }]
    });

    editForm.render('edit-form');

});