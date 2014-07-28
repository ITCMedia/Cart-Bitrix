<?php
namespace Mlife\Asz;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class BasketTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'mlife_asz_basket';
	}

	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_BASKET_ENTITY_ID_FIELD'),
			),
			'USERID' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_BASKET_ENTITY_USERID_FIELD'),
			),
			'PROD_ID' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_BASKET_ENTITY_PROD_ID_FIELD'),
			),
			'PARENT_PROD_ID' => array(
				'data_type' => 'integer',
				'title' => Loc::getMessage('MLIFE_ASZ_BASKET_ENTITY_PARENT_PROD_ID_FIELD'),
			),
			'PRICE_VAL' => array(
				'data_type' => 'string',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_BASKET_ENTITY_PRICE_VAL_FIELD'),
			),
			'PRICE_CUR' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validatePriceCur'),
				'title' => Loc::getMessage('MLIFE_ASZ_BASKET_ENTITY_PRICE_CUR_FIELD'),
			),
			'UPDATE' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_BASKET_ENTITY_UPDATE_FIELD'),
			),
			'QUANT' => array(
				'data_type' => 'string',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_BASKET_ENTITY_QUANT_FIELD'),
			),
			'DISCOUNT_VAL' => array(
				'data_type' => 'string',
				'title' => Loc::getMessage('MLIFE_ASZ_BASKET_ENTITY_DISCOUNT_VAL_FIELD'),
			),
			'DISCOUNT_CUR' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateDiscountCur'),
				'title' => Loc::getMessage('MLIFE_ASZ_BASKET_ENTITY_DISCOUNT_CUR_FIELD'),
			),
			'SITE_ID' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateSiteId'),
				'title' => Loc::getMessage('MLIFE_ASZ_BASKET_ENTITY_SITE_ID_FIELD'),
			),
			'PROD_NAME' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateProdName'),
				'title' => Loc::getMessage('MLIFE_ASZ_BASKET_ENTITY_PROD_NAME_FIELD'),
			),
			'PROD_DESC' => array(
				'data_type' => 'string',
				'required' => false,
				'validation' => array(__CLASS__, 'validateProdDesc'),
				'title' => Loc::getMessage('MLIFE_ASZ_BASKET_ENTITY_PROD_DESC_FIELD'),
			),
			'ORDER_ID' => array(
				'data_type' => 'integer',
				'title' => Loc::getMessage('MLIFE_ASZ_BASKET_ENTITY_ORDER_ID_FIELD'),
			),
			'PROD_LINK' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateProdLink'),
				'title' => Loc::getMessage('MLIFE_ASZ_BASKET_ENTITY_PROD_LINK_FIELD'),
			),
		);
	}
	public static function validatePriceCur()
	{
		return array(
			new Entity\Validator\Length(null, 3),
		);
	}
	public static function validateDiscountCur()
	{
		return array(
			new Entity\Validator\Length(null, 3),
		);
	}
	public static function validateSiteId()
	{
		return array(
			new Entity\Validator\Length(null, 2),
		);
	}
	public static function validateProdName()
	{
		return array(
			new Entity\Validator\Length(null, 255),
		);
	}
	public static function validateProdDesc()
	{
		return array(
			new Entity\Validator\Length(null, 255),
		);
	}
	public static function validateProdLink()
	{
		return array(
			new Entity\Validator\Length(null, 255),
		);
	}
}