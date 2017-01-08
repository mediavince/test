<?php #۞ #
/**
 * process and sanitize all requests
 */
if (!get_magic_quotes_gpc()) {
	$in = array(&$_GET, &$_POST, &$_COOKIE);
	while (list($k,$v) = each($in)) {
		foreach ($v as $key => $val) {
			if (!is_array($val)) {
				$in[$k][$key] = addslashes($val);
				continue;
			}
			$in[] =& $in[$k][$key];
		}
	}
	unset($in);
}
##############################################################################################

if (!isset($_SERVER["HTTP_HOST"]))
Die('Please try a standard browser!');

if (@function_exists('date_default_timezone_set'))
@date_default_timezone_set(@date_default_timezone_get());
// time set for php5
$now_time = time();

if (!isset($default_lg))
$default_lg = "en";

if (!isset($urladmin))
$urladmin = "admin/";
if (!isset($defaultsdir))
$defaultsdir = $urladmin."defaults/";
if (!isset($urlintro))// needs to be a dir
$urlintro = "blog/";
// File Directory where your files shall be uploaded
if (!isset($filedir))
$filedir = "content/";
// File Directory where your protected files and configs shall be uploaded
if (!isset($safedir))
$safedir = "SQL/";
// File Directory where your library files and modules shall be uploaded/created
if (!isset($libdir))
$libdir = "lib/";
// Templates Directory where your templates shall be uploaded as dir
if (!isset($tpldir))
$tpldir = "lib/templates/";
// rss dir
if (!isset($rssdir))
$rssdir = 'rss/';

if (!isset($urlsafe))
$urlsafe = "_download_/";
if (!isset($htaccess4sef))
$htaccess4sef = false;
if (!isset($type2htaccess))
$type2htaccess = false;

// setup to allow relative call to resources according whether logged in or not
if (!isset($up))
$up = "";
//PHP_SELF instead of REQUEST_URI to prevent possible recon in url not necessarily at first pos
if (stristr($_SERVER['PHP_SELF'],$urladmin)
|| (stristr($_SERVER['PHP_SELF'],$urlintro) && is_dir("../".$urlintro))
|| (stristr($_SERVER['PHP_SELF'],$rssdir) && is_dir("../".$rssdir))
) {
	$up = "../"	;
}

$null_time = '00:00:00';
if (@file_exists($up.$urladmin.'defaults/_config.php'))
	include $up.$urladmin.'defaults/_config.php'; // always included then overwritten with SQL one
else
	{Header("Location: index.php");Die();}

############################### _CONFIG
if (@file_exists($up.$safedir.'_config.php'))
include $up.$safedir.'_config.php';
### END INCLUDE _CONFIG

if (!isset($htaccess4sef) || ($htaccess4sef === false))
$urlsafe = $up.$urladmin.'_download.php';

$indexdotphpfroform = '';

$urlclient = $client.'/';
if ($client == '')	$urlclient = $client	;

#############################################
include $up.$urladmin.'defaults/_params.php';
if (@file_exists($up.$safedir.'_params.php'))
include $up.$safedir.'_params.php';

$redirect = $domain.$indexdotphpfroform;

if (stristr($_SERVER['PHP_SELF'],"_incdb.php"))
{Header("Location: $redirect");Die();}

if (!isset($root_writable)) {
	$root_writable = is_writable(($getcwd.$up==''?".":"$getcwd$up"));
} else {
	if (($root_writable === true) && !is_writable(($getcwd.$up==''?".":"$getcwd$up")))
	$root_writable = false;
}

$cookie_codename = str_replace('.', '-', $codename); // no dots allowed in cookie keys

$array_annee = array($this_annee => $dbname);
if (isset($annee) && array_key_exists($annee, $array_annee)) {
	$dbname = $array_annee["$annee"];
} else {
	$annee = $this_annee;
}

