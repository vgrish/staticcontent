<?php

class scContentSetPropertyProcessor extends modObjectUpdateProcessor
{
	/** @var scContent $object */
	public $object;
	public $objectType = 'scContent';
	public $classKey = 'scContent';
	public $languageTopics = array('staticcontent');
	public $permission = '';

	/**
	 *
	 */
	public function beforeSet()
	{
		$fieldName = $this->getProperty('field_name', null);
		$fieldValue = $this->getProperty('field_value', null);

		$this->properties = array();

		if (!is_null($fieldName) && !is_null($fieldValue)) {
			$this->setProperty('field_name', $fieldName);
			$this->setProperty('field_value', $fieldValue);
		}

		return true;
	}

	/**
	 * @return bool|null|string
	 */
	public function beforeSave()
	{
		$fieldName = $this->getProperty('field_name', null);
		$fieldValue = $this->getProperty('field_value', null);
		if (!is_null($fieldName) && !is_null($fieldValue)) {
			$array = $this->object->toArray();
			if (isset($array[$fieldName])) {
				$this->object->fromArray(array(
					$fieldName => $fieldValue,
				));
			}
		}

		return parent::beforeSave();
	}
}

return 'scContentSetPropertyProcessor';