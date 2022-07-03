<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}


if (!CModule::IncludeModule('iblock')) {
	return;
}

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock = [];
$rsIBlock = CIBlock::GetList(
	['sort' => 'asc'],
	['TYPE' => $arCurrentValues['IBLOCK_TYPE'], 'ACTIVE' => 'Y']
);
while ($arr = $rsIBlock->Fetch()) {
	$arIBlock[$arr['ID']] = '[' . $arr['ID'] . '] ' . $arr['NAME'];
}

$arComponentParameters = [
	'PARAMETERS' => [
		'SEF_MODE' => [],
		'AJAX_MODE' => [],
		'CACHE_TIME' => 3600,
		'URL_TEMPLATES' => [
			'PARENT' => 'BASE',
			'NAME' => GetMessage('T_URL_TEMPLATES'),
			'TYPE' => 'STRING',
			'MULTIPLE' => 'Y',
			'COLS' => '60',
		],
		'IBLOCK_TYPE' => [
			'PARENT' => 'BASE',
			'NAME' => GetMessage('BN_P_IBLOCK_TYPE'),
			'TYPE' => 'LIST',
			'VALUES' => $arIBlockType,
			'REFRESH' => 'Y',
		],
		'IBLOCK_ID' => [
			'PARENT' => 'BASE',
			'NAME' => GetMessage('BN_P_IBLOCK'),
			'TYPE' => 'LIST',
			'VALUES' => $arIBlock,
			'REFRESH' => 'Y',
			'ADDITIONAL_VALUES' => 'Y',
		]
	]
];