##############################################################################################
include '_incerror.php';
$connection = connect();
if (!$connection) {
	if (stristr($_SERVER['PHP_SELF'], $urladmin)
		&& @file_exists('_install.php')
		&& !@file_exists($up.$safedir.'_params.php')
	) {
		if (!stristr($_SERVER['PHP_SELF'], '_install.php'))
		{Header("Location: _install.php");Die();}
	} else {
// 		Header("Location: {$urladmin}_install.php");Die();
// 		 Header("Location: $redirect");
// 		Die('<a href="'.$redirect.'">'.ucfirst($client).' -> Error: please try again...</a>');
	}
}
##############################################################################################

#########
session_id();// ensures we are using the right session
session_start();
if (isset($_SESSION['mv_error']) || isset($_SESSION['mv_notice'])) {
	if (isset($_SESSION['mv_error']) && ($_SESSION['mv_error'] != '')) {
		$error = $_SESSION['mv_error'];
		$_SESSION['mv_error'] = '';
	}
	if (isset($_SESSION['mv_notice']) && ($_SESSION['mv_notice'] != '')) {
		$notice = $_SESSION['mv_notice'];
		$_SESSION['mv_notice'] = '';
	}
}
#########

$mainurl = $domain;
$mainurljs = addslashes($mainurl);

$local = $domain;
if (stristr($_SERVER['PHP_SELF'],$urladmin))
$local = $domain.$urladmin;
if (stristr($_SERVER['PHP_SELF'],$urlintro))
$local = $mainurl.$urlintro;

$extractfromphpself = strlen($url_mirror);

if (!isset($coinfo))
$coinfo = 'info'.$comail;
if (!isset($default_email))
$default_email = 'adresse&#064;changer.ctt';

$default_pass = $codename.date('Y');
$default_pass_md5 = md5($default_pass);

$default_array_modules = array();

if (!isset($array_modules))
$array_modules = array();
$array_modules = array_unique(array_merge($default_array_modules,$array_modules));
foreach($array_modules as $key)
if (!isset(${"mod_".$key})) ${"mod_".$key} = false;

$default_array_tables = array(
	"admin", "bannedips", "cont", "contdoc", "contphoto",
	"enum", "help", "newsletter", "string", "user", "htaccess",
//	"membre" ,"event" ,"forum", "comment",
);

if (!isset($array_tables))
$array_tables = array("");
$array_tables = array_unique(array_merge($default_array_tables,$array_tables));
foreach($array_tables as $key)
${"tbl".$key} = "_".$key;

######################### ADMIN MENU GEN DYNAMICALLY
if (!isset($array_basic_admin_menu))
$array_basic_admin_menu = array(
	"admin"			=>	"support.png",
	"user"			=>	"user.png",
	"newsletter"	=>	"massemail.png",
	"string"		=>	"addedit.png",
	"bannedip"		=>	"cancel_f2.png",
	"communications"=>	"massemail.png",
	"media"			=>	"mediamanager.png",
);
if (!isset($array_super_admin_menu))
$array_super_admin_menu = array(
	"template"	=>	"templatemanager.png",
	"module"	=>	"install.png",
	"analytics"	=>	"credits.png",
	"params"	=>	"config.png",
	"config"	=>	"cpanel.png",
);
// merge 2dim arrays
$array_admin_menu = array_merge($array_basic_admin_menu,$array_super_admin_menu);
// extract keys of 2dim arrays and list along array_modules
$array_keys_admin_menu = array_merge(array_keys($array_admin_menu),$array_modules);
// merge 3rd 2dim array
$array_admin_menu = array_merge($array_admin_menu,$array_modules);

######################### TEMPLATE
if (!isset($active_template)) {
	$active_template = "";
} else {
	$url_active_template = "$tpldir$active_template/";// ! slash is important
}
// call css, js and images resources from template if they exist
if (!isset($overwrite_stylesheet))
$stylesheet = '
<link rel="stylesheet" type="text/css" media="screen" href="'
	.$mainurl.($active_template!=''&&@file_exists($up.$url_active_template."main.css")?
	$url_active_template:$libdir).'main.css" />'
	.(@file_exists($up."favicon.gif")||@file_exists($up."images/favicon.gif")?'
<link rel="icon" type="image/gif" href="'
		.$mainurl.(@file_exists($up."favicon.gif")?'':'images/').'favicon.gif" />'
	:'
<link rel="icon" type="image/png" href="'
		.$mainurl.(@file_exists($up."favicon.png")?'':'images/').'favicon.png" />'
	).'
