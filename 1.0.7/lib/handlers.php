<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage mlife.asz
 * @copyright  2014 Zahalski Andrew
 */

namespace Mlife\Asz;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class Handlers {
	
	public static $oldStatus = false;
	public static $siteId = false;
	public static $arFinBasket = array();
	public static $arFinBasketRefresh = true;
	public static $deleteOrder = false;
	public static $UID = false;
	public static $deleteRow = false;
	
	//тут будет корзина, перед OrderOnAfterAdd
	public static $basketItemsArray = false;
	
	//событие после добавления заказа
	public static function OrderOnAfterAdd($orderId){
		
		//file_put_contents("/var/www/artlux/data/www/demo3.kalinkovichi.ru/bitrix/modules/mlife.asz/test.txt",print_r(array($orderId),true));
		$arMacros = array();
		
		$fields = array();
		
		$res = \Mlife\Asz\OrderTable::getList(array(
			"select"=>array("*","ADDPAY"=>"ADDPAY.NAME","ADDSTAT"=>"ADDSTAT.NAME","ADDDELIVERY"=>"ADDDELIVERY.NAME"),
			"filter"=>array("ID"=>$orderId)));
			$fields = $res->Fetch();
		
		self::$siteId = $fields["SITEID"];
		//file_put_contents("/var/www/artlux/data/www/demo3.kalinkovichi.ru/bitrix/modules/mlife.asz/test.txt",print_r(array($fields,$orderId),true));
		//добавлен новый заказ
		$arMacros["ORDER_ID"] = $fields["ID"];
		
		$arMacros["ORDER_STATUS_NAME"] = $fields["ADDSTAT"];
		$arMacros["ORDER_PAY_NAME"] = $fields["ADDPAY"];
		$arMacros["ORDER_DELIVERY_NAME"] = $fields["ADDDELIVERY"];
		$arMacros["ORDER_PASSW"] = $fields["PASSW"];
		$arMacros["ORDER_DELIVERY_PRICE"] = \Mlife\Asz\CurencyFunc::priceFormat($fields["DELIVERY_PRICE"],false,self::$siteId);
		$arMacros["ORDER_PRICE"] = \Mlife\Asz\CurencyFunc::priceFormat($fields["PRICE"],false,self::$siteId);
		$arMacros["ORDER_PAYMENT_PRICE"] = \Mlife\Asz\CurencyFunc::priceFormat($fields["PAYMENT_PRICE"],false,self::$siteId);
		$arMacros["ORDER_DISCOUNT"] = \Mlife\Asz\CurencyFunc::priceFormat($fields["DISCOUNT"],false,self::$siteId);
		$arMacros["ORDER_TAX"] = \Mlife\Asz\CurencyFunc::priceFormat($fields["TAX"],false,self::$siteId);
		$arMacros["ORDER_ID"] = $fields["ID"];
		
		$res = \Mlife\Asz\OrderpropsTable::getList(
			array(
				'select' => array("CODE","VALUE"=>"VAL.VALUE"),
				'filter' => array("SITEID"=>self::$siteId,"ACTIVE"=>"Y","VAL.UID"=>$fields["USERID"]),
			)
		);
		while($arData = $res->Fetch()){
			$arMacros["USERPROP_".$arData["CODE"]] = $arData["VALUE"];
		}
		
		$basketItemsArrayCache = self::$basketItemsArray;
		
		//макрос товаров в заказе
		$arMacros["PRODUCTS"] = "";
		if(is_array($basketItemsArrayCache)){
			$event = new \Bitrix\Main\Event("mlife.asz", "OnMacrosProductCreate",array($basketItemsArrayCache));
			$event->send();
			if ($event->getResults()){
				foreach($event->getResults() as $evenResult){
					if($evenResult->getResultType() == \Bitrix\Main\EventResult::SUCCESS){
						$arMacros["PRODUCTS"] = $evenResult->getParameters();
					}
				}
			}
		}
		
		$postid = "MLIFE_ASZ_ORDER";
		$rsET = \CEventType::GetByID($postid,"ru");
		if($rsET->Fetch()){
			\CEvent::Send("MLIFE_ASZ_ORDER", self::$siteId, $arMacros);
		}
		
		//отправка смс
		if(\Bitrix\Main\Loader::IncludeModule("mlife.smsservices") || \Bitrix\Main\Loader::IncludeModule("asd.smsswitcher")){
			
			$postid = "MLIFE_ASZSMS_ORDER";
			$rsET = \CEventType::GetByID($postid,"ru");
			if($rsET->Fetch()){
				$rsMess = \CEventMessage::GetList($by="site_id", $order="desc", array("SITE_ID"=>self::$siteId,"ACTIVE"=>"Y","TYPE_ID"=>"MLIFE_ASZSMS_ORDER"));
				while($messAr = $rsMess->Fetch()){
					$phone = $messAr["EMAIL_TO"];
					if(strpos($phone,"#")!==false){
						$phone = str_replace("#","",$phone);
						$phone = trim($phone);
						if(isset($arMacros[$phone])){
							$phone = $arMacros[$phone];
						}else{
							$phone = false;
						}
					}
					if($phone){
						$mess = $messAr["MESSAGE"];
						$mess = \Mlife\Asz\Functions::replaceBySms($mess,$arMacros);
						if($mess) {
							//тут отправка смс
							if(\Bitrix\Main\Loader::IncludeModule("mlife.smsservices")) {
								$obSmsServ = new \CMlifeSmsServices();
								$arSend = $obSmsServ->sendSms($phone,$mess,0);
							}
							else {
								\CSMSS::Send($phones, $mess);
							}
						}
					}
				}
			}
			
		}
		
		//file_put_contents("/var/www/artlux/data/www/demo3.kalinkovichi.ru/bitrix/modules/mlife.asz/test.txt",print_r($basketItemsArrayCache,true));
		if(is_array($basketItemsArrayCache)){
		
			//установка остатков и резервов
			$optQuant1 = \Bitrix\Main\Config\Option::get("mlife.asz", "asz_status1", "0", self::$siteId);
			$optQuant2 = \Bitrix\Main\Config\Option::get("mlife.asz", "asz_status2", "0", self::$siteId);
			$optQuant3 = \Bitrix\Main\Config\Option::get("mlife.asz", "asz_status3", "0", self::$siteId);
			
			if($optQuant1==$fields["STATUS"] || $optQuant2==$fields["STATUS"] || $optQuant3==$fields["STATUS"]){
				$arIdsItems = array();
				foreach($basketItemsArrayCache as $row){
					$arIdsItems[] = $row["ID"];
				}
				
				$sostav = \Mlife\Asz\BasketTable::getList(
					array(
						'select' => array("PROD_ID","QUANT"),
						'filter' => array("ID"=>$arIdsItems),
					)
				);
				$arFinBasket = array();
				$arFinBasketIds = array();
				while ($arBasketItem = $sostav->Fetch()){
					$arFinBasket[$arBasketItem["PROD_ID"]] = array(
						"ID" => $arBasketItem["PROD_ID"],
						"QUANT" => $arBasketItem["QUANT"],
					);
					$arFinBasketIds[] = $arBasketItem["PROD_ID"];
				}
				if(!empty($arFinBasketIds)){
					$arIbIds = array();
					$res = \Mlife\Asz\ElementTable::getList(array(
						'select' => array("IBLOCK_ID","ID"),
						'filter' => array("ID"=>$arFinBasketIds)
					));
					while($arData = $res->Fetch()){
						$arIbIds[$arData["IBLOCK_ID"]][] = $arData["ID"];
					}
					
					\CModule::IncludeModule("iblock");
					foreach($arIbIds as $iblock=>$prodId){
						$res = \CIBlockElement::GetList(Array(), array("ID"=>$prodId,"IBLOCK_ID"=>$iblock), false, false, array("PROPERTY_ASZ_SYSTEM","ID"));
						while($arProdGuant = $res->Fetch(false,false)){
							$arFinBasket[$arProdGuant["ID"]]["SYSTEM"] = $arProdGuant["PROPERTY_ASZ_SYSTEM_VALUE"];
							$arFinBasket[$arProdGuant["ID"]]["IBLOCK_ID"] = $iblock;
						}
					}
					
					
					foreach($arFinBasket as $basketItem){
						$arGuant = \Mlife\Asz\Functions::getPriceValue($basketItem["SYSTEM"]);
						
						$update = false;
						if($optQuant1==$fields["STATUS"]){
							$newKol = ($arGuant['KOL']['KOL'] - $basketItem["QUANT"]);
							$newRez = ($arGuant['KOL']['ZAK'] + $basketItem["QUANT"]);
							$update = true;
						}elseif($optQuant2==$fields["STATUS"]){
							$newKol = $arGuant['KOL']['KOL'];
							$newRez = ($arGuant['KOL']['ZAK'] - $basketItem["QUANT"]);
							$update = true;
						}elseif($optQuant3==$fields["STATUS"]){
							$newKol = ($arGuant['KOL']['KOL'] + $basketItem["QUANT"]);
							$newRez = ($arGuant['KOL']['ZAK'] - $basketItem["QUANT"]);
							$update = true;
						}
						if($update){
						$newSystemStr = str_replace('kol:::'.$arGuant['KOL']['KOL'].':::'.$arGuant['KOL']['ZAK'],'kol:::'.$newKol.':::'.$newRez,$basketItem["SYSTEM"]);
						\CIBlockElement::SetPropertyValues($basketItem["ID"],$basketItem["IBLOCK_ID"],$newSystemStr,"ASZ_SYSTEM");
						\Mlife\Asz\QuantTable::update(array("PRODID"=>$basketItem["ID"]),array("KOL"=>$newKol,"ZAK"=>$newRez));
						}
					}
				}
				
			}
		
		}
		$result = new Entity\EventResult();
		return $result;
		
	}
	
