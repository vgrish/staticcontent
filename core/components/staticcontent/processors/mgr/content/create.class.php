<?php

/**
 * Create an scResource
 */
class scResourceCreateProcessor extends modObjectCreateProcessor
{
	public $objectType = 'scResource';
	public $classKey = 'scResource';
	public $languageTopics = array('staticcontent');
	public $permission = '';

	/**
	 * @return bool
	 */
	public function beforeSet()
	{
		$name = trim($this->getProperty('name'));
		if (empty($name)) {
			$this->modx->error->addField('name', $this->modx->lexicon('staticcontent_err_name'));
		} elseif ($this->modx->getCount($this->classKey, array('name' => $name))) {
			$this->modx->error->addField('name', $this->modx->lexicon('staticcontent_err_ae'));
		}

		return parent::beforeSet();
	}

}

return 'scResourceCreateProcessor';