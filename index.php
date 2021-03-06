<?php
session_start();
ob_start();
DEFINE("ROOT_DIRECTORY", dirname( __FILE__ ) ."/" );
require_once(ROOT_DIRECTORY.'registry/registry.class.php');
$registry = new Registry();
include(FRAMEWORK_PATH . 'framework.conf');
try {
	$registry->createAndStoreObject('mysqldb', $frameConfigs['mysqldb']);
	$registry->createAndStoreObject( 'template', $frameConfigs['template']);
	$registry->createAndStoreObject('urlprocessor', $frameConfigs['urlprocessor']);
	$registry->createAndStoreObject('authenticate', $frameConfigs['authenticate']);
	$registry->createAndStoreObject('passwordHash', 'hash');
	$registry->createAndStoreObject('errorlog', $frameConfigs['errorlog']);
	$registry->createAndStoreObject('mail', $frameConfigs['mail']);
	include(FRAMEWORK_PATH . 'db.conf');
	$connectionId = $registry->getObject('db')->newConnection( $configs['db_host_sn'], $configs['db_user_sn'], $configs['db_pass_sn'], $configs['db_name_sn']);
	$registry->storeSetting( $connectionId, 'dbactive' );
	$settingsSQL = "SELECT * FROM settings";
	try {
		$registry->getObject('db')->executeQuery($settingsSQL);
	}catch(storeException $e) {
		echo '<pre>';
		print_r($e->completeException());
	}
	while( $setting = $registry->getObject('db')->getRows() )
	{
		$registry->storeSetting( $setting['value'], $setting['key'] );
	}
	$registry->getObject('url')->getURLData();
	$registry->getObject('template')->getPage()->addTag('error','');
	$registry->getObject('template')->getPage()->addPPTag('siteurl',$registry->getSetting('siteurl'));
	$registry->getObject('auth')->checkForAuthentication();
	$controller = $registry->getObject('url')->getURLBit(0);
	$controllers = array();
	$controllersSQL = "SELECT * FROM controllers WHERE active=1";
	$registry->getObject('db')->executeQuery( $controllersSQL );
	while( $cttrlr = $registry->getObject('db')->getRows() )
	{
		$controllers[] = $cttrlr['controller'];
	}
	$query = "SELECT service FROM services WHERE status = '1'";
	$cacheId = $registry->getObject('db')->cacheQuery($query);
	$registry->getObject('template')->getPage()->addTag('service', ['SQL', $cacheId]);
	$query = "SELECT service FROM services WHERE status = '1'";
	$cacheId = $registry->getObject('db')->executeQuery($query);
	while($row = $registry->getObject('db')->getRows($cacheId)){	
		$query = "SELECT DISTINCT region FROM {$row['service']}";
		$cacheId = $registry->getObject('db')->cacheQuery($query);
		$registry->getObject('template')->getPage()->addTag("region{$row['service']}", ['SQL', $cacheId]);
	}
	$registry->getObject('template')->getPage()->addTag('urlData', json_encode($registry->getObject('url')->getURLBits()));
	if( in_array( $controller, $controllers ) )
	{
		require_once( ROOT_DIRECTORY . 'controller/' . $controller . '/controller.php');
		$controllerInc = $controller.'controller';
		$controller = new $controllerInc( $registry, true );

	}
	else {
		$controller = 'home';
		require_once( ROOT_DIRECTORY . 'controller/' . $controller . '/controller.php');
		$controllerInc = $controller.'controller';
		$controller = new $controllerInc( $registry, true );
	}
	if(!$registry->getObject('auth')->isloggedIn()) {
		unset($_SESSION['session_user_uid']);
		$_SESSION['session_user_uid'] = $registry->getObject('hash')->create_hash(crypt(uniqid(),uniqid()));
	}
	$registry->getObject('template')->parseOutput();
	print preg_replace( '/\t|\s\s/','', $registry->getObject('template')->getPage()->getContentToPrint());
} catch(storeException $e) {
	$registry->getObject('log')->logError($e->completeException());
	print "Sorry There wass some problem please check errorlog.txt for more information.";
}
?>
