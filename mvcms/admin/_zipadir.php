<?php #۞ #
if (stristr($_SERVER['PHP_SELF'], basename(__FILE__))){include '_security.php';Header("Location: $redirect");Die();}

if ($logged_in === true) {

	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT\n");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Content-type: application/zip;\n"); //or yours?
	header("Content-Transfer-Encoding: binary\n");

  // get a tmp name for the .zip
  $tmp_zip = $up.$safedir.$now_time."_".$admin_name."-".$filename_no_ext_nodir.".zip";//

	// insert time range to avoid errors
	$valid_date = false;
	$d_date = $now_time;
  if (isset($_REQUEST['d']) && preg_match("/^20[0-9]{2}[0-1][0-9][0-9]{2}\$/", $_REQUEST['d'])) {
   	$d = $_REQUEST['d'];
    $d_year = substr($d,0,4);
    $d_month = substr($d,4,2);
    $d_day = substr($d,-2);
    $d_date = mktime(0,0,0,$d_month,$d_day,$d_year);
    if  ((time() > $d_date) && ($d_date > mktime(0,0,0,0,0,0)))
    $valid_date = true;
  }

  if ($valid_date === true)
  $range = " -t $d_year-$d_month-$d_day ";//-t 2008-05-29 ";
//  $range = " -t $d_year-$d_month-$d_day ";//-t 2008-05-29 ";
  else $range = '';

	if (!class_exists('ZipArchive')) {

		$filename_no_ext = escapeshellcmd($filename_no_ext);
		$up = escapeshellcmd($up);
		$tmp_zip = escapeshellcmd($tmp_zip);
		$range = escapeshellcmd($range);
		$exclu = escapeshellcmd(' -x . .. .htaccess .htpasswd *.zip .svn *.svn* dir-prop-base entries format '); //->  -x . .. .htaccess .htpasswd \*.zip .svn \*.svn\* dir-prop-base entries format

		// issue cd command with directory we want to zip
		// zip current dir . to 2 levels up in filename_no_ext
	  `cd $up ; zip -r $safedir$tmp_zip $range $filename_no_ext $exclu `;

		// .:..:.htaccess:.htpasswd:\*.zip:.svn:\*.svn\*:dir-prop-base:entries:format
  /*rj19D
C:\>zip
Copyright (C) 1990-1999 Info-ZIP
Type 'zip "-L"' for software license.
Zip 2.3 (November 29th 1999). Usage: ( -t option is actually mm-dd-yyyy with hyphen ...)
zip [-options] [-b path] [-t mmddyyyy] [-n suffixes] [zipfile list] [-xi list]
  The default action is to add or replace zipfile entries from list, which
  can include the special name - to compress standard input.
  If zipfile and list are omitted, zip compresses stdin to stdout.
  -f   freshen: only changed files  -u   update: only changed or new files
  -d   delete entries in zipfile    -m   move into zipfile (delete files)
  -r   recurse into directories     -j   junk (don't record) directory names
  -0   store only                   -l   convert LF to CR LF (-ll CR LF to LF)
  -1   compress faster              -9   compress better
  -q   quiet operation              -v   verbose operation/print version info
  -c   add one-line comments        -z   add zipfile comment
  -@   read names from stdin        -o   make zipfile as old as latest entry
  -x   exclude the following names  -i   include only the following names
  -F   fix zipfile (-FF try harder) -D   do not add directory entries
  -A   adjust self-extracting exe   -J   junk zipfile prefix (unzipsfx)
  -T   test zipfile integrity       -X   eXclude eXtra file attributes
  -!   use privileges (if granted) to obtain all aspects of WinNT security
  -R   PKZIP recursion (see manual)
  -$   include volume label         -S   include system and hidden files
  -h   show this help               -n   don't compress these suffixes
  */

	  // calc the length of the zip. it is needed for the progress bar of the browser
	  $filesize = filesize($tmp_zip);
	  header("Content-Length: $filesize\n");

	  // filename for the browser to save the zip file
	  header("Content-Disposition: attachment; filename=$filename_no_ext_nodir".".zip\n\n");

	  // deliver the zip file
	  $fp = fopen("$tmp_zip","r");
	  echo fpassthru($fp);
		fclose($fp); // close the opened file or might be unlinked ok, especially on windblowz

	  // clean up the tmp zip file
	  `rm $tmp_zip `;
	  @unlink($tmp_zip);

		die();

	} else {

		if (class_exists('ZipArchive'))
		include 'ziparchive.call.php'; // only valid with php5 oop

	  // calc the length of the zip. it is needed for the progress bar of the browser
	  $filesize = filesize($tmp_zip);
	  header("Content-Length: $filesize\n");

	  // filename for the browser to save the zip file
	  header("Content-Disposition: attachment; filename=$filename_no_ext_nodir".".zip\n\n");

	  // deliver the zip file
	  $fp = fopen("$tmp_zip","r");
	  echo fpassthru($fp);
		fclose($fp); // close the opened file or might be unlinked ok, especially on windblowz

	  // clean up the tmp zip file
	  `rm $tmp_zip `;
	  @unlink($tmp_zip);

	} // class exists?

} else {
	Header("Location: $redirect");Die();
}
