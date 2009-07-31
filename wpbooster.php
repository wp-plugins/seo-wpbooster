<?php
/*
Plugin Name: SEO WPBooster
Plugin URI: http://wpbooster.pl/
Description: Automatyczna wtyczka wymiany reklamy w postach pomiędzy blogami użytkowników.
Author: WPBooster Development Team
Version: 1.1
Author URI: http://wpbooster.pl/
*/

$wp_booster_magic_key = '321000123';
require_once('soap_a.php');
require_once('soap_c.php');
require_once('soap_e.php');
require_once('soap_g.php');
require_once('soap_h.php');
require_once('soap_i.php');
$wsdl = 'http://wpbooster.pl/swlserwer/core.php?wsdl';
$klient = new wpbooster_nusoap_client($wsdl, 'wsdl');
include('wpbadmin.php');
function wpb_main() {
    global $wpdb;
    global $klient;
    global $wp_booster_magic_key;
    $idbloga = $klient->call('czyjestwsystemie', array('adresbloga' => get_bloginfo('url')));
    if (isset($idbloga)) {
				$maxid_from_blog = $klient->call('maxpostid', array('blogid' => $idbloga));
        $posty = $wpdb->get_results("SELECT post_content, id, post_name, post_date FROM $wpdb->posts WHERE post_status = 'publish' AND id > $maxid_from_blog LIMIT 0,100 ");
        $czy_zapisal_date_pierwszego = '0';
        foreach ($posty as $post) {
            $czyjest = $klient->call('issetpost', array('blogid' => $idbloga, 'postid' => $post->id, 'magic' => $wp_booster_magic_key));
            if ( ($maxid_from_blog == '0') && ($czy_zapisal_date_pierwszego == '0') ) {
                $zapisz_data = $klient->call('firstpostdate', array('date' => $post->post_date, 'blogid' => $idbloga));
								$czy_zapisal_date_pierwszego = '1';
            }
            if ($czyjest == 'ok') {
                $content_bez_html = strip_tags($post->post_content);
                $badphrase = array('&gt;', '&lt;', '&nbsp;', ';', '&', '!', '?', ',', '.', '"', '\'', "\n", "\r");
                $content_bez_krzakow = str_replace($badphrase, '', $content_bez_html);
                $stopwordsy = array(' aczkolwiek ', ' ale� ', ' bardziej ', ' bardzo ', ' byli ', ' by�a ', ' by�o ', ' by�y ', ' b�dzie ', ' b�d� ', ' cali ', ' ca�a ', ' ca�y ', ' cokolwiek ', ' czasami ', ' czasem ', ' czemu ' , ' czyli ' , ' dlaczego ', ' dlatego ', ' gdy� ', ' gdzie ', ' gdziekolwiek ', ' gdzie� ', ' inna ', ' inne ', ' inny ', ' innych ', ' jaka� ', ' jakich� ', ' jakie ', ' jaki� ', ' jaki� ', ' jakkolwiek ', ' jako ', ' jako� ', ' jednak ', ' jednak�e ', ' jego ', ' jest ', ' jeszcze ', ' je�li ', ' je�eli ', ' kiedy ', ' kilka ', ' kim� ', ' ktokolwiek ', ' kto� ', ' kt�ra ', ' kt�re ', ' kt�rego ', ' kt�rej ', ' kt�ry ', ' kt�rych ', ' kt�rym ', ' kt�rzy ', 'lecz ', ' maj� ', ' mimo ', ' mi�dzy ', ' mnie ', ' mog� ', ' moim ' , ' mo�e ' , ' mo�liwe ' , ' mo�na ' , ' musi ' , ' naszego ' , ' naszych ' , ' natomiast ' , ' nawet ' , ' nich ' , ' nigdy ' , ' obok ' , ' oko�o ' , ' oraz ' , ' pana ' , ' pani ' , ' podczas ' , ' pomimo ' , ' ponad ' , ' poniewa� ' , ' powinien ' , ' powinna ' , ' powinni ' , ' powinno ' , ' poza ' , ' prawie ' , ' przecie� ' , ' przed ' , ' przede ' , ' przez ' , ' przy ' , ' roku ' , ' r�wnie� ' , ' sobie ' , ' sob� ' , ' spos�b ' , ' swoje ' , ' taka ' , ' taki ' , ' takie ' , ' tak�e ' , ' tego ' , ' teraz ' , ' tobie ' , ' tote� ' , ' trzeba ' , ' twoim ' , ' twoja ' , ' twoje ' , ' twym ' , ' tw�j ' , ' tych ' , ' tylko ', ' wed�ug ' , ' wiele ' , ' wielu ' , ' wi�c ' , ' wi�cej ' , ' wszyscy ' , ' wszystkich ' , ' wszystkie ' , ' wszystkim ' , ' wszystko ' , ' w�a�nie ' , ' zapewne ' , ' zawsze ' , ' znowu ' , ' zn�w ' , ' zosta� ' , ' �adna ' , ' �adne ' , ' �adnych ' , ' �eby ' );
                $content_bez_stop_words = str_replace($stopwordsy, ' ', $content_bez_krzakow);
                $content_regexp = trim(preg_replace("% ..?.? %i"," ", $content_bez_stop_words));
    						$klient->call('addpost', array('blogid' => $idbloga, 'postid' => $post->id, 'post' => $content_regexp, 'url' => get_permalink($post->id), 'title' => get_the_title($post->id)));
						}
        }
    }
}

function links_from_wpb() {
    global $wp_query;
    global $klient;
    $PostId = $wp_query->post->ID;
    $idbloga = $klient->call('czyjestwsystemie', array('adresbloga' => get_bloginfo('url')));
    return $klient->call('getlinks', array('postid' => $PostId, 'blogid' => $idbloga));
}

$is_link_in_loop = $klient->call('linkinloop_check', array('url' => get_bloginfo('url')));

function wpb_getlinks() {
    global $is_link_in_loop;
    if ($is_link_in_loop == '0') return links_from_wpb();
}

function link_in_loop($content) {
    global $is_link_in_loop;
    if ($is_link_in_loop == '1') $dodane = links_from_wpb();
    return $content.$dodane;
}

add_filter('the_content', 'link_in_loop');

add_action('wpb_regular_task', 'wpb_main');

function wpb_activate() {
    wp_schedule_event(time(), 'hourly', 'wpb_regular_task');
}

function wpb_deactivate() {
    wp_clear_scheduled_hook('wpb_regular_task');
}

register_activation_hook(__FILE__, 'wpb_activate' );

register_deactivation_hook(__FILE__, 'wpb_deactivate' );

?>