<?php #۞ # ADMIN

include '_security.php';

$dbtable = $tblstring;

  $content .= '<p><a href="?lg='.$lg.'&x=z&y=1">Extra '.$adminString.' '.$optionsString.'</a></b><br /> <br /><a href="?lg='.$lg.'&x='.$x.'&y=2">'.$gestionString.' '.$surString.' '.$adminString.'</a> | <a href="?lg='.$lg.'&x='.$x.'&y=3">'.$ajoutString.' '.$surString.' '.$adminString.'</a><br /> <br />';
	if ($tinyMCE === false) 
  $content .= '<a href="?lg='.$lg.'&x='.$x.'&y=4">'.$gestionString.' '.$surString.' '.$aideString.'</a><br /> <br />';
  $content .= '<!-- <a href="'.$local.'?lg='.$lg.'&x='.$x.'&y=7">'.$gestionString.' '.$surString.' '.$topicString.'/'.$commentString.'</a> | <a href="'.$local.'?lg='.$default_lg.'&x='.$x.'&y=7&send=edit">'.$ajoutString.' '.$surString.' '.$topicString.'/'.$commentString.'</a><br /> <br /> --><b>'.$modificationString.' '.$detexteString.'</b> | <a href="?lg='.$lg.'&x='.$x.'&y=6">'.$ajoutString.' '.$detexteString.'</a> | <b>'.$ajoutString.' '.$detexteString.'</b></p>';

#######################################################MONTRE FORME string
#######################################################SHOW string FORM
if (!isset($send)) {

  $content .= $form_methody.'> '.$typeString.' '.$detexteString.' <select name="stringType"><option value="general"> general &nbsp;</option><option value="menu"> menu &nbsp;</option><!-- <option value="zone"> zone &nbsp;</option> --></select><br />> '.$titreString.'<br /><input name="stringTitre" type="text" size="60" maxlength="60" /><br />> '.$descriptionString.'<br /><textarea name="stringDesc" rows="5" cols="20" style="width: 100%"></textarea><br /> <br /><input name="send" type="submit" value="'.$envoyerString.'" /> | <a href="javascript:history.back()//">'.$retourString.'</a></form>';

#######################################################VALIDE ET POSTE NOUVELLE string 
#######################################################VALIDATE AND SEND NEW string
} else if ($send == $envoyerString) {

	$stringType = strip_tags($stringType);
	$stringTitre = html_encode(strip_tags(stripslashes($stringTitre)));
	$stringDesc = html_encode(strip_tags(stripslashes($stringDesc)));

	$check = sql_nrows($dbtable, " WHERE stringtype='$stringType' AND stringtitle='$stringTitre' AND stringentry='$stringDesc' ");

	if ( !$stringType || (!$stringType == "general") || (!$stringType == "menu") || (!$stringType == "zone") || 
		!$stringTitre || preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $stringTitre) || 
		(strlen($stringTitre) > 60) || 
		!$stringDesc || preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $stringDesc) || 
		($check > '0')
		) {

  $content .= '<p style="text-align: center"><font color="Red"><b>'.$erreurString.'!</b></font><br /> <br />'.$listecorrectionString.'</p><table border=0 cellspacing=0 cellpadding=0><tr><td align=left><ul>';

		if ( !$stringType || (!$stringType == "general") || (!$stringType == "menu") || (!$stringType == "zone") ) {
  $content .= '<li>'.$typeString.' > '.$error_invmiss.'<br /> <br /></li>'; }
		if ( !$stringTitre || preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $stringTitre) || (strlen($stringTitre) > 60) ) {
  $content .= '<li>'.$titreString.' > '.$error_invmiss.'<br /> <br /></li>'; }
		if ( !$stringDesc || preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $stringDesc) ) {
  $content .= '<li>'.$descriptionString.' > '.$error_invmiss.' <br /> <br /></li>'; }
		if ($check > '0') {
  $content .= '<li>'.$enregistrementString.' '.$dejaexistantString.' !<br /> <br /></li>'; }

  $content .= '</ul></td></tr></table><p style="text-align: center"><a href="javascript:history.back()//">'.$retourString.'</a></p>';

	} else {

			$insertquery = @mysqli_query("
									INSERT INTO $dbtable 
									(`stringid`, `stringpg`, `stringlang`, `stringtype`, `stringtitle`, `stringentry`)
									VALUES 
									('', '', '$lg', '$stringType', '$stringTitre', '$stringDesc')
									");

			if (!$insertquery) {

  $content .= '<p style="text-align: center">'.$error_request.'<br />&nbsp;<br /><a href="javascript:history.back()//">'.$retourString.'</a></p>';

			} else {

					$editstring = sql_get($dbtable, " WHERE stringtitle='$stringTitre' ", "stringid");

  $content .= '<p style="text-align: center"><font color="Green"><b>'.$enregistrementString.' '.$effectueString.' [i]<br /> <br />'.$pourString.' "<i>'.$stringType.'</i> '.$stringTitre.'"</b></font><br /> <br /><a href="?lg='.$lg.'&x='.$x.'&y=5">'.$retourString.' '.$verslisteString.' '.$detexteString.'</a></p>';

			}
	}

} else {
	Header("Location: $redirect");
}
