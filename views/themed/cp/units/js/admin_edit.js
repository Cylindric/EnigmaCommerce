Ext.onReady(function(){

    Ext.QuickTips.init();

    // turn on validation errors beside the field globally
    Ext.form.Field.prototype.msgTarget = 'side';

    var editForm = new Ext.FormPanel({
        labelWidth: 75,
        url: '<?php echo $this->Html->url(); ?>',
        frame: true,
        title: '<?php echo __("Editing \'%s\'", $data["Unit"]["name"]);?>',
        bodyStyle: 'padding:5px 5px 0',
        width: 600,
        onSubmit: Ext.emptyFn,
        submit: function(){
            this.getForm().getEl().dom.submit();
        },

        items: [{
                xtype: 'hidden',
                id: 'data[Unit][id]',
                name: 'data[Unit][id]',
                value: '<?php echo $data["Unit"]["id"];?>'
            },{
                xtype: 'textfield',
                id: 'data[Unit][name]',
                name: 'data[Unit][name]',
                fieldLabel: '<?php echo __("Name");?>',
                value: '<?php echo $data["Unit"]["name"];?>',
                allowBlank: false
            },{
                xtype: 'textfield',
                id: 'data[Unit][unit]',
                name: 'data[Unit][unit]',
                fieldLabel: '<?php echo __("Abbreviation");?>',
                value: '<?php echo $data["Unit"]["unit"];?>',
                allowBlank: false
            },{
                xtype: 'numberfield',
                id: 'data[Unit][scale_factor]',
                name: 'data[Unit][scale_factor]',
                fieldLabel: '<?php echo __("Scale Factor");?>',
                value: '<?php echo $data["Unit"]["scale_factor"];?>',
                allowNegative: false,
                decimalPrecision: 4,
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