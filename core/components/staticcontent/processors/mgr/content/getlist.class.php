<?php

/**
 * Get a list of scContent
 */
class scContentGetListProcessor extends modObjectGetListProcessor
{
	public $objectType = 'scContent';
	public $classKey = 'scContent';
	public $defaultSortField = 'id';
	public $defaultSortDirection = 'ASC';
	public $languageTopics = array('default', 'staticcontent');
	public $permission = '';

	/**
	 * * We doing special check of permission
	 * because of our objects is not an instances of modAccessibleObject
	 *
	 * @return boolean|string
	 */
	public function beforeQuery()
	{
		if (!$this->checkPermissions()) {
			return $this->modx->lexicon('access_denied');
		}

		return true;
	}


	/**
	 * @param xPDOQuery $c
	 *
	 * @return xPDOQuery
	 */
	public function prepareQueryBeforeCount(xPDOQuery $c)
	{

		if ($this->getProperty('combo')) {
			$c->select('id,name');
			$c->where(array('active' => 1));
			if ($requestId = $this->getProperty('request_id')) {
				/* @var psRequest $request */
				if ($request = $this->modx->getObject('psRequest', $requestId)) {
					/* @var scContent $status */
					$status = $request->getOne('Status');
					if ($status->get('final') == 1) {
						$c->where(array('id' => $status->get('id')));
					} else if ($status->get('fixed') == 1) {
						$c->where(array('rank:>=' => $status->get('rank')));
					}
				}
			}
		}

		$query = trim($this->getProperty('query'));
		if ($query) {
			$c->where(array(
				'name:LIKE' => "%{$query}%",
				'OR:description:LIKE' => "%{$query}%",
			));
		}

		return $c;
	}

	/** {@inheritDoc} */
	public function outputArray(array $array, $count = false)
	{
		if ($this->getProperty('addall')) {
			$array = array_merge_recursive(array(array(
				'id' => 0,
				'name' => $this->modx->lexicon('staticcontent_all')
			)), $array);
		}
		return parent::outputArray($array, $count);
	}

	/**
	 * @param xPDOObject $object
	 *
	 * @return array
	 */
	public function prepareRow(xPDOObject $object)
	{
		$icon = 'fa';
		$array = $object->toArray();
		$array['actions'] = array();

		// Edit
		$array['actions'][] = array(
			'cls' => '',
			'icon' => "$icon $icon-edit green",
			'title' => $this->modx->lexicon('staticcontent_action_update'),
			'action' => 'update',
			'button' => true,
			'menu' => true,
		);

		if (!$array['active']) {
			$array['actions'][] = array(
				'cls' => '',
				'icon' => "$icon $icon-toggle-off red",
				'title' => $this->modx->lexicon('staticcontent_action_active'),
				'action' => 'active',
				'button' => true,
				'menu' => true,
			);
		} else {
			$array['actions'][] = array(
				'cls' => '',
				'icon' => "$icon $icon-toggle-on green",
				'title' => $this->modx->lexicon('staticcontent_action_inactive'),
				'action' => 'inactive',
				'button' => true,
				'menu' => true,
			);
		}

		$array['actions'][] = array(
			'cls' => '',
			'icon' => "$icon $icon-trash-o red",
			'title' => $this->modx->lexicon('staticcontent_action_remove'),
			'action' => 'remove',
			'button' => true,
			'menu' => true,
		);

		return $array;
	}

}

return 'scContentGetListProcessor';