<!--[if lte IE 8]><link rel="stylesheet" type="text/css" title="IE8 Fixes" href="'
	.$mainurl.($active_template!=''&&@file_exists($up.$url_active_template."ie8fix.css")?
	$url_active_template:$libdir).'ie8fix.css" /><![endif]-->
<!--[if lte IE 7]><link rel="stylesheet" type="text/css" title="IE7 Fixes" href="'
	.$mainurl.($active_template!=''&&@file_exists($up.$url_active_template."ie7fix.css")?
	$url_active_template:$libdir).'ie7fix.css" /><![endif]-->
<!--[if lte IE 6]><link rel="stylesheet" type="text/css" title="IE5/6 Fixes" href="'
	.$mainurl.($active_template!=''&&@file_exists($up.$url_active_template."ie6fix.css")?
	$url_active_template:$libdir).'ie6fix.css" />
<script type="text/javascript" src="'.$mainurl.$libdir.'iepngfix_tilebg.js"></script>
<style type="text/css">
#header .logo,.contour .top-left,.contour .top-center,.contour .top-right,.contour-body-left,
.contour-body-middle,.contour .bottom-left,.contour .bottom-center,.contour .bottom-right,
#toplogin{
	behavior:url("'.$mainurl.$libdir.'iepngfix.htc");
}
</style>
<![endif]-->
<!--[if IE 5]><link rel="stylesheet" type="text/css" title="IE5.5 Fixes" href="'
	.$mainurl.($active_template!=''&&@file_exists($up.$url_active_template."ie5fix.css")?
	$url_active_template:$libdir).'ie5fix.css" /><![endif]-->
<!--[if gt IE 6]><link rel="shortcut icon" type="image/x-icon" href="'
	.$mainurl.(@file_exists($up."favicon.ico")?'':'images/').'favicon.ico" /><![endif]-->'
	.(empty($stylesheet)?'':$stylesheet);
//<!--[if gt IE 7]><link rel="stylesheet" type="text/css" title="IE8 Fixes" href="'
//	.$mainurl.$libdir.'ie8fix.css" /><![endif]-->
// 1 is the latest at the top but must revalidate more often,
// to avoid select the exact version you wish, check out jquery.org
$google_jquery_ver = (isset($google_jquery_ver)?$google_jquery_ver:'1');
// google_jquery enabled by default
$google_jquery = (isset($google_jquery)&&($google_jquery===false)?
		''
	:'
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/'
		.$google_jquery_ver.'/jquery.min.js"></script>'
);
$stylesheet .= $google_jquery;
//if ($google_jquery !== '')
$javascript = '
<script type="text/javascript" src="'
			.$mainurl.$libdir.'mvdyn_selectable_options.js"></script>'
			.(isset($javascript)?$javascript:'');

$_pre_css_bgimg = '
<div tabindex="0" act="0" role="button" class="bgimg-one">
	<div class="bgimg-two">
		<div class="bgimg-two-top">
		</div>
		<div class="bgimg-two-bot">';
$_post_css_bgimg = '
		</div>
	</div>
</div>';

if ($menu_pagine === true)
$javascript .= '
<script type="text/javascript" src="'.$mainurl.$libdir.'menu-min.js"></script>';
//<script type="text/javascript" src="'.$mainurl.$libdir.'common-min.js"></script>';

