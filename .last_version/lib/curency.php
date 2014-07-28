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

class CurencyTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'mlife_asz_curency';
	}

	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_CURENCY_ENTITY_ID_FIELD'),
			),
			'CODE' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateCode'),
				'title' => Loc::getMessage('MLIFE_ASZ_CURENCY_ENTITY_CODE_FIELD'),
			),
			'BASE' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateBase'),
				'title' => Loc::getMessage('MLIFE_ASZ_CURENCY_ENTITY_BASE_FIELD'),
			),
			'CURS' => array(
				'data_type' => 'string',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_CURENCY_ENTITY_CURS_FIELD'),
			),
			'SITEID' => array(
				'data_type' => 'string',
				'required' => false,
				'validation' => array(__CLASS__, 'validateSiteId'),
				'title' => Loc::getMessage('MLIFE_ASZ_CURENCY_ENTITY_SITEID_FIELD'),
			),
		);
	}
	
	public static function validateCode()
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
	
	public static function validateBase()
	{
		return array(
			new Validator\LengthFix(1),
		);
	}
}
?>