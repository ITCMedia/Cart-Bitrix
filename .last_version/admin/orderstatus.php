<?php
/**
 * Bitrix Framework
 * @package    Bitrix
 * @subpackage mlife.asz
 * @copyright  2014 Zahalski Andrew
 */

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

CModule::IncludeModule("mlife.asz");
use Bitrix\Main\Localization\Loc;
use Mlife\Asz;
Loc::loadMessages(__FILE__);

require_once("check_right.php");

$listTableId = "tbl_mlife_asz_order_status";

$oSort = new CAdminSorting($listTableId, "ID", "ASC");
$arOrder = (strtoupper($by) === "ID"? array($by => $order): array($by => $order, "ID" => "ASC"));

$adminList = new CAdminList($listTableId, $oSort);

// ��������� ��������� � ��������� ��������
if(($arID = $adminList->GroupAction()) && $POST_RIGHT=="W")
{
	if($_REQUEST['action_target']=='selected')
	{
		$rsData = Asz\OrderStatusTable::getList(
			array(
				'order' => $arOrder,
				'select' => array('ID'),
			)
		);
		while($arRes = $rsData->Fetch())
		  $arID[] = $arRes['ID'];
	}
	
	if($_REQUEST['action']=="delete") {
		foreach($arID as $ID)
		{
			if(strlen($ID)<=0)
				continue;
				$ID = IntVal($ID);
				
			$res = Asz\OrderStatusTable::delete(array("ID"=>$ID));
		}
	}
	
}

$arFilter = array();
if($FilterSiteId) {
	$arFilter["SITEID"] = $FilterSiteId;
}

$ASZStatus = Asz\OrderStatusTable::getList(
	array(
		'order' => $arOrder,
		'filter' => $arFilter,
	)
);

$ASZStatus = new CAdminResult($ASZStatus, $listTableId);
$ASZStatus->NavStart();

$adminList->NavText($ASZStatus->GetNavPrint(Loc::getMessage("MLIFE_ASZ_OSLIST_NAV")));

$cols = Asz\OrderStatusTable::getMap();
$colHeaders = array();

foreach ($cols as $colId => $col)
{
	$tmpAr = array(
		"id" => $colId,
		"content" => $col["title"],
		"sort" => $colId,
		"default" => true,
	);
	$colHeaders[] = $tmpAr;
}
$adminList->AddHeaders($colHeaders);

$visibleHeaderColumns = $adminList->GetVisibleHeaderColumns();
$arUsersCache = array();

while ($arRes = $ASZStatus->GetNext())
{
	$row =& $adminList->AddRow($arRes["ID"], $arRes);
	$row->AddCheckField("ACTIVE", false);
	$arActions = array();
	$arActions[] = array(
		"ICON" => "delete",
		"TEXT" => Loc::getMessage("MLIFE_ASZ_OSLIST_MENU_DELETE"),
		"TITLE" => Loc::getMessage("MLIFE_ASZ_OSLIST_MENU_DELETE"),
		"ACTION" => "if(confirm('".GetMessageJS("MLIFE_ASZ_OSLIST_MENU_DELETE_CONF")."')) ".$adminList->ActionDoGroup($arRes["ID"], "delete"),
	);
	$arActions[] = array(
		"ICON"=>"edit",
		"DEFAULT"=>true,
		"TEXT"=>Loc::getMessage("MLIFE_ASZ_OSLIST_MENU_EDIT"),
		"TITLE"=>Loc::getMessage("MLIFE_ASZ_OSLIST_MENU_EDIT"),
		"ACTION"=>$adminList->ActionRedirect('mlife_asz_orderstatus_edit.php?ID='.$arRes["ID"].'&lang='.LANG)
		);
	$row->AddActions($arActions);
}

// actions buttins
$adminList->AddGroupActionTable(array(
	"delete" => Loc::getMessage("MLIFE_ASZ_OSLIST_MENU_DELETE"),
));

$adminList->AddFooter(
	array(
		array(
			"title" => Loc::getMessage("MAIN_ADMIN_LIST_SELECTED"),
			"value" => $ASZStatus->SelectedRowsCount()
		),
		array(
			"counter" => true,
			"title" => Loc::getMessage("MAIN_ADMIN_LIST_CHECKED"),
			"value" => "0"
		),
	)
);

//������ �� ������
$aContext = array(
  array(
    "TEXT"=>Loc::getMessage("MLIFE_ASZ_OSLIST_MENU_ADD"),
    "LINK"=>"mlife_asz_orderstatus_edit.php?lang=".LANG,
    "TITLE"=>Loc::getMessage("MLIFE_ASZ_OSLIST_MENU_ADD"),
    "ICON"=>"btn_new",
  ),
);

$adminList->AddAdminContextMenu($aContext);

$adminList->CheckListMode();

$APPLICATION->SetTitle(Loc::getMessage("MLIFE_ASZ_OSLIST_TITLE"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

?>
<?
$adminList->DisplayList();
?>
<?echo BeginNote();?>
<?echo Loc::getMessage("MLIFE_ASZ_OSLIST_NOTE")?>
<?echo EndNote();?>

<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>