<?php

require_once dirname(dirname(__FILE__)) . '/index.class.php';

class ControllersContentManagerController extends staticcontentMainController
{

	public static function getDefaultController()
	{
		return 'content';
	}

}

class staticcontentContentManagerController extends staticcontentMainController
{

	public function getPageTitle()
	{
		return $this->modx->lexicon('staticcontent') . ' :: ' . $this->modx->lexicon('staticcontent_content');
	}

	public function getLanguageTopics()
	{
		return array('staticcontent:default');
	}

	public function loadCustomCssJs()
	{
		$this->addJavascript(MODX_MANAGER_URL . 'assets/modext/util/datetime.js');
		$this->addJavascript($this->staticcontent->config['jsUrl'] . 'mgr/misc/staticcontent.utils.js');
		$this->addJavascript($this->staticcontent->config['jsUrl'] . 'mgr/misc/staticcontent.combo.js');

		$this->addJavascript($this->staticcontent->config['jsUrl'] . 'mgr/content/content.window.js');
		$this->addJavascript($this->staticcontent->config['jsUrl'] . 'mgr/content/content.grid.js');
		$this->addJavascript($this->staticcontent->config['jsUrl'] . 'mgr/content/content.panel.js');

		$gridFields = $this->staticcontent->getContentFields();

		$this->addHtml(str_replace('			', '', '
			<script type="text/javascript">
				Ext.onReady(function() {
					staticcontent.config.content_grid_fields = ' . $this->modx->toJSON($gridFields) . ';
					MODx.load({ xtype: "staticcontent-page-content"});
				});
			</script>'
		));
		$this->modx->invokeEvent('scOnManagerCustomCssJs', array('controller' => &$this, 'page' => 'content'));
	}

	public function getTemplateFile()
	{
		return $this->staticcontent->config['templatesPath'] . 'content.tpl';
	}

}

// MODX 2.3
class ControllersMgrContentManagerController extends ControllersContentManagerController
{

	public static function getDefaultController()
	{
		return 'content';
	}

}

class staticcontentMgrContentManagerController extends staticcontentContentManagerController
{

}
