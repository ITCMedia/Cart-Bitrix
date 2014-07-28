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

class OrderpropsValuesTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}
	
	public static function getTableName()
	{
		return 'mlife_asz_order_propsvalues';
	}
	
	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
				'title' => Loc::getMessage('MLIFE_ASZ_ORDERPROPSVALUESENTITY_ID_FIELD'),
			),
			'UID' => array(
				'data_type' => 'integer',
				'title' => Loc::getMessage('MLIFE_ASZ_ORDERPROPSVALUESENTITY_UID_FIELD'),
			),
			'PROPID' => array(
				'data_type' => 'integer',
				'title' => Loc::getMessage('MLIFE_ASZ_ORDERPROPSVALUESENTITY_PROPID_FIELD'),
			),
			'VALUE' => array(
				'data_type' => 'string',
				'title' => Loc::getMessage('MLIFE_ASZ_ORDERPROPSVALUESENTITY_VALUE_FIELD'),
			),
			
		);
	}

}