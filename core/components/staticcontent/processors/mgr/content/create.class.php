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
		$uri = mb_strtolower(trim($this->getProperty('uri')), 'UTF-8');
		$this->setProperty('uri', $uri);
		if (empty($uri)) {
			$this->modx->error->addField('uri', $this->modx->lexicon('staticcontent_err_uri'));
		} elseif ($this->modx->getCount($this->classKey, array('uri' => $uri))) {
			$this->modx->error->addField('uri', $this->modx->lexicon('staticcontent_err_ae'));
		}

		$hash = $this->getProperty('hash', md5($uri));
		$this->setProperty('hash', $hash);
		if ($this->modx->getCount($this->classKey, array('hash' => $hash))) {
			$this->modx->error->addField('uri', $this->modx->lexicon('staticcontent_err_ae'));
		}

		return parent::beforeSet();
	}

}

return 'scContentCreateProcessor';