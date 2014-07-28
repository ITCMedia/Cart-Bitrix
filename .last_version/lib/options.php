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

class OptionsTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}
	
	public static function getTableName()
	{
		return 'mlife_asz_options';
	}
	
	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_OPTIONS_ENTITY_ID_FIELD'),
			),
			'SITEID' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateSiteId'),
				'title' => Loc::getMessage('MLIFE_ASZ_OPTIONS_ENTITY_SITEID_FIELD'),
			),
			'CODE' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateCode'),
				'title' => Loc::getMessage('MLIFE_ASZ_OPTIONS_ENTITY_CODE_FIELD'),
			),
			'VALUE' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateName'),
				'title' => Loc::getMessage('MLIFE_ASZ_OPTIONS_ENTITY_VALUE_FIELD'),
			),
		);
	}
	
	public static function validateName()
	{
		return array(
			new Entity\Validator\Length(null, 1900),
		);
	}
	
	public static function validateCode()
	{
		return array(
			new Entity\Validator\Length(null, 50),
		);
	}
	
	public static function validateSiteId()
	{
		return array(
			new Validator\LengthFix(2),
		);
	}
	
}