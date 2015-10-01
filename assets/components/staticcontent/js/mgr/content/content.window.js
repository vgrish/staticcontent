staticcontent.window.CreateContent = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        title: _('create'),
        width: 550,
        autoHeight: true,
        url: staticcontent.config.connector_url,
        action: 'mgr/content/create',
        fields: this.getFields(config),
        keys: this.getKeys(config),
        buttons: this.getButtons(config)
    });
    staticcontent.window.CreateContent.superclass.constructor.call(this, config);

    if (!config.update) {
        config.update = false;
    }

    console.log(config.update);

};
Ext.extend(staticcontent.window.CreateContent, MODx.Window, {

    getKeys: function (config) {
        return [{
            key: Ext.EventObject.ENTER,
            shift: true,
            fn: this.submit,
            scope: this
        }];
    },

    getButtons: function (config) {
        return [{
            text: !config.update ? _('create') : _('save'),
            scope: this,
            handler: function () {
                this.submit();
            }
        }];
    },

    getFields: function (config) {
        return [{
            xtype: 'hidden',
            name: 'id'
        }, {
            items: [{
                layout: 'form',
                cls: 'modx-panel',
                items: [{
                    layout: 'column',
                    defaults: {msgTarget: 'under',border: false},
                    border: false,
                    items: [{
                        columnWidth: .49,
                        border: false,
                        layout: 'form',
                        items: this.getLeftFields(config)
                    }, {
                        columnWidth: .505,
                        border: false,
                        layout: 'form',
                        cls: 'right-column',
                        items: this.getRightFields(config)
                    }]
                }]
            }]
        }, {
            xtype: 'xcheckbox',
            hideLabel: true,
            boxLabel: _('staticcontent_callbacks'),
            msgTarget: 'under',
            name: '_callbacks',
            checked: false,
            listeners: {
                check: staticcontent.utils.handleChecked,
                afterrender: staticcontent.utils.handleChecked
            }
        }, {
            xtype: 'textfield',
            fieldLabel: '',
            name: 'callbacks',
            anchor: '99%',
            allowBlank: false
        }, {
            xtype: 'xcheckbox',
            hideLabel: true,
            boxLabel: _('staticcontent_purpose'),
            name: '_purpose',
            checked: false,
            listeners: {
                check: staticcontent.utils.handleChecked,
                afterrender: staticcontent.utils.handleChecked
            }
        }, {
            xtype: 'textarea',
            fieldLabel: _('staticcontent_purpose'),
            msgTarget: 'under',
            name: 'purpose',
            anchor: '99%',
            height: 50,
            allowBlank: true
        }, {
            xtype: 'xcheckbox',
            hideLabel: true,
            boxLabel: _('staticcontent_comment'),
            name: '_comment',
            checked: false,
            listeners: {
                check: staticcontent.utils.handleChecked,
                afterrender: staticcontent.utils.handleChecked
            }
        }, {
            xtype: 'textarea',
            fieldLabel: '',
            msgTarget: 'under',
            name: 'comment',
            anchor: '99%',
            height: 50,
            allowBlank: true
        }];
    },

    getLeftFields: function (config) {
        return [{
            xtype: 'staticcontent-combo-client',
            custm: true,
            clear: true,
            fieldLabel: _('staticcontent_sender'),
            msgTarget: 'under',
            name: 'sender',
            anchor: '99%',
            allowBlank: true,
            disabled: config.update
        }, {
            xtype: 'numberfield',
            fieldLabel: _('staticcontent_sum'),
            msgTarget: 'under',
            name: 'sum',
            anchor: '99%',
            allowBlank: true,
            disabled: config.update
        }, {
            xtype: 'staticcontent-combo-payment-option',
            custm: true,
            clear: true,
            fieldLabel: _('staticcontent_payment'),
            msgTarget: 'under',
            name: 'payment',
            anchor: '99%',
            allowBlank: false,
            disabled: config.update
        }];
    },

    getRightFields: function (config) {
        return [{
            xtype: 'staticcontent-combo-client',
            custm: true,
            clear: true,
            fieldLabel: _('staticcontent_recipient'),
            msgTarget: 'under',
/*            blankText: '',
            invalidText: '',*/
            name: 'recipient',
            anchor: '99%',
            allowBlank: false,
            disabled: config.update
        }, {
            xtype: 'numberfield',
            fieldLabel: _('staticcontent_tax'),
            msgTarget: 'under',
            name: 'tax',
            anchor: '99%',
            allowBlank: true,
            disabled: true
        }, {
            xtype: 'staticcontent-combo-request-status',
            custm: true,
            clear: true,
            fieldLabel: _('staticcontent_status'),
            msgTarget: 'under',
            name: 'status',
            anchor: '99%',
            allowBlank: false,
            disabled: !config.update
        }, {
            xtype: 'staticcontent-combo-context',
            custm: true,
            clear: true,
            fieldLabel: _('staticcontent_context'),
            msgTarget: 'under',
            name: 'context',
            anchor: '99%',
            allowBlank: false
        }];
    }

});
Ext.reg('staticcontent-content-window-create', staticcontent.window.CreateContent);

