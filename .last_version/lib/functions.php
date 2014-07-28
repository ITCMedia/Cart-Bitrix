<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage siteshouse.asz
 * @copyright  2014 Zahalski Andrew
 */

namespace Mlife\Asz;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class Functions {

	//преобразование цен и остатков из системной строки
	public static function getPriceValue($strPrice){
		if(!$strPrice) return array();
		$res = array();
		$tempar = explode('+++',$strPrice);
		foreach($tempar as $val){
			$curAr = explode(':::',$val);
			if(substr($curAr[0],0,3)=="cod"){
				$res['PRICE'][substr($curAr[0],3,10)] = array(
					"VAL" => round($curAr[1],2),
					"CUR" => $curAr[2]
				);
			}elseif(substr($curAr[0],0,3)=="kol"){
				$res['KOL'] = array(
					"KOL" => intval($curAr[1]),
					"ZAK" => intval($curAr[2])
				);
			}
		}
		return $res;
	}
	
	//получение прав на админку магазина для текущей группы пользователя
	public static function GetGroupRightSiteId($code="ADMIN"){
		
		global $USER;
		$arGroups = $USER->GetUserGroupArray();
		
		$res = \Mlife\Asz\OptionsTable::getList(array(
			"select" => array("*"),
			"filter" => array("VALUE"=>$arGroups,"CODE"=>$code),
		));
		$arSites = array();
		while ($arData = $res->Fetch()) {
			$arSites[] = $arData["SITEID"];
		}
		return $arSites;
	}

}