	//событие перед обнволением заказа
	public static function OrderOnBeforeUpdate($field){
		
		$fields = $field;
		
		$res = \Mlife\Asz\OrderTable::getList(array(
			"select"=>array("STATUS","SITEID","ID"),
			"filter"=>array("ID"=>$fields["ID"])));
			$fields = $res->Fetch();
		
		self::$oldStatus = $fields["STATUS"];
		self::$siteId = $fields["SITEID"];
		
		//установка текущих остатков у товаров в заказе, для следующиего обработчика после изменения заказа
		$optQuant1 = \Bitrix\Main\Config\Option::get("mlife.asz", "asz_status1", "0", self::$siteId);
		$optQuant2 = \Bitrix\Main\Config\Option::get("mlife.asz", "asz_status2", "0", self::$siteId);
		$optQuant3 = \Bitrix\Main\Config\Option::get("mlife.asz", "asz_status3", "0", self::$siteId);
		if($optQuant1>0 || $optQuant2>0 || $optQuant3>0){
			$sostav = \Mlife\Asz\BasketTable::getList(
					array(
						'select' => array("PROD_ID","QUANT"),
						'filter' => array("ORDER_ID"=>$fields["ID"]),
					)
				);
			$arFinBasket = array();
			while ($arBasketItem = $sostav->Fetch()){
				$arFinBasket[$arBasketItem["PROD_ID"]] = $arBasketItem["QUANT"];
			}
			if(self::$arFinBasketRefresh){
				self::$arFinBasket = $arFinBasket;
			}
			//if(empty(self::$arFinBasket)) self::$arFinBasket = $arFinBasket;
		}
		
		$result = new Entity\EventResult();
		return $result;
		
	}
	
