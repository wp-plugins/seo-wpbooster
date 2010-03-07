<?php
/*
Plugin Name: SEO WPBooster
Plugin URI: http://wpbooster.pl/
Description: Automatyczna wtyczka wymiany reklamy w postach pomiędzy blogami użytkowników.
Author: WPBooster Development Team
Version: 1.5
Author URI: http://wpbooster.pl/
*/

include('wpbadmin.php');

if(!get_option('wpbooster_linkinloop')) {
	$data = urlencode(base64_encode(get_bloginfo('url')));
	$link_in_loop = file_get_contents('http://wpbooster.pl/swlserwer/wpadmin/linkinloop_check.php?blogurl='.$data);
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