<?php #۞ #
if (stristr($_SERVER["PHP_SELF"],'_strings.php')) {
  include '_security.php';
  Header("Location: $redirect");Die();
}

$banned_ips = sql_array($tblbannedips,"","ip");

if (!isset($array_lang))
$array_lang = sql_array($tblenum,"WHERE enumstatut='Y' AND enumwhat='lang' ","enumtype");

if (!in_array($lg,$array_lang))
$lg = $default_lg;
    
$array_months = array();
$array_days = array();

$sql = @mysqli_query("SELECT * FROM $tblstring WHERE stringlang='$lg' ORDER BY stringtype,stringtitle ASC ");// AND stringtype='general' ");//(stringtype='general' OR stringtype='jour' OR stringtype='mois')//
if (!$sql) {
  if (@file_exists($getcwd.$up.$safedir.'_full_strings_'.$lg.'.php'))
    require_once $getcwd.$up.$safedir.'_full_strings_'.$lg.'.php';
  else {
    //echo 'NO DB SET';
    //Die();
    require_once $up.$urladmin.'defaults/_full_strings.php';
  }
} else {
  if ((@mysqli_num_rows($sql) == 0) && ($lg != $default_lg))
  $sql = @mysqli_query("SELECT * FROM $tblstring WHERE stringlang='$default_lg' ORDER BY stringtype,stringtitle ASC ");
  while($row = @mysqli_fetch_array($sql)) {
  	if ($row['stringtype'] == 'general') ${$row['stringtitle']."String"} = $row['stringentry'];
  	if ($row['stringtype'] == 'help') ${$row['stringtitle']."HelpString"} = $row['stringentry'];
  	if ($row['stringtype'] == 'sujet') $array_subject[$row['stringtitle']] = $row['stringentry'];
  	if ($row['stringtype'] == 'jour') $array_days[($row['stringtitle']-1)] = $row['stringentry'];//$row['stringtitle']."=>".$row['stringentry'];
  	if ($row['stringtype'] == 'mois') $array_months[($row['stringtitle']-1)] = $row['stringentry'];//$row['stringtitle']."=>".$row['stringentry'];
    if ($row['stringtype'] == 'lang') $array_langs[$row['stringtitle']] = $row['stringentry'];
  }
}

  ksort($array_months);//reorder months by key (FIX wrong order given before of 1 10 11 12 2 3 4...)

  $coname = $codename.' > '.$confinfoString;
  $slogan = $confinfoString;
  
  if (!isset($banner_src))
  $banner_src = 'images/banner_'.$client.'.jpg';
  
  $cologo_img = '<img src="'.$mainurl.$banner_src.'" '.show_img_attr($banner_src).' title="'.$slogan.'" alt="'.$slogan.'" border="0" class="logo" />';
  $cologo = '<a href="'.$local.'">'.$cologo_img.'</a>';
  