	//событие при обновлении заказа
	public static function OrderOnAfterUpdate($field){
		
		$fields = $field;
		
		$arMacros = array();
		
		$res = \Mlife\Asz\OrderTable::getList(array(
			"select"=>array("*","ADDPAY"=>"ADDPAY.NAME","ADDSTAT"=>"ADDSTAT.NAME","ADDDELIVERY"=>"ADDDELIVERY.NAME"),
			"filter"=>array("ID"=>$fields["ID"])));
			$fields = $res->Fetch();
		
		//Изменение статуса заказа
		if(self::$oldStatus != $fields["STATUS"]) {
			
			$arMacros["ORDER_ID"] = $fields["ID"];
			
			$arMacros["ORDER_STATUS_NAME"] = $fields["ADDSTAT"];
			$arMacros["ORDER_PAY_NAME"] = $fields["ADDPAY"];
			$arMacros["ORDER_DELIVERY_NAME"] = $fields["ADDDELIVERY"];
			$arMacros["ORDER_PASSW"] = $fields["PASSW"];
			$arMacros["ORDER_DELIVERY_PRICE"] = \Mlife\Asz\CurencyFunc::priceFormat($fields["DELIVERY_PRICE"],false,self::$siteId);
			$arMacros["ORDER_PRICE"] = \Mlife\Asz\CurencyFunc::priceFormat($fields["PRICE"],false,self::$siteId);
			$arMacros["ORDER_PAYMENT_PRICE"] = \Mlife\Asz\CurencyFunc::priceFormat($fields["PAYMENT_PRICE"],false,self::$siteId);
			$arMacros["ORDER_DISCOUNT"] = \Mlife\Asz\CurencyFunc::priceFormat($fields["DISCOUNT"],false,self::$siteId);
			$arMacros["ORDER_TAX"] = \Mlife\Asz\CurencyFunc::priceFormat($fields["TAX"],false,self::$siteId);
			$arMacros["ORDER_ID"] = $fields["ID"];
			
			$res = \Mlife\Asz\OrderpropsTable::getList(
				array(
					'select' => array("CODE","VALUE"=>"VAL.VALUE"),
					'filter' => array("SITEID"=>self::$siteId,"ACTIVE"=>"Y","VAL.UID"=>$fields["USERID"]),
				)
			);
			while($arData = $res->Fetch()){
				$arMacros["USERPROP_".$arData["CODE"]] = $arData["VALUE"];
			}
			
			$postid = "MLIFE_ASZ_STATUS_".$fields["STATUS"];
			$rsET = \CEventType::GetByID($postid,"ru");
			if($rsET->Fetch()){
				\CEvent::Send($postid, self::$siteId, $arMacros);
			}
			
			//отправка смс
			if(\Bitrix\Main\Loader::IncludeModule("mlife.smsservices") || \Bitrix\Main\Loader::IncludeModule("asd.smsswitcher")){
				
				$postid = "MLIFE_ASZSMS_STATUS_".$fields["STATUS"];
				$rsET = \CEventType::GetByID($postid,"ru");
				if($rsET->Fetch()){
					$rsMess = \CEventMessage::GetList($by="site_id", $order="desc", array("SITE_ID"=>self::$siteId,"ACTIVE"=>"Y","TYPE_ID"=>$postid));
					while($messAr = $rsMess->Fetch()){
						$phone = $messAr["EMAIL_TO"];
						if(strpos($phone,"#")!==false){
							$phone = str_replace("#","",$phone);
							$phone = trim($phone);
							if(isset($arMacros[$phone])){
								$phone = $arMacros[$phone];
							}else{
								$phone = false;
							}
						}
						if($phone){
							$mess = $messAr["MESSAGE"];
							$mess = \Mlife\Asz\Functions::replaceBySms($mess,$arMacros);
							if($mess) {
								//тут отправка смс
								if(\Bitrix\Main\Loader::IncludeModule("mlife.smsservices")) {
									$obSmsServ = new \CMlifeSmsServices();
									$arSend = $obSmsServ->sendSms($phone,$mess,0);
								}
								else {
									\CSMSS::Send($phones, $mess);
								}
							}
						}
					}
				}
				
			}
			
		}
		
		//установка остатков и резервов
		$optQuant1 = \Bitrix\Main\Config\Option::get("mlife.asz", "asz_status1", "0", self::$siteId);
		$optQuant2 = \Bitrix\Main\Config\Option::get("mlife.asz", "asz_status2", "0", self::$siteId);
		$optQuant3 = \Bitrix\Main\Config\Option::get("mlife.asz", "asz_status3", "0", self::$siteId);
		if($optQuant1==$fields["STATUS"] || $optQuant2==$fields["STATUS"] || $optQuant3==$fields["STATUS"]){
			$oldQuantArray = self::$arFinBasket;
			
			$sostav = \Mlife\Asz\BasketTable::getList(
				array(
					'select' => array("PROD_ID","QUANT"),
					'filter' => array("ORDER_ID"=>$fields["ID"]),
				)
			);
			$arFinBasket = array();
			$arFinBasketIds = array();
			while ($arBasketItem = $sostav->Fetch()){
				$arFinBasket[$arBasketItem["PROD_ID"]] = array(
					"ID" => $arBasketItem["PROD_ID"],
					"QUANT" => $arBasketItem["QUANT"],
				);
				$arFinBasketIds[] = $arBasketItem["PROD_ID"];
			}
			
			if(!empty($arFinBasketIds)){
				$arIbIds = array();
				$res = \Mlife\Asz\ElementTable::getList(array(
					'select' => array("IBLOCK_ID","ID"),
					'filter' => array("ID"=>$arFinBasketIds)
				));
				while($arData = $res->Fetch()){
					$arIbIds[$arData["IBLOCK_ID"]][] = $arData["ID"];
				}
				\CModule::IncludeModule("iblock");
				foreach($arIbIds as $iblock=>$prodId){
					$res = \CIBlockElement::GetList(Array(), array("ID"=>$prodId,"IBLOCK_ID"=>$iblock), false, false, array("PROPERTY_ASZ_SYSTEM","ID"));
					while($arProdGuant = $res->Fetch(false,false)){
						$arFinBasket[$arProdGuant["ID"]]["SYSTEM"] = $arProdGuant["PROPERTY_ASZ_SYSTEM_VALUE"];
						$arFinBasket[$arProdGuant["ID"]]["IBLOCK_ID"] = $iblock;
					}
				}
				
				
				foreach($arFinBasket as $basketItem){
					$arGuant = \Mlife\Asz\Functions::getPriceValue($basketItem["SYSTEM"]);
					$update = false;
					if($optQuant1==$fields["STATUS"] && $fields["STATUS"]!=self::$oldStatus){
						$newKol = ($arGuant['KOL']['KOL'] - $basketItem["QUANT"]);
						$newRez = ($arGuant['KOL']['ZAK'] + $basketItem["QUANT"]);
						$update = true;
					}elseif($optQuant2==$fields["STATUS"] && $fields["STATUS"]!=self::$oldStatus){
						$newKol = $arGuant['KOL']['KOL'];
						$newRez = ($arGuant['KOL']['ZAK'] - $basketItem["QUANT"]);
						$update = true;
					}elseif($optQuant3==$fields["STATUS"] && $fields["STATUS"]!=self::$oldStatus){
						$newKol = ($arGuant['KOL']['KOL'] + $basketItem["QUANT"]);
						$newRez = ($arGuant['KOL']['ZAK'] - $basketItem["QUANT"]);
						$update = true;
					}elseif($optQuant1==$fields["STATUS"] && $fields["STATUS"]==self::$oldStatus && !self::$arFinBasketRefresh){
						if(isset($oldQuantArray[$basketItem["ID"]])){
							if($oldQuantArray[$basketItem["ID"]]!=$basketItem["QUANT"]){
								$newKol = ($arGuant['KOL']['KOL'] - ($basketItem["QUANT"] - $oldQuantArray[$basketItem["ID"]]));
								$newRez = ($arGuant['KOL']['ZAK'] + ($basketItem["QUANT"] - $oldQuantArray[$basketItem["ID"]]));
								$update = true;
							}
						}else{
							$newKol = ($arGuant['KOL']['KOL'] - $basketItem["QUANT"]);
							$newRez = ($arGuant['KOL']['ZAK'] + $basketItem["QUANT"]);
							$update = true;
						}
					}elseif($optQuant2==$fields["STATUS"] && $fields["STATUS"]==self::$oldStatus && !self::$arFinBasketRefresh){
						if(isset($oldQuantArray[$basketItem["ID"]])){
							if($oldQuantArray[$basketItem["ID"]]!=$basketItem["QUANT"]){
								$newKol = $arGuant['KOL']['KOL'];
								$newRez = ($arGuant['KOL']['ZAK'] - ($basketItem["QUANT"] - $oldQuantArray[$basketItem["ID"]]));
								$update = true;
							}
						}else{
							$newKol = $arGuant['KOL']['KOL'];
							$newRez = ($arGuant['KOL']['ZAK'] - $basketItem["QUANT"]);
							$update = true;
						}
					}elseif($optQuant3==$fields["STATUS"] && $fields["STATUS"]==self::$oldStatus && !self::$arFinBasketRefresh){
						if(isset($oldQuantArray[$basketItem["ID"]])){
							if($oldQuantArray[$basketItem["ID"]]!=$basketItem["QUANT"]){
								$newKol = ($arGuant['KOL']['KOL'] + ($basketItem["QUANT"] - $oldQuantArray[$basketItem["ID"]]));
								$newRez = ($arGuant['KOL']['ZAK'] - ($basketItem["QUANT"] - $oldQuantArray[$basketItem["ID"]]));
								$update = true;
							}
						}else{
							$newKol = ($arGuant['KOL']['KOL'] + $basketItem["QUANT"]);
							$newRez = ($arGuant['KOL']['ZAK'] - $basketItem["QUANT"]);
							$update = true;
						}
					}
					if($update){
					$newSystemStr = str_replace('kol:::'.$arGuant['KOL']['KOL'].':::'.$arGuant['KOL']['ZAK'],'kol:::'.$newKol.':::'.$newRez,$basketItem["SYSTEM"]);
					\CIBlockElement::SetPropertyValues($basketItem["ID"],$basketItem["IBLOCK_ID"],$newSystemStr,"ASZ_SYSTEM");
					\Mlife\Asz\QuantTable::update(array("PRODID"=>$basketItem["ID"]),array("KOL"=>$newKol,"ZAK"=>$newRez));
					
					}
					//file_put_contents("/var/www/artlux/data/www/demo3.kalinkovichi.ru/bitrix/modules/mlife.asz/test.txt",print_r(array($basketItem,$newSystemStr,$newKol,$newRez,$oldQuantArray),true));
				}
			}
			
		}
		
		$result = new Entity\EventResult();
		return $result;
		
	}
	
