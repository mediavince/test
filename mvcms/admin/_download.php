<?php #۞ #

include '_incdb.php';
//include '_incerror.php';// included in _incdb.php
include '_strings.php';

if (!isset($safedir)) $safedir = 'SQL/';

if (isset($_COOKIE[$cookie_codename."user"])) {
	$cookie = $_COOKIE[$cookie_codename."user"];
	$read_cookie = explode("^",base64_decode($cookie));
	$user_name = $read_cookie[0];
	$pass_word = $read_cookie[1];
	if (sql_nrows($tbluser," WHERE userutil='$user_name' AND userpass='$pass_word' AND userstatut='Y' ") > 0) {// (in_array("userrid",sql_fields($tbluser,'array'))?count($array_lang):'1')
  	    $logged_in = true;
	} else {
		$logged_in = false;
	}
} else if (isset($_COOKIE[$cookie_codename."admin"])) {
	$cookie = $_COOKIE[$cookie_codename."admin"];
	$read_cookie = explode("^",base64_decode($cookie));
	$admin_name = $read_cookie[0];
	$pass_word = $read_cookie[1];
	if (sql_nrows($tbladmin," WHERE adminutil='$admin_name' AND adminpass='$pass_word' AND adminstatut='Y' ") > 0) {//(in_array("adminrid",sql_fields($tbladmin,'array'))?count($array_lang):'1')
  	    $logged_in = true;
	} else {
		$logged_in = false;
	}
} else {
  $_SESSION['mv_error'] = $error_accesspriv.'<br /><a href="javascript:history.back()//">'.$retourString.'</a><br />';
  Header("Location: $redirect");Die();
}

if (($logged_in === true) && (isset($_GET["sql"]) || isset($_GET["file"]))) {
  if (isset($_GET["sql"])) {
    $download_file = base64_decode($_GET["sql"]);
    if (!stristr($download_file,$safedir))
    $download_file = $safedir.$download_file;
  }
  if (isset($_GET["file"])) {
    $download_file = base64_decode($_GET["file"]);
    if (!stristr($download_file,$filedir)&&!stristr($download_file,$safedir))
    $download_file = $filedir.$download_file;
  }
  $local_file = $getcwd.$up.$download_file;
  if (@file_exists($local_file) && is_file($local_file)) {
    Header("Cache-Control: no-cache, must-revalidate");
    Header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    Header("Content-Type: application/octet-stream");
    Header('Content-Length: '.filesize($local_file));
    Header('Content-Disposition: attachment; filename='.(stristr($download_file,$safedir)?substr($download_file,strlen($safedir)):(stristr($download_file,$filedir)?substr($download_file,strlen($filedir)):$download_file)));
    readfile($local_file);
  } else {
    Die('Error: The file <i>"'.$download_file.'"</i> does not exist!');//
  }
} else {
  $_SESSION['mv_error'] = $error_accesspriv.'<br /><a href="javascript:history.back()//">'.$retourString.'</a><br />';
  Header("Location: $redirect");Die();
}
