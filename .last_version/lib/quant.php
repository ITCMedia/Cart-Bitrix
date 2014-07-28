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

class QuantTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'mlife_asz_quant';
	}

	public static function getMap()
	{
		return array(
			'PRODID' => array(
				'data_type' => 'integer',
				'required' => true,
				'primary' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_QUANT_ENTITY_PRODID_FIELD'),
			),
			'IBLOCKID' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_QUANT_ENTITY_IBLOCKID_FIELD'),
			),
			'KOL' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_QUANT_ENTITY_KOL_FIELD'),
			),
			'ZAK' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_QUANT_ENTITY_ZAK_FIELD'),
			),
			'EL' => array(
				'data_type' => '\Mlife\Asz\ElementTable',
				'reference' => array('=this.PRODID' => 'ref.ID'),
				'join_type' => "LEFT"
			),
		);
	}
	
	public static function deletequant($elId)
	{

		$entity = static::getEntity();
		$result = new Entity\Result();
		
		//TODO сделать нормальную проверку
		if(intval($elId)<1) return false;
		
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