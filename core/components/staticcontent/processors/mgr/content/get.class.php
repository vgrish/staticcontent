<?php

/**
 * Get an scContent
 */
class scContentGetProcessor extends modObjectGetProcessor
{
	public $objectType = 'scContent';
	public $classKey = 'scContent';
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

return 'scContentGetProcessor';