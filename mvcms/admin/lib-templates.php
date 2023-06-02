<?php #۞ #

include '_incdb.php';
//include '_incerror.php';// included in _incdb.php
include '_strings.php';

	if (isset($_COOKIE[$cookie_codename."admin"])) {
		$cookie = $_COOKIE[$cookie_codename."admin"];
		$read_cookie = explode("^", base64_decode($cookie));
		$admin_name = $read_cookie[0];
		$passWord = $read_cookie[1];
		if (sql_nrows($tbladmin," WHERE adminutil='$admin_name' AND adminpass='$passWord' AND adminstatut='Y' ") == 1) {
  		$logged_in = true;
		} else {
			$logged_in = false;
		}
	} else {
    Header("Location: $redirect");Die();
  }
	
if ($logged_in === true) {

// https://www.trash.net/~ck/ontheflyzip/index.html
// this script zips up a directory (on the fly) and delivers it
// C.Kaiser 2002
// No Copyright, free to use.

  // get the filename of this php file without extension.
  // that is also the directory to zip and the name of the
  // zip file to deliver
  $filename_no_ext_nodir = basename($_SERVER['SCRIPT_FILENAME'], ".php");
	/* exposes root path, not so good...
	// double dirname is like cd ../ (going in parent directory)
	$filename_no_ext = dirname(dirname($_SERVER['SCRIPT_FILENAME']))."/"
										.str_replace("-","/",$filename_no_ext_nodir)."/";
	*/
	// needs up for cd in command line and ziparchive otherwise
	$filename_no_ext = str_replace("-","/",$filename_no_ext_nodir)."/"; // DIRECTORY_SEPARATOR is bad

	include '_zipadir.php';

} else {
	Header("Location: $redirect");Die();
}
