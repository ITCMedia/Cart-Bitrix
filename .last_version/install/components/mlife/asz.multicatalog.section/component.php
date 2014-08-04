<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use Mlife\Asz as ASZ;
global $DB;
/** @global CUser $USER */
global $USER;
/** @global CMain $APPLICATION */
global $APPLICATION;
/** @global CCacheManager $CACHE_MANAGER */
global $CACHE_MANAGER;

CPageOption::SetOptionString("main", "nav_page_in_session", "N");

if(!CModule::IncludeModule("mlife.asz")) return;

$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);
$arParams["SECTION_ID"] = intval($arParams["SECTION_ID"]);
$arParams["SECTION_CODE"] = trim($arParams["SECTION_CODE"]);

//параметры постраничной навигации
$arParams["DISPLAY_TOP_PAGER"] = $arParams["DISPLAY_TOP_PAGER"]=="Y";
$arParams["DISPLAY_BOTTOM_PAGER"] = $arParams["DISPLAY_BOTTOM_PAGER"]!="N";
$arParams["PAGER_TITLE"] = trim($arParams["PAGER_TITLE"]);
$arParams["PAGER_SHOW_ALWAYS"] = $arParams["PAGER_SHOW_ALWAYS"]!="N";
$arParams["PAGER_TEMPLATE"] = trim($arParams["PAGER_TEMPLATE"]);
$arParams["PAGER_DESC_NUMBERING"] = $arParams["PAGER_DESC_NUMBERING"]=="Y";
$arParams["PAGER_DESC_NUMBERING_CACHE_TIME"] = intval($arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]);
$arParams["PAGER_SHOW_ALL"] = $arParams["PAGER_SHOW_ALL"]!=="N";

$arNavParams = array(
	"nPageSize" => $arParams["PAGE_ELEMENT_COUNT"],
	"bDescPageNumbering" => $arParams["PAGER_DESC_NUMBERING"],
	'iNumPage' => is_set($_GET['PAGEN_1']) ? $_GET['PAGEN_1'] : 1,
	"bShowAll" => $arParams["PAGER_SHOW_ALL"],
);
$arNavigation = CDBResult::GetNavParams($arNavParams);
if($arNavigation["PAGEN"]==0 && $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]>0)
	$arParams["CACHE_TIME"] = $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"];

//разрешенные свойства
if(!is_array($arParams["PROPERTY_CODE"]))
	$arParams["PROPERTY_CODE"] = array();
foreach($arParams["PROPERTY_CODE"] as $k=>$v)
	if($v==="")
		unset($arParams["PROPERTY_CODE"][$k]);

global $arFilterMain;
$filterVar = $arFilterMain;
//print_r($filterVar);

$arParams['CACHE_GROUPS'] = trim($arParams['CACHE_GROUPS']);
if ('N' != $arParams['CACHE_GROUPS'])
	$arParams['CACHE_GROUPS'] = 'Y';

$arParams["CACHE_FILTER"]=$arParams["CACHE_FILTER"]=="Y";
if(!$arParams["CACHE_FILTER"] && count($filterVar)>0)
	$arParams["CACHE_TIME"] = 0;

if(!$arParams["ELEMENT_SORT_FIELD"] || $arParams["ELEMENT_SORT_FIELD"]=="sort") $arParams["ELEMENT_SORT_FIELD"] = "NAME";
if(!$arParams["ELEMENT_SORT_ORDER"]) $arParams["ELEMENT_SORT_ORDER"] = "ASC";

