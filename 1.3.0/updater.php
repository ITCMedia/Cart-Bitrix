<?
if(IsModuleInstalled("mlife.asz")) {
	$updater->CopyFiles('install/components/mlife', 'components/mlife');
	$updater->CopyFiles('install/wizards/mlife', 'wizards/mlife');
	$updater->CopyFiles('install/admin', 'admin');
	
	//установка новых таблиц
	global $DB, $DBType, $APPLICATION;
	$DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mlife.asz/install/db/".strtolower($DB->type)."/install.sql");
	
}