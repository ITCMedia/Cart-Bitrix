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

class ElementSectionTable extends Entity\DataManager
{
	/**
	 * Returns path to the file which contains definition of the class.
	 *
	 * @return string
	 */
	public static function getFilePath()
	{
		return __FILE__;
	}

	/**
	 * Returns DB table name for entity
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'b_iblock_section_element';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
		return array(
			'IBLOCK_SECTION_ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'title' => Loc::getMessage('IBLOCK_SECTION_ELEMENT_ENTITY_IBLOCK_SECTION_ID_FIELD'),
			),
			'IBLOCK_ELEMENT_ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'title' => Loc::getMessage('IBLOCK_SECTION_ELEMENT_ENTITY_IBLOCK_ELEMENT_ID_FIELD'),
			),
		);
	}
}