	public static function OrderOnBeforeDelete($orderId){
		
		$fields = array();
		
		$res = \Mlife\Asz\OrderTable::getList(array(
			"select"=>array("USERID"),
			"filter"=>array("ID"=>$orderId["ID"])));
			$fields = $res->Fetch();
		
		self::$UID = $fields["USERID"];
		
		$result = new Entity\EventResult();
		return $result;
		
	}
	
	public static function OrderOnAfterDelete($orderId){
		
		self::$deleteOrder = true;
		
		$UID = self::$UID;
		
		if($orderId["ID"]>0){
			
			//удаление корзины
			$entity = \Mlife\Asz\BasketTable::getEntity();
			$connection = \Bitrix\Main\Application::getConnection();
			$helper = $connection->getSqlHelper();
			$tableName = $entity->getDBTableName();
			$where = 'ORDER_ID='.$orderId["ID"];
			$sql = "DELETE FROM ".$tableName." WHERE ".$where;
			$connection->queryExecute($sql);
			
			//удаление значений пользовательских свойств
			if($UID){
			$entity = \Mlife\Asz\OrderpropsValuesTable::getEntity();
			$connection = \Bitrix\Main\Application::getConnection();
			$helper = $connection->getSqlHelper();
			$tableName = $entity->getDBTableName();
			$where = 'UID='.$UID;
			$sql = "DELETE FROM ".$tableName." WHERE ".$where;
			$connection->queryExecute($sql);
			}
			
		}
		
		self::$deleteOrder = false;
		
		$result = new Entity\EventResult();
		return $result;
		
	}
	
