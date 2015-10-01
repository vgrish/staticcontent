<?php

/**
 * Class staticcontentMainController
 */
abstract class staticcontentMainController extends modExtraManagerController {
	/** @var staticcontent $staticcontent */
	public $staticcontent;


	/**
	 * @return void
	 */
	public function initialize() {
		$corePath = $this->modx->getOption('staticcontent_core_path', null, $this->modx->getOption('core_path') . 'components/staticcontent/');
		require_once $corePath . 'model/staticcontent/staticcontent.class.php';

		$this->staticcontent = new staticcontent($this->modx);
		$this->staticcontent->initialize($this->modx->context->key);

		$this->addCss($this->staticcontent->config['cssUrl'] . 'mgr/main.css');
		$this->addCss($this->staticcontent->config['cssUrl'] . 'mgr/bootstrap.buttons.css');
		$this->addCss($this->staticcontent->config['cssUrl'] . 'mgr/font-awesome.min.css');
		$this->addJavascript($this->staticcontent->config['jsUrl'] . 'mgr/staticcontent.js');

		$this->addHtml('
		<script type="text/javascript">
			staticcontent.config = ' . $this->modx->toJSON($this->staticcontent->config) . ';
			staticcontent.config.connector_url = "' . $this->staticcontent->config['connectorUrl'] . '";
		</script>
		');

		parent::initialize();
	}


	/**
	 * @return array
	 */
	public function getLanguageTopics() {
		return array('staticcontent:default');
	}


	/**
	 * @return bool
	 */
	public function checkPermissions() {
		return true;
	}
}


/**
 * Class IndexManagerController
 */
class IndexManagerController extends staticcontentMainController {

	/**
	 * @return string
	 */
	public static function getDefaultController() {
		return 'content';
	}
}