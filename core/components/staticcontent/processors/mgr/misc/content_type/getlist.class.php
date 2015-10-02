<?php

class modContentTypeGetListProcessor extends modObjectGetListProcessor
{
	public $classKey = 'modContentType';
	public $languageTopics = array('resource');
	public $defaultSortField = 'name';

	/** {@inheritDoc} */
	public function prepareQueryBeforeCount(xPDOQuery $c)
	{
		if ($this->getProperty('combo')) {
			$c->select('id,name');
		}
		$query = $this->getProperty('query');
		if (!empty($query)) {
			$c->where(array('name:LIKE' => '%' . $query . '%'));
		}
		return $c;
	}

	/** {@inheritDoc} */
	public function prepareRow(xPDOObject $object)
	{
		if ($this->getProperty('combo')) {
			$array = array(
				'id' => $object->get('id'),
				'pagetitle' => $object->get('name')
			);
		} else {
			$array = $object->toArray();
		}
		return $array;
	}
}

return 'modContentTypeGetListProcessor';