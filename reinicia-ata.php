#!/usr/bin/php
<?php

exit();

set_include_path(implode(PATH_SEPARATOR, array('.', '/usr/share/php', get_include_path())));
require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();

$autoloader->registerNamespace('Zend_');
$autoloader->registerNamespace('Concentre_');

$atas = array('192.1.3.185','192.1.3.186');

foreach ($atas as $ata) {

	$options = array(
			'host' => $ata,
	);

	$ping = new Concentre_Net_Ping_Udp($ata);
	$pingTime = $ping->start();

	
	if ($pingTime->getStatus() == -1) {
		continue;
	}
	
	echo 'rebooting ' . $ata;

	$cli = new Concentre_Telnet_Client($options);
	
	$cli->read('Password: ')
	         ->write('Nixus2011!')
		 ->read('GS>')
	         ->write("reboot");

	unset($telnet);

	echo ' [done]'.PHP_EOL;
} 
