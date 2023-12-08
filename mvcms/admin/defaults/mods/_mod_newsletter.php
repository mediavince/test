<?php if (stristr($_SERVER['PHP_SELF'],'_mod_newsletter.php')) {include '_security.php';Header("Location: $redirect");Die();}

$dbtable = $tblnewsletter;
$type_page = sql_getone($tblcont,"WHERE $where_statut_lang conttype='$this_is' ","contpg");
$title = ($x==$type_page?$title:$newsletterString);
$_mod_newsletter = '';

if (isset($nlid) && preg_match("/^[0-9]{0,3}-[0-9]{0,5}\$/", $nlid)) {
	$nldata = explode('-', $nlid);
	$nl_newsletterid = sql_get($tblnewsletter, " WHERE newsletterid='$nldata[0]' ","newsletterid, newslettersujet, newslettercontent");
	$nl_membreid = sql_get($tblmembre, " WHERE membreid='$nldata[1]' ","membregendre, membreprenom, membrenom");
	$membreGendre = $nl_membreid[0];
	$membreUtil = $nl_membreid[1]." ".$nl_membreid[2];
} else {
	$nl_newsletterid = sql_get($tblnewsletter, " WHERE newsletterstatut='Y' ORDER BY newsletterdate DESC LIMIT 0, 1 ","newsletterid, newslettersujet, newslettercontent");
    $nldata[0] = ($nl_newsletterid[0]=='.'?'':$nl_newsletterid[0]);
}

if ($nl_newsletterid[0] == $nldata[0]) {
    $_mod_newsletter .= '<b>'.$nl_newsletterid[1].'</b><br /> <br />';
    $_mod_newsletter .= $nl_newsletterid[2];//$chermembre.
} else if (sql_nrows($tblnewsletter," WHERE newsletterstatut='Y' ")==0) {
    $_mod_newsletter .= '';//'<b>0 '.$enregistrementString.'</b><br /> <br />';
} else {
    Header("Location: $redirect");
    Die();
}
