<?php

/**
 * Update an scContent
 */
class scContentUpdateProcessor extends modObjectUpdateProcessor
{
	public $objectType = 'scContent';
	public $classKey = 'scContent';
	public $languageTopics = array('staticcontent');
	public $permission = '';


	/**
	 * We doing special check of permission
	 * because of our objects is not an instances of modAccessibleObject
	 *
	 * @return bool|string
	 */
	public function beforeSave()
	{
		if (!$this->checkPermissions()) {
			return $this->modx->lexicon('access_denied');
		}

		return true;
	}


	/**
	 * @return bool
	 */
	public function beforeSet()
	{
		$id = (int)$this->getProperty('id');
		$uri = $this->object->cleanUri($this->getProperty('uri'));
		$this->setProperty('uri', $uri);
		if (empty($uri)) {
			$this->modx->error->addField('uri', $this->modx->lexicon('staticcontent_err_ns'));
		} elseif ($this->modx->getCount($this->classKey, array('uri' => $uri, 'id:!=' => $id))) {
			$this->modx->error->addField('uri', $this->modx->lexicon('staticcontent_err_ae'));
		}

		$hash = $this->object->getHash($uri);
		$this->setProperty('hash', $hash);
		if ($this->modx->getCount($this->classKey, array('hash' => $hash, 'id:!=' => $id))) {
			$this->modx->error->addField('uri', $this->modx->lexicon('staticcontent_err_ae'));
		}

		return parent::beforeSet();
	}
}

return 'scContentUpdateProcessor';