if ($menu_prototype === true) {
	$stylesheet .= '
<link rel="stylesheet" type="text/css" href="'.$mainurl.$libdir.'prototype-menu.css" />';
	$javascript .= '
<script type="text/javascript" src="'.$mainurl.$libdir.'prototype.js"></script>
<script type="text/javascript" src="'
	.$mainurl.$libdir.'scriptaculous.js?load=effects,builder"></script>
<script type="text/javascript" src="'.$mainurl.$libdir.'prototype-menu.js"></script>';
	/*
<script type="text/javascript" src="'.$mainurl.$libdir.'builder.js"></script>
<script type="text/javascript" src="'.$mainurl.$libdir.'effects.js"></script>
<script type="text/javascript" src="'.$mainurl.$libdir.'dragdrop.js"></script>
<script type="text/javascript" src="'.$mainurl.$libdir.'controls.js"></script>
<script type="text/javascript" src="'.$mainurl.$libdir.'slider.js"></script>
<script type="text/javascript" src="'.$mainurl.$libdir.'sound.js"></script>
<script type="text/javascript" src="'.$mainurl.$libdir.'unittest.js"></script>
*/
}

if (!isset($lightbox_css))
$lightbox_css = '
<link rel="stylesheet" type="text/css" href="'.$mainurl.$libdir.'lightbox.css" />';
if (!isset($lightbox_js))
$lightbox_js = ($menu_prototype===true?'
<script type="text/javascript" src="'.$mainurl.$libdir.'lightbox.js"></script>
':'
<script type="text/javascript" src="'.$mainurl.$libdir.'prototype.js"></script>
<script type="text/javascript" src="'
.$mainurl.$libdir.'scriptaculous.js?load=effects,builder"></script>
<script type="text/javascript" src="'.$mainurl.$libdir.'lightbox.js"></script>
');

if (!isset($getvars_pg_array))
$getvars_pg_array = array();

$getvars_pg_array = array_merge(
	array(
		"do", "x", "y", "lg", "pg", "q", "send", "toggle", "ban", "login",
		"adminName", "userName", "passWord", "adminId", "adminStatut",
		"adminUtil", "adminPass", "adminEmail", "newadminUtil", "newadminPass",
		"newadminEmail", "newadminStatut", "userId", "userStatut", "userUtil",
		"userPass", "userEmail", "newuserUtil", "newuserPass", "newuserEmail",
		"newuserStatut", "contTitle", "contEntry", "contMetadesc", "contMetakeyw",
		"contType", "contOrient", "contStatut", "cont", "logo", "contphotoId",
		"contdocId", "menuId", "rqst", "statut", "par", "ordre", "redirURL",
	),
	$getvars_pg_array
);//,"contphotoDesc", "contdocDesc" are arrays, see $nof

if (!isset($array_multilingual))
$array_multilingual = array();

$array_entrytitletitre = array("Entry", "Title", "Titre", "Desc");

// assign all elements in array_entrytitletitre to multilingual
foreach($getvars_pg_array as $k)
foreach($array_entrytitletitre as $p)
if (strstr($k,$p))
$array_multilingual[] = $k;

// assign metas to each modules
if (isset($tblhtaccess))
foreach($array_modules as $key) {
	$getvars_pg_array[] = $key."Metadesc";
	$getvars_pg_array[] = $key."Metakeyw";
}

if (!isset($array_img_ext))
$array_img_ext = array("jpg", "jpeg", "gif", "png",);
if (!isset($array_swf_ext))
$array_swf_ext = array("swf", "flv", "wmv", "avi", "mp3", "mov", "rm", "mpeg", "mpg", "asf",);
$array_media_ext = array_unique(array_merge($array_img_ext,$array_swf_ext));
if (!isset($array_doc_ext))
$array_doc_ext = array("doc", "xls", "ppt", "pps", "pdf", "xml",);
if (!isset($invisibleFileNames))
$invisibleFileNames = array(
	".", "..", ".htaccess", ".htpasswd", ".svn", "dir-prop-base", "entries", "format",
); // as of PHP 5: can implement isDot() for . and ..
if (!isset($invisibleFileExts))
$invisibleFileExts = array("zip", "svn-prop-base",); // we do not want these files...
if (!isset($tiny_mp3_inflash))
$tiny_mp3_inflash = false;
if (!isset($mp3_logo))
$mp3_logo = "images/mp3logo.png";
if (!isset($mp3_params))
$mp3_params = "&vol=60";//"&autostart=false&playlist=empty_playlist.xml&action=stop&vol=50";
//&color=AFED2F&textcolor=006600&loop=no&lma=yes&textcolor=006600&color=AFED2F
if (!isset($array_vars_mp3_player))
$array_vars_mp3_player = array("playlist", "autostart", "vol", "artist", "title", "url",);
if (!isset($array_form))
$array_form = array("name", "fname", "organisation", "email", "code", "subject", "message",);
if (!isset($array_orient))
$array_orient = array("left", "center", "right",);
if (!isset($array_admin_priv))
$array_admin_priv = array("0", "1",);
if (!isset($array_preferred_fields))
$array_preferred_fields = array("title", "titre", "name", "nom", "util",);

foreach($getvars_pg_array as $key) {
	if (isset($_REQUEST[$key])) {
		${$key} = trim($_REQUEST[$key]);
		//	reason why _incerror.php is included here
		if ($key == 'send') ${$key} = html_encode(${$key});
		${$key} = stripslashes(str_replace("'", "&acute;", ${$key}));//´
		if (${$key} == "<p></p>")	${$key} = "";
		if (${$key} == "<p>&nbsp;</p>")	${$key} = "";
	}
}

if (!isset($s))
$s = "s"; // used for plurals on condition of quantity > 1
if (!isset($y_menu))
$y_menu = ""; // to avoid redundant lines

if (!isset($array_supported_lg))
$array_supported_lg = array("en", "ar", "de", "es", "fr", "it", "jp", "ru", "zh",);

if (!isset($default_lg) || !in_array($default_lg,$array_supported_lg))
$default_lg = "en";

$array_lang = sql_array(
	$tblenum,
	"WHERE enumstatut='Y' AND enumwhat='lang' AND enumtype!='$default_lg' ",
	"enumtype"
);
if (!isset($array_lang[0]) || ($array_lang[0] == "")) {
	$array_lang = array($default_lg);
} else {
	$array_lang = array_reverse($array_lang);
	$array_lang[] = $default_lg;
	$array_lang = array_reverse($array_lang);// so default_lg comes in first
}

// strip http://{http_host} from rqst_uri
if (substr($_SERVER['REQUEST_URI'],0,strlen("http://".$_SERVER['HTTP_HOST']))
	== "http://".$_SERVER['HTTP_HOST']
) {
	$_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'],
	(strlen($_SERVER['HTTP_HOST'])+7));
	//	http:// = 7 ,strlen("http://".$_SERVER['HTTP_HOST'])
}

