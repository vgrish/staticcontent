staticcontent.page.Content = function(config) {
	config = config || {};
	Ext.applyIf(config, {
		components: [{
			xtype: 'staticcontent-panel-content',
			renderTo: 'staticcontent-panel-content-div',
			baseCls: 'staticcontent-formpanel'
		}]
	});
	staticcontent.page.Content.superclass.constructor.call(this, config);
};
Ext.extend(staticcontent.page.Content, MODx.Component);
Ext.reg('staticcontent-page-content', staticcontent.page.Content);

staticcontent.panel.Content = function(config) {
	config = config || {};
	Ext.apply(config, {
		baseCls: 'modx-formpanel',
		layout: 'anchor',
		/*
		 stateful: true,
		 stateId: 'staticcontent-panel-content',
		 stateEvents: ['tabchange'],
		 getState:function() {return {activeTab:this.items.indexOf(this.getActiveTab())};},
		 */
		hideMode: 'offcontent',
		items: [{
			html: '<h2>' + _('staticcontent') + ' :: ' + _('staticcontent_content') + '</h2>',
			cls: '',
			style: {
				margin: '15px 0'
			}
		}, {
			xtype: 'modx-tabs',
			defaults: {
				border: false,
				autoHeight: true
			},
			border: true,
			hideMode: 'offcontent',
			items: [{
				title: _('staticcontent_content'),
				layout: 'anchor',
				items: [{
					html: _('staticcontent_content_intro'),
					cls: 'panel-desc'
				}, {
					xtype: 'staticcontent-grid-content',
					cls: 'main-wrapper'
				}]
			}]
		}]
	});
	staticcontent.panel.Content.superclass.constructor.call(this, config);
};
Ext.extend(staticcontent.panel.Content, MODx.Panel);
Ext.reg('staticcontent-panel-content', staticcontent.panel.Content);
