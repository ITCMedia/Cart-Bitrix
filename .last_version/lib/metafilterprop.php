<?php/** * Bitrix Framework * @package    Bitrix * @subpackage mlife.asz * @copyright  2014 Zahalski Andrew */namespace Mlife\Asz;use Bitrix\Main\Entity;use Bitrix\Main\Localization\Loc;Loc::loadMessages(__FILE__);class MetafilterpropTable extends Entity\DataManager{		public static function getFilePath()	{		return __FILE__;	}	public static function getTableName()	{		return 'mlife_asz_metafilter_props';	}	public static function getMap()	{		return array(			new Entity\IntegerField('ID', array(				'primary' => true,				'autocomplete' => false,				)			),			new Entity\IntegerField('PROPID', array(				'required' => true,				)			),		);	}		public static function deleterow($Id)	{		$entity = static::getEntity();		$result = new Entity\Result();		// delete		$connection = \Bitrix\Main\Application::getConnection();		$helper = $connection->getSqlHelper();		$tableName = $entity->getDBTableName();		$where = 'ID='.$Id;		$sql = "DELETE FROM ".$tableName." WHERE ".$where;				//print_r($sql);//die();				$connection->queryExecute($sql);		return $result;	}	}?>