if (($lg == $default_lg) || in_array($lg,$array_lang)) {
// insert if needed ($lg == "ar") || ($lg == "es") || ($lg == "fr") || ($lg == "it")
//	if (in_array($lg,array('en','de','jp','zh'))) {
    if (!$array_months) $array_months = array('January','February','March','April','May','June','July','August','September','October','November','December');
    if (!$array_days) $array_days = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
    $meta_desc = "Media Vince brings your content to the frontrow in a multilingual style using web development, imaging and audio video editing with flexibility and proven standard of quality.";
    $meta_keyw = "media, web, design, development, webdesigner, developer, flash, php, ruby, rails, ror, imaging, multimedia, publishing, quality, service, traduction, translation, multilingual, english, french, italian, audio, video, picture";
  	$lang = sql_stringit('lang', $lg);
  	if (!isset($array_subject))
  	$array_subject = array("My Next Project","WebDev","Web Marketing","Translation","Other"); // !!do not htmlencode will be done upon call
  	$pgver = 'This page is in '.$lang.$n;
    $text_styleB = '<a class="button" href="javascript:void(0);" title="Bold text" onclick="insertTag(\'b\')"><b>Bold</b></a>';
    $text_styleI = '<a class="button" href="javascript:void(0);" title="Italic text" onclick="insertTag(\'i\')"><i>Italic</i></a>';
    $text_styleU = '<a class="button" href="javascript:void(0);" title="Underlined text" onclick="insertTag(\'u\')"><u>Underlined</u></a>';
    $text_styleHr = '<a class="button" href="javascript:void(0);" title="Horizontal Rule" onclick="insertTag(\'---\')">Horizontal Rule</a>';
    $text_styleLink = '<a class="button" href="javascript:void(0);" title="Insert a web page link" onclick="insertLink()">Web page Link</a>';
    $text_styleEmail = '<a class="button" href="javascript:void(0);" title="insert an email adress" onclick="insertEmail()">Em@il</a>';
    $text_styleDiv = '<a class="button" href="javascript:void(0);" title="Insert a division" onclick="insertDiv()">Division</a>';
    $text_style = 'Text Styles:<br /> ';
    $max_filesizeString = 'size max. '.$max_filesize_str.'B';
    $max_filesideString = ', the image will be resized by ratio to '.$max_width.'px.';
//	}
	if ($lg == 'ar') {
    if (!$array_months) $array_months = array('January','February','March','April','May','June','July','August','September','October','November','December');
    if (!$array_days) $array_days = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
    $meta_desc = "Media Vince brings your content to the frontrow in a multilingual style using web development, imaging and audio video editing with flexibility and proven standard of quality.";
    $meta_keyw = "media, web, design, development, webdesigner, developer, flash, php, ruby, rails, ror, imaging, multimedia, publishing, quality, service, traduction, translation, multilingual, english, french, italian, audio, video, picture";
		$lang = "Arabic";
  	if (!isset($array_subject))
  	$array_subject = array("My Next Project","WebDev","Web Marketing","Translation","Other"); // !!do not htmlencode will be done upon call
		$pgver = 'This page is in '.$lang.$n;
    $text_styleB = '<a class="button" href="javascript:void(0);" title="Bold text" onclick="insertTag(\'b\')"><b>Bold</b></a>';
    $text_styleI = '<a class="button" href="javascript:void(0);" title="Italic text" onclick="insertTag(\'i\')"><i>Italic</i></a>';
    $text_styleU = '<a class="button" href="javascript:void(0);" title="Underlined text" onclick="insertTag(\'u\')"><u>Underlined</u></a>';
    $text_styleHr = '<a class="button" href="javascript:void(0);" title="Horizontal Rule" onclick="insertTag(\'---\')">Horizontal Rule</a>';
    $text_styleLink = '<a class="button" href="javascript:void(0);" title="Insert a web page link" onclick="insertLink()">Web page Link</a>';
    $text_styleEmail = '<a class="button" href="javascript:void(0);" title="insert an email adress" onclick="insertEmail()">Em@il</a>';
    $text_styleDiv = '<a class="button" href="javascript:void(0);" title="Insert a division" onclick="insertDiv()">Division</a>';
    $text_style = 'Text Styles:<br /> ';
    $max_filesizeString = 'size max. '.$max_filesize_str.'B';
    $max_filesideString = ', the image will be resized by ratio to '.$max_width.'px.';
	}
	if ($lg == 'es') {
    if (!$array_months) $array_months = array('Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre');
    if (!$array_days) $array_days = array('Domenica','Lunedì','Martedì','Mercoledì','Giovedì','Venerdì','Sabato');
    $meta_desc = "Media Vince imporre il tuo contenuto su internet con uno stile multi linguale usando snelli processi di qualit&agrave; nei campi della realizzazione e dello sviluppo di siti web con immagine, audio e video.";
    $meta_keyw = "italia, roma, media, web, design, sviluppo, webdesigner, developer, flash, php, mysql, ruby, rails, ror, multimedia, pubblicazione, qualit&agrave;, servizio, traduzione, multilingue, inglese, francese, italiano, immagine, audio, video";
		$lang = "Espa&ntilde;ol";
  	if (!isset($array_subject))
  	$array_subject = array("Il Mio Prossimo Progetto","Sviluppo WEB","Marketing WEB","Traduzione","Altre"); // !!do not htmlencode will be done upon call
		$pgver = '(italian copied) Estas paginas &egrave;s in '.$lang.$n;
    $text_styleB = '<a class="button" href="javascript:void(0);" title="testo Grasso" onclick="insertTag(\'g\')"><b>Grasso</b></a>';
    $text_styleI = '<a class="button" href="javascript:void(0);" title="testo Italico" onclick="insertTag(\'i\')"><i>Italico</i></a>';
    $text_styleU = '<a class="button" href="javascript:void(0);" title="testo sottolineato" onclick="insertTag(\'s\')"><u>Sottolineato</u></a>';
    $text_styleHr = '<a class="button" href="javascript:void(0);" title="Linea orizontale" onclick="insertTag(\'---\')">Linea orizontale</a>';
    $text_styleLink = '<a class="button" href="javascript:void(0);" title="Inserimento di un collegamento verso una pagina web" onclick="insertLink()">Collega web</a>';
    $text_styleEmail = '<a class="button" href="javascript:void(0);" title="Inserimento di un indirizzo email" onclick="insertEmail()">Em@il</a>';
    $text_styleDiv = '<a class="button" href="javascript:void(0);" title="Inserimento di una divisione" onclick="insertDiv()">Divisione</a>';
    $text_style = 'Stili di testo:<br /> ';
    $max_filesizeString = 'taglia mass. '.$max_filesize_str.'B';
    $max_filesideString = ', l&#039;immagine sar&agrave; ridimenzionta a '.$max_width.'px di lato.';
	}
	if ($lg == 'it') {
    if (!$array_months) $array_months = array('Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre');
    if (!$array_days) $array_days = array('Domenica','Lunedì','Martedì','Mercoledì','Giovedì','Venerdì','Sabato');
    $meta_desc = "Media Vince imporre il tuo contenuto su internet con uno stile multi linguale usando snelli processi di qualit&agrave; nei campi della realizzazione e dello sviluppo di siti web con immagine, audio e video.";
    $meta_keyw = "italia, roma, media, web, design, sviluppo, webdesigner, developer, flash, php, mysql, ruby, rails, ror, multimedia, pubblicazione, qualit&agrave;, servizio, traduzione, multilingue, inglese, francese, italiano, immagine, audio, video";
		$lang = "Italiano";
  	if (!isset($array_subject))
  	$array_subject = array("Il Mio Prossimo Progetto","Sviluppo WEB","Marketing WEB","Traduzione","Altre"); // !!do not htmlencode will be done upon call
		$pgver = 'Questa pagina &egrave; in '.$lang.$n;
    $text_styleB = '<a class="button" href="javascript:void(0);" title="testo Grasso" onclick="insertTag(\'g\')"><b>Grasso</b></a>';
    $text_styleI = '<a class="button" href="javascript:void(0);" title="testo Italico" onclick="insertTag(\'i\')"><i>Italico</i></a>';
    $text_styleU = '<a class="button" href="javascript:void(0);" title="testo sottolineato" onclick="insertTag(\'s\')"><u>Sottolineato</u></a>';
    $text_styleHr = '<a class="button" href="javascript:void(0);" title="Linea orizontale" onclick="insertTag(\'---\')">Linea orizzontale</a>';
    $text_styleLink = '<a class="button" href="javascript:void(0);" title="Inserimento di un collegamento verso una pagina web" onclick="insertLink()">Collega web</a>';
    $text_styleEmail = '<a class="button" href="javascript:void(0);" title="Inserimento di un indirizzo email" onclick="insertEmail()">Em@il</a>';
    $text_styleDiv = '<a class="button" href="javascript:void(0);" title="Inserimento di una divisione" onclick="insertDiv()">Divisione</a>';
    $text_style = 'Stili di testo:<br /> ';
    $max_filesizeString = 'taglia mass. '.$max_filesize_str.'B';
    $max_filesideString = ', l&#039;immagine sar&agrave; ridimenzionta a '.$max_width.'px di lato.';
	}
	if ($lg == 'fr') {
    if (!$array_months) $array_months = array('Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre');
    if (!$array_days) $array_days = array('Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi');
    $meta_desc = "Media Vince impose votre activit&eacute; sur le WEB dans un style multilingue avec des standards de qualit&eacute; s&ucirc;rs du domaine multimedia tels que audio, vid&eacute; et imagerie.";
    $meta_keyw = "media, web, design, d&egrave;veloppement, webdesigner, d&eacute;veloppeur, flash, php, ruby, rails, ror, imagerie, multimedia, publication, qualit&eacute;, service, traduction, multilingue, anglais, fran&ccedil;ais, italien, audio, vid&eacute;o";
  	$lang = "Fran&ccedil;ais";
  	if (!isset($array_subject))
  	$array_subject = array("Mon prochain projet","Développement WEB","Marketing WEB","Traduction","Autre"); // !!do not htmlencode will be done upon call
  	$pgver = 'Cette page est en '.$lang.$n;
    $text_styleB = '<a class="button" href="javascript:void(0);" title="texte Gras" onclick="insertTag(\'g\')"><b>Gras</b></a>';
    $text_styleI = '<a class="button" href="javascript:void(0);" title="texte Italique" onclick="insertTag(\'i\')"><i>Italique</i></a>';
    $text_styleU = '<a class="button" href="javascript:void(0);" title="texte Soulign&eacute;" onclick="insertTag(\'s\')"><u>Soulign&eacute;</u></a>';
    $text_styleHr = '<a class="button" href="javascript:void(0);" title="Ligne Horizontale" onclick="insertTag(\'---\')">Ligne Horizontale</a>';
    $text_styleLink = '<a class="button" href="javascript:void(0);" title="Insertion d&#039;un lien web" onclick="insertLink()">Lien web</a>';
    $text_styleEmail = '<a class="button" href="javascript:void(0);" title="Insertion d&#039;une adresse email" onclick="insertEmail()">Em@il</a>';
    $text_styleDiv = '<a class="button" href="javascript:void(0);" title="Insertion d&#039;une division" onclick="insertDiv()">Division</a>';
    $text_style = 'Styles de texte:<br /> ';
    $max_filesizeString = 'taille max. '.$max_filesize_str.'B';
    $max_filesideString = ', l&#039;image sera redimensionn&eacute;e &agrave; '.$max_width.'px de c&ocirc;t&eacute;.';
	}
}

