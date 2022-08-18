<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$arComponentParameters = array(
	'GROUPS' => array(
		'IBLOCK_PARAMS' => array(
			'SORT' => 110,
			'NAME' => Loc::GetMessage('IBLOCK_PARAMS'),
		),
		'CACHE_SETTINGS' => array(
			'SORT' => 130,
			'NAME' => Loc::GetMessage('CACHE_SETTINGS'),
		)
	),
	'PARAMETERS' => array(
		'IBLOCK_ID' => array(
			"PARENT" => "IBLOCK_PARAMS",
			"NAME" => Loc::GetMessage("T_IBLOCK_ID"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25
		),
		'LINK_IBLOCK_ID' => array(
			"PARENT" => "IBLOCK_PARAMS",
			"NAME" => Loc::GetMessage("T_LINK_IBLOCK_ID"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25
		),
		'LINK_PROPERTY' => array(
			"PARENT" => "IBLOCK_PARAMS",
			"NAME" => Loc::GetMessage("T_LINK_PROPERTY"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => "",
			"COLS" => 25
		),
		'CACHE_TIME' => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => Loc::GetMessage("CP_BNL_CACHE_TIME"),
			'DEFAULT' => '3600'
		),
		"CACHE_GROUPS" => array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("CP_BNL_CACHE_GROUPS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
	)
);


$templates = array('-' => GetMessage("ADV_NOT_SELECTED"));
$arTemplates = CComponentUtil::GetTemplatesList('test:simplecomp.exam');
if (is_array($arTemplates) && !empty($arTemplates)){
	foreach ($arTemplates as $template)
		$templates[$template['NAME']] = $template['NAME'];
}

?>