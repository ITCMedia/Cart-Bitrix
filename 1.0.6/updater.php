<?
if(IsModuleInstalled("mlife.asz")) {
	$updater->CopyFiles('install/tools', 'tools');
	$updater->CopyFiles('install/components/mlife', 'components/mlife');
}
?>