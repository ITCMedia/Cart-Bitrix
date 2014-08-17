<?
if(IsModuleInstalled("mlife.asz")) {
	
	$updater->CopyFiles('install/components/mlife', 'components/mlife');
	$updater->CopyFiles('install/wizards/mlife', 'wizards/mlife');
	$updater->CopyFiles('install/admin', 'admin');
	
	//удаление всех обработчиков в модуле
	UnRegisterModuleDependences("mlife.asz", "BasketOnBeforeAdd", "mlife.asz", '\Mlife\Asz\DiscountHandlers', "BasketOnBeforeAdd");
	UnRegisterModuleDependences("mlife.asz", "BasketOnBeforeUpdate", "mlife.asz", '\Mlife\Asz\DiscountHandlers', "BasketOnBeforeUpdate");
	UnRegisterModuleDependences("mlife.asz", "OrderOnAfterAdd", "mlife.asz", '\Mlife\Asz\Handlers', "OrderOnAfterAdd");
	UnRegisterModuleDependences("mlife.asz", "OrderOnBeforeUpdate", "mlife.asz", '\Mlife\Asz\Handlers', "OrderOnBeforeUpdate");
	UnRegisterModuleDependences("mlife.asz", "OrderOnAfterUpdate", "mlife.asz", '\Mlife\Asz\Handlers', "OrderOnAfterUpdate");
	UnRegisterModuleDependences("mlife.asz", "OrderOnAfterDelete", "mlife.asz", '\Mlife\Asz\Handlers', "OrderOnAfterDelete");
	UnRegisterModuleDependences("mlife.asz", "OrderOnBeforeDelete", "mlife.asz", '\Mlife\Asz\Handlers', "OrderOnBeforeDelete");
	
	UnRegisterModuleDependences("iblock", "OnIBlockPropertyBuildList", "mlife.asz", '\Mlife\Asz\Properties\AszMagazine', "GetUserTypeDescription");
	UnRegisterModuleDependences("main", "OnBuildGlobalMenu", "mlife.asz", '\Mlife\Asz\Handlers', "OnBuildGlobalMenu");
	UnRegisterModuleDependences("main", "OnAdminTabControlBegin", "mlife.asz", '\Mlife\Asz\Handlers', "OnAdminTabControlBegin");
	UnRegisterModuleDependences("iblock", "OnAfterIBlockElementAdd", "mlife.asz", '\Mlife\Asz\Handlers', "OnAfterIBlockElementAdd");
	UnRegisterModuleDependences("iblock", "OnAfterIBlockElementUpdate", "mlife.asz", '\Mlife\Asz\Handlers', "OnAfterIBlockElementAdd");
	UnRegisterModuleDependences("iblock", "OnAfterIBlockElementDelete", "mlife.asz", '\Mlife\Asz\Handlers', "OnAfterIBlockElementDelete");
	
	//перерегистрация обработчиков
	$eventManager = \Bitrix\Main\EventManager::getInstance();
	$eventManager->registerEventHandler("mlife.asz", "BasketOnBeforeAdd", "mlife.asz", '\Mlife\Asz\DiscountHandlers', "BasketOnBeforeAdd");
	$eventManager->registerEventHandler("mlife.asz", "BasketOnBeforeUpdate", "mlife.asz", '\Mlife\Asz\DiscountHandlers', "BasketOnBeforeUpdate");
	$eventManager->registerEventHandler("mlife.asz", "OrderOnAfterAdd", "mlife.asz", '\Mlife\Asz\Handlers', "OrderOnAfterAdd");
	$eventManager->registerEventHandler("mlife.asz", "OrderOnBeforeUpdate", "mlife.asz", '\Mlife\Asz\Handlers', "OrderOnBeforeUpdate");
	$eventManager->registerEventHandler("mlife.asz", "OrderOnAfterUpdate", "mlife.asz", '\Mlife\Asz\Handlers', "OrderOnAfterUpdate");
	$eventManager->registerEventHandler("mlife.asz", "OrderOnAfterDelete", "mlife.asz", '\Mlife\Asz\Handlers', "OrderOnAfterDelete");
	$eventManager->registerEventHandler("mlife.asz", "OrderOnBeforeDelete", "mlife.asz", '\Mlife\Asz\Handlers', "OrderOnBeforeDelete");
	
	$eventManager->registerEventHandlerCompatible("iblock", "OnIBlockPropertyBuildList", "mlife.asz", '\Mlife\Asz\Properties\AszMagazine', "GetUserTypeDescription");
	$eventManager->registerEventHandlerCompatible("main", "OnBuildGlobalMenu", "mlife.asz", '\Mlife\Asz\Handlers', "OnBuildGlobalMenu");
	$eventManager->registerEventHandlerCompatible("main", "OnAdminTabControlBegin", "mlife.asz", '\Mlife\Asz\Handlers', "OnAdminTabControlBegin");
	$eventManager->registerEventHandlerCompatible("iblock", "OnAfterIBlockElementAdd", "mlife.asz", '\Mlife\Asz\Handlers', "OnAfterIBlockElementAdd");
	$eventManager->registerEventHandlerCompatible("iblock", "OnAfterIBlockElementUpdate", "mlife.asz", '\Mlife\Asz\Handlers', "OnAfterIBlockElementAdd");
	$eventManager->registerEventHandlerCompatible("iblock", "OnAfterIBlockElementDelete", "mlife.asz", '\Mlife\Asz\Handlers', "OnAfterIBlockElementDelete");
	
	//установка новых таблиц
	global $DB, $DBType, $APPLICATION;
	$DB->RunSQLBatch($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mlife.asz/install/db/".strtolower($DB->type)."/install.sql");
	
}
?>