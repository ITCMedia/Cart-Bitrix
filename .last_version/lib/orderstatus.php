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

class OrderStatusTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}
	
	public static function getTableName()
	{
		return 'mlife_asz_order_status';
	}
	
	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_ORDERSTATUS_ENTITY_ID_FIELD'),
			),
			'SITEID' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateSiteId'),
				'title' => Loc::getMessage('MLIFE_ASZ_ORDERSTATUS_ENTITY_SITEID_FIELD'),
			),
			'NAME' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateName'),
				'title' => Loc::getMessage('MLIFE_ASZ_ORDERSTATUS_ENTITY_NAME_FIELD'),
			),
			'CODE' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateCode'),
				'title' => Loc::getMessage('MLIFE_ASZ_ORDERSTATUS_ENTITY_CODE_FIELD'),
			),
			'ACTIVE' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateCode'),
				'title' => Loc::getMessage('MLIFE_ASZ_ORDERSTATUS_ENTITY_ACTIVE_FIELD'),
			),
			'DESC' => array(
				'data_type' => 'string',
				'required' => false,
				'validation' => array(__CLASS__, 'validateDesc'),
				'title' => Loc::getMessage('MLIFE_ASZ_ORDERSTATUS_ENTITY_DESC_FIELD'),
			),
		);
	}
	
	public static function validateCode()
	{
		return array(
			new Validator\LengthFix(1),
		);
	}
	
	public static function validateSiteId()
	{
		return array(
			new Validator\LengthFix(2),
		);
	}
	
	public static function validateName()
	{
		return array(
			new Entity\Validator\Length(null, 255),
		);
	}
	
	public static function validateDesc()
	{
		return array(
			new Entity\Validator\Length(null, 1800),
		);
	}
	
}