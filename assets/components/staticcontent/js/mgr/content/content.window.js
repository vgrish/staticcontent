staticcontent.window.CreateContent = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        title: _('create'),
        width: 750,
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
        },/* {
            xtype: 'hidden',
            name: 'context_key'
        },*//* {
            xtype: 'hidden',
            name: 'hash'
        }, */{
            items: [{
                layout: 'form',
                cls: 'modx-panel',
                items: [{
                    layout: 'column',
                    defaults: {msgTarget: 'under',border: false},
                    border: false,
                    items: [{
                        columnWidth: .6,
                        border: false,
                        layout: 'form',
                        items: this.getLeftFields(config)
                    }, {
                        columnWidth: .4,
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
            boxLabel: _('staticcontent_content'),
            name: 'ch_content',
            checked: false,
            workCount: 1,
            listeners: {
                check: staticcontent.utils.handleChecked,
                afterrender: staticcontent.utils.handleChecked
            }
        }, {
            xtype: 'textarea',
            fieldLabel: _('staticcontent_content'),
            name: 'content',
            anchor: '99.5%',
            height: 150,
            allowBlank: true
        }, {
            items: [{
                layout: 'form',
                cls: 'modx-panel',
                items: [{
                    layout: 'column',
                    defaults: {msgTarget: 'under',border: false},
                    border: false,
                    items: [{
                        columnWidth: .5,
                        border: false,
                        layout: 'form',
                        items: [{
                            xtype: 'xdatetime',
                            cls: 'date-combo',
                            ctCls: 'date-combo',
                            dateFormat: 'Y-m-d',
                            timeFormat: 'H:i',
                            fieldLabel: _('staticcontent_createdon'),
                            name: 'createdon',
                            anchor: '99%',
                            allowBlank: false
                        }]
                    }, {
                        columnWidth: .5,
                        border: false,
                        layout: 'form',
                        cls: 'right-column',
                        items: [{
                            xtype: 'xdatetime',
                            cls: 'date-combo',
                            ctCls: 'date-combo',
                            dateFormat: 'Y-m-d',
                            timeFormat: 'H:i',
                            fieldLabel: _('staticcontent_updatedon'),
                            name: 'updatedon',
                            anchor: '99%',
                            allowBlank: false
                        }]
                    }]
                }]
            }]
        }, {
            xtype: 'xcheckbox',
            hideLabel: true,
            boxLabel: _('staticcontent_properties'),
            checked: false,
            workCount: 1,
            listeners: {
                check: staticcontent.utils.handleChecked,
                afterrender: staticcontent.utils.handleChecked
            }
        }, {
            xtype: 'textarea',
            fieldLabel: _('staticcontent_properties'),
            name: 'properties',
            anchor: '99%',
            allowBlank: true
        }];
    },

    getLeftFields: function (config) {
        return [{
            xtype: 'textfield',
            fieldLabel: _('staticcontent_uri'),
            name: 'uri',
            anchor: '99%',
            allowBlank: false
        }, /*{
            xtype: 'displayfield',
            fieldLabel: _('staticcontent_hash'),
            msgTarget: 'under',
            name: 'hash',
            anchor: '99%'
        },*/ {
            xtype: 'textfield',
            fieldLabel: _('staticcontent_pagetitle'),
            name: 'pagetitle',
            anchor: '99%',
            allowBlank: true
        }, {
            xtype: 'xcheckbox',
            hideLabel: true,
            boxLabel: _('staticcontent_longtitle'),
            checked: false,
            workCount: 1,
            listeners: {
                check: staticcontent.utils.handleChecked,
                afterrender: staticcontent.utils.handleChecked
            }
        }, {
            xtype: 'textfield',
            fieldLabel: _('staticcontent_longtitle'),
            name: 'longtitle',
            anchor: '99%',
            allowBlank: true
        }, {
            xtype: 'xcheckbox',
            hideLabel: true,
            boxLabel: _('staticcontent_description'),
            checked: false,
            workCount: 1,
            listeners: {
                check: staticcontent.utils.handleChecked,
                afterrender: staticcontent.utils.handleChecked
            }
        }, {
            xtype: 'textarea',
            fieldLabel: _('staticcontent_description'),
            name: 'description',
            anchor: '99%',
            allowBlank: true
        }, {
            xtype: 'xcheckbox',
            hideLabel: true,
            boxLabel: _('staticcontent_introtext'),
            checked: false,
            workCount: 1,
            listeners: {
                check: staticcontent.utils.handleChecked,
                afterrender: staticcontent.utils.handleChecked
            }
        }, {
            xtype: 'textarea',
            fieldLabel: _('staticcontent_introtext'),
            name: 'introtext',
            anchor: '99%',
            allowBlank: true
        }];
    },

    //content_type

    getRightFields: function (config) {
        return [{
            xtype: 'staticcontent-combo-content_type',
            custm: true,
            clear: true,
            fieldLabel: _('staticcontent_content_type'),
            name: 'content_type',
            anchor: '99%',
            allowBlank: true
        }, {
            xtype: 'staticcontent-combo-template',
            custm: true,
            clear: true,
            fieldLabel: _('staticcontent_template'),
            name: 'template',
            anchor: '99%',
            allowBlank: true
        }, {
            xtype: 'xcheckbox',
            hideLabel: true,
            boxLabel: _('staticcontent_resource_override'),
            name: 'resource_override',
            checked: false,
            workCount: 1,
            listeners: {
                check: staticcontent.utils.handleChecked,
                afterrender: staticcontent.utils.handleChecked
            }
        }, {
            xtype: 'staticcontent-combo-resource',
            custm: true,
            clear: true,
            fieldLabel: _('staticcontent_resource'),
            name: 'resource',
            anchor: '99%',
            allowBlank: true
        }, {
            xtype: 'xcheckbox',
            hideLabel: true,
            boxLabel: _('staticcontent_cacheable'),
            name: 'cacheable',
            checked: false
        }, {
            xtype: 'xcheckbox',
            hideLabel: true,
            boxLabel: _('staticcontent_context_key'),
            checked: false,
            workCount: 1,
            listeners: {
                check: staticcontent.utils.handleChecked,
                afterrender: staticcontent.utils.handleChecked
            }
        }, {
            xtype: 'staticcontent-combo-context',
            custm: true,
            clear: true,
            fieldLabel: _('staticcontent_context_key'),
            name: 'context_key',
            anchor: '99%',
            allowBlank: false
        }, {
            xtype: 'xcheckbox',
            hideLabel: true,
            boxLabel: _('staticcontent_active'),
            name: 'active',
            checked: false
        }];
    }

});
Ext.reg('staticcontent-content-window-create', staticcontent.window.CreateContent);

