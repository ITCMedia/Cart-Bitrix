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

class PricetipTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'mlife_asz_pricetip';
	}

	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_PRICETIP_ENTITY_ID_FIELD'),
			),
			'CODE' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateCode'),
				'title' => Loc::getMessage('MLIFE_ASZ_PRICETIP_ENTITY_CODE_FIELD'),
			),
			'NAME' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateName'),
				'title' => Loc::getMessage('MLIFE_ASZ_PRICETIP_ENTITY_NAME_FIELD'),
			),
			"BASE" => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateBase'),
				'title' => Loc::getMessage('MLIFE_ASZ_PRICETIP_ENTITY_BASE_FIELD'),
			),
			"GROUP" => array(
				'data_type' => 'string',
				'required' => true,
				'serialized' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_PRICETIP_ENTITY_GROUP_FIELD'),
			),
			"SITE_ID" => array(
				'data_type' => 'string',
				'required' => false,
				'validation' => array(__CLASS__, 'validateSiteId'),
				'title' => Loc::getMessage('MLIFE_ASZ_PRICETIP_ENTITY_SITE_ID_FIELD'),
			),
			'PRICETIPRIGHT' => array(
				'data_type' => 'Mlife\Asz\Pricetipright',
				'reference' => array('=this.ID' => 'ref.IDTIP'),
			),
		);
	}
	
	public static function validateCode()
	{
		return array(
			new Validator\LengthFix(4),
		);
	}
	public static function validateBase()
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
	
}
?>