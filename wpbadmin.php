<?
add_action( 'admin_menu', 'submenu_admina_wpbooster' );
function submenu_admina_wpbooster() {add_options_page('WPBOOSTER', 'WPBOOSTER', 10, __FILE__, 'konfigurator_wtyczki_wpbooster');}
function konfigurator_wtyczki_wpbooster() {
	global $wpdb;
	echo '<div class="wrap">';
	if ($_POST['mode'] == 'linkinloop') {
		echo '<div id="message" class="updated fade"><p>Zapisano zmianę ustawień wyświetlania linków z systemu</p></div>';
		update_option('wpbooster_linkinloop', $_POST['linkinloopval']);
		$data_tab = array(get_bloginfo('url'), $_POST['linkinloopval']);
		$data = urlencode(base64_encode(serialize($data_tab)));
		file_get_contents('http://wpbooster.pl/swlserwer/wpadmin/linkinloop.php?data='.$data);
	}
	elseif ($_POST['mode'] == 'addblog') {
		if ($_POST['category'] == '0') echo '<div id="message" class="updated fade"><p>Proszę wybrać kategorię</p></div>';
		else {
			$data_tab = array($_POST['user'], $_POST['pass'], get_bloginfo('url'), $_POST['category']);
			$data = urlencode(base64_encode(serialize($data_tab)));
			$addvalid = file_get_contents('http://wpbooster.pl/swlserwer/wpadmin/addblog.php?data='.$data);
			if ($addvalid == 'yes') echo '<div id="message" class="updated fade"><p>Blog został dodany do systemu</p></div>';
			elseif ($addvalid == 'no') echo '<div id="message" class="updated fade"><p>Podane dane są nieprawidłowe!</p></div>';
			elseif ($addvalid == 'isset') echo '<div id="message" class="updated fade"><p>Inny użytkownik dodał tą domenę do systemu. Prosimy o kontakt (formularz na stronie wpbooster.pl)</p></div>';
		}
	}
	$zakodowany_url = urlencode(base64_encode(get_bloginfo('url')));
	$issetblog = file_get_contents('http://wpbooster.pl/swlserwer/wpadmin/issetblog.php?blogurl='.$zakodowany_url);
	if ($issetblog == 'yes') {
		echo '<br />Blog znajduje się w systemie WPBooster.<br /><br />';
		if (get_option('wpbooster_linkinloop') == '1') {
			echo 'Linki z systemu pojawiają się teraz automatycznie w każdym poście.
			<form name="put_definiction" method="post" action="options-general.php?page=seo-wpbooster/wpbadmin.php">
			<input type="hidden" value="linkinloop" name="mode" />
			<input type="hidden" value="0" name="linkinloopval" />
			<input type="submit" style="border: 1px solid rgb(0, 0, 0); cursor: pointer; background-color: rgb(247, 247, 247); margin-top: 5px;" value=" &nbsp; Wlacz reczne dodawanie linkow do szablonu &nbsp; " name="mode_log"/>
			</form>';
		}
		else {
			echo 'Linki z systemu możesz teraz ręcznie umieścić w dowolnym miejscu w szablonie.
			<form name="put_definiction" method="post" action="options-general.php?page=seo-wpbooster/wpbadmin.php">
			<input type="hidden" value="linkinloop" name="mode" />
			<input type="hidden" value="1" name="linkinloopval" />
			<input type="submit" style="border: 1px solid rgb(0, 0, 0); cursor: pointer; background-color: rgb(247, 247, 247); margin-top: 5px;" value=" &nbsp; Wlacz automatyczne dodawanie linkow do kazdego postu &nbsp; " name="mode_log"/>
			</form>
			Jeśli chcesz żeby linki z systemu wpbooster ukazały się na Twoim blogu wklej w dowolnym miejscu szablonu (prócz pętli) kod:<br />&lt;?php echo wpb_getlinks(); ?&gt;<br />';
		}
		$divname = file_get_contents('http://wpbooster.pl/swlserwer/wpadmin/divname.php?blogurl='.$zakodowany_url);
		echo '<br>Kontener w którym znajdują się linki z systemu ma id = "'.$divname.'"
		<br>Możesz zdefiniować jego wygląd w arkuszu stylu pisząc:<br>div#'.$divname.' {<br />...<br />}<br />';
	}
	elseif ($issetblog == 'no') {
		echo '<br />Bloga nie ma w systemie WPBooster. Podaj swoją nazwę użytkownika i hasło aby dodać bloga do systemu.<br />
		<form name="put_definiction" method="post" action="options-general.php?page=seo-wpbooster/wpbadmin.php">
		<input type="text" style="border: 1px solid rgb(0, 0, 0); width: 175px; background-color: rgb(247, 247, 247); margin-top: 5px;" value="" name="user" />
		(nazwa uzytkownika)<br/>
		<input type="password" style="border: 1px solid rgb(0, 0, 0); width: 175px; background-color: rgb(247, 247, 247); margin-top: 5px;" value="" name="pass" />
		(haslo)<br/>';
		$listakategorii = unserialize(base64_decode(file_get_contents('http://wpbooster.pl/swlserwer/wpadmin/listcategory.php')));
		echo '<br /><select name="category"><option value="0" selected="selected">Wybierz kategorię bloga</option>';
		foreach ($listakategorii as $onecat) {
		    echo '<option value="'.$onecat[0].'">'.$onecat[1].'</option>';
		}
		echo '</select><br /><br />
		<input type="hidden" value="addblog" name="mode" />
		<input type="submit" style="border: 1px solid rgb(0, 0, 0); cursor: pointer; background-color: rgb(247, 247, 247); margin-top: 5px;" value=" &nbsp; Dodaj blog do systemu &nbsp; " name="mode_log"/>
		</form>';
	}
	else {
		echo '<br />Błąd wymiany danych pomiędzy wtyczką i systemem.<br />
		|'.$issetblog.'|<br />
		Prosimy o kontakt z administracją systemu wpbooster. Prosimy do wiadomości załączyć kopię lub screen tej strony.<br />
		Przepraszamy za utrudnienia.<br />';
	}
	echo '</div>';
}
?>