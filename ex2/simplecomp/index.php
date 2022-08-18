<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Компонент");
?>
<?$APPLICATION->IncludeComponent(
	"test:simplecomp.exam", 
	".default", 
	array(
		"CACHE_GROUPS" => "N",
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "N",
		"IBLOCK_ID" => "3",
		"LINK_IBLOCK_ID" => "4",
		"LINK_PROPERTY" => "UF_NEWS_LINK",
		"COMPONENT_TEMPLATE" => ".default"
	),
	false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>