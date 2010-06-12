<?PHP ## ADMIN

include '_security.php';

$dbtable = $tblhelp;

/*
  $content .= '<p><a href="?lg='.$lg.'&x=z&y=1">Extra '.$adminString.' '.$optionsString.'</a><br /> <br /><a href="?lg='.$lg.'&x='.$x.'&y=2">'.$gestionString.' '.$surString.' '.$adminString.'</a> | <a href="?lg='.$lg.'&x='.$x.'&y=3">'.$ajoutString.' '.$surString.' '.$adminString.'</a><br /> <br /><b>'.$gestionString.' '.$surString.' '.$aideString.'</b><br /> <br /><!-- <a href="'.$local.'?lg='.$lg.'&x='.$x.'&y=7">'.$gestionString.' '.$surString.' '.$topicString.'/'.$commentString.'</a> | <a href="'.$local.'?lg='.$default_lg.'&x='.$x.'&y=7&send=edit">'.$ajoutString.' '.$surString.' '.$topicString.'/'.$commentString.'</a><br /> <br /> --><b>'.$modificationString.' '.$detexteString.'</b> | <a href="?lg='.$lg.'&x='.$x.'&y=6">'.$ajoutString.' '.$detexteString.'</a></p>';
*/

$content .= $admin_menu.'<div style="width:98%;clear:right;">';

