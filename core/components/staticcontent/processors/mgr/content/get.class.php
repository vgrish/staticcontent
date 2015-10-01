<?php

/**
 * Get an scResource
 */
class scResourceGetProcessor extends modObjectGetProcessor
{
	public $objectType = 'scResource';
	public $classKey = 'scResource';
	public $languageTopics = array('staticcontent');
	public $permission = '';


	/**
	 * We doing special check of permission
	 * because of our objects is not an instances of modAccessibleObject
	 *
	 * @return mixed
	 */
	public function process()
	{
		if (!$this->checkPermissions()) {
			return $this->failure($this->modx->lexicon('access_denied'));
		}

		return parent::process();
	}

}

return 'scResourceGetProcessor';