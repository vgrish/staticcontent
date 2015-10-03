<?php


class scContent extends xPDOSimpleObject
{

	const _NAMESPACE = 'staticcontent';

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

	public function cleanUri($alias, array $options = array())
	{
		return $this->xpdo->call($this->_class, 'filterPathSegment', array(&$this->xpdo, $alias, $options));
	}

	/**
	 * Filter a string for use as a URL path segment.
	 *
	 * @param modX|xPDO &$xpdo A reference to a modX or xPDO instance.
	 * @param string $segment The string to filter into a path segment.
	 * @param array $options Local options to override global filter settings.
	 *
	 * @return string The filtered string ready to use as a path segment.
	 */
	public static function filterPathSegment(&$xpdo, $segment, array $options = array())
	{
		$settings = array();
		$namespace = self::_NAMESPACE;
		$q = $xpdo->newQuery('modSystemSetting');
		$q->select('key,value');
		$q->where(array(
			'namespace' => $namespace,
			'key:LIKE' => $namespace . '\_friendly_%'
		));
		$q->limit(10);
		if ($q->prepare() && $q->stmt->execute()) {
			$settings = $q->stmt->fetchAll(PDO::FETCH_ASSOC);
		}
		foreach ($settings as $setting) {
			$setting['key'] = str_replace(array($namespace . '_'), array(), $setting['key']);
			$options[$setting['key']] = $setting['value'];
		}
		return $xpdo->call('modResource', 'filterPathSegment', array(&$xpdo, $segment, $options));
	}

}