<?php
$wp_booster_magic_key = '321111111111';
require_once('soap_a.php');
require_once('soap_c.php');
require_once('soap_e.php');
require_once('soap_g.php');
require_once('soap_h.php');
require_once('soap_i.php');
$wsdl = 'http://wpbooster.pl/swlserwer/core.php?wsdl';
$klient = new wpbooster_nusoap_client($wsdl, 'wsdl');
include('../../../wp-config.php');
$idbloga = $klient->call('czyjestwsystemie', array('adresbloga' => get_bloginfo('url')));
if (isset($idbloga)) {
	$cachetab = $klient->call('getlinkscache', array('blogid' => $idbloga));
	$cacheser = base64_decode($cachetab);
	$cache = unserialize($cacheser);
	if ($cache) {
		if(!get_option('wpbooster_cache')) {
			add_option('wpbooster_cache',$cacheser);
		}
		else {
			$update_tab = unserialize(get_option('wpbooster_cache'));
			foreach ($cache as $klucz => $wartosc) {
				$update_tab[$klucz] = $wartosc;
			}
			update_option('wpbooster_cache', serialize($update_tab));
		}
	}
}
?>