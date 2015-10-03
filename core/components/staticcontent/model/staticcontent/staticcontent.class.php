<?php

/**
 * The base class for staticcontent.
 */
class staticcontent
{

	/* @var modX $modx */
	public $modx;
	public $namespace;
	public $config = array();
	/* @var array The array of errors */
	public $errors = array();
	/* @var array The array of error messages */
	public $messages = array();
	/* @var array $initialized */
	public $initialized = array();

	/* @var Jevix $jevix */
	public $jevix;

	/**
	 * @param modX $modx
	 * @param array $config
	 */
	function __construct(modX &$modx, array $config = array())
	{
		$this->modx =& $modx;

		$this->namespace = $this->getOption('namespace', $config, 'staticcontent');
		$corePath = $this->modx->getOption('staticcontent_core_path', $config, $this->modx->getOption('core_path') . 'components/staticcontent/');
		$assetsUrl = $this->modx->getOption('staticcontent_assets_url', $config, $this->modx->getOption('assets_url') . 'components/staticcontent/');
		$connectorUrl = $assetsUrl . 'connector.php';

		$this->config = array_merge(array(
			'assetsUrl' => $assetsUrl,
			'cssUrl' => $assetsUrl . 'css/',
			'jsUrl' => $assetsUrl . 'js/',
			'imagesUrl' => $assetsUrl . 'images/',
			'connectorUrl' => $connectorUrl,

			'corePath' => $corePath,
			'modelPath' => $corePath . 'model/',
			'chunksPath' => $corePath . 'elements/chunks/',
			'templatesPath' => $corePath . 'elements/templates/',
			'chunkSuffix' => '.chunk.tpl',
			'snippetsPath' => $corePath . 'elements/snippets/',
			'processorsPath' => $corePath . 'processors/',

		), $config);

		$this->modx->addPackage('staticcontent', $this->config['modelPath']);
		$this->modx->lexicon->load('staticcontent:default');
	}

	/**
	 * @param $key
	 * @param array $config
	 * @param null $default
	 * @return mixed|null
	 */
	public function getOption($key, $config = array(), $default = null)
	{
		$option = $default;
		if (!empty($key) && is_string($key)) {
			if ($config != null && array_key_exists($key, $config)) {
				$option = $config[$key];
			} elseif (array_key_exists($key, $this->config)) {
				$option = $this->config[$key];
			} elseif (array_key_exists("{$this->namespace}_{$key}", $this->modx->config)) {
				$option = $this->modx->getOption("{$this->namespace}_{$key}");
			}
		}
		return $option;
	}

	/**
	 * Initializes component into different contexts.
	 *
	 * @param string $ctx The context to load. Defaults to web.
	 * @param array $scriptProperties
	 *
	 * @return boolean
	 */
	public function initialize($ctx = 'web', $scriptProperties = array())
	{
		$this->config = array_merge($this->config, $scriptProperties);
		$this->config['ctx'] = $ctx;
		if (!$this->jevix) {
			$this->loadJevix();
		}
		$this->setJevixParams();
		if (!empty($this->initialized[$ctx])) {
			return true;
		}
		$this->initialized[$ctx] = true;
		return true;
	}

	/**
	 * Loads an instance of Jevix
	 *
	 * @return boolean
	 */
	public function loadJevix()
	{
		require_once dirname(__FILE__) . '/lib/jevix/jevix.class.php';

		if (!is_object($this->jevix) OR !($this->jevix instanceof Jevix)) {
			$this->jevix = new Jevix();
		}
		return !empty($this->jevix) && $this->jevix instanceof Jevix;
	}

