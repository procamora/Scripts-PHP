<?php
include('Net/SSH2.php');

define("userubnt", "ubnt");
define("passubnt", "PASSWORD");
define("userdb", "root");
define("passdb", "PASSWORD");

//hay que mejorarla y hacer que de dias horas minutos y segundos
//la funcion esta sacada de internet
function makeTimeFromSeconds( $total_seconds ) {
	$horas              = floor ( $total_seconds / 3600 );
	$minutes            = ( ( $total_seconds / 60 ) % 60 );
	$seconds            = ( $total_seconds % 60 );
	 
	$time['horas']      = str_pad( $horas, 2, "0", STR_PAD_LEFT );
	$time['minutes']    = str_pad( $minutes, 2, "0", STR_PAD_LEFT );
	$time['seconds']    = str_pad( $seconds, 2, "0", STR_PAD_LEFT );
	 
	$time               = implode( ':', $time );
	 
	return $time;
}


function ConectSSHAp($server) {
	global $mca, $wstalist;
	$ssh = new Net_SSH2($server);
	if (!$ssh->login(userubnt, passubnt))
		print("Login Failed $server");

	$mca = $ssh->exec("mca-status");
	$wstalist = $ssh->exec("wstalist | grep lastip | cut -d '\"' -f 4");
}


function ConectSSHStation($server) {
	$ssh = new Net_SSH2($server);
	if (!$ssh->login(userubnt, passubnt))
		print("Login Failed $server");

	$mca = $ssh->exec("mca-status");
	return $mca;
}


$IP = array(
	"172.1.1.1", "172.1.1.2", "172.1.1.3", "172.1.1.4",
	"172.1.2.1", "172.1.2.2", "172.1.2.3", "172.1.2.4",
);


foreach($IP as $antena) {
	ConectSSHAp($antena);

	$data = explode("\n",$mca);
	$clientes = explode("\n",$wstalist);

	$essid = explode('=', $data[6]);
	$signal = explode('=', $data[8]);
	$ccq = explode('=', $data[10]);
	$uptime = explode('=', $data[11]);
	$noise = explode('=', $data[9]);
	$connections = explode('=', $data[4]);

	$ccq[1] = number_format($ccq[1]/10, 2);
	$uptime[1] = makeTimeFromSeconds($uptime[1]);


	$mysql = mysql_connect('localhost', userdb, passdb);
	if(!$mysql)
		die('<br />No pudo conectarse: ' . mysql_error());
	if(!mysql_select_db('LogAntenas',$mysql))
		print "<br />No pudo seleccionarse la base de datos: ". mysql_error();

	
	mysql_query("INSERT INTO Paneles(SSID, Signal, CCQ, Uptime, Noise, Connections) 
	VALUES (\"$essid[1]\", $signal[1], $ccq[1], \"$uptime[1]\", $noise[1], $connections[1]) ",$mysql);

	//ahora me conecto a los clientes y saco los valores y los meto en la base de datos
	foreach($clientes as $station) {
		$info = ConectSSHStation("$station");
		$info = explode("\n",$info);

		$essid = explode('=', $info[6]);
		$signal = explode('=', $info[8]);
		$ccq = explode('=', $info[10]);
		$uptime = explode('=', $info[11]);
		$noise = explode('=', $info[9]);
		$name = explode('=', $info[0]);
		$name = explode(',', $name[1]);

		$ccq[1] = number_format($ccq[1]/10, 2);
		$uptime[1] = makeTimeFromSeconds($uptime[1]);

		mysql_query("INSERT INTO Stations(SSID, Signal, CCQ, Uptime, Noise, Host)
		VALUES (\"$essid[1]\", $signal[1], $ccq[1], \"$uptime[1]\", $noise[1], \"$name[0]\") ",$mysql);
	}
}

mysql_close($mysql);

?>


