<?PHP
if (stristr($_SERVER['PHP_SELF'],'shownewsletter.php')) {
  include '_security.php';
  Header("Location: $redirect");Die();
}

$dbtable = $tblnewsletter;

if (isset($nlid) && preg_match("/^[0-9]{0,3}-[0-9]{0,5}\$/", $nlid)) {
	$nldata = explode('-', $nlid);
	$nl_newsletterid = sql_get($tblnewsletter, " WHERE newsletterid='$nldata[0]' ","newsletterid, newslettersujet, newslettercontent");
	$nl_membreid = sql_get($tblmembre, " WHERE membreid='$nldata[1]' ","membregendre, membreprenom, membrenom");
	$membreGendre = $nl_membreid[0];
	$membreUtil = $nl_membreid[1]." ".$nl_membreid[2];
	if ($nl_newsletterid[0] == $nldata[0]) {
    $title = $newsletterString;
    $content = '<b>'.$nl_newsletterid[1].'</b><br /> <br />';
    /*
  	if  ( (!isset($membreGendre)) || ($membreGendre == '.') )  $membreGendre = ""  ;
  	$fem_mark = "Chers";
  	if  ($membreGendre == "Mr")  $fem_mark = "Cher"  ;
  	if  ($membreGendre == "Mr & Mme")  $fem_mark = "Chers"  ;
  	if  ( (!isset($membreUtil)) || ($membreUtil == '.') || ($membreUtil == '') )  $membreUtil = "Amis"  ;
  	$chermembre = $fem_mark.' '.$membreGendre.' '.$membreUtil.' ,<br /> <br />';
    */
    $content .= $nl_newsletterid[2];//$chermembre.
	}
} else {
  Header("Location: $redirect");Die();
}
?>