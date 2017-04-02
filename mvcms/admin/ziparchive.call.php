<?php #۞ #
if (stristr($_SERVER['PHP_SELF'], basename(__FILE__))){include '_security.php';Header("Location: $redirect");Die();}

$zip = new ZipArchive();

if ($zip->open($tmp_zip,ZIPARCHIVE::CREATE)!==TRUE) {
	$_SESSION['notice'] = "Cannot open $tmp_zip";
	Header("Location: $redirect");Die();
}

foreach(scanDirectories($up.$filename_no_ext) as $k)
$zip->addFile($k,substr($k,strlen("$up")));

$zip->close();
