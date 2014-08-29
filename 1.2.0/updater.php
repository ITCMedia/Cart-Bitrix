<?
if(IsModuleInstalled("mlife.asz")) {
	
	$updater->CopyFiles('install/wizards/mlife', 'wizards/mlife');
	$updater->CopyFiles('install/admin', 'admin');
	
}
?>