$default_desc_keyw = sql_get($tblcont,"WHERE contpg='1' AND contlang='$lg' ","contmetadesc,contmetakeyw");
if ($default_desc_keyw[0] != '.') {
  $meta_desc = $default_desc_keyw[0];
  $meta_keyw = $default_desc_keyw[1];
}
/*
$sql_enum_subject = sql_array($tblenum,"WHERE enumstatut='Y' AND enumwhat='sujet' ","enumtitre");
if (isset($sql_enum_subject[0]) && ($sql_enum_subject[0] != '')) {
  $array_subject = array();
  foreach($sql_enum_subject as $key)
    $array_subject[] = sql_getone($tblstring,"WHERE stringtype='sujet' AND stringlang='$lg' AND stringtitle='$key' ","stringentry");
}
*/
$text_style .= $text_styleB.' | '.$text_styleI.' | '.$text_styleU.' | '.$text_styleHr.' | '.$text_styleLink.' | '.$text_styleEmail.' | '.$text_styleDiv;

if ($tinyMCE === true)
	$text_style = '';

if (count($array_lang) > 1)
  $text_style = '<span style="width:100%;text-align:center;color:red;font-weight:bold;">'.$pgver.'</span><br />'.$text_style;

$error_delete = '<font color="Red"> * '.$enregistrementString.' '.$nonString.' '.$effaceString.' ! * </font>';
$error_exists = '<font color="Red"> * '.$enregistrementString.' '.($lg=='en'?$dejaString.' exists':$dejaexistantString).' ! * </font>';
$error_invmiss = '<font color="Red"> * '.$enregistrementString.' '.$invalideString.' '.$ouString.' '.$manquantString.' ! * </font>';
$error_inv = '<font color="Red"> * '.$enregistrementString.' '.$invalideString.' ! * </font>';
$error_request = '<font color="Red"> * '.$erreurString.' '.$derequeteString.' ! * </font>';
$error_update = '<font color="Red"> * '.$enregistrementString.' '.$nonString.' '.$modifieString.' ! * </font>';

