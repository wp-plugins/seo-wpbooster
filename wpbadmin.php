<?
add_action( 'admin_menu', 'submenu_admina_wpbooster' );
function submenu_admina_wpbooster() {add_options_page('WPBOOSTER', 'WPBOOSTER', 10, __FILE__, 'konfigurator_wtyczki_wpbooster');}
function konfigurator_wtyczki_wpbooster() {
global $wpdb;
global $klient;
echo '<div class="wrap">';
if ($_POST['mode'] == 'linkinloop') {
	echo '<div id="message" class="updated fade"><p>Zapisano zmianę ustawień wyświetlania linków z systemu</p></div>';
	$link_in_loop_change = $klient->call('linkinloop_change', array('url' => get_bloginfo('url'),'value' => $_POST['linkinloopval']));
}
elseif ($_POST['mode'] == 'addblog') {
	$addvalid = $klient->call('addblog', array('user' => $_POST['user'], 'pass' => $_POST['pass'], 'domain' => get_bloginfo('url'), 'cat' => $_POST['category']));
	if ($addvalid == '0') echo '<div id="message" class="updated fade"><p>podane dane są nieprawidłowe!</p></div>';
	if ($addvalid == '1') echo '<div id="message" class="updated fade"><p>blog został dodany do systemu</p></div>';
}
$odpowiedz = $klient->call('czyjestwsystemie', array('adresbloga' => get_bloginfo('url')));
if (!empty($odpowiedz)) {
	echo '<br />Blog znajduje się w systemie WPBooster.';
	echo '<br /><br />';
	$is_link_in_loop = $klient->call('linkinloop_check', array('url' => get_bloginfo('url')));
	if ($is_link_in_loop == '1') {
		echo 'Linki z systemu pojawiają się teraz automatycznie w każdym poście.';
		echo '<form name="put_definiction" method="post" action="options-general.php?page=seo-wpbooster/wpbadmin.php">
		<input type="hidden" value="linkinloop" name="mode" />
		<input type="hidden" value="0" name="linkinloopval" />
		<input type="submit" style="border: 1px solid rgb(0, 0, 0); cursor: pointer; background-color: rgb(247, 247, 247); margin-top: 5px;" value=" &nbsp; Wlacz reczne dodawanie linkow do szablonu &nbsp; " name="mode_log"/>
		</form>';
	}
	else {
		echo 'Linki z systemu możesz teraz ręcznie umieścić w dowolnym miejscu w szablonie.';
		echo '<form name="put_definiction" method="post" action="options-general.php?page=seo-wpbooster/wpbadmin.php">
		<input type="hidden" value="linkinloop" name="mode" />
		<input type="hidden" value="1" name="linkinloopval" />
		<input type="submit" style="border: 1px solid rgb(0, 0, 0); cursor: pointer; background-color: rgb(247, 247, 247); margin-top: 5px;" value=" &nbsp; Wlacz automatyczne dodawanie linkow do kazdego postu &nbsp; " name="mode_log"/>
		</form>';
		echo 'Jeśli chcesz żeby linki z systemu wpbooster ukazały się na Twoim blogu wklej w dowolnym miejscu szablonu (prócz pętli) kod:<br />&lt;?php echo wpb_getlinks(); ?&gt;<br />';
	}
	$divname = $klient->call('divname_check', array('url' => get_bloginfo('url')));
	echo '<br>Kontener w którym znajdują się linki z systemu ma id = "'.$divname.'"';
	echo '<br>Możesz zdefiniować jego wygląd w arkuszu stylu pisząc:<br>div#'.$divname.' {<br />...<br />}<br />';
}
else {
	echo '<br />Bloga nie ma w systemie WPBooster. Podaj swoją nazwę użytkownika i hasło aby dodać bloga do systemu.<br>';
	echo '<form name="put_definiction" method="post" action="options-general.php?page=seo-wpbooster/wpbadmin.php">
	<input type="text" style="border: 1px solid rgb(0, 0, 0); width: 175px; background-color: rgb(247, 247, 247); margin-top: 5px;" value="" name="user" />
	(nazwa uzytkownika)<br/>
	<input type="password" style="border: 1px solid rgb(0, 0, 0); width: 175px; background-color: rgb(247, 247, 247); margin-top: 5px;" value="" name="pass" />
	(haslo)<br/>';
	$listakategorii = $klient->call('getcategory', array('yyyy' => 'yyyy')); //sprawdza czy blog jest w systemie
	$caty = explode('|', substr($listakategorii, 1));
	echo '<br />Wybierz kategorię bloga:<br /><select name="category">';
	foreach ($caty as $onecat) {
	    $kawalek = explode('.', $onecat);
	    echo '<option value="'.$kawalek[0].'">'.$kawalek[1].'</option>';
	}
	echo '</select><br /><br />';
	echo '
	<input type="hidden" value="addblog" name="mode" />
	<input type="submit" style="border: 1px solid rgb(0, 0, 0); cursor: pointer; background-color: rgb(247, 247, 247); margin-top: 5px;" value=" &nbsp; Dodaj blog do systemu &nbsp; " name="mode_log"/>
	</form>
	';
}

echo '</div>';
}
?>