	/*
	 * from https://github.com/bezumkin/modx-jevix/blob/master/core/components/jevix/model/jevix/jevix.class.php#L28
	 */
	public function processJevix($text = '')
	{
		if (empty($text)) {
			return '';
		}
		$logLevel = $this->modx->getLogLevel();
		$display_errors = ini_get('display_errors');
		$error_reporting = ini_get('error_reporting');
		if (!empty($this->config['debug'])) {
			ini_set('display_errors', 1);
			ini_set('error_reporting', -1);
			$this->modx->setLogLevel(xPDO::LOG_LEVEL_INFO);
		}
		$this->setJevixParams($this->config);
		$errors = null;
		$text = $this->jevix->parse($text, $errors);
		if (!empty($errors) && !empty($this->config['logErrors'])) {
			$this->modx->log(modX::LOG_LEVEL_ERROR, 'Jevix errors: ' . print_r($errors, true));
		}
		if (!empty($this->config['debug'])) {
			ini_set('display_errors', $display_errors);
			ini_set('error_reporting', $error_reporting);
			$this->modx->setLogLevel($logLevel);
		}
		return $text;
	}

	/**
	 * @param array $params
	 *
	 * from https://github.com/bezumkin/modx-jevix/blob/master/core/components/jevix/model/jevix/jevix.class.php#L84
	 *
	 */
	public function setJevixParams(array $params = array())
	{
		// Allowed tags
		if (isset($params['cfgAllowTags'])) {
			$this->setJevixParam('cfgAllowTags', array_map('trim', explode(',', $params['cfgAllowTags'])));
			unset($params['cfgAllowTags']);
		}
		// Other settings
		foreach ($params as $k => $v) {
			if (strpos($k, 'cfg') === false) {
				continue;
			} elseif (!method_exists($this, $k)) {
				$this->modx->log(modX::LOG_LEVEL_ERROR, 'Error on Jevix init. There is no method ' . $k);
				continue;
			} elseif (is_bool($v)) {
				$this->setJevixParam($k, $v);
			} elseif (empty($v)) {
				continue;
			} elseif (is_string($v) && $v[0] != '{' && $v[0] != '[') {
				$value = array_map('trim', explode(',', $v));
				$this->setJevixParam($k, $value);
			} else {
				$value = $this->modx->fromJSON($v);
				switch ($k) {
					case 'cfgAllowTagParams':
					case 'cfgSetTagParamsRequired':
						foreach ($value as $k2 => $v2) {
							try {
								$this->jevix->$k($k2, $v2);
							} catch (Exception $ex) {
								$this->modx->log(modX::LOG_LEVEL_INFO, $ex);
							}
						}
						break;
					case 'cfgSetAutoReplace':
					case 'cfgSetAutoPregReplace':
						if (count($value) != 2) {
							continue;
						}
						try {
							$this->jevix->$k($value[0], $value[1]);
						} catch (Exception $ex) {
							$this->modx->log(modX::LOG_LEVEL_INFO, $ex);
						}
						break;
					case 'cfgSetTagChilds':
						foreach ($value as $tmp) {
							try {
								$this->jevix->$k($tmp[0], $tmp[1], $tmp[2], $tmp[3]);
							} catch (Exception $ex) {
								$this->modx->log(modX::LOG_LEVEL_INFO, $ex);
							}
						}
						break;
					case 'cfgSetTagParamDefault':
						foreach ($value as $tmp) {
							try {
								$this->jevix->$k($tmp[0], $tmp[1], $tmp[2], $tmp[3]);
							} catch (Exception $ex) {
								$this->modx->log(modX::LOG_LEVEL_INFO, $ex);
							}
						}
						break;
					default:
						$this->setJevixParam($k, $value);
				}
			}
		}
	}

	/**
	 * @param $param
	 * @param $value
	 */
	function setJevixParam($param, $value)
	{
		try {
			$this->jevix->$param($value);
		} catch (Exception $ex) {
			$this->modx->log(modX::LOG_LEVEL_INFO, $ex);
		}
	}

	/**
	 * @return array Content fields
	 */
	public function getContentFields()
	{
		$gridFields = array_map('trim', explode(',', $this->getOption('content_grid_fields', null,
			'id,uri,template,content_type,resource', true)));
		$gridFields = array_values(array_unique(array_merge($gridFields, array(
			'id', 'uri', 'hash', 'template', 'content_type',
			'cacheable', 'resource_override', 'properties', 'actions'))));
		return $gridFields;
	}


}