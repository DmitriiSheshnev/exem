<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
$arComponentDescription = array(
	'NAME' => GetMessage('COMPONENT_NAME'),
	'DESCRIPTION' => GetMessage('COMPONENT_DESCRIPTION'),
	'ICON' => '/images/comp_result_new.gif',
	'CACHE_PATH' => 'Y',
	'PATH' => array(
		'ID' => 'TEST',
		'NAME' => GetMessage('PATH_NAME'),	
	),
	'COMPLEX' => 'N' 
);
?>
