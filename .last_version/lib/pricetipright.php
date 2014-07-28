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

class PricetiprightTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'mlife_asz_pricetip_right';
	}

	public static function getMap()
	{
		return array(
			'IDTIP' => array(
				'data_type' => 'integer',
				'required' => true,
				'primary' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_PRICETIPRIGHT_ENTITY_IDTIP_FIELD'),
			),
			'IDGROUP' => array(
				'data_type' => 'integer',
				'required' => false,
				'title' => Loc::getMessage('MLIFE_ASZ_PRICETIPRIGHT_ENTITY_IDGROUP_FIELD'),
			),
		);
	}
	
	public static function validateCode()
	{
		return array(
			new Validator\LengthFix(4),
		);
	}
	public static function validateName()
	{
		return array(
			new Entity\Validator\Length(null, 255),
		);
	}
	
	public static function deleteright($priceId)
	{

		$entity = static::getEntity();
		$result = new Entity\Result();

		// delete
		$connection = \Bitrix\Main\Application::getConnection();
		$helper = $connection->getSqlHelper();

		$tableName = $entity->getDBTableName();

		$where = 'IDTIP='.$priceId;

		$sql = "DELETE FROM ".$tableName." WHERE ".$where;
		
		//print_r($sql);//die();
		
		$connection->queryExecute($sql);


		return $result;
	}
	
}
?>