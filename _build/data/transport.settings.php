<?php

$settings = array();

$tmp = array(

	'content_grid_fields' => array(
		'value' => 'id,uri,template,content_type,resource',
		'xtype' => 'textfield',
		'area' => 'staticcontent_temp',
	),


	//временные

	'assets_path' => array(
		'value' => '{base_path}staticcontent/assets/components/staticcontent/',
		'xtype' => 'textfield',
		'area' => 'staticcontent_temp',
	),
	'assets_url' => array(
		'value' => '/staticcontent/assets/components/staticcontent/',
		'xtype' => 'textfield',
		'area' => 'staticcontent_temp',
	),
	'core_path' => array(
		'value' => '{base_path}staticcontent/core/components/staticcontent/',
		'xtype' => 'textfield',
		'area' => 'staticcontent_temp',
	),

	//временные
	
/*
	'some_setting' => array(
		'xtype' => 'combo-boolean',
		'value' => true,
		'area' => 'staticcontent_main',
	),
	*/
);

foreach ($tmp as $k => $v) {
	/* @var modSystemSetting $setting */
	$setting = $modx->newObject('modSystemSetting');
	$setting->fromArray(array_merge(
		array(
			'key' => 'staticcontent_' . $k,
			'namespace' => PKG_NAME_LOWER,
		), $v
	), '', true, true);

	$settings[] = $setting;
}

unset($tmp);
return $settings;
