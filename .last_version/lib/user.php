<?php
namespace Mlife\Asz;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class UserTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'mlife_asz_user';
	}

	public static function getMap()
	{
		return array(
			'UID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_USER_ENTITY_UID_FIELD'),
			),
			'TIME' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_USER_ENTITY_TIME_FIELD'),
			),
			'BX_UID' => array(
				'data_type' => 'integer',
				'title' => Loc::getMessage('MLIFE_ASZ_USER_ENTITY_BX_UID_FIELD'),
			),
			'SITE_ID' => array(
				'data_type' => 'string',
				'required' => false,
				'validation' => array(__CLASS__, 'validateSiteId'),
				'title' => Loc::getMessage('MLIFE_ASZ_USER_ENTITY_SITEID_FIELD'),
			),
		);
	}
	
	public static function validateSiteId()
	{
		return array(
			new Validator\LengthFix(2),
		);
	}
}