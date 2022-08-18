<?
if( !defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true ) die();

use Bitrix\Main\Data\Cache,
	Bitrix\Main\Loader,
	Bitrix\Main\Localization\Loc,
	Bitrix\Iblock\Model\Section,
	Bitrix\Iblock\ElementTable;
Loader::includeModule('iblock');
Loader::includeModule('cache');

class SystemsList extends CBitrixComponent{
	private $bCache;
	private $cacheId;
	private $cachePath;
	
	public function executeComponent(){
		global $APPLICATION;
		$this->arResult["COUNT_ELEMS"] = 0;
		$obCache = new CPHPCache;
		$this->bCache = ($this->arParams["CACHE_TYPE"] == "A" || ($this->arParams["CACHE_TYPE"] == "Y" && intval($this->arParams["CACHE_TIME"]) <= 0));
		if($this->_checkParams()){
			if($this->bCache && !$this->arRequest){
				$this->_setCacheParams();
				if ($obCache->InitCache($this->arParams['CACHE_TIME'], $this->cacheId, $this->cachePath)){
					$arCacheVars = $obCache->GetVars();
					$bVarsFromCache = true;
					$this->arResult = $arCacheVars["arResult"];
				}elseif($obCache->StartDataCache()){
					$this->getResult();
					$obCache->EndDataCache(["arResult" => $this->arResult]);
				}
			}else{
				if(!$this->arResult["ERROR"]) $this->getResult();
			}
		}
		if(!$this->arResult["ERROR"]){
			$this->IncludeComponentTemplate();
		}else{
			ShowError($this->arResult["ERROR"]);
		}
		$APPLICATION->SetTitle(Loc::GetMessage('SET_TITLE', ['#N#' => $this->arResult["COUNT_ELEMS"]]));
	}
	
	/*Формируем нужный массив*/
	private function getResult(){
		/*Получаем классификатор из новостей*/
		$resLinked = ElementTable::getList([
			'order' => ['SORT' => 'ASC'],
			'select' => ['ID', 'NAME', 'ACTIVE_FROM'],
			'filter' => ['IBLOCK_ID' => $this->arParams['LINK_IBLOCK_ID'], 'ACTIVE' => 'Y'],
		]);
		$linkedElems = [];
		while($rl = $resLinked->Fetch())
			$linkedElems[$rl['ID']] = [
				'NAME' => $rl['NAME'],
				'ACTIVE_FROM' => $rl['ACTIVE_FROM'] ? $rl['ACTIVE_FROM']->toString(new \Bitrix\Main\Context\Culture(["FORMAT_DATETIME" => "d.m.Y"])) : '',
			];
		
		if(!$linkedElems)
			return ShowError(Loc::GetMessage('EMPTY_ELEMENTS'));
		
		/*Получаем разделы*/
		$enetySection = Section::compileEntityByIblock($this->arParams['IBLOCK_ID']);
		$resSection = $enetySection::getList([
			"select" => ['ID', 'NAME', $this->arParams['LINK_PROPERTY']],
			"filter" => [$this->arParams['LINK_PROPERTY'] => array_keys($linkedElems), 'ACTIVE' => 'Y']
		]);		
		$arSections = [];
		while($rs = $resSection->Fetch())
			$arSections[$rs['ID']] = $rs;
		if(!$arSections)
			return ShowError(Loc::GetMessage('EMPTY_SECTIONS'));
		
		/*Получаем элементы разделов и их свойства*/
		$resCatalogElements = ElementTable::getList([
			'order' => ['SORT' => 'ASC'],
			'select' => ['ID', 'NAME', 'IBLOCK_SECTION_ID'],
			'filter' => ['IBLOCK_ID' => $this->arParams['IBLOCK_ID'], 'IBLOCK_SECTION_ID' => array_keys($arSections), 'ACTIVE' => 'Y'],
		]);
		$arCatalogElements = [];
		$arCatalogElementsKeys = [];
		while($ce = $resCatalogElements->Fetch()){
			$this->arResult["COUNT_ELEMS"]++;
			$arCatalogElements[$ce['IBLOCK_SECTION_ID']][$ce['ID']] = $ce;
			$arCatalogElementsKeys[$ce['ID']] = '';
		}
		if(arCatalogElementsKeys)
			CIBlockElement::GetPropertyValuesArray($arCatalogElementsKeys, $this->arParams['IBLOCK_ID'], ['ACTIVE' => 'Y'], ['CODE' => ['MATERIAL', 'ARTNUMBER', 'PRICE']]);
		
		/*Формируем итоговый массив*/
		$this->arResult['ITEMS'] = $linkedElems;
		foreach($arSections as $kSection => &$vSection){
			if($arCatalogElements[$kSection]){
				foreach($arCatalogElements[$kSection] as $kElem => &$vElem){
					if($arCatalogElementsKeys[$kElem])
						foreach($arCatalogElementsKeys[$kElem] as $val){
							if($val['VALUE'])
								$vElem['PROPS'][] = $val['VALUE'];
						}
				}
				$vSection['ELEMENTS'] = $arCatalogElements[$kSection];
			}
			foreach($vSection['UF_NEWS_LINK'] as $k=>$v){
				$this->arResult['ITEMS'][$v]['SECTIONS_NAMES'][] = $vSection['NAME'];
				$this->arResult['ITEMS'][$v]['SECTIONS'][] = $vSection;
			}
		}
	}
	
	/*Проверяем параметры инфоблока*/
	private function _checkParams(){
		if(!$this->arParams['IBLOCK_ID'])
			$this->arResult['ERROR'] = Loc::GetMessage('EMPTY_IBLOCK');
		elseif(!$this->arParams['LINK_IBLOCK_ID'])
			$this->arResult['ERROR'] = Loc::GetMessage('EMPTY_LINK_IBLOCK_ID');
		elseif(!$this->arParams['LINK_PROPERTY'])
			$this->arResult['ERROR'] = Loc::GetMessage('EMPTY_LINK_PROPERTY');		
		if($this->arResult['ERROR']) return false;
		return true;
	}
	
	/*Настройки кеширования*/
	private function _setCacheParams(){
		$arCacheParams = [];
		foreach($this->arParams as $key => $value )
			if( substr($key, 0, 1) != "~" )
				$arCacheParams[$key] = $value;
		if($this->arParams["CACHE_GROUPS"] == "Y")
			$arCacheParams["USER_GROUPS"] = $GLOBALS["USER"]->GetGroups();
		$this->cacheId = SITE_ID."|".$this->__name."|".md5(serialize($arCacheParams));
		if(($tzOffset = CTimeZone::GetOffset()) <> 0)
			$this->cacheId .= "|".$tzOffset;
		$this->cachePath = '/' . SITE_ID . $this->GetRelativePath();
	}

}
?>