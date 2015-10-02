<?php
/*
 *
 * /nsis/{name:[^ ]+}
 *
 */

if ($object->xpdo) {
	/** @var modX $modx */
	$modx =& $object->xpdo;
	switch ($options[xPDOTransport::PACKAGE_ACTION]) {
		case xPDOTransport::ACTION_INSTALL:
		case xPDOTransport::ACTION_UPGRADE:
			$modelPath = $modx->getOption('virtualpage_core_path', null, $modx->getOption('core_path') . 'components/virtualpage/') . 'model/';
			$modx->addPackage('virtualpage', $modelPath);
			$lang = $modx->getOption('manager_language') == 'en' ? 1 : 0;
			// Plugin Events
			$events = array(
				'1' => array(
					'name' => 'OnPageNotFound',
				),
				'2' => array(
					'name' => 'OnHandleRequest',
				)
			);
			foreach ($events as $id => $properties) {
				if (!$event = $modx->getObject('vpEvent', array('name' => $properties['name']))) {
					$event = $modx->newObject('vpEvent', array_merge(array(
						//'name' => $properties['name'],
					), $properties));
				}
				$event->set('active', 1);
				$event->save();
			}
			// Handler
			$entry = 0;
			if($template = $modx->getObject('modTemplate', array('templatename' => 'Bootstrap.inner'))) {
				$entry = $template->id;
			}
			$handlers = array(
//				'1' => array(
//					'name' => !$lang ? 'Отмена заявки' : 'Cancelled request',
//					'type' => 3,
//					'entry' => $entry,
//					'cache' => 0,
//					'content' => '[[$psConfirm.request]]',
//					'description' => !$lang ? 'Отмена заявки' : 'Cancelled request',
//				),
//				'2' => array(
//					'name' => !$lang ? 'Отмена операции' : 'Cancelled operation',
//					'type' => 3,
//					'entry' => $entry,
//					'cache' => 0,
//					'content' => '[[$psConfirm.operation]]',
//					'description' => !$lang ? 'Отмена операции' : 'Cancelled operation',
//				),
			);
			foreach ($handlers as $id => $properties) {
				if (!$handler = $modx->getObject('vpHandler', array('name' => $properties['name']))) {
					$handler = $modx->newObject('vpHandler', array_merge(array(
						'cache' => 1,
					), $properties));
				}
				$handler->set('active', 1);
				$handler->save();
			}
			// Routes
			$routes = array(
//				'1' => array(
//					'description' => !$lang ? 'Отмена заявки' : 'Cancelled request',
//					'metod' => 'GET,POST',
//					'route' => '/request/cancel/{id:[0-9]+}/{hash}',
//					'properties' => '{"class":"psRequest","action":"request/cancel"}'
//				),
//				'2' => array(
//					'description' => !$lang ? 'Отмена операции' : 'Cancelled operation',
//					'metod' => 'GET,POST',
//					'route' => '/operation/cancel/{id:[0-9]+}/{hash}',
//					'properties' => '{"class":"psOperation","action":"operation/cancel"}'
//				),
			);
			foreach ($routes as $id => $properties) {
				if (!$route = $modx->getObject('vpRoute', array('route' => $properties['route']))) {
					$route = $modx->newObject('vpRoute', array_merge(array(
						//'name' => $properties['name'],
					), $properties));
					$route->set('active', 1);
					$route->save();
				}
			}
			break;
		case xPDOTransport::ACTION_UNINSTALL:
			break;
	}
}
return true;