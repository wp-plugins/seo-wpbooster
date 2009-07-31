<?
add_action( 'admin_menu', 'submenu_admina_wpbooster' ); //dodajemy guzik do belki z opcjami
function submenu_admina_wpbooster() {add_options_page('WPBOOSTER', 'WPBOOSTER', 10, __FILE__, 'konfigurator_wtyczki_wpbooster');} //definicja guzika
function konfigurator_wtyczki_wpbooster() { //funkcja ktora zawiera panel admina wtyczki
global $wpdb; //dajemy wtyczce dostep do obslugi bazy danych
global $klient; //dajemy wtyczne dostep do soap'a
echo '<div class="wrap">'; //poczatek kontenera z trescia

if ($_POST['mode'] == 'linkinloop') { //jesli ma dodac blog do systemu

//echo 'zmienia ustawienia link_in_loop i wyswietla komunikat -'.$_POST['linkinloopval'];

echo '<div id="message" class="updated fade"><p>Zapisano zmiane ustawien wyswietlania linkow z systemu</p></div>';

$link_in_loop_change = $klient->call('linkinloop_change', array('url' => get_bloginfo('url'),'value' => $_POST['linkinloopval'])); //zmienia ustawienie link in loop
// TUTAJ FUNKCJA CHANGE LINK IN LOOP I KOMUNIKAT
}
elseif ($_POST['mode'] == 'addblog') { //jesli ma dodac blog do systemu
$addvalid = $klient->call('addblog', array('user' => $_POST['user'], 'pass' => $_POST['pass'], 'domain' => get_bloginfo('url'), 'cat' => $_POST['category'])); //wysyla dane bloga

//echo $addvalid;
//print_r($_SERVER);
//echo $_SERVER['DOCUMENT_ROOT'];
/*
$password = 'tester';
require_once( $_SERVER['DOCUMENT_ROOT'].'/swl/wp-includes/class-phpass.php');
$wp_hasher = new PasswordHash(8, TRUE);
echo $wp_hasher->HashPassword($password);
*/

/*
$password = 'tester';
$hash = '$P$Bgu5Ik0Q7bgI9dlQEZCDfcnk9Rcl3N1';
require_once( $_SERVER['DOCUMENT_ROOT'].'/swl/wp-includes/class-phpass.php');
$wp_hasher = new PasswordHash(8, TRUE);
$check = $wp_hasher->CheckPassword($password, $hash);
echo $check;
*/
//echo $addvalid;


if ($addvalid == '0') echo '<div id="message" class="updated fade"><p>podane dane sa nieprawidlowe!</p></div>'; //komunikat
if ($addvalid == '1') echo '<div id="message" class="updated fade"><p>blog zostal dodany do systemu</p></div>'; //komunikat
}
$odpowiedz = $klient->call('czyjestwsystemie', array('adresbloga' => get_bloginfo('url'))); //sprawdza czy blog jest w systemie
if (!empty($odpowiedz)) {
echo '<br />Blog znajduje sie w systemie WPBooster.'; //komunikat
echo '<br /><br />';
//echo 'Czy linki z systemu WPBooster maja ukazywac sie pod kazdym postem czy wolisz sam umiescic funkcje wyswietlajaca linki w szablonie?<br />';

$is_link_in_loop = $klient->call('linkinloop_check', array('url' => get_bloginfo('url'))); //sprawdza czy linki maja byc w petli

if ($is_link_in_loop == '1') {
echo 'Linki z systemu pojawiaja sie teraz automatycznie w kazdym poscie.';

echo '<form name="put_definiction" method="post" action="options-general.php?page=wpbooster/wpbadmin.php">
<input type="hidden" value="linkinloop" name="mode" />
<input type="hidden" value="0" name="linkinloopval" />
<input type="submit" style="border: 1px solid rgb(0, 0, 0); cursor: pointer; background-color: rgb(247, 247, 247); margin-top: 5px;" value=" &nbsp; Wlacz reczne dodawanie linkow do szablonu &nbsp; " name="mode_log"/>
</form>';

?><?
}
else {
echo 'Linki z systemu mozesz teraz recznie umiescic w dowolnym miejscu w szablonie.';

echo '<form name="put_definiction" method="post" action="options-general.php?page=wpbooster/wpbadmin.php">
<input type="hidden" value="linkinloop" name="mode" />
<input type="hidden" value="1" name="linkinloopval" />
<input type="submit" style="border: 1px solid rgb(0, 0, 0); cursor: pointer; background-color: rgb(247, 247, 247); margin-top: 5px;" value=" &nbsp; Wlacz automatyczne dodawanie linkow do kazdego postu &nbsp; " name="mode_log"/>
</form>';

?><?
echo 'Jesli chcesz zeby linki z systemu wpbooster ukazaly sie na Twoim blogu wklej w dowolnym miejscu szablonu (procz petli) kod:<br />&lt;?php echo wpb_getlinks(); ?&gt;<br />';
}


$divname = $klient->call('divname_check', array('url' => get_bloginfo('url'))); //sprawdza czy linki maja byc w petli
echo '<br>Kontener w ktorym znajduja sie linki z systemu ma id = "'.$divname.'"';
echo '<br>Mozesz zdefiniowac jego wyglad w arkuszu stylu piszac:<br>div#'.$divname.' {<br />...<br />}<br />';


}
else { //jesli bloga nie ma w systemie to wyswietla formularz
echo '<br />Bloga nie ma w systemie WPBooster. Podaj swoja nazwe uzytkownika i haslo aby dodac bloga do systemu.<br>';
echo '
<form name="put_definiction" method="post" action="options-general.php?page=wpbooster/wpbadmin.php">
<input type="text" style="border: 1px solid rgb(0, 0, 0); width: 175px; background-color: rgb(247, 247, 247); margin-top: 5px;" value="" name="user" />
(nazwa uzytkownika)<br/>
<input type="password" style="border: 1px solid rgb(0, 0, 0); width: 175px; background-color: rgb(247, 247, 247); margin-top: 5px;" value="" name="pass" />
(haslo)<br/>';

$listakategorii = $klient->call('getcategory', array('yyyy' => 'yyyy')); //sprawdza czy blog jest w systemie

//echo substr($listakategorii, 1);

$caty = explode('|', substr($listakategorii, 1));

echo '<br />Wybierz kategorie bloga:<br /><select name="category">';
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

echo '</div>'; //koniec kontenera z trescia
}
?>