	public static function BasketOnBeforeDelete($field){
		
		if($field["ID"]>0){
			
			$fields = array();
		
			$res = \Mlife\Asz\BasketTable::getList(array(
			"select"=>array("PROD_ID","QUANT", "ORDER_ID", "SITE_ID"),
			"filter"=>array("ID"=>$field["ID"])));
			$fields = $res->Fetch();
			
			if($fields["ORDER_ID"]>0){
				self::$deleteRow = array(
					"PROD_ID" => $fields["PROD_ID"],
					"QUANT" => $fields["QUANT"],
					"ORDER_ID" => $fields["ORDER_ID"],
					"SITE_ID" => $fields["SITE_ID"],
				);
			}
			
		}
		
	}
	
	public static function BasketOnAfterDelete($field){
		
		if($field["ID"]>0 && self::$deleteRow){
			
			$data = self::$deleteRow;
			$optQuant1 = \Bitrix\Main\Config\Option::get("mlife.asz", "asz_status1", "0", $data["SITE_ID"]);
			
			if($optQuant1>0){
			
				$fields = array();
				$res = \Mlife\Asz\OrderTable::getList(array(
				"select"=>array("STATUS","SITEID","ID"),
				"filter"=>array("ID"=>$data["ORDER_ID"])));
				$fields = $res->Fetch();
				
				//вернуть резерв, если товар удаляется из корзины в админке при наличии уже созданного заказа
				if($optQuant1==$fields["STATUS"]){
					
					$arIbIds = array();
					$res = \Mlife\Asz\ElementTable::getList(array(
						'select' => array("IBLOCK_ID","ID"),
						'filter' => array("ID"=>$data["PROD_ID"])
					));
					if($arData = $res->Fetch()){
						\CModule::IncludeModule("iblock");
						$res2 = \CIBlockElement::GetList(Array(), array("ID"=>$data["PROD_ID"],"IBLOCK_ID"=>$arData["IBLOCK_ID"]), false, false, array("PROPERTY_ASZ_SYSTEM","ID"));
						if($arProdGuant = $res2->Fetch(false,false)){
							$arGuant = \Mlife\Asz\Functions::getPriceValue($arProdGuant["PROPERTY_ASZ_SYSTEM_VALUE"]);
							
							$newKol = ($arGuant['KOL']['KOL'] + $data["QUANT"]);
							$newRez = ($arGuant['KOL']['ZAK'] - $data["QUANT"]);
							
							$newSystemStr = str_replace('kol:::'.$arGuant['KOL']['KOL'].':::'.$arGuant['KOL']['ZAK'],'kol:::'.$newKol.':::'.$newRez,$arProdGuant["PROPERTY_ASZ_SYSTEM_VALUE"]);
							\CIBlockElement::SetPropertyValues($data["PROD_ID"],$arData["IBLOCK_ID"],$newSystemStr,"ASZ_SYSTEM");
							\Mlife\Asz\QuantTable::update(array("PRODID"=>$data["PROD_ID"]),array("KOL"=>$newKol,"ZAK"=>$newRez));
							
						}
					}
					
				}
				
			}
			
			self::$deleteRow = false;
			
		}
		
	}
	
