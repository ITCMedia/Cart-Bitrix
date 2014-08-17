<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage mlife.asz
 * @copyright  2014 Zahalski Andrew
 */

namespace Mlife\Asz;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class DiscountTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'mlife_asz_discount';
	}

	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_DISCOUNT_ENTITY_ID_FIELD'),
			),
			'IBLOCK_ID' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_DISCOUNT_ENTITY_IBLOCK_ID_FIELD'),
			),
			'CATEGORY_ID' => array(
				'data_type' => 'integer',
				'required' => false,
				'title' => Loc::getMessage('MLIFE_ASZ_DISCOUNT_ENTITY_CATEGORY_ID_FIELD'),
			),
			'PRODUCT_ID' => array(
				'data_type' => 'integer',
				'required' => false,
				'title' => Loc::getMessage('MLIFE_ASZ_DISCOUNT_ENTITY_PRODUCT_ID_FIELD'),
			),
			'TIP' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_DISCOUNT_ENTITY_TIP_FIELD'),
			),
			'PRIOR' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_DISCOUNT_ENTITY_PRIOR_FIELD'),
			),
			'NAME' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateName'),
				'title' => Loc::getMessage('MLIFE_ASZ_DISCOUNT_ENTITY_NAME_FIELD'),
			),
			'DESC' => array(
				'data_type' => 'string',
				'required' => false,
				'validation' => array(__CLASS__, 'validateDesc'),
				'title' => Loc::getMessage('MLIFE_ASZ_DISCOUNT_ENTITY_DESC_FIELD'),
			),
			'VALUE' => array(
				'data_type' => 'string',
				'required' => false,
				'title' => Loc::getMessage('MLIFE_ASZ_DISCOUNT_ENTITY_VALUE_FIELD'),
			),
			'MAXSUMM' => array(
				'data_type' => 'string',
				'required' => false,
				'title' => Loc::getMessage('MLIFE_ASZ_DISCOUNT_ENTITY_MAXSUMM_FIELD'),
			),
			'PRIORFIX' => array(
				'data_type' => 'string',
				'required' => false,
				'validation' => array(__CLASS__, 'validatePrior'),
				'title' => Loc::getMessage('MLIFE_ASZ_DISCOUNT_ENTITY_PRIORFIX_FIELD'),
			),
			'ACTIVE' => array(
				'data_type' => 'string',
				'required' => false,
				'validation' => array(__CLASS__, 'validatePrior'),
				'title' => Loc::getMessage('MLIFE_ASZ_DISCOUNT_ENTITY_ACTIVE_FIELD'),
			),
			'DATE_START' => array(
				'data_type' => 'datetime',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_DISCOUNT_ENTITY_DATE_START_FIELD'),
			),
			'DATE_END' => array(
				'data_type' => 'datetime',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_DISCOUNT_ENTITY_DATE_END_FIELD'),
			),
			'GROUPS' => array(
				'data_type' => 'string',
				'required' => true,
				'serialized' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_DISCOUNT_ENTITY_GROUPS_FIELD'),
			),
		);
	}
	
	public static function validatePrior()
	{
		return array(
			new Validator\LengthFix(1),
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
			new Entity\Validator\Length(null, 255),
		);
	}
	
}
?>