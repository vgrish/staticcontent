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

	/* @var Qevix $qevix */
	public $qevix;
	public $qevixConfig = array();

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
		if (!$this->qevix) {
			$this->loadQevix();
		}
		$this->setQevixParams();
		if (!empty($this->initialized[$ctx])) {
			return true;
		}
		$this->initialized[$ctx] = true;
		return true;
	}

	/**
	 * Loads an instance of Qevix
	 *
	 * @return boolean
	 */
	public function loadQevix()
	{
		require_once dirname(__FILE__) . '/lib/qevix/qevix.php';

		if (!is_object($this->qevix) OR !($this->qevix instanceof Qevix)) {
			$this->qevix = new Qevix();
		}
		return !empty($this->qevix) && $this->qevix instanceof Qevix;
	}

	/*
	 * from https://github.com/bezumkin/modx-jevix/blob/master/core/components/jevix/model/qevix/jevix.class.php#L28
	 */
	public function processQevix($text = '')
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
		$errors = null;
		$text = $this->qevix->parse($text, $errors);
		if (!empty($errors) && !empty($this->config['logErrors'])) {
			$this->modx->log(modX::LOG_LEVEL_ERROR, 'Qevix errors: ' . print_r($errors, true));
		}
		if (!empty($this->config['debug'])) {
			ini_set('display_errors', $display_errors);
			ini_set('error_reporting', $error_reporting);
			$this->modx->setLogLevel($logLevel);
		}
		return $text;
	}

	/*
	 * Загружает конфиг Qevix
	 */
	public function setQevixParams($qevixType = 'default', $qevixConfigClear = true)
	{
		if ($qevixConfigClear) {
			$this->qevix->tagsRules = array();
		}
		$qConfig = $this->getQevixConfig();
		if (is_array($qConfig)) {
			foreach ($qConfig[$qevixType] as $sMethod => $aExec) {
				foreach ($aExec as $aParams) {
					if (!method_exists($this->qevix, $sMethod)) {
						$this->modx->log(modX::LOG_LEVEL_ERROR, 'Error on Qevix init. There is no method ' . $sMethod);
						continue;
					}
					call_user_func_array(array($this->qevix, $sMethod), $aParams);
				}
			}
		}
	}

	/**
	 * @param $param
	 * @param $value
	 */
	function setQevixParam($param, $value)
	{
		try {
			$this->qevix->$param($value);
		} catch (Exception $ex) {
			$this->modx->log(modX::LOG_LEVEL_INFO, $ex);
		}
	}

	/*
	 * Дефолтный конфиг Qevix
	 */
	protected function getQevixConfig()
	{
		$config = array(
			'default' => array(

				// Разрешённые теги
				'cfgAllowTags' => array(
					// вызов метода с параметрами
					array(
						array('p', 'ls', 'div', 'cut', 'a', 'img', 'i', 'b', 'u', 's', 'video', 'em', 'strong', 'nobr', 'li', 'ol', 'ul', 'sup', 'abbr', 'sub', 'acronym', 'h3', 'h4', 'h5', 'h6', 'br', 'hr', 'pre', 'code', 'object', 'param', 'embed', 'blockquote', 'iframe', 'table', 'th', 'tr', 'td'),
					),
				),
				// Коротие теги типа
				'cfgSetTagShort' => array(
					array(
						array('br', 'img', 'hr', 'cut', 'ls')
					),
				),
				// Преформатированные теги
				'cfgSetTagPreformatted' => array(
					array(
						array('pre', 'code', 'video')
					),
				),
				// Разрешённые параметры тегов
				'cfgAllowTagParams' => array(
					// вызов метода

					# array(
					# 	'p',
					# 	array('class')
					# ),
					# array(
					# 	'div',
					# 	array('class')
					# ),
					# array(
					# 	'h3',
					# 	array('class')
					# ),
					# array(
					# 	'h4',
					# 	array('class')
					# ),
					# array(
					# 	'h5',
					# 	array('class')
					# ),
					array(
						'img',
						array(
							'src', 'alt' => '#text', 'title',
							'align' => array('right', 'left', 'center', 'middle'),
							'width' => '#int', 'height' => '#int', 'hspace' => '#int', 'vspace' => '#int', /*'class'*/
							"style" => array(
								"float",
								"width",
							),
						)
					),
					// следующий вызов метода
					array(
						'a',
						array('title', 'href', 'rel' => '#text', 'name' => '#text', 'target' => array('_blank'))
					),
					// и т.д.
					array(
						'cut',
						array('name')
					),
					array(
						'object',
						array('width' => '#int', 'height' => '#int', 'data' => array('#domain' => array('youtube.com', 'rutube.ru', 'vimeo.com')), 'type' => '#text')
					),
					array(
						'param',
						array('name' => '#text', 'value' => '#text')
					),
					array(
						'embed',
						array('src' => array('#domain' => array('youtube.com', 'rutube.ru', 'vimeo.com')), 'type' => '#text', 'allowscriptaccess' => '#text', 'allowfullscreen' => '#text', 'width' => '#int', 'height' => '#int', 'flashvars' => '#text', 'wmode' => '#text')
					),
					array(
						'acronym',
						array('title')
					),
					array(
						'abbr',
						array('title')
					),
					array(
						'iframe',
						array('width' => '#int', 'height' => '#int', 'src' => array('#domain' => array('youtube.com', 'rutube.ru', 'vimeo.com')))
					),
					array(
						'ls',
						array('user' => '#text')
					),
					array(
						'td',
						array('colspan' => '#int', 'rowspan' => '#int', 'align' => array('right', 'left', 'center', 'justify'), 'height' => '#int', 'width' => '#int'/*, 'class'*/)
					),
					array(
						'th',
						array('colspan' => '#int', 'rowspan' => '#int', 'align' => array('right', 'left', 'center', 'justify'), 'height' => '#int', 'width' => '#int'/*, 'class'*/)
					),
					array(
						'table',
						array('border' => '#int', 'cellpadding' => '#int', 'cellspacing' => '#int', 'align' => array('right', 'left', 'center'), 'height' => '#int', 'width' => '#int'/*, 'class'*/)
					),
				),
				// Параметры тегов являющиеся обязательными
				'cfgSetTagParamsRequired' => array(
					array(
						'img',
						'src'
					),
				),
				// Теги которые необходимо вырезать из текста вместе с контентом
				'cfgSetTagCutWithContent' => array(
					array(
						array('script', 'style')
					),
				),
				// Вложенные теги
				'cfgSetTagChilds' => array(
					array(
						'ul',
						array('li'),
						false,
						true
					),
					array(
						'ol',
						array('li'),
						false,
						true
					),
					array(
						'object',
						'param',
						false,
						true
					),
					array(
						'object',
						'embed',
						false,
						false
					),
					array(
						'table',
						array('tr'),
						false,
						true
					),
					array(
						'tr',
						array('td', 'th'),
						false,
						true
					),
				),
				// Если нужно оставлять пустые не короткие теги
				'cfgSetTagIsEmpty' => array(
					array(
						array('param', 'embed', 'a', 'iframe')
					),
				),
				// Не нужна авто-расстановка <br>
				'cfgSetTagNoAutoBr' => array(
					array(
						array('ul', 'ol', 'object', 'table', 'tr')
					)
				),
				// Теги с обязательными параметрами
				'cfgSetTagParamDefault' => array(
					array(
						'embed',
						'wmode',
						'opaque',
						true
					),
				),
				// Отключение авто-добавления <br>
				'cfgSetAutoBrMode' => array(
					array(
						false
					)
				),
				'cfgSetTagNoTypography' => array(
					array(
						array('code', 'video', 'object')
					),
				),
				// Теги, после которых необходимо пропускать одну пробельную строку
				'cfgSetTagBlockType' => array(
					array(
						array('h4', 'h5', 'h6', 'ol', 'ul', 'blockquote', 'pre')
					)
				),
			),


		);
		return array_merge($config, $this->qevixConfig);
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