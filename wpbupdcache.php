<?php
include('../../../wp-config.php');
$cacheser = base64_decode($_GET['cache']);
$cache = unserialize($cacheser);
if ($cache) {
	if(!get_option('wpbooster_cache')) {
		add_option('wpbooster_cache',$cacheser);
	}
	else {
		$update_tab = unserialize(get_option('wpbooster_cache'));
		foreach ($cache as $cache_key => $cache_val) {
			$update_tab[$cache_key] = $cache_val;
		}
		update_option('wpbooster_cache', serialize($update_tab));
	}
}
?>