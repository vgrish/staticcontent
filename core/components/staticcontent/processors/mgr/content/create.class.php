<?php

/**
 * Create an scContent
 */
class scContentCreateProcessor extends modObjectCreateProcessor
{
	public $objectType = 'scContent';
	public $classKey = 'scContent';
	public $languageTopics = array('staticcontent');
	public $permission = '';

	/**
	 * @return bool
	 */
	public function beforeSet()
	{
		$uri = $this->object->cleanUri($this->getProperty('uri'));
		$this->setProperty('uri', $uri);
		if (empty($uri)) {
			$this->modx->error->addField('uri', $this->modx->lexicon('staticcontent_err_ns'));
		} elseif ($this->modx->getCount($this->classKey, array('uri' => $uri))) {
			$this->modx->error->addField('uri', $this->modx->lexicon('staticcontent_err_ae'));
		}

		$hash = $this->object->getHash($uri);
		$this->setProperty('hash', $hash);
		if ($this->modx->getCount($this->classKey, array('hash' => $hash))) {
			$this->modx->error->addField('uri', $this->modx->lexicon('staticcontent_err_ae'));
		}

		return parent::beforeSet();
	}

}

return 'scContentCreateProcessor';