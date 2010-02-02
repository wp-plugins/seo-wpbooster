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
	$maxid_from_blog = $klient->call('maxpostid', array('blogid' => $idbloga, 'v' => '14'));
	$posty = $wpdb->get_results("SELECT post_content, id, post_name, post_date FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' AND id > $maxid_from_blog AND LENGTH(post_content) > 100 LIMIT 0,100 ");
	$czy_zapisal_date_pierwszego = '0';
	foreach ($posty as $post) {
		$czyjest = $klient->call('issetpost', array('blogid' => $idbloga, 'postid' => $post->ID, 'magic' => $wp_booster_magic_key));
		if ( ($maxid_from_blog == '0') && ($czy_zapisal_date_pierwszego == '0') ) {
			$zapisz_data = $klient->call('firstpostdate', array('date' => $post->post_date, 'blogid' => $idbloga));
			$czy_zapisal_date_pierwszego = '1';
		}
		if ($czyjest == 'ok') {
			$content_bez_html = strip_tags($post->post_content);
			$badphrase = array('&gt;', '&lt;', '&nbsp;', ';', '&', '!', '?', ',', '.', '"', '\'', "\n", "\r");
			$content_bez_krzakow = str_replace($badphrase, '', $content_bez_html);
			$stopwordsy = array(' aczkolwiek ', ' ależ ', ' bardziej ', ' bardzo ', ' byli ', ' była ', ' było ', ' były ', ' będzie ', ' będą ', ' cali ', ' cała ', ' cały ', ' cokolwiek ', ' czasami ', ' czasem ', ' czemu ' , ' czyli ' , ' dlaczego ', ' dlatego ', ' gdyż ', ' gdzie ', ' gdziekolwiek ', ' gdzieś ', ' inna ', ' inne ', ' inny ', ' innych ', ' jakaś ', ' jakichś ', ' jakie ', ' jakiś ', ' jakiż ', ' jakkolwiek ', ' jako ', ' jakoś ', ' jednak ', ' jednakże ', ' jego ', ' jest ', ' jeszcze ', ' jeśli ', ' jeżeli ', ' kiedy ', ' kilka ', ' kimś ', ' ktokolwiek ', ' ktoś ', ' która ', ' które ', ' którego ', ' której ', ' który ', ' których ', ' którym ', ' którzy ', 'lecz ', ' mają ', ' mimo ', ' między ', ' mnie ', ' mogą ', ' moim ' , ' może ' , ' możliwe ' , ' można ' , ' musi ' , ' naszego ' , ' naszych ' , ' natomiast ' , ' nawet ' , ' nich ' , ' nigdy ' , ' obok ' , ' około ' , ' oraz ' , ' pana ' , ' pani ' , ' podczas ' , ' pomimo ' , ' ponad ' , ' ponieważ ' , ' powinien ' , ' powinna ' , ' powinni ' , ' powinno ' , ' poza ' , ' prawie ' , ' przecież ' , ' przed ' , ' przede ' , ' przez ' , ' przy ' , ' roku ' , ' również ' , ' sobie ' , ' sobą ' , ' sposób ' , ' swoje ' , ' taka ' , ' taki ' , ' takie ' , ' także ' , ' tego ' , ' teraz ' , ' tobie ' , ' toteż ' , ' trzeba ' , ' twoim ' , ' twoja ' , ' twoje ' , ' twym ' , ' twój ' , ' tych ' , ' tylko ', ' według ' , ' wiele ' , ' wielu ' , ' więc ' , ' więcej ' , ' wszyscy ' , ' wszystkich ' , ' wszystkie ' , ' wszystkim ' , ' wszystko ' , ' właśnie ' , ' zapewne ' , ' zawsze ' , ' znowu ' , ' znów ' , ' został ' , ' żadna ' , ' żadne ' , ' żadnych ' , ' żeby ' );
			$content_bez_stop_words = str_replace($stopwordsy, ' ', $content_bez_krzakow);
			$content_regexp = trim(preg_replace("% ..?.? %i"," ", $content_bez_stop_words));
			$m_title = trim(str_replace(array('"', '`', "'"), '', strip_tags(get_the_title($post->ID))));
			$klient->call('addpost', array('blogid' => $idbloga, 'postid' => $post->ID, 'post' => $content_regexp, 'url' => get_permalink($post->ID), 'title' => $m_title));
		}
	}
}
?>