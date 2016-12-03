<?php #۞ # ADMIN
if (stristr($_SERVER['PHP_SELF'],'newsletteradmin.php')) {
  include '_security.php';
  Header("Location: $redirect");Die();
}

$content .= $admin_menu;
$local_url = $local.'?lg='.$lg.'&amp;x='.$x.'&amp;y='.$y;

if (!isset($nRowsNewsletter)) {
	$nRowsNewsletter = sql_nrows($tblnewsletter,"");
	$nRowsNewslettery = sql_nrows($tblnewsletter," WHERE newsletterstatut='Y' ");
	$nRowsNewslettern = sql_nrows($tblnewsletter," WHERE newsletterstatut='N' ");
}
if (isset($send) && ($send == "delnl")) {
  if (!isset($nlid) || !preg_match("/^[0-9]+\$/", $nlid) || ($nlid == ""))
    {Header("Location: $redirect");Die();}
  $nlid  = strip_tags($nlid);
  $deletequery = sql_del($tblnewsletter, " WHERE newsletterid='$nlid' ");
  if ($deletequery == '0')
  $notice .= '<font color="Green"><b>'.$enregistrementString.' '.$effaceString.'</b></font><br />';
  else
  $error .= $error_request.'<br />&nbsp;<br /><a href="javascript:history.back()//">'.$retourString.'</a><br />';
  $_SESSION['mv_notice'] = $notice;
  $_SESSION['mv_error'] = $error;
  Header("Location: ".html_entity_decode($local_url));Die();
}
######################################################################################################
if (!isset($send) || (isset($send) && ($send == 'delnl'))) {
  $content .= '<a href="'.$local_url.'&amp;send=new"><img src="'.$mainurl.'images/go_f2.png" alt="'.$envoyerString.' '.$newsletterString.'" title="'.$envoyerString.' '.$newsletterString.'" align="left" border="0" /></a>';
  	if	(!isset($statut))	$statut = $toutString	;
  	if	(!isset($par))		$par = 'newsletterdate'	;
  	if	(!isset($ordre))	$ordre = 'DESC'	;
  	if	($statut == $toutString) $statutstr = $toutString	;
  	if	($statut == 'Y')	$statutstr = $confirmeString	;
  	if	($statut == 'N')	$statutstr = $enattenteString	;
		if  (!isset($ordre))  $ordre  = 'DESC';
		if  (!isset($par))  $par = 'newsletterdate';
  	if	($ordre == 'ASC')	$ordrestr = $ASCString	;
  	if	($ordre == 'DESC')	$ordrestr = $DESCString	;
		if ($par == 'newsletterdate')  $parstr = $dateString;
		if ($par == 'newsletterid')        $parstr = $numidString;
		$passedordre = $ordre;
	  if ($passedordre == 'ASC')  ($nextordre = 'DESC') ;
	  if ($passedordre == 'DESC') ($nextordre = 'ASC') ;
	  $voirvar = '&nbsp;';
  	$selectHead = '<div class="selectHead">'.gen_form($lg,$x,$y).'<!-- <label for="statut">'.$statutString.' : </label><select name="statut"><option value="'.$statut.'"> >> '.$statutstr.' </option><option value="'.$toutString.'"> '.$toutString.' </option><option value="Y"> '.$confirmeString.' </option><option value="N"> '.$enattenteString.' </option></select> | --><label for="par">'.$parString.' : </label><select name="par"><option value="'.$par.'"> >> '.$parstr.' </option><option value="newsletterid"> '.$numidString.' </option><option value="newsletterdate"> '.$dateString.' </option></select> | <label for="ordre">'.$ordreString.' : </label><select name="ordre"><option value="'.$ordre.'"> >> '.$ordrestr.' </option><option value="ASC"> '.$ASCString.' </option><option value="DESC"> '.$DESCString.' </option></select> || <input type="submit" value="'.$voirString.'" /></form></div>';
		$tableHead = '<table align="center" style="width:95%" border="1" cellspacing="2" cellpadding="2"><tr><th>'.$infodbString.'</th><th>'.$paramsString.' '.$newsletterString.'</th></tr><tr><td><a href="'.$local_url.'&amp;par=newsletterid&ordre='.$nextordre.'">'.$numidString.'</a><br /><a href="'.$local_url.'&amp;par=newsletterstatut&ordre='.$nextordre.'">'.$statutString.'</a><br /><a href="'.$local_url.'&amp;par=newsletterdate&ordre='.$nextordre.'">'.$dateString.'</a></td><td>'.$sujetString.' ('.$ouvrirString.')<br />'.$messageString.'<br />'.$rapportString.'<br />'.$voirvar.'</td></tr>';
		$fullread = @mysql_query("
							select * FROM $tblnewsletter
							ORDER BY $par $ordre
							");
		$fullnRows = @mysql_num_rows($fullread);
		$read = @mysql_query("
						select * FROM $tblnewsletter
						ORDER BY $par $ordre
						$queryLimit
						");
		$nRows = @mysql_num_rows($read);
		$totalpg = ceil( $fullnRows / $listPerpg );
		if  ($pg > $totalpg)  $pg = '1'  ;
		for ($i=1; $i<=$totalpg; $i++) {
			if ($i == $pg) {
				$pages .= ' <b> '.$i.' </b> ';
			} else {
				$pages .= ' <a href="'.$local_url.'&amp;par='.$par.'&amp;ordre='.$ordre.'&amp;pg='.$i.'">'.$i.'</a> ';
			}
		}
		if ( (!isset($nRowsNewsletter)) || ($nRowsNewsletter == "0") ) {
  $content .= '<div class="selectHead"></div><p>0 '.$enregistrementString.' '.$surString.' '.$newsletterString.'.</p>';
		} else {
  $content .= $selectHead;
  $content .= '<p>'.$nRowsNewsletter.' '.$class_conjugaison->plural($enregistrementString,'M',$nRowsNewsletter).' '.$surString.' '.$newsletterString.'.</p>';
			if ($listPerpg < $fullnRows) $content .= $pages.' << &nbsp;</p>' ;
  $content .= $tableHead;
			for ($i=0; $i<$nRows; $i++) {
				$row = mysql_fetch_array($read);
  $content .= '<tr><td>'.$row["newsletterid"].'<br />'.$row["newsletterstatut"].'<br />'.$row["newsletterdate"].'</td><td><a href="'.$local_url.'&amp;nlid='.$row["newsletterid"].'&amp;send=delnl" onclick="return confirm(\''.$confirmationeffacementString.'\');"><img src="'.$mainurl.'images/delete.gif" width="10" height="10" align="right" title="'.$effacerString.'" alt="'.$effacerString.'" border="0" /></a><a href="'.$mainurl.(count($array_lang)>1?$lg:'').'?nlid='.$row["newsletterid"].'-0" target="_blank">'.$row["newslettersujet"].'</a><br />'.(isset($nlid)?$row["newslettercontent"]:'').'<hr />'.$rapportString.':<br /><font color="Green">'.$row["newslettersent"].'</font><hr /><font color="Red">'.$row["newslettererror"].'</font><br /></td></tr>';//<!-- <a href="'.$local_url.'&amp;send=edit&amp;newsletterId='.$row["newsletterid"].'">'.$modifierString.'</a> -->
			}
  $content .= '</table><br />';
			if ($listPerpg < $fullnRows) $content .= $pages.' << &nbsp;</p>' ;
		}
######################################################################################################
} else if (($send == "new") || ($send == $envoyerString)) {
  include $getcwd.$up.$urladmin.'newslettersend.php';
} else {
  Header("Location: $redirect");Die();
}
