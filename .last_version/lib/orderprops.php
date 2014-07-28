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

class OrderpropsTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}
	
	public static function getTableName()
	{
		return 'mlife_asz_order_props';
	}
	
	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_ORDERPROPSENTITY_ID_FIELD'),
			),
			'SITEID' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateSiteId'),
				'title' => Loc::getMessage('MLIFE_ASZ_ORDERPROPSENTITY_SITEID_FIELD'),
			),
			'NAME' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateName'),
				'title' => Loc::getMessage('MLIFE_ASZ_ORDERPROPSENTITY_NAME_FIELD'),
			),
			'CODE' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateCodeN'),
				'title' => Loc::getMessage('MLIFE_ASZ_ORDERPROPSENTITY_CODE_FIELD'),
			),
			'TYPE' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateCodeN'),
				'title' => Loc::getMessage('MLIFE_ASZ_ORDERPROPSENTITY_TYPE_FIELD'),
			),
			'SORT' => array(
				'data_type' => 'string',
				'required' => false,
				'title' => Loc::getMessage('MLIFE_ASZ_ORDERPROPSENTITY_SORT_FIELD'),
			),
			'ACTIVE' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateCode'),
				'title' => Loc::getMessage('MLIFE_ASZ_ORDERPROPSENTITY_ACTIVE_FIELD'),
			),
			'REQ' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateCode'),
				'title' => Loc::getMessage('MLIFE_ASZ_ORDERPROPSENTITY_REQ_FIELD'),
			),
			'DELIVERY' => array(
				'data_type' => 'string',
				'required' => true,
				'validation' => array(__CLASS__, 'validateCode'),
				'title' => Loc::getMessage('MLIFE_ASZ_ORDERPROPSENTITY_DELIVERY_FIELD'),
			),
			'PARAMS' => array(
				'data_type' => 'string',
				'required' => false,
				'validation' => array(__CLASS__, 'validateParams'),
				'title' => Loc::getMessage('MLIFE_ASZ_ORDERPROPSENTITY_PARAMS_FIELD'),
			),
			'VAL' => array(
				'data_type' => '\Mlife\Asz\OrderpropsValuesTable',
				'reference' => array('=this.ID' => 'ref.PROPID'),
				'join_type' => "left"
			),
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
	
	public static function validateAction()
	{
		return array(
			new Entity\Validator\Length(null, 255),
		);
	}
	
	public static function validateParams()
	{
		return array(
			new Entity\Validator\Length(null, 19000),
		);
	}
	
	public static function validateCode()
	{
		return array(
			new Validator\LengthFix(1),
		);
	}
	public static function validateCodeN()
	{
		return array(
			new Entity\Validator\Length(null, 50),
		);
	}

}