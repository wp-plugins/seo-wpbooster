<?php
include('../../../wp-config.php');
if ($_GET['cache']) {
	$cacheser = base64_decode($_GET['cache']);
	$cache = unserialize($cacheser);
	if ($cache) {
		if(!get_option('wpbooster_cache')) {
			$response['cacheexist'] = false;
			add_option('wpbooster_cache',$cacheser);
		}
		else {
			$response['cacheexist'] = true;
			$update_index = 0;
			$update_tab = unserialize(get_option('wpbooster_cache'));
			foreach ($cache as $cache_key => $cache_val) {
				$update_tab[$cache_key] = $cache_val;
				$update_index++;
			}
			update_option('wpbooster_cache', serialize($update_tab));
			$response['cachecounter'] = $update_index;
		}
		echo base64_encode(serialize($response));
	}
}
elseif ($_GET['check_cache']) {
	$cache_tab = unserialize(get_option('wpbooster_cache'));
	print_r($cache_tab);
}
elseif ($_GET['check_cache_encode']) {
	$cache_ser = get_option('wpbooster_cache');
	echo '|wpbooster|'.base64_encode($cache_ser).'|wpbooster|';
}
?>