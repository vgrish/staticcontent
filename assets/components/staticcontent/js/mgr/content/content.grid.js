staticcontent.grid.Content = function (config) {
    config = config || {};

    this.exp = new Ext.grid.RowExpander({
        expandOnDblClick: false,
        tpl: new Ext.Template('<p class="desc">{description}</p>'),
        renderer: function (v, p, record) {
            return record.data.description != '' && record.data.description != null ? '<div class="x-grid3-row-expander">&#160;</div>' : '&#160;';
        }
    });

    this.dd = function (grid) {
        this.dropTarget = new Ext.dd.DropTarget(grid.container, {
            ddGroup: 'dd',
            copy: false,
            notifyDrop: function (dd, e, data) {
                var store = grid.store.data.items;
                var target = store[dd.getDragData(e).rowIndex].id;
                var source = store[data.rowIndex].id;
                if (target != source) {
                    dd.el.mask(_('loading'), 'x-mask-loading');
                    MODx.Ajax.request({
                        url: staticcontent.config.connector_url,
                        params: {
                            action: config.action || 'mgr/content/sort',
                            source: source,
                            target: target
                        },
                        listeners: {
                            success: {
                                fn: function (r) {
                                    dd.el.unmask();
                                    grid.refresh();
                                },
                                scope: grid
                            },
                            failure: {
                                fn: function (r) {
                                    dd.el.unmask();
                                },
                                scope: grid
                            }
                        }
                    });
                }
            }
        });
    };

    this.sm = new Ext.grid.CheckboxSelectionModel();

    Ext.applyIf(config, {
        id: 'staticcontent-grid-content',
        url: staticcontent.config.connector_url,
        baseParams: {
            action: 'mgr/content/getlist'
        },
        save_action: 'mgr/content/updatefromgrid',
        autosave: true,
        save_callback: this._updateRow,
        fields: this.getFields(config),
        columns: this.getColumns(config),
        tbar: this.getTopBar(config),
        listeners: this.getListeners(config),

        sm: this.sm,
        plugins: this.exp,
        /*ddGroup: 'dd',
         enableDragDrop: true,*/

        autoHeight: true,
        paging: true,
        remoteSort: true,
        viewConfig: {
            forceFit: true,
            enableRowBody: true,
            autoFill: true,
            showPreview: true,
            scrollOffset: 0
        },
        cls: 'staticcontent-grid',
        bodyCssClass: 'grid-with-buttons',
        stateful: true,
        stateId: 'staticcontent-grid-content-state'

    });
    staticcontent.grid.Content.superclass.constructor.call(this, config);
    this.getStore().sortInfo = {
        field: 'createdon',
        direction: 'DESC'
    };
};
Ext.extend(staticcontent.grid.Content, MODx.grid.Grid, {
    windows: {},

    getFields: function (config) {
        var fields = staticcontent.config.content_grid_fields;

        return fields;
    },

    getTopBar: function (config) {
        var tbar = [];
        tbar.push({
            text: '<i class="fa fa-cogs"></i> ', // + _('staticcontent_actions'),
            menu: [{
                text: '<i class="fa fa-plus"></i> ' + _('staticcontent_action_create'),
                cls: 'staticcontent-cogs',
                handler: this.createContent,
                scope: this
            }, {
                text: '<i class="fa fa-trash-o red"></i> ' + _('staticcontent_action_remove'),
                cls: 'staticcontent-cogs',
                handler: this.remove,
                scope: this
            }, '-', {
                text: '<i class="fa fa-toggle-on green"></i> ' + _('staticcontent_action_active'),
                cls: 'staticcontent-cogs',
                handler: this.active,
                scope: this
            }, {
                text: '<i class="fa fa-toggle-off red"></i> ' + _('staticcontent_action_inactive'),
                cls: 'staticcontent-cogs',
                handler: this.inactive,
                scope: this
            }]
        });
        tbar.push({
            text: '<i class="fa fa-refresh"></i>',
            handler: this.updateContent,
            scope: this
        });

        tbar.push('->');
        /* tbar.push({
         xtype: 'staticcontent-combo-operation-status',
         width: 210,
         custm: true,
         clear: true,
         addall: true,
         description: _('staticcontent_description_operation_status'),
         value: 1,
         listeners: {
         select: {
         fn: this._filterByContentStatus,
         scope: this
         }
         }
         });

         */
        if (1 != MODx.config.staticcontent_content_field_search_disable) {
            tbar.push({
                xtype: 'staticcontent-field-search',
                width: 210,
                listeners: {
                    search: {
                        fn: function (field) {
                            this._doSearch(field);
                        },
                        scope: this
                    },
                    clear: {
                        fn: function (field) {
                            field.setValue('');
                            this._clearSearch();
                        },
                        scope: this
                    }
                }
            });
        }

        return tbar;
    },

    getColumns: function (config) {
        var columns = [this.exp, this.sm];

        var add = {
            id: {
                width: 5,
                sortable: true
            },
            uri: {
                width: 25,
                sortable: true,
                editor: {
                    xtype: 'textfield',
                    allowBlank: false
                }
            },
            pagetitle: {
                width: 25,
                sortable: true
            },
            content_type: {
                width: 25,
                sortable: true,
                editor: {
                    xtype: 'staticcontent-combo-content_type',
                    custm: true,
                    clear: true,
                    allowBlank: false
                },
                renderer: function (value, metaData, record) {
                    return staticcontent.utils.renderReplace(value, record['json']['content_type_name'])
                }
            },
            context_key: {
                width: 15,
                sortable: true,
                editor: {
                    xtype: 'staticcontent-combo-context',
                    custm: true,
                    clear: true,
                    allowBlank: false
                }
            },
            template: {
                width: 25,
                sortable: true,
                editor: {
                    xtype: 'staticcontent-combo-template',
                    custm: true,
                    clear: true
                },
                renderer: function (value, metaData, record) {
                    return staticcontent.utils.renderReplace(value, record['json']['template_name'])
                }
            },
            resource: {
                width: 25,
                sortable: true,
                editor: {
                    xtype: 'staticcontent-combo-resource',
                    custm: true,
                    clear: true
                },
                renderer: function (value, metaData, record) {
                    return staticcontent.utils.renderReplace(value, record['json']['resource_name'])
                }
            },
            createdon: {
                width: 25,
                sortable: true,
                renderer: staticcontent.utils.formatDate
            },
            updatedon: {
                width: 25,
                sortable: true,
                renderer: staticcontent.utils.formatDate
            },
            actions: {
                width: 25,
                sortable: false,
                renderer: staticcontent.utils.renderActions,
                id: 'actions'
            }
        };

        for (var i = 0; i < staticcontent.config.content_grid_fields.length; i++) {
            var field = staticcontent.config.content_grid_fields[i];
            if (add[field]) {
                Ext.applyIf(add[field], {
                    header: _('staticcontent_header_' + field),
                    tooltip: _('staticcontent_tooltip_' + field),
                    dataIndex: field
                });
                columns.push(add[field]);
            }
        }

        return columns;
    },

    getListeners: function (config) {
        return {
            render: {
                fn: this.dd,
                scope: this
            }
        };
    },

    getMenu: function (grid, rowIndex) {
        var ids = this._getSelectedIds();
        var row = grid.getStore().getAt(rowIndex);
        var menu = staticcontent.utils.getMenu(row.data['actions'], this, ids);
        this.addContextMenuItem(menu);
    },


    onClick: function (e) {
        var elem = e.getTarget();
        if (elem.nodeName == 'BUTTON') {
            var row = this.getSelectionModel().getSelected();
            if (typeof(row) != 'undefined') {
                var action = elem.getAttribute('action');
                if (action == 'showMenu') {
                    var ri = this.getStore().find('id', row.id);
                    return this._showMenu(this, ri, e);
                } else if (typeof this[action] === 'function') {
                    this.menu.record = row.data;
                    return this[action](this, e);
                }
            }
        }
        return this.processEvent('click', e);
    },


    setAction: function (method, field, value) {
        var ids = this._getSelectedIds();
        if (!ids.length && (field !== 'false')) {
            return false;
        }
        MODx.Ajax.request({
            url: staticcontent.config.connector_url,
            params: {
                action: 'mgr/content/multiple',
                method: method,
                field_name: field,
                field_value: value,
                ids: Ext.util.JSON.encode(ids)
            },
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    },
                    scope: this
                },
                failure: {
                    fn: function (response) {
                        MODx.msg.alert(_('error'), response.message);
                    },
                    scope: this
                }
            }
        })
    },

    remove: function () {
        Ext.MessageBox.confirm(
            _('staticcontent_action_remove'),
            _('staticcontent_confirm_remove'),
            function (val) {
                if (val == 'yes') {
                    this.setAction('remove');
                }
            },
            this
        );
    },

    active: function (btn, e) {
        this.setAction('setproperty', 'active', 1);
    },

    inactive: function (btn, e) {
        this.setAction('setproperty', 'active', 0);
    },

    updateContent: function (btn, e) {
        this._updateRow();
    },

    createContent: function (btn, e) {
        var record = {
            ch_content: 1,
            content_type: 1,
            cacheable: 1,
            active: 1
        };
        var w = MODx.load({
            xtype: 'staticcontent-content-window-create',
            listeners: {
                success: {
                    fn: function () {
                        this.refresh();
                    }, scope: this
                }
            }
        });
        w.reset();
        w.setValues(record);
        w.show(e.target);
    },

    sendNotify: function () {
        Ext.MessageBox.confirm(
            _('staticcontent_action_notify'),
            _('staticcontent_confirm_send'),
            function (val) {
                if (val == 'yes') {
                    this.setAction('sendnotify', 'false', 0);
                }
            },
            this
        );
    },

    editContent: function (btn, e, row) {
        if (typeof(row) != 'undefined') {
            this.menu.record = row.data;
        }
        else if (!this.menu.record) {
            return false;
        }
        var id = this.menu.record.id;
        MODx.Ajax.request({
            url: this.config.url,
            params: {
                action: 'mgr/content/get',
                id: id
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var record = r.object;
                        if (!!record.properties) {
                            record.properties = Ext.util.JSON.encode(record.properties);
                        }
                        var w = MODx.load({
                            xtype: 'staticcontent-content-window-create',
                            title: _('update'),
                            action: 'mgr/content/update',
                            update: true,
                            record: r,
                            listeners: {
                                success: {
                                    fn: function () {
                                        this.refresh();
                                    }, scope: this
                                }
                            }
                        });
                        w.reset();
                        w.setValues(record);
                        w.show(e.target);
                    }, scope: this
                }
            }
        });
    },


    editSet: function (btn, e, row) {
        var record = typeof(row) != 'undefined' ? row.data : this.menu.record;

        MODx.Ajax.request({
            url: staticcontent.config.connector_url,
            params: {
                action: 'mgr/content/get',
                id: record.id
            },
            listeners: {
                success: {
                    fn: function (r) {
                        var record = r.object;
                        var w = MODx.load({
                            xtype: 'staticcontent-content-window-create',
                            record: record,
                            listeners: {
                                success: {
                                    fn: this.refresh,
                                    scope: this
                                }
                            }
                        });
                        w.fp.getForm().reset();
                        w.fp.getForm().setValues(record);
                        w.show(e.target);
                    },
                    scope: this
                }
            }
        });
    },

    _filterByContentStatus: function (cb) {
        this.getStore().baseParams[cb.hiddenName] = cb.value;
        this.getBottomToolbar().changePage(1);
    },

    _doSearch: function (tf) {
        this.getStore().baseParams.query = tf.getValue();
        this.getBottomToolbar().changePage(1);
    },

    _clearSearch: function () {
        this.getStore().baseParams.query = '';
        this.getBottomToolbar().changePage(1);
    },

    _updateRow: function (response) {
        this.refresh();
    },

    _getSelectedIds: function () {
        var ids = [];
        var selected = this.getSelectionModel().getSelections();

        for (var i in selected) {
            if (!selected.hasOwnProperty(i)) {
                continue;
            }
            ids.push(selected[i]['id']);
        }

        return ids;
    }

});
Ext.reg('staticcontent-grid-content', staticcontent.grid.Content);