if($this->StartResultCache(false, array($arNavigation, $filterVar, $limit, ($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups())))){
	
	//TODO тегированный кеш, не факт, что работает корректно
	
	if(!CModule::IncludeModule("iblock"))
	{
		$this->AbortResultCache();
		ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
		return;
	}
	if(!CModule::IncludeModule('mlife.asz')) {
		$this->AbortResultCache();
		return;
	}
		
		$main_query = new \Bitrix\Main\Entity\Query(ASZ\ElementTable::getEntity());
		
		//получаем параметры инфоблока
		$res = CIBlock::GetByID($arParams["IBLOCK_ID"]);
		$arResult["IBLOCK"] = $res->GetNext();
		//$arResult["IBLOCK"]["VERSION"] - версия инфоблока
		
		//получаем свойства инфоблока
		$arProperties = array();
		$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("IBLOCK_ID"=>$arParams["IBLOCK_ID"]));
		while ($prop_fields = $properties->GetNext())
		{
			$arProperties[$prop_fields["ID"]] = $prop_fields;
		}
		
		//необходимые поля
		$arSelect = array("ID","IBLOCK_ID","CODE","NAME","ACTIVE","PREVIEW_TEXT","IBLOCK_SECTION_ID","DETAIL_PICTURE","PRICE");
		
		//получаем id свойств для выборки из настроек компонента
		$arSelectPropId = array();
	
		if(is_array($arParams["PROPERTY_CODE"]) && count($arParams["PROPERTY_CODE"])>0){
			foreach($arProperties as $prop) {
				if(in_array($prop["CODE"],$arParams["PROPERTY_CODE"])){
					$arSelectPropId[] = $prop["ID"];
				}
			}
		}
		//print_r($arSelectPropId);
		//фильтрация
		$arFilter = array(
			"IBLOCK_ID"=>$arParams["IBLOCK_ID"],
			"ACTIVE"=>"Y",
			);
		if($arParams["SECTION_ID"]>0) {
			$arFilter["IBLOCK_SECTION_ID"][] = $arParams["SECTION_ID"];
		}
		elseif(strlen($arParams["SECTION_CODE"])>0) {
			$db_list = CIBlockSection::GetList(array(), array("IBLOCK_ID"=>$arParams["IBLOCK_ID"],"CODE"=>$arParams["SECTION_CODE"]), false, array("ID"));
			if($ar_result = $db_list->GetNext())
			{
			$arParams["SECTION_ID"] = $ar_result["ID"];
			$arFilter["IBLOCK_SECTION_ID"][] = $arParams["SECTION_ID"];
			}
		}
		
		//получаем список вложенных разделов
		if($arParams["SECTION_ID"]>0) {
			$filterSection = array("IBLOCK_ID"=>$arParams["IBLOCK_ID"]);
			$filterSection["SECTION_ID"]=$arParams["SECTION_ID"];

			$db_list = CIBlockSection::GetList(array(), $filterSection, false, array("ID"));
			while($ar_result = $db_list->GetNext())
			{
				$arFilter["IBLOCK_SECTION_ID"][] = $ar_result["ID"];
			}
		}
		
		$main_query->setFilter($arFilter); //addFilter
		
		//устанавливаем айдишник и тип инфоблока
		ASZ\PropertyValuesTable::$iblockNameIb = $arParams["IBLOCK_ID"];
		
		if($arResult["IBLOCK"]["VERSION"]==2){ //инфоблоки 2.0
			ASZ\PropertyValuesTable::$iblockTyperIb = true;
		}elseif($arResult["IBLOCK"]["VERSION"]==1){ //инфоблоки 1.0
			ASZ\PropertyValuesTable::$iblockTyperIb = false;
		}
		
		$arProps = array();
		$arPropsKey = array();
		
		//echo'<pre>';print_r($arProperties);echo'</pre>';
		$dist = false;
		
		$runtime = array();
		
		if(is_array($filterVar)){
			$runtimeRegistered_s = false;
			$runtimeRegistered_m = false;
			
			$arFilterLogicOr = array();
			
			foreach($filterVar as $prop=>$val){
				if($arResult["IBLOCK"]["VERSION"]==2){
					
					$prop_id = preg_replace("/(.*?)([0-9]{1,7})(.*?)/","$2",$prop);
						
					$newProp = 'PROP.'.$prop;
					$newProp = str_replace("PROP.>=",">=PROP.",$newProp);
					$newProp = str_replace("PROP.<=","<=PROP.",$newProp);
					//$main_query->addFilter(array($newProp => $val));
						
					if($arProperties[$prop_id]["MULTIPLE"]=="N"){
						$newProp = str_replace("PROP.","PROPERTY.",$newProp);
						$arFilter = $main_query->getFilter();
						if($val){
							$arFilter[$newProp] = $val;
							$main_query->setFilter($arFilter);
						}
					}elseif($arProperties[$prop_id]["MULTIPLE"]=="Y"){
						
						if(!$runtimeRegistered_m){
							\Mlife\Asz\PropmultipleTable::$iblockNameIb = $arParams["IBLOCK_ID"];
							$runtimeTmp = array(
								'data_type' => '\Mlife\Asz\PropmultipleTable',
								'reference'=> array('=this.ID' => 'ref.IBLOCK_ELEMENT_ID'),
							);
							$runtime["PROPM"] = $runtimeTmp;
							
							$main_query->registerRuntimeField("PROPM",$runtime["PROPM"]);
							
							$dist = true;
							$runtimeRegistered_m = true;
						}
						$newProp = str_replace("PROP","PROPM",$newProp);
						$arFilter = $main_query->getFilter();
						if($val){
							if(strpos($newProp,"<=")!==false){
								$arFilterLogicOr[$prop_id]["LOGIC"] = "AND";
								$arFilterLogicOr[$prop_id][] = array("<=PROPM_".$prop_id.".VALUE_NUM"=>$val);
							}elseif(strpos($newProp,">=")!==false){
								$arFilterLogicOr[$prop_id]["LOGIC"] = "AND";
								$arFilterLogicOr[$prop_id][] = array(">=PROPM_".$prop_id.".VALUE_NUM"=>$val);
							}else{
								if($arProperties[$prop_id]["PROPERTY_TYPE"]=="N"){
									$arFilterLogicOr[$prop_id] = array("PROPM_".$prop_id.".VALUE_NUM"=>$val);
								}else{
									$arFilterLogicOr[$prop_id] = array("PROPM_".$prop_id.".VALUE_ENUM"=>$val);
								}
							}
							//echo'<pre>';print_r($arFilter);echo'</pre>';echo'<br/>';
							$main_query->setFilter($arFilter);
						}
					}
					
				}elseif($arResult["IBLOCK"]["VERSION"]==1){
					
					$prop_id = preg_replace("/(.*?)([0-9]{1,7})(.*?)/","$2",$prop);
						
					$newProp = 'PROP.'.$prop;
					$newProp = str_replace("PROP.>=",">=PROP.",$newProp);
					$newProp = str_replace("PROP.<=","<=PROP.",$newProp);
					
					if(!$runtimeRegistered_s){
						\Mlife\Asz\PropertyValuesTable::$iblockTyperIb = false;
						$runtimeTmp = array(
							'data_type' => '\Mlife\Asz\PropertyValuesTable',
							'reference'=> array('=this.ID' => 'ref.IBLOCK_ELEMENT_ID'),
						);
						$runtime["PROPM"] = $runtimeTmp;
						
						$main_query->registerRuntimeField("PROPM",$runtime["PROPM"]);
						
						$runtimeRegistered_s = true;
						
					}
					$newProp = str_replace("PROP","PROPM",$newProp);
					$arFilter = $main_query->getFilter();
					if($val){
						if(strpos($newProp,"<=")!==false){
							$arFilterLogicOr[$prop_id]["LOGIC"] = "AND";
							$arFilterLogicOr[$prop_id][] = array("<=PROPM_".$prop_id.".VALUE_NUM"=>$val);
						}elseif(strpos($newProp,">=")!==false){
							$arFilterLogicOr[$prop_id]["LOGIC"] = "AND";
							$arFilterLogicOr[$prop_id][] = array(">=PROPM_".$prop_id.".VALUE_NUM"=>$val);
						}else{
							if($arProperties[$prop_id]["PROPERTY_TYPE"]=="N"){
								$arFilterLogicOr[$prop_id] = array("PROPM_".$prop_id.".VALUE_NUM"=>$val);
							}else{
								$arFilterLogicOr[$prop_id] = array("PROPM_".$prop_id.".VALUE_ENUM"=>$val);
							}
						}
						//echo'<pre>';print_r($arFilter);echo'</pre>';echo'<br/>';
						$main_query->setFilter($arFilter);
					}
					
				}
			}
			
			if(!empty($arFilterLogicOr)) {
				$arFilter = $main_query->getFilter();
				foreach($arFilterLogicOr as $key=>$val){
					$arPropFilter[] = $val;
					$arPropFilter[] = array("PROPM_".$key.".IBLOCK_PROPERTY_ID"=>$key);
					if($arResult["IBLOCK"]["VERSION"]==1){
						$runtimeTmp = array(
								'data_type' => '\Mlife\Asz\PropertyValuesTable',
								'reference'=> array('=this.ID' => 'ref.IBLOCK_ELEMENT_ID'),
							);
					}else{
						$runtimeTmp = array(
								'data_type' => '\Mlife\Asz\PropmultipleTable',
								'reference'=> array('=this.ID' => 'ref.IBLOCK_ELEMENT_ID'),
							);
					}
					$runtime["PROPM_".$key] = $runtimeTmp;
					
					$main_query->registerRuntimeField("PROPM_".$key,$runtime["PROPM_".$key]);
					
				}
				
				$arFilter[] = $arPropFilter;
				//echo'<pre>';print_r($arFilter);echo'</pre>';
				$main_query->setFilter($arFilter);
			}
			
		}
		
		//добавляем сортировку
		/*
		$orderPrice = true; //пока не выносим в параметры компонента
		if($orderPrice){
			$order = array("PRICE.PRICEVAL" => "ASC");
			$main_query->setOrder($order);
		}else{
			$order = array("NAME" => "DESC");
			$main_query->setOrder($order);
		}*/
		$order = array($arParams["ELEMENT_SORT_FIELD"] => $arParams["ELEMENT_SORT_ORDER"]);
		$main_query->setOrder($order);
		
		//добавляем лимиты для постранички
		if (isset($arNavParams['nPageTop']))
		{
			$main_query->setLimit($arNavParams['nPageTop']);
		}
		else
		{
			$main_query->setLimit($arNavParams['nPageSize']);
			$offset = ($arNavParams['iNumPage']-1) * $arNavParams['nPageSize'];
			$main_query->setOffset($offset);
		}
		
		$filtr = $main_query->getFilter();
		$filtr[] = array("PRICE.PRICEID" => $arParams["PRICE"]);
		$main_query->setFilter($filtr);
		
		$global_query = array(
			'filter' => $main_query->getFilter(),
			'limit' => $main_query->getLimit(),
			'offset' => $main_query->getOffset(),
			'order' => $main_query->getOrder(),
			'runtime' => $runtime,
		);
		
		//print_r($main_query->getQuery());
		
		//print_r($global_query);
		
		$main_query->setSelect(array('UNIQUE_ID'));
		$main_query->setOrder($global_query['order']);
		$main_query->registerRuntimeField("UNIQUE_ID", array('data_type' => 'integer', 'expression' => array('DISTINCT(%s)', 'ID')));
		
		//собираем элементы
		$result = $main_query->exec();
		
		$arResult["ITEM_IDS"] = array();
		
		while($ar = $result->fetch()){
		//print_r($ar);
			$arResult["ITEM_IDS"][] = $ar["UNIQUE_ID"];
		}
		//print_r($arResult["ITEM_IDS"]);
		
		$main_query = new \Bitrix\Main\Entity\Query(ASZ\ElementTable::getEntity());
		
		$main_query = new \Bitrix\Main\Entity\Query(ASZ\ElementTable::getEntity());
		$main_query->setSelect($arSelect);
		$main_query->setFilter(array("ID"=>$arResult["ITEM_IDS"],"IBLOCK_ID"=>$arParams["IBLOCK_ID"], "PRICE.PRICEID" => $arParams["PRICE"]));
		$main_query->setOrder($global_query['order']);
		//$main_query->disableDataDoubling();

		$result = $main_query->exec();
		$result = new CIBlockResult($result);
		$result->SetUrlTemplates($arParams["DETAIL_URL"]);
		//$result->SetSectionContext();
		
		$arResult["ITEMS"] = array();
		$countProp = count($arSelectPropId);
		
		while($obItem = $result->GetNextElement())
		{
			$arItem = $obItem->GetFields();
			$arItem["PROP"] = $obItem->GetProperties();
			/*if($countProp>0){
				$db_props = CIBlockElement::GetProperty($arParams["IBLOCK_ID"], $arItem["ID"], array(), Array("ID"=>$arSelectPropId));
				while($ar_props = $db_props->Fetch()){
					$arItem["PROP"][$ar_props["ID"]] = $ar_props;
				}
			}*/
			
			$arButtons = CIBlock::GetPanelButtons(
				$arItem["IBLOCK_ID"],
				$arItem["ID"],
				0,
				array("SECTION_BUTTONS"=>false, "SESSID"=>false)
			);
			$arItem["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
			$arItem["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];
			
			$arResult["ITEMS"][] = $arItem;
			//echo'<pre>';print_r($arItem);echo'</pre>';
			
		}
		
		//echo'<pre style="font-size:12px;">';print_r($main_query->getQuery());echo'</pre>';
		
		//подсчет для постранички
		$main_query = new \Bitrix\Main\Entity\Query(ASZ\ElementTable::getEntity());
		//$main_query->setSelect(array('CNT' => array('expression' => array('COUNT(DISTINCT %s)','ID'), 'data_type'=>'integer')));
		$main_query->registerRuntimeField("CNT",array('expression' => array('COUNT(DISTINCT %s)','ID'), 'data_type'=>'integer'));
		$main_query->setSelect(array("CNT"));
		$main_query->setFilter($global_query['filter']);
		if(count($global_query['runtime'])>0){
			foreach($global_query['runtime'] as $key=>$val){
				$main_query->registerRuntimeField($key,$val);
			}
		}
		$result_chain = $main_query->setLimit(null)->setOffset(null)->exec()->fetch();
		$result_chain = $result_chain["CNT"];
		
		//постраничная
		$result->NavStart($global_query['limit']);
		$result->NavRecordCount = $result_chain;
		$result->NavPageSize = $arNavParams['nPageSize'];
		$result->bShowAll = $arNavParams['bShowAll'];
		$result->NavPageCount = ceil($result->NavRecordCount/$result->NavPageSize);
		$result->NavPageNomer = $arNavParams['iNumPage'];
		
		$arResult["NAV_STRING"] = $result->GetPageNavStringEx($navComponentObject, $arParams["PAGER_TITLE"], $arParams["PAGER_TEMPLATE"], $arNavParams['bShowAll']);
		$arResult["NAV_CACHED_DATA"] = $navComponentObject->GetTemplateCachedData();
		$arResult["NAV_RESULT"] = $result;
		$arResult["NAV_PARAMS"] = $result->GetNavParams();
		$arResult["NAV_NUM"] = $result->NavNum;
		
		//мета теги для категории
		$res = CIBlockSection::GetList(
			$arOrder = Array("SORT"=>"DESC") ,
			$arFilter = array('IBLOCK_ID'=>$arParams["IBLOCK_ID"],'CODE'=>$arParams["SECTION_CODE"]),
			false,
			array("NAME","ID","SECTION_PAGE_URL")
		);
		
		while($obElement = $res->GetNextElement(false,false))
		{
			$arItem = $obElement->GetFields();
			
			$ipropValues = new \Bitrix\Iblock\InheritedProperty\SectionValues($arParams["IBLOCK_ID"], $arItem["ID"]);
			$arResult["IPROPERTY_VALUES"] = $ipropValues->getValues();
			
			$arResult["CHAIN"]["NAME"] = $arItem["NAME"];
			$arResult["CHAIN"]["URL"] = $arItem['SECTION_PAGE_URL'];
		}
		
		if(count($arResult["ITEM_IDS"])>0){

			//получаем типы цен для групп текущего пользователя
			$arGroups = $USER->GetUserGroupArray();
			
			if(is_array($arGroups)){
				$priceTip = \Mlife\Asz\CurencyFunc::getPriceForGroup($arGroups,SITE_ID);
			}else{
				$priceTip = \Mlife\Asz\CurencyFunc::getPriceForGroup();
			}
			//типы цен из настроек компонента
			if(is_array($arParams["PRICE"])){
				$newArPrice = array();
				foreach($priceTip as $key=>$p_id){
					if(in_array($p_id,$arParams["PRICE"])) $newArPrice[] = $p_id;
				}
				$priceTip = $newArPrice;
			}
			
			//получаем цены
			$arResult["PRICE"] = \Mlife\Asz\CurencyFunc::getPriceBase($priceTip,$arResult["ITEM_IDS"],SITE_ID);
			
		}
		
		//echo'<pre>';print_r($arResult);echo'</pre>';
		
		$this->SetResultCacheKeys(array(
		"NAV_CACHED_DATA",
		"CHAIN",
		"IPROPERTY_VALUES",
		"ITEM_IDS"
		));
		
	$this->IncludeComponentTemplate();

}
//print_r($arResult["IPROPERTY_VALUES"]);
if(isset($arResult['IPROPERTY_VALUES']["SECTION_META_TITLE"]) && $arParams["SET_TITLE"]=="Y") {
	//$APPLICATION->SetTitle($arResult['IPROPERTY_VALUES']["SECTION_META_TITLE"]);
	$APPLICATION->SetPageProperty("title", $arResult['IPROPERTY_VALUES']["SECTION_META_TITLE"]);
	$APPLICATION->SetPageProperty("keywords", $arResult['IPROPERTY_VALUES']["SECTION_META_KEYWORDS"]);
	$APPLICATION->SetPageProperty("description", $arResult['IPROPERTY_VALUES']["SECTION_META_DESCRIPTION"]);
}

if(isset($arResult["CHAIN"]["NAME"]) && $arParams["ADD_SECTIONS_CHAIN"]=="Y"){
	//$APPLICATION->AddChainItem($arResult["CHAIN"]["NAME"], $arResult["CHAIN"]["URL"]);
}
?>