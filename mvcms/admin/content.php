<?PHP

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

// http://www.trash.net/~ck/ontheflyzip/index.html
// this script zips up a directory (on the fly) and delivers it
// C.Kaiser 2002
// No Copyright, free to use.

  // get the filename of this php file without extension.
  // that is also the directory to zip and the name of the
  // zip file to deliver
  $filename_no_ext=basename($_SERVER['SCRIPT_FILENAME'], ".php");

  // we deliver a zip file
  header("Content-Type: archive/zip");

  // filename for the browser to save the zip file
  header("Content-Disposition: attachment; filename=$filename_no_ext".".zip");

  // get a tmp name for the .zip
  $tmp_zip = $getcwd.$up."SQL/".time()."_".$admin_name.".zip";//

  // zip the stuff (dir and all in there) into the tmp_zip file
  //`zip -r $tmp_zip $up$filename_no_ext/`;
  `zip -r19jlD $tmp_zip $up$filename_no_ext -x \*.zip`;
  /* 1jl9D
C:\>zip
Copyright (C) 1990-1999 Info-ZIP
Type 'zip "-L"' for software license.
Zip 2.3 (November 29th 1999). Usage:
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
  header("Content-Length: $filesize");

  // deliver the zip file
  $fp = fopen("$tmp_zip","r");
  echo fpassthru($fp);

  // clean up the tmp zip file
  `rm $tmp_zip `;
  @unlink($tmp_zip);
  
$void = "<!-- Zip a directory structure on the fly and deliver it via http using PHP
For offline-browsing, it is often convinient to download a whole directory structure and read it offline. That is especially true for photos embeded in html.

Instead of offering a zipped archive, which needs maintanance whenever something changes, it is possible to do a bit of php and just zip the stuff on the fly when it is acutually requested.

The introduced bit of php must have the same name as the directory structure (plus .php as extension) to zip and must reside in the same directory (as the source directory).

That's all!

For the zip, just link to the php and the visitor gets prompted where to store the stuff. Try it here (link to ../ontheflyzip.php).

This is the .php:  -->";

} else {
	Header("Location: $redirect");Die();
}
?>