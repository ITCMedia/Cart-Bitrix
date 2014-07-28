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

class OrderTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}
	
	public static function getTableName()
	{
		return 'mlife_asz_order';
	}
	
	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_ORDER_ENTITY_ID_FIELD'),
			),
			'SITEID' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateSiteId'),
				'title' => Loc::getMessage('MLIFE_ASZ_ORDER_ENTITY_SITEID_FIELD'),
			),
			'USERID' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_ORDER_ENTITY_USERID_FIELD'),
			),
			'STATUS' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_ORDER_ENTITY_STATUS_FIELD'),
			),
			'PAY_ID' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_ORDER_ENTITY_PAY_ID_FIELD'),
			),
			'DELIVERY_ID' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_ORDER_ENTITY_DELIVERY_ID_FIELD'),
			),
			'PRICE' => array(
				'data_type' => 'string',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_ORDER_ENTITY_PRICE_FIELD'),
			),
			'DISCOUNT' => array(
				'data_type' => 'string',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_ORDER_ENTITY_DISCOUNT_FIELD'),
			),
			'TAX' => array(
				'data_type' => 'string',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_ORDER_ENTITY_TAX_FIELD'),
			),
			'CURRENCY' => array(
				'data_type' => 'string',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_ORDER_ENTITY_CURRENCY_FIELD'),
			),
			'DELIVERY_PRICE' => array(
				'data_type' => 'string',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_ORDER_ENTITY_DELIVERY_PRICE_FIELD'),
			),
			'PAYMENT_PRICE' => array(
				'data_type' => 'string',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_ORDER_ENTITY_PAYMENT_PRICE_FIELD'),
			),
			'DATE' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_ORDER_ENTITY_DATE_FIELD'),
			),
			'PASSW' => array(
				'data_type' => 'string',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_ORDER_ENTITY_PAYMENT_PASSW_FIELD'),
			),
			'USER' => array(
				'data_type' => '\Mlife\Asz\UserTable',
				'reference' => array('=this.USERID' => 'ref.UID'),
				'join_type' => "LEFT",
			),
			'ADDSTAT' => array(
				'data_type' => '\Mlife\Asz\OrderstatusTable',
				'reference' => array('=this.STATUS' => 'ref.ID'),
				'join_type' => "LEFT",
			),
			'ADDPAY' => array(
				'data_type' => '\Mlife\Asz\PaysystemTable',
				'reference' => array('=this.PAY_ID' => 'ref.ID'),
				'join_type' => "LEFT",
			),
			'ADDDELIVERY' => array(
				'data_type' => '\Mlife\Asz\DeliveryTable',
				'reference' => array('=this.DELIVERY_ID' => 'ref.ID'),
				'join_type' => "LEFT",
			),
			
		);
	}
	
	public static function getMapAdmin()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_ORDER_ENTITY_ID_FIELD'),
			),
			'SITEID' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateSiteId'),
				'title' => Loc::getMessage('MLIFE_ASZ_ORDER_ENTITY_SITEID_FIELD'),
			),
			'STATUS' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_ORDER_ENTITY_STATUS_FIELD'),
			),
			'PAY_ID' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_ORDER_ENTITY_PAY_ID_FIELD'),
			),
			'DELIVERY_ID' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_ORDER_ENTITY_DELIVERY_ID_FIELD'),
			),
			'PRICE' => array(
				'data_type' => 'string',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_ORDER_ENTITY_PRICE_FIELD'),
			),
			'DISCOUNT' => array(
				'data_type' => 'string',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_ORDER_ENTITY_DISCOUNT_FIELD'),
			),
			'TAX' => array(
				'data_type' => 'string',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_ORDER_ENTITY_TAX_FIELD'),
			),
			'DELIVERY_PRICE' => array(
				'data_type' => 'string',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_ORDER_ENTITY_DELIVERY_PRICE_FIELD'),
			),
			'PAYMENT_PRICE' => array(
				'data_type' => 'string',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_ORDER_ENTITY_PAYMENT_PRICE_FIELD'),
			),
			'DATE' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_ORDER_ENTITY_DATE_FIELD'),
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