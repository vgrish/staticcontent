<?php

if ($object->xpdo) {
	/** @var modX $modx */
	$modx =& $object->xpdo;

	switch ($options[xPDOTransport::PACKAGE_ACTION]) {
		case xPDOTransport::ACTION_UPGRADE:
		case xPDOTransport::ACTION_INSTALL:
			$modelPath = $modx->getOption('staticcontent_core_path', null, $modx->getOption('core_path') . 'components/staticcontent/') . 'model/';
			$modx->addPackage('staticcontent', $modelPath);

			$manager = $modx->getManager();
			$objects = array(
				'scContent',
			);
			foreach ($objects as $tmp) {
				$manager->createObjectContainer($tmp);
			}
			break;

		case xPDOTransport::ACTION_UNINSTALL:
			break;
	}
}
return true;
