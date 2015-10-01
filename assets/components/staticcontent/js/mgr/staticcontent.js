var staticcontent = function (config) {
	config = config || {};
	staticcontent.superclass.constructor.call(this, config);
};
Ext.extend(staticcontent, Ext.Component, {
	page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, view: {}, utils: {}
});
Ext.reg('staticcontent', staticcontent);

staticcontent = new staticcontent();