if (!isset($erroraccesprivString))
  $erroraccesprivString = ucfirst($pleaseString).' '.$entrerString.' '.$pouraccesprivString;
$error_accesspriv = '<font color="Red"> * '.$erroraccesprivString.' ! * </font>';

if (!isset($copyrightnoticeString))
$copyrightnoticeString = 'Webdevelopment by <a href="http://www.mediavince.com" target="_blank">www.mediavince.com</a>';

// private
$idString = (isset($numidString)?$numidString:"id");
$ridString = (isset($numidString)?"default $numidString":"rid");
$titleString = (isset($titreString)?$titreString:"title");
$entryString = (isset($descriptionString)?$descriptionString:"entry");
$descString = $entryString;
$utilString = (isset($nomutilString)?$nomutilString:"util");
$userString = (isset($nomutilString)?$nomutilString:"user");
// membreHelpString inherits userHelpString unless specified
$membreHelpString = (isset($membreHelpString)?$membreHelpString:$userHelpString);
$respString = (isset($responsablescientifiqueString)?$responsablescientifiqueString:"resp");
$privString = (isset($privilegeString)?$privilegeString:"priv");
$passString = (isset($motdepasseString)?$motdepasseString:"pass");
$langString = (isset($langueString)?$langueString:"lang");
$lgString = (isset($langString)?$langString:"lg");
$documentString = (isset($docString)?$docString:"document");
$stringString = (isset($texteString)?$texteString:"string");
//

