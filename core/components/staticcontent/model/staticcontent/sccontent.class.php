<?php
class scContent extends xPDOSimpleObject {


	/**
	 * @param bool $cacheFlag
	 * @return bool
	 */
	public function save($cacheFlag = false)
	{
		$isNew = $this->isNew();

		if ($isNew) {
			$this->set('createdon', date('Y-m-d H:i:s'));
		} else {
			$this->set('updatedon', date('Y-m-d H:i:s'));
		}

		$saved = parent:: save($cacheFlag);

		return $saved;
	}

}