// prevents repetition of slash in URL
$_SERVER['REQUEST_URI'] = preg_replace("/(\/)+/", "$1", $_SERVER['REQUEST_URI']);
if (stristr($_SERVER['PHP_SELF'],$urladmin))
	$uri = substr($_SERVER['PHP_SELF'],strlen($url_mirror));
else
	$uri = substr($_SERVER['REQUEST_URI'],strlen($url_mirror));

if (strstr($uri,"?")) {
	$array_uri = explode("?", $uri);
	$uri = $array_uri[0];
	$local_uri = "/".$uri;
	$params = $array_uri[1];
	$array_params = explode("&", $params);
	foreach($array_params as $key) {
		$key = explode("=", $key);
		if (is_array($key))
		${$key[0]} = (isset($key[1])?$key[1]:'');
	}
} else $local_uri = "/".$uri;

if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
$_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en-us';

//redirection of index according to language of browser
if (!stristr($_SERVER['PHP_SELF'],$urladmin))
if (!isset($_GET['lg']) && stristr($_SERVER['PHP_SELF'],"index.php")
&& ($_SERVER['REQUEST_URI'] == $url_mirror))
$lg = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2);

if (!isset($lg)) {
	if (($htaccess4sef === true) && (!stristr($_SERVER['REQUEST_URI'],".php"))) {
		if (stristr($_SERVER['PHP_SELF'],$urlsafe))
		{Header("Location: ".$mainurl.$urladmin.'_download.php?'
			.$_SERVER['QUERY_STRING']);Die();}
		if (!is_array($uri))
		$uri = array("$uri");
		$array_uri = explode("/", $uri[0]);
		if (!is_array($array_uri))
		$array_uri = array($uri[0]);
		if (count($array_lang)>1)
		if (in_array($array_uri[0],$array_lang))
			$lg = $array_uri[0];// construct : /lg/page/
		else
			$lg = $default_lg; // no /lg/ construct
	} else {
		if (stristr($_SERVER['PHP_SELF'],"/index.php"))
			$lg = $default_lg ;
		else
			$lg = substr(substr($_SERVER['PHP_SELF'],$extractfromphpself),-6,-4);
		if (($htaccess4sef === true) && !stristr($_SERVER['PHP_SELF'], $urladmin)) {
			if (stristr($_SERVER['PHP_SELF'],"/index.php")) {
				$x = '1';
			} else {
				$url_from_php = substr(substr($_SERVER['REQUEST_URI'],
				$extractfromphpself),0,-7);
				if ($url_from_php == "index")
					$x = '1';
				else
					$x = sql_getone($tblcont,"WHERE conturl='$url_from_php' ", "contpg");
			}
			if ($x != '') {
				$redir_tourl = space2underscore(sql_getone($tblcont,
				"WHERE contlang='$lg' AND contpg='$x' ",
				"conttitle"));
				if (strlen($x)>1) {
					for($hj=1;$hj<strlen($x);$hj++) {
						$substr_pg = substr($x,0,$hj);
						$redir_tourl = space2underscore(sql_getone($tblcont,
						"WHERE contstatut='Y'
							AND contlang='$lg'
							AND contpg='$substr_pg' ",
						"conttitle"))."/".$redir_tourl;
					}
				}
				Header("Location: ".$mainurl.(count($array_lang)>1?"$lg/":'')
				.$redir_tourl.($_SERVER['QUERY_STRING']!=""?'?'.$_SERVER['QUERY_STRING']:''));
				Die;
			} else { // not a page but an id
				if (isset($tblhtaccess))
				if (sql_nrows($tblhtaccess,"WHERE htaccessstatut='Y'
											AND htaccessurl='$url_from_php' ") > 0) {
					$get_x = sql_get($tblhtaccess,
									"WHERE htaccessstatut='Y'
										AND htaccesslang='$lg'
										AND htaccessurl='$url_from_php'
										ORDER BY htaccessdate DESC ",
									"htaccessitem,htaccesstype");
					if ($get_x[0] != '.') {
						${$get_x[1]."Id"} = $get_x[0];
						Header("Location: ".$mainurl."?".$get_x[1]."Id=".$get_x[0]
						.($_SERVER['QUERY_STRING']!=""?'&amp;'
						.$_SERVER['QUERY_STRING']:''));
						Die();
					}
				}
			}
		}
	}
}

if (!isset($array_uri)) $array_uri = array();

if (!isset($lg) || !in_array($lg,$array_lang))
$lg = $default_lg;

$charset_iso = "ISO-8859-1"; // this one needs to be before mail headers

if (!isset($CRLF))
$CRLF = true;

if ($CRLF === false)
	$CRLF = "\n";
else
	$CRLF = "\r\n";

// To send HTML mail, the Content-type header must be set
$random_hash = md5(date('r',time()));
$php_alt_random_hash = "--PHP-alt-$random_hash";
$mail_headers = $CRLF.'X-Mailer: PHP/' . phpversion();
//if (stristr($_SERVER['HTTP_HOST'],"mediavince.com") && ($client !== 'mediavince'))
//$mail_headers .= $CRLF . 'Bcc: developer@mediavince.com';

// To send HTML mail, the Content-type header must be set
$mail_headers_basic = $mail_headers.$CRLF.'MIME-Version: 1.0';
$mail_headers_basic .= $CRLF.'Content-type: text/html; charset='.$charset_iso;

$mail_headers .= $CRLF.'Content-Type: multipart/alternative; boundary="PHP-alt-'.$random_hash.'"';
$mail_headers_text = $CRLF.$php_alt_random_hash.$CRLF.'Content-Type: text/plain; charset="'
					.$charset_iso.'"'.$CRLF.'Content-Transfer-Encoding: 7bit';
$mail_headers_html = $CRLF.$php_alt_random_hash.$CRLF.'Content-Type: text/html; charset="'
					.$charset_iso.'"'.$CRLF.'Content-Transfer-Encoding: 7bit';
$mail_headers_end = $CRLF.$php_alt_random_hash;
//$CRLF . 'Content-type: text/html; charset='.$charset_iso;

// resets charset_iso after mailer setup
$charset_iso = "UTF-8"; // buggy
//$charset_iso = "ISO-8859-1";

$n = "$CRLF";
$n = nl2br($n);

// Max file size of file upload
$max_filesize = (ini_get('post_max_size')>=ini_get('upload_max_filesize')?
ini_get('post_max_size'):ini_get('upload_max_filesize'));
$max_filesize_str = $max_filesize;
$mul = substr($max_filesize, -1);
$mul = ($mul == 'M' ? 1048576 : ($mul == 'K' ? 1024 : ($mul == 'G' ? 1073741824 : 1)));
$max_filesize = $mul*(int)$max_filesize;
if (!is_int($max_filesize)) $max_filesize = "3145728";

if (!isset($listPerpg))
$listPerpg = "5";

$pages = '<p style="text-align:right;">&gt;&gt; ';

if (!isset($pg) || !preg_match("/^[0-9]+\$/", $pg) || ($pg == ''))
$pg = "1";

$nextLimit = $pg * $listPerpg - $listPerpg;
$queryLimit = " LIMIT $nextLimit , $listPerpg ";

if (!isset($x) || ($x == ''))
$x = "1";
if (!preg_match("/^[0-9z]+\$/",  $x))
{Header("Location: $redirect");Die();}

if (isset($y)) {
	if (!in_array($y,array_keys($array_admin_menu)))
	if (!preg_match("/^[0-9]+\$/", $y))
	$y = '1';
} else $y = '1';

$postgetmethod = "post"; // small case for XHTML compliance

// all these forms pass now in function gen_form($lg,$x=NULL,$y=NULL)
// if (stristr($_SERVER["HTTP_HOST"], "localhost"))	$postgetmethod = "get"	;
$form_method = '
<form enctype="multipart/form-data" action="'.$local.$indexdotphpfroform
			.'?lg='.$lg.'&amp;x='.$x.'" method="'.$postgetmethod.'">';
$form_methody = '
<form enctype="multipart/form-data" action="'.$local.$indexdotphpfroform
			.'?lg='.$lg.'&amp;x='.$x.'&amp;y='.$y.'" method="'.$postgetmethod.'">';
// !! // !! needed for login as admin
$form_method_self = '
<form enctype="multipart/form-data" action="" method="'.$postgetmethod.'">';
//http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'

if (!isset($nof))
$nof = 1;

if (!isset($menu_pad_left))
$menu_pad_left = '-9';
if (!isset($menu_pad_top))
$menu_pad_top = '13';

$optionselected = ' selected="selected" ';
$inputchecked = ' checked="checked" ';

// produces textarea by id for tinyMCE, gets added along the process if textarea is needed
$i_elm = 0;
// rid is item id of default by lang
$basic_array = array("id", "date", "statut", "lang", "rid",);
if (!isset($notice)) $notice = '';
if (!isset($error)) $error = '';
if (!isset($full_url)) $full_url = true;
if (!isset($index_show_title)) $index_show_title = true;
if (!isset($index_style)) $index_style = '';
if (!isset($array_hidden)) $array_hidden = array();
if (!isset($array_csv_list)) $array_csv_list = array("admin","user",);
if (!isset($array_modules_as_form)) $array_modules_as_form = array();
if (!isset($array_preferred_fields)) $array_preferred_fields = array(
	"title", "titre", "name", "nom", "util", "id",
);
if (!isset($forum_commenting)) $forum_commenting = false;
if (!isset($array_galleries)) $array_galleries = array();
if (!isset($array_fixed_modules)) $array_fixed_modules = array();
if (!isset($array_unmanaged_modules)) $array_unmanaged_modules = array("contact", "profil",);
$mod_array = array_unique(array_merge($array_fixed_modules,$array_modules));
//,"catalog","inscrire",);
