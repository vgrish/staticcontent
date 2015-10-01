<?php

class scResourceRemoveProcessor extends modObjectRemoveProcessor
{
	public $classKey = 'scResource';
	public $languageTopics = array('staticcontent');
	public $permission = '';

	/** {@inheritDoc} */
	public function initialize()
	{
		if (!$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}
		return parent::initialize();
	}

	/** {@inheritDoc} */
	public function beforeRemove()
	{
		if (!$this->object->get('editable')) {
			$this->failure($this->modx->lexicon('staticcontent_err_lock'));
		}
		return parent::beforeRemove();
	}
}

return 'scResourceRemoveProcessor';