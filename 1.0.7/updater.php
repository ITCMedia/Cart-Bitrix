<?
if(IsModuleInstalled("mlife.asz")) {
	$updater->CopyFiles('install/components/mlife', 'components/mlife');
	$updater->CopyFiles('install/wizards/mlife', 'wizards/mlife');
}
?>