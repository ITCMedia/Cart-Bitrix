<?
if(IsModuleInstalled("mlife.asz")) {
	$updater->CopyFiles('install/components', 'components');
	$updater->CopyFiles('install/wizards', 'wizards');
}