	//очень главное меню в админке
	public static function OnBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu){
		
		$key = 'global_menu_mlifeasz';
		$newMenu = array(
			"menu_id" => "mlifeasz",
			"page_icon" => "content_title_icon",
			"index_icon" => "content_title_icon",
			"text" => Loc::getMessage("MLIFE_ASZ_HANDLERS_MENU_MAINADMIN"),
			"title" => Loc::getMessage("MLIFE_ASZ_HANDLERS_MENU_MAINADMIN"),
			"url" => "index.php?lang=ru",
			"sort" => "300",
			"items_id" => "global_menu_mlifeasz",
			"help_section" => "desktop",
			"items" => array()
		);
		
		global $APPLICATION;
		$POST_RIGHT = $APPLICATION->GetGroupRight("mlife.asz");
		if($POST_RIGHT == "D") {
		$FilterSiteId = false;
		$arSites = \Mlife\Asz\Functions::GetGroupRightSiteId();
			if(count($arSites)>0) $FilterSiteId = $arSites;
			if($FilterSiteId) $POST_RIGHT = "W";
		}
		
		if($POST_RIGHT != "D")
			$aGlobalMenu[$key] = $newMenu;

	}
	
	//вывод таба в админке
	public static function OnAdminTabControlBegin(&$form){
		
		if($GLOBALS["APPLICATION"]->GetCurPage() == "/bitrix/admin/iblock_element_edit.php")
		{
			\CModule::IncludeModule('iblock');
			
			$elId = intval($_REQUEST["ID"]);
			$elIb = intval($_REQUEST['IBLOCK_ID']);
			
			//получаем ид системного свойства
			$asz_system_id = 0;
			$properties = \CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("IBLOCK_ID"=>$elIb, "CODE"=>"ASZ_SYSTEM"));
			if($prop_fields = $properties->GetNext()){
				$asz_system_id = $prop_fields["ID"];
			}
			
			if($asz_system_id==0) return;
			
			$res = \CIBlock::GetByID($elIb);
			if($ar_res = $res->GetNext()){
				$siteId = $ar_res["LID"];
				if(is_array($siteId)) $siteId = $ar_res["LID"][0];
			}
			
			//получаем типы цен для текущего сайта
			$price = \Mlife\Asz\PricetipTable::getList(
				array(
					'select' => array('ID','NAME',"BASE"),
					'filter' => array("LOGIC"=>"OR",array("=SITE_ID"=>$siteId),array("=SITE_ID"=>false)),
				)
			);
			$arPrice = array();
			while($arPricedb = $price->Fetch()){
				$arPrice[$arPricedb["ID"]] = array(
					"CODE" => $arPricedb["ID"],
					"NAME" => $arPricedb["NAME"],
					"BASE" => $arPricedb["BASE"],
				);
			}
			
			//получаем валюты для текущего сайта
			$curency = \Mlife\Asz\CurencyTable::getList(
				array(
					'select' => array('CODE'),
					'filter' => array("LOGIC"=>"OR",array("=SITEID"=>$siteId),array("=SITEID"=>false)),
					'group' => array("CODE"),
				)
			);
			$arCurency = array();
			while($arCurencydb = $curency->Fetch()){
				$arCurency[] = $arCurencydb["CODE"];
			}
			
			
			//получаем текущие значения
			$arCurPrice = array();
			if($elId>0){
				$db_props = \CIBlockElement::GetProperty($elIb, $elId, array("sort" => "asc"), Array("CODE"=>"ASZ_SYSTEM"));
				if($ar_props = $db_props->Fetch()){
					$arCurPrice = \Mlife\Asz\Functions::getPriceValue($ar_props["VALUE"]);
				}
			}
			
			//получаем ид системного свойства
			$asz_system_id = 0;
			$properties = \CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("IBLOCK_ID"=>$elIb, "CODE"=>"ASZ_SYSTEM"));
			if($prop_fields = $properties->GetNext()){
				$asz_system_id = $prop_fields["ID"];
			}
			
			//html цен
			$htmlprice = '';
			$htmlkol = '';
			$jspricer = '';
			$jskol = '';
			$jskolr = '';
			//количество
			$jskolr .= 'kol:::"+$("input[name=aszcol_val]").val()+":::"+$("input[name=aszcol_zak]").val()+"+++';
			
			$htmlkol .= '<tr valign="top"><td>'.Loc::getMessage("MLIFE_ASZ_HANDLERS_TABCONTROL_KOL").':</td><td><input type="text" name="aszcol_val" id="aszcol_val" value="';
			if(isset($arCurPrice['KOL']['KOL'])) $htmlkol .= $arCurPrice['KOL']['KOL'];
			$htmlkol .= '"/></td></tr><tr valign="top"><td>'.Loc::getMessage("MLIFE_ASZ_HANDLERS_TABCONTROL_REZ").':</td><td><input type="text" name="aszcol_zak" value="';
			if(isset($arCurPrice['KOL']['ZAK'])) $htmlkol .= $arCurPrice['KOL']['ZAK'];
			$htmlkol .= '"/></td></tr>';
			
			foreach($arPrice as $price){
				$jspricer .= 'cod'.$price['CODE'].':::"+$("input[name=aszprice_val_'.$price['CODE'].']").val()+":::"+$("select[name=aszprice_cur_'.$price['CODE'].']").val()+"+++';
				$htmlprice .= '<tr valign="top"><td>'.$price['NAME'];
				if($price['BASE']=='Y')  $htmlprice .= ' ('.Loc::getMessage("MLIFE_ASZ_HANDLERS_TABCONTROL_BASEPRICE").')';
				$htmlprice .=':</td><td>
				<input type="text" name="aszprice_val_'.$price['CODE'].'" value="';
				if(isset($arCurPrice["PRICE"][$price['CODE']]["VAL"])) $htmlprice .= $arCurPrice["PRICE"][$price['CODE']]["VAL"];
				$htmlprice .= '"/><select name="aszprice_cur_'.$price['CODE'].'">';
					foreach($arCurency as $val){
						$sel = "";
						if(isset($arCurPrice["PRICE"][$price['CODE']]["CUR"]) && $arCurPrice["PRICE"][$price['CODE']]["CUR"]==$val){
							$sel = " selected";
						}
						$htmlprice .= '<option value="'.$val.'"'.$sel.'>'.$val.'</option>';
					}
				$htmlprice .='</select></td></tr>';
			}
			$jsprice = 'var aszprice = "'.$jspricer.'";';
			$jskol = 'var aszkol = "'.$jskolr.'";';
			//echo $jsprice;
			
			\CUtil::InitJSCore('jquery');
			//добавление таба в форму редактирования
			$form->tabs[] = array("DIV" => "my_edit", "TAB" => Loc::getMessage("MLIFE_ASZ_HANDLERS_TABCONTROL_NAME"), "ICON"=>"aszmagazin", "TITLE"=>Loc::getMessage("MLIFE_ASZ_HANDLERS_TABCONTROL_NAME"), "CONTENT"=>
				'<tr class="heading" id="tr_DETAIL_TEXT_LABEL">
					<td colspan="2">'.Loc::getMessage("MLIFE_ASZ_HANDLERS_TABCONTROL_KOL").'</td>
				</tr>
				'.$htmlkol.'
				<tr class="heading" id="tr_DETAIL_TEXT_LABEL">
					<td colspan="2">'.Loc::getMessage("MLIFE_ASZ_HANDLERS_TABCONTROL_PRICE_LABEL").'</td>
				</tr>
				'.$htmlprice.'
				<tr class="heading" id="tr_DETAIL_TEXT_LABEL">
					<td colspan="2">'.Loc::getMessage("MLIFE_ASZ_HANDLERS_TABCONTROL_SKID_LABEL").'</td>
				</tr><tr valign="top">
					<td>'.Loc::getMessage("MLIFE_ASZ_HANDLERS_TABCONTROL_SKID_EMPTY").'</td>
				</tr><tr class="heading" id="tr_DETAIL_TEXT_LABEL">
					<td colspan="2">'.Loc::getMessage("MLIFE_ASZ_HANDLERS_TABCONTROL_KOMP_LABEL").'</td>
				</tr><tr valign="top">
					<td>'.Loc::getMessage("MLIFE_ASZ_HANDLERS_TABCONTROL_KOMP_EMPTY").'</td>
				</tr><tr class="heading" id="tr_DETAIL_TEXT_LABEL">
					<td colspan="2">'.Loc::getMessage("MLIFE_ASZ_HANDLERS_TABCONTROL_OFFER_LABEL").'</td>
				</tr><tr valign="top">
					<td>'.Loc::getMessage("MLIFE_ASZ_HANDLERS_TABCONTROL_OFFER_EMPTY").'</td>
				</tr>
				<script>
					$(document).ready(function(){
						$("tr#tr_PROPERTY_'.$asz_system_id.'").css({"display":"none"});
						$(document).on("click","#save, #apply",function(e){
							'.$jsprice.$jskol.'
							var asz_system = aszkol+aszprice;
							$("tr#tr_PROPERTY_'.$asz_system_id.' input").val(asz_system);
						});
					});
				</script>
				'
			);
			
		}
	}
	
	
	//установка цен и остатков
	public static function OnAfterIBlockElementAdd(&$arFields) {

		if($arFields["ID"]>0 && $arFields['IBLOCK_ID']>0 && $arFields['RESULT']>0){
			\CModule::IncludeModule('iblock');
			
			//получаем ид системного свойства
			$asz_system_id = 0;
			$properties = \CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("IBLOCK_ID"=>$arFields['IBLOCK_ID'], "CODE"=>"ASZ_SYSTEM"));
			if($prop_fields = $properties->GetNext()){
				$asz_system_id = $prop_fields["ID"];
			}
			
			if($asz_system_id==0) return;
			
			$res = \CIBlock::GetByID($arFields['IBLOCK_ID']);
			if($ar_res = $res->GetNext()){
				$siteId = $ar_res["LID"];
				if(is_array($siteId)) $siteId = $ar_res["LID"][0];
			}
			
			//получаем текущие значения
			$arCurPrice = array();
			if($arFields["ID"]>0){
				//TODO значение уже есть в $arFields, убрать лишний запрос при наличии значения
				$db_props = \CIBlockElement::GetProperty($arFields['IBLOCK_ID'], $arFields["ID"], array("sort" => "asc"), Array("CODE"=>"ASZ_SYSTEM"));
				if($ar_props = $db_props->Fetch()){
					$arCurPrice = \Mlife\Asz\Functions::getPriceValue($ar_props["VALUE"]);
					
					if(is_array($arCurPrice['PRICE'])){
						foreach($arCurPrice['PRICE'] as $priceId=>$val){
							//TODO убрать запросы в цикле
							//удаление старой цены для типа ид
							\Mlife\Asz\PriceTable::deleteprice($priceId,$arFields["ID"]);
							//установка новой цены для типа ид
							if(intval($val["VAL"])>0){
								$res = \Mlife\Asz\PriceTable::add(array(
									"IBLOCK" => intval($arFields['IBLOCK_ID']),
									"PRODID" => intval($arFields['ID']),
									"PRICEID" => intval($priceId),
									"PRICEVAL" => $val["VAL"],
									"PRICECUR" => $val["CUR"],
									"SORTVAL" => \Mlife\Asz\CurencyFunc::convertBase($val["VAL"],$val["CUR"],$siteId),
								));
							}else{
								$res = \Mlife\Asz\PriceTable::add(array(
									"IBLOCK" => intval($arFields['IBLOCK_ID']),
									"PRODID" => intval($arFields['ID']),
									"PRICEID" => intval($priceId),
									"PRICEVAL" => "0",
									"PRICECUR" => ($val["CUR"]) ? $val["CUR"] : "USD",
									"SORTVAL" => \Mlife\Asz\CurencyFunc::convertBase($val["VAL"],$val["CUR"],$siteId),
								));
							}
						}
					}
					
					if(is_array($arCurPrice["KOL"])){
						$checkQuant = \Mlife\Asz\QuantTable::getById($arFields["ID"]);
						if($checkQuant->Fetch()){
							//установка новых остатков
							$res = \Mlife\Asz\QuantTable::update(array("PRODID"=>$arFields["ID"]), array(
								"IBLOCKID" => intval($arFields['IBLOCK_ID']),
								"PRODID" => intval($arFields['ID']),
								"KOL" => intval($arCurPrice["KOL"]["KOL"]),
								"ZAK" => intval($arCurPrice["KOL"]["ZAK"]),
							));
						}else{
							//установка новых остатков
							$res = \Mlife\Asz\QuantTable::add(array(
								"IBLOCKID" => intval($arFields['IBLOCK_ID']),
								"PRODID" => intval($arFields['ID']),
								"KOL" => intval($arCurPrice["KOL"]["KOL"]),
								"ZAK" => intval($arCurPrice["KOL"]["ZAK"]),
							));
						}
					}
					
				}else{
					
					$checkQuant = \Mlife\Asz\QuantTable::getById($arFields["ID"]);
					if($checkQuant->Fetch()){
						//установка новых остатков
						$res = \Mlife\Asz\QuantTable::update(array("PRODID"=>$arFields["ID"]), array(
							"IBLOCKID" => intval($arFields['IBLOCK_ID']),
							"PRODID" => intval($arFields['ID']),
							"KOL" => "0",
							"ZAK" => "0",
						));
					}else{
						//установка новых остатков
						$res = \Mlife\Asz\QuantTable::add(array(
							"IBLOCKID" => intval($arFields['IBLOCK_ID']),
							"PRODID" => intval($arFields['ID']),
							"KOL" => "0",
							"ZAK" => "0",
						));
					}
					
				}
			}
			
		}
		
	}
	
	public static function OnAfterIBlockElementDelete(&$arFields) {
		
		if($arFields["ID"]>0){
			
			//удаляем остатки
			\Mlife\Asz\QuantTable::delete(array("PRODID"=>$arFields["ID"]));
			
			//удаляем цены
			\Mlife\Asz\PriceTable::deletepriceProd($arFields["ID"]);
			
		}
		
	}
	
}

?>