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

class PriceTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'mlife_asz_price';
	}

	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_PRICE_ENTITY_ID_FIELD'),
			),
			'IBLOCK' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_PRICE_ENTITY_IBLOCK_FIELD'),
			),
			'PRODID' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_PRICE_ENTITY_PRODID_FIELD'),
			),
			'PRICEID' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_PRICE_ENTITY_PRICEID_FIELD'),
			),
			'PRICEVAL' => array(
				'data_type' => 'string',
				'required' => false,
				'title' => Loc::getMessage('MLIFE_ASZ_PRICE_ENTITY_PRICEVAL_FIELD'),
			),
			'SORTVAL' => array(
				'data_type' => 'string',
				'required' => false,
				'title' => Loc::getMessage('MLIFE_ASZ_PRICE_ENTITY_SORTVAL_FIELD'),
			),
			'PRICECUR' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateCode'),
				'title' => Loc::getMessage('MLIFE_ASZ_PRICE_ENTITY_PRICECUR_FIELD'),
			),
		);
	}
	
	public static function validateCode()
	{
		return array(
			new Validator\LengthFix(3),
		);
	}
	
	public static function deleteprice($priceId,$elId)
	{

		$entity = static::getEntity();
		$result = new Entity\Result();
		
		//TODO сделать нормальную проверку
		if(intval($priceId)<1 || intval($elId)<1) return false;
		
		// delete
		$connection = \Bitrix\Main\Application::getConnection();
		$helper = $connection->getSqlHelper();

		$tableName = $entity->getDBTableName();

		$where = 'PRICEID='.$priceId.' AND PRODID='.$elId;

		$sql = "DELETE FROM ".$tableName." WHERE ".$where;
		
		//print_r($sql);//die();
		
		$connection->queryExecute($sql);


		return $result;
	}
	
	public static function deletepriceProd($elId)
	{

		$entity = static::getEntity();
		$result = new Entity\Result();
		
		// delete
		$connection = \Bitrix\Main\Application::getConnection();
		$helper = $connection->getSqlHelper();

		$tableName = $entity->getDBTableName();

		$where = 'PRODID='.$elId;

		$sql = "DELETE FROM ".$tableName." WHERE ".$where;
		
		//print_r($sql);//die();
		
		$connection->queryExecute($sql);


		return $result;
	}
	
}
?>