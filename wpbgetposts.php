<?php
include('../../../wp-config.php');
if ( ($_GET['last'] == '0') || ($_GET['last']) ) {
	$last_post_id = $_GET['last'] + '0';
	$posty = $wpdb->get_results("
		SELECT post_content, id, post_name, post_date FROM $wpdb->posts
		WHERE post_status = 'publish' AND post_type = 'post' AND id > $last_post_id AND LENGTH(post_content) > 100
		ORDER BY id
		LIMIT 10
	");
	$return_posts[] = 15.9;
	foreach ($posty as $post) {
		$content_bez_html = strip_tags($post->post_content);
		$badphrase = array('&gt;', '&lt;', '&nbsp;', ';', '&', '!', '?', ',', '.', '"', '\'', "\n", "\r");
		$content_bez_krzakow = str_replace($badphrase, '', $content_bez_html);
		$stopwordsy = array(' aczkolwiek ', ' ależ ', ' bardziej ', ' bardzo ', ' byli ', ' była ', ' było ', ' były ', ' będzie ', ' będą ', ' cali ', ' cała ', ' cały ', ' cokolwiek ', ' czasami ', ' czasem ', ' czemu ' , ' czyli ' , ' dlaczego ', ' dlatego ', ' gdyż ', ' gdzie ', ' gdziekolwiek ', ' gdzieś ', ' inna ', ' inne ', ' inny ', ' innych ', ' jakaś ', ' jakichś ', ' jakie ', ' jakiś ', ' jakiż ', ' jakkolwiek ', ' jako ', ' jakoś ', ' jednak ', ' jednakże ', ' jego ', ' jest ', ' jeszcze ', ' jeśli ', ' jeżeli ', ' kiedy ', ' kilka ', ' kimś ', ' ktokolwiek ', ' ktoś ', ' która ', ' które ', ' którego ', ' której ', ' który ', ' których ', ' którym ', ' którzy ', 'lecz ', ' mają ', ' mimo ', ' między ', ' mnie ', ' mogą ', ' moim ' , ' może ' , ' możliwe ' , ' można ' , ' musi ' , ' naszego ' , ' naszych ' , ' natomiast ' , ' nawet ' , ' nich ' , ' nigdy ' , ' obok ' , ' około ' , ' oraz ' , ' pana ' , ' pani ' , ' podczas ' , ' pomimo ' , ' ponad ' , ' ponieważ ' , ' powinien ' , ' powinna ' , ' powinni ' , ' powinno ' , ' poza ' , ' prawie ' , ' przecież ' , ' przed ' , ' przede ' , ' przez ' , ' przy ' , ' roku ' , ' również ' , ' sobie ' , ' sobą ' , ' sposób ' , ' swoje ' , ' taka ' , ' taki ' , ' takie ' , ' także ' , ' tego ' , ' teraz ' , ' tobie ' , ' toteż ' , ' trzeba ' , ' twoim ' , ' twoja ' , ' twoje ' , ' twym ' , ' twój ' , ' tych ' , ' tylko ', ' według ' , ' wiele ' , ' wielu ' , ' więc ' , ' więcej ' , ' wszyscy ' , ' wszystkich ' , ' wszystkie ' , ' wszystkim ' , ' wszystko ' , ' właśnie ' , ' zapewne ' , ' zawsze ' , ' znowu ' , ' znów ' , ' został ' , ' żadna ' , ' żadne ' , ' żadnych ' , ' żeby ' );
		$content_bez_stop_words = str_replace($stopwordsy, ' ', $content_bez_krzakow);
		$content_regexp = trim(preg_replace("% ..?.? %i"," ", $content_bez_stop_words));
		$m_title = trim(str_replace(array('"', '`', "'"), '', strip_tags(get_the_title($post->id))));
		$return_posts[] = array($post->id, $content_regexp, get_permalink($post->id), $m_title, $post->post_date);
	}
	echo '|wpbooster|'.base64_encode(serialize($return_posts)).'|wpbooster|';
}
?>