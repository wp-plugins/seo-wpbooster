<?php
/*
Plugin Name: SEO WPBooster
Plugin URI: http://wpbooster.pl/
Description: Automatyczna wtyczka wymiany reklamy w postach pomiędzy blogami użytkowników.
Author: WPBooster Development Team
Version: 1.4
Author URI: http://wpbooster.pl/
*/

$wp_booster_magic_key = '321111111111';
require_once('soap_a.php');
require_once('soap_c.php');
require_once('soap_e.php');
require_once('soap_g.php');
require_once('soap_h.php');
require_once('soap_i.php');
$wsdl = 'http://wpbooster.pl/swlserwer/core.php?wsdl';
$klient = new wpbooster_nusoap_client($wsdl, 'wsdl');
include('wpbadmin.php');

if(!get_option('wpbooster_linkinloop')) {
	$link_in_loop = $klient->call('linkinloop_check', array('url' => get_bloginfo('url')));
	add_option('wpbooster_linkinloop', $link_in_loop);
}

function links_from_wpb() {
	global $wp_query;
	$PostId = $wp_query->post->ID;
	$cache_tab = unserialize(get_option('wpbooster_cache'));
	return $cache_tab[$PostId];
}

function wpb_getlinks() {
    if ( (get_option('wpbooster_linkinloop') == '0') && (is_single()) ) return links_from_wpb();
}

function link_in_loop($content) {
    if ( (get_option('wpbooster_linkinloop') == '1') && (is_single()) ) $dodane = links_from_wpb();
    return $content.$dodane;
}

add_filter('the_content', 'link_in_loop');

?>