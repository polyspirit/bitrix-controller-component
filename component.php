<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

if (!is_array($arParams['~URL_TEMPLATES'])) {
	$arParams['~URL_TEMPLATES'] = [];
}

$arUrlTemplates = [];
foreach ($arParams['~URL_TEMPLATES'] as $template) {
	if (preg_match('#([a-z._-]+)=(.+)$#i', $template, $match)) {
		$arUrlTemplates[$match[1]] = $match[2];
	}
}

$componentPage = preg_replace(
	'#-[0-9]+$#',
	'',
	CComponentEngine::ParseComponentPath(
		$arParams['SEF_FOLDER'],
		$arUrlTemplates,
		$arVariables
	)
);

$arDefaultVariableAliases404 = [];
$arVariableAliases = CComponentEngine::MakeComponentVariableAliases(
	$arDefaultVariableAliases404,
	$arParams['VARIABLE_ALIASES']
);

$arResult = array(
	'FOLDER' => $arParams['SEF_FOLDER'],
	'URL_TEMPLATES' => $arUrlTemplates,
	'VARIABLES' => $arVariables,
	'ALIASES' => $arVariableAliases,
	'PAGE' => $componentPage,
);

$this->IncludeComponentTemplate($componentPage);