if (!isset($send)) {

	if ( !isset($helpId) || !preg_match("/^[0-9]+\$/", $helpId) ) {
		if	($nRowsHelp < 3)	
  $content .= 'Remplir ce formulaire pour enregistrer une nouvelle Aide.<br />'.gen_form($lg,$x,$y).'> '.$titreString.'<br /><input name="helpTitle" type="text" size="60" maxlength="60" /><br />> '.$descriptionString.'<br /><textarea name="helpEntry" rows="5" cols="20"></textarea><br />> '.$pageString.' <select name="helpPg"><option> >> choisir </option><option value="0"> '.$modificationString.' '.$detexteString.' </option><option value="1"> '.$imgString.' </option><option value="2"> '.$docString.' </option></select><input name="helpLang" type="hidden" value="fr" /><!--  | > Langue <select name="helpLang"><option> >> choisir </option><option value="en"> English </option><option selected value="fr"> Fran&ccedil;ais </option><option value="it"> Italiano </option></select> --><br /> <br /><input name="send" type="submit" value="'.$envoyerString.'" /> | <input type="reset" value="Reset" /> | <a href="javascript:history.back()//">'.$retourString.'</a><br />Aide (\n\r represente un saut de ligne/is a line break, utiliser ` pour les apostrophes/use ` for apostrophes, ne pas utiliser d&#039;accents/no accented chars allowed [a-zA-Z])</form>';

		if ( ( (!isset($ordre)) || (!$ordre = 'ASC') || (!$ordre = 'DESC') ) || 
			( (!isset($par)) || (!$par == 'helpid') || (!$par == 'helppg') || (!$par == 'helplang') || (!$par == 'helptitle') || (!$par == 'helpentry') )
			) {
			$ordre = 'DESC';
			$par = 'helppg';
		}
		if	($ordre = 'ASC')	$ordrestring = $ASCString	;
		if	($ordre = 'DESC')	$ordrestring = $DESCString	;

		if	($par == 'helpid')	$parstring = $numidString	;
		if	($par == 'helppg')	$parstring = $numString.' '.$deString.' '.$pageString	;
		if	($par == 'helplang')	$parstring = $langueString	;
		if	($par == 'helptitle')	$parstring = $titreString	;
		if	($par == 'helpentry')	$parstring = $descriptionString	;

		$passedordre = $ordre;

		if	($passedordre == 'ASC')	($nextordre = 'DESC')	;
		if	($passedordre == 'DESC')	($nextordre = 'ASC')	;

		$selectHead = '<center>'.gen_form($lg,$x,$y).'<label for="ordre">'.$ordreString.' : </label><select name="ordre"><option value="'.$ordre.'"> >> '.$ordrestring.' </option><option value="ASC"> '.$ASCString.' </option><option value="DESC"> '.$DESCString.' </option></select> | <label for="par">'.$parString.' : </label><select name="par"><option value="'.$par.'"> >> '.$parstring.' </option><option value="helpid"> '.$numidString.' </option><option value="helppg"> '.$numString.' '.$deString.' '.$pageString.' </option><!-- <option value="helplang"> Langue </option> --><option value="helptitle"> '.$titreString.' </option><option value="helpentry"> '.$descriptionString.' </option></select> || <input type="submit" value="Relancer" /></form></center>';

		$tableHead = '<table align="center" style="width:95%" border="1" cellspacing="2" cellpadding="2"><tr><th>'.$infodbString.'<br />'.$langueString.'</th><th>'.$paramsString.' '.$aideString.'</th><th>'.$optionsString.'</th></tr><tr><td><a href="?lg='.$lg.'&x='.$x.'&y='.$y.'&par=helpid&ordre='.$nextordre.'">'.$numidString.'</a><br /><a href="?lg='.$lg.'&x='.$x.'&y='.$y.'&par=helplang&ordre='.$nextordre.'">'.$langueString.'</a></td><td><a href="?lg='.$lg.'&x='.$x.'&y='.$y.'&par=helppg&ordre='.$nextordre.'">'.$pageString.'</a><br /><a href="?lg='.$lg.'&x='.$x.'&y='.$y.'&par=helptitle&ordre='.$nextordre.'">'.$titreString.'</a><br /><a href="?lg='.$lg.'&x='.$x.'&y='.$y.'&par=helpentry&ordre='.$nextordre.'">'.$descriptionString.'</a></td><td>&nbsp;<!-- a etudier --></td></tr>';

		$fullread = @mysql_query("
							SELECT * FROM $tblhelp
							ORDER BY $par $ordre
							");
		$fullnRows = @mysql_num_rows($fullread);

		$read = @mysql_query("
						SELECT * FROM $tblhelp
						ORDER BY $par $ordre
						$queryLimit
						");
		$nRows = @mysql_num_rows($read);

		$totalpg = ceil( $fullnRows / $listPerpg );
		for ($i=1;$i<=$totalpg;$i++) {
			if ($i == $pg) {
				$pages .= ' <b> '.$i.' </b> ';
			} else {
				$pages .= ' <a href='.$local.'?lg='.$lg.'&x='.$x.'&y='.$y.'&par='.$par.'&ordre='.$ordre.'&pg='.$i.'>'.$i.'</a> ';
			}
		}

  $content .= $selectHead;

		if (!isset($nRowsHelp)) {
  $content .= '<p>&nbsp;'.$pasdeString.' '.$enregistrementString.' '.$surString.' '.$aideString.'.</p>';
		} else {
			if ($nRowsHelp == '1') {
  $content .= '<p>&nbsp;'.$nRowsHelp.' '.$class_conjugaison->plural($enregistrementString,'M',$nRowsHelp).' '.$surString.' '.$aideString.'.</p>';
			} else {
  $content .= '<p>&nbsp;<a href="?lg='.$lg.'&x='.$x.'&y='.$y.'&par='.$par.'&ordre='.$ordre.'">'.$nRowsHelp.' '.$class_conjugaison->plural($enregistrementString,'M',$nRowsHelp).' '.$surString.' '.$aideString.'</a></p>';
			}

			if	($listPerpg < $fullnRows)
  $content .= $pages.' << &nbsp;</p>'	;

  $content .= $tableHead;

			for ($i=0;$i<$nRows;$i++) {
				$row = mysql_fetch_array($read);
  $content .= '<tr><td>'.$row["helpid"].'<br />'.$row["helplang"].'</td><td>'.$row["helppg"].'<br />'.$row["helptitle"].'<br />'.$row["helpentry"].'</td><td><a href="?lg='.$lg.'&x='.$x.'&y=4&send=delete&helpId='.$row["helpid"].'"><img src="'.$mainurl.'images/delete.gif" width="10" height="10" align="right" border="0" alt="'.$effacerString.'" /></a><br /><a href="?lg='.$lg.'&x='.$x.'&y=4&send=edit&helpId='.$row["helpid"].'">'.$modifierString.'</a></td></tr>';
			}

  $content .= '</table><br />';

			if	($listPerpg < $fullnRows)
  $content .= $pages.' << &nbsp;</p>'	;

			if ($nRowsHelp == '1') {
  $content .= '<p>&nbsp;'.$nRowsHelp.' '.$class_conjugaison->plural($enregistrementString,'M',$nRowsHelp).' '.$surString.' '.$aideString.'.</p>';
			} else {
  $content .= '<p>&nbsp;<a href="?lg='.$lg.'&x='.$x.'&y='.$y.'&par='.$par.'&ordre='.$ordre.'">'.$nRowsHelp.' '.$class_conjugaison->plural($enregistrementString,'M',$nRowsHelp).' '.$surString.' '.$aideString.'</a></p>';
			}
		}

	} else {
		Header("Location: $redirect");
	}

} else if ($send == $envoyerString) {

	$count_exist = sql_nrows($dbtable, " WHERE helppg='$helpPg' AND helplang='$helpLang' ");

	if ( !isset($helpTitle) || preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $helpTitle) || 
		!isset($helpEntry) || preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $helpEntry) || 
		!isset($helpPg) || !preg_match("/^[0-2]+\$/", $helpPg) || 
		!isset($helpLang) || !preg_match("/^[a-z]{2}\$/", $helpLang) || 
		($count_exist > '0')
		) {

  $content .= '<p style="text-align: center"><font color="Red"><b>'.$erreurString.'!</b></font><br /> <br />'.$listecorrectionString.'Check and correct the following:</p><table border=0 cellspacing=0 cellpadding=0><tr><td align=left><ul>';

		if ( !$helpTitle || preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $helpTitle) ) {
  $content .= '<li>'.$titreString.' > '.$error_invmiss.'<br /> <br /></li>'; }
		if ( !$helpEntry || preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $helpEntry) ) {
  $content .= '<li>'.$descriptionString.' > '.$error_invmiss.'<br /> <br /></li>'; }
		if ( !$helpPg||!preg_match("/^[0-2]+\$/", $helpPg) ) {
  $content .= '<li>'.$pageString.' > '.$error_invmiss.'<br /> <br /></li>'; }
		if ( !$helpLang|| !preg_match("/^[a-z]{2}\$/", $helpLang) ) {
  $content .= '<li>'.$langueString.' > '.$error_invmiss.'</li>'; }
		if ($count_exist > '0') {
  $content .= '<li>Aide d&eacute;j&agrave; existante !</li>'; }

  $content .= '</ul></td></tr></table><p style="text-align: center"><a href="javascript:history.back()//">'.$retourString.'</a></p>';

	} else {

		$insertquery = @mysql_query("
								INSERT INTO $dbtable 
								(`helpid`, `helppg`, `helplang`, `helptitle`, `helpentry`)
								VALUES 
								('', '$helpPg', '$helpLang', '$helpTitle', '$helpEntry')
								");

		if (!$insertquery) {

	$content .= '<p style="text-align: center">'.$error_request.'<br />&nbsp;<br /><a href="javascript:history.back()//">'.$retourString.'</a></p>';

		} else {
			$redirect .= "admin/?lg='.$lg.'&x=z&y=$y";
			Header("Location: $redirect");
		}
	}

} else if ($send == "edit") {

	$read = sql_get($dbtable, " WHERE helpid=$helpId ", "helpid, helptitle, helpentry, helppg, helplang");

	if (isset($read[3])) {
		if	($read[3] == "0")	$varhelpPg = $modificationString.' '.$detexteString	;
		if	($read[3] == "1")	$varhelpPg = $imgString	;
		if	($read[3] == "2")	$varhelpPg = $docString	;
		if	(!isset($varhelpPg))	$varhelpPg = $modificationString.' '.$detexteString	;
	} else {
		$varhelpPg = "Toutes";
	}
	if (isset($read[4])) {
		if	($read[4] == "en")	$varhelpLang = "English"	;
		if	($read[4] == "fr")	$varhelpLang = "Fran&ccedil;ais"	;
		if	($read[4] == "it")	$varhelpLang = "Italiano"	;
	} else {
		$varhelpLang = "Fran&ccedil;ais";
	}

  $content .= 'Remplir ce formulaire pour enregistrer une nouvelle Aide.<br />'.gen_form($lg,$x,$y).'<input name="newhelpId" type="hidden" value="'.$helpId.'">> '.$titreString.'<br /><input name="newhelpTitle" type="text" size="60" maxlength="60" value="'.$read[1].'" /><br />> '.$descriptionString.'<br /><textarea name="newhelpEntry" rows="5" cols="20">'.$read[2].'</textarea><br />> '.$pageString.' <select name="newhelpPg"><option value="'.$read[3].'"> >> '.$varhelpPg.' </option><option value="0"> '.$modificationString.' '.$detexteString.' </option><option value="1"> '.$imgString.' </option><option value="2"> '.$docString.' </option></select><input name="newhelpLang" type="hidden" value="fr" /><!--  | > Langue <select name="newhelpLang"><option value="'.$read[4].'"> >> '.$varhelpLang.' </option><option value="en"> English </option><option selected value="fr"> Fran&ccedil;ais </option><option value="it"> Italiano </option></select> --><br /> <br /><input name="send" type="submit" value="'.$sauverString.'" /> | <input type="reset" value="Reset" /> | <a href="javascript:history.back()//">'.$retourString.'</a><br />'.$aideString.' (\n\r represente un saut de ligne, utiliser ` pour les apostrophes, ne pas utiliser d&#039;accents [a-zA-Z])</form>';

} else if ($send == $sauverString) {

	if ( !isset($newhelpTitle) || preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $newhelpTitle) || 
		!isset($newhelpEntry) || preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $newhelpEntry) || 
		!isset($newhelpPg) || !preg_match("/^[0-2]+\$/", $newhelpPg) || 
		!isset($newhelpLang) || !preg_match("/^[a-z]{2}\$/", $newhelpLang)
		) {

  $content .= '<p style="text-align: center"><font color="Red"><b>'.$erreurString.'!</b></font><br /> <br />'.$listecorrectionString.'Check and correct the following:</p><table border=0 cellspacing=0 cellpadding=0><tr><td align=left><ul>';

		if ( !$newhelpTitle || preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $newhelpTitle) ) {
  $content .= '<li>'.$titreString.' > '.$error_invmiss.'<br /> <br /></li>'; }
		if ( !$newhelpEntry || preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $newhelpEntry) ) {
  $content .= '<li>'.$descriptionString.' > '.$error_invmiss.'<br /> <br /></li>'; }
		if ( !$newhelpPg||!preg_match("/^[0-2]+\$/", $newhelpPg) ) {
  $content .= '<li>'.$pageString.' > '.$error_invmiss.'<br /> <br /></li>'; }
		if ( !$newhelpLang|| !preg_match("/^[a-z]{2}\$/", $newhelpLang) ) {
  $content .= '<li>'.$langueString.' > '.$error_invmiss.'</li>'; }

  $content .= '</ul></td></tr></table><p style="text-align: center"><a href="javascript:history.back()//">'.$retourString.'</a></p>';

	} else {

		$read = sql_get($dbtable, " WHERE helpid='$newhelpId' ", "helpid, helptitle, helpentry, helppg, helplang");

		$sql_helpTitle = "";
		$sql_helpEntry = "";
		$sql_helpPg = "";
		$sql_helpLang = "";

		if	($read[1] != $newhelpTitle)	$sql_helpTitle = "helptitle='$newhelpTitle', "	;
		if	($read[2] != $newhelpEntry)	$sql_helpEntry = "helpentry='$newhelpEntry', "	;
		if	($read[3] != $newhelpPg)		$sql_helpPg = "helppg='$newhelpPg', "	;
		if	($read[4] != $newhelpLang)	$sql_helpLang = "helplang='$newhelpLang', "	;

		$updatequery = sql_update($dbtable, " SET
										$sql_helpPg
										$sql_helpLang
										$sql_helpTitle
										$sql_helpEntry
										helpid='$newhelpId'
										", " WHERE helpid='$newhelpId' 
										", "helpentry");

		if (!$updatequery) {
	$content .= '<p style="text-align: center">'.$error_request.'<br />&nbsp;<br /><a href="javascript:history.back()//">'.$retourString.'</a></p>';
		} else {
  $content .= '<p style="text-align: center"><font color="Green"><b>'.$enregistrementString.' '.$effectueString.'  !</b></font><br /> <br /><a href="?lg='.$lg.'&x=z&y=4">'.$retourString.' '.$verslisteString.' '.$surString.' '.$aideString.'.</a></p>';
		}
	}

} else if ($send == "delete") {

	if ( !isset($helpId) || !preg_match("/^[0-9]+\$/", $helpId) ) {
		Header("Location: $redirect");
	} else {
		$deletequery = sql_del($dbtable, " WHERE helpid='$helpId' ");
		if ($deletequery > '0') {
  $content .= '<p style="text-align: center">'.$error_request.'<br />&nbsp;<br /><a href="javascript:history.back()//">'.$retourString.'</a></p>';
		} else {
  $content .= '<p style="text-align: center"><font color="Green"><b>'.$enregistrementString.' '.$effaceString.' !</b></font><br /> <br /><a href="?lg='.$lg.'&x=z&y=4">'.$retourString.' '.$verslisteString.' '.$surString.' '.$aideString.'.</a></p>';
		}
	}

} else {
	Header("Location: $redirect");
}
$content .= '</div>';
?>