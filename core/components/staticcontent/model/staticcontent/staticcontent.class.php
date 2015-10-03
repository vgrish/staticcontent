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
			'processorsPath' => $corePath . 'processors/'
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
		if (!empty($this->initialized[$ctx])) {
			return true;
		}
		$this->initialized[$ctx] = true;
		return true;
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