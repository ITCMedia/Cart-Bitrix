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

class CountryTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}
	
	public static function getTableName()
	{
		return 'mlife_asz_country';
	}
	
	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_COUNTRY_ENTITY_ID_FIELD'),
			),
			'SITEID' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateSiteId'),
				'title' => Loc::getMessage('MLIFE_ASZ_COUNTRY_ENTITY_SITEID_FIELD'),
			),
			'NAME' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateName'),
				'title' => Loc::getMessage('MLIFE_ASZ_COUNTRY_ENTITY_NAME_FIELD'),
			),
			'CODE2' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateCode2'),
				'title' => Loc::getMessage('MLIFE_ASZ_COUNTRY_ENTITY_CODE2_FIELD'),
			),
			'CODE3' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateCode3'),
				'title' => Loc::getMessage('MLIFE_ASZ_COUNTRY_ENTITY_CODE3_FIELD'),
			),
			'ACTIVE' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateCode'),
				'title' => Loc::getMessage('MLIFE_ASZ_COUNTRY_ENTITY_ACTIVE_FIELD'),
			),
		);
	}
	
	public static function validateCode()
	{
		return array(
			new Validator\LengthFix(1),
		);
	}
	
	public static function validateCode2()
	{
		return array(
			new Validator\LengthFix(2),
		);
	}
	
	public static function validateCode3()
	{
		return array(
			new Validator\LengthFix(3),
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