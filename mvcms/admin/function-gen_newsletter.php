<?php #۞ #
if (stristr($_SERVER['PHP_SELF'], basename(__FILE__))){include '_security.php';Header("Location: $redirect");Die();}

// ########## generate last numero de FORUM
function gen_docpdfdl($nlid) {
/*
	global $mainurl,$tbldocpdfdl;
	$editdocpdfdl = sql_get($tbldocpdfdl, " WHERE docpdfdlstatut='Y' ORDER BY docpdfdldate DESC LIMIT 1 ", "docpdfdlpdf,docpdfdlimg,docpdfdltitre");
	if ( (!isset($editdocpdfdl[0])) || ($editdocpdfdl[0] == ".") ) {
		$content_docpdfdl = '';//'</td><td align="center" valign="middle" width="150">&nbsp;';
	} else {
		$pdf_link = '<a href="'.$mainurl.$editdocpdfdl[0].'?nlid='.$nlid.'">';
		if  ( stristr($nlid, "-0") || ($nlid == '0') )  $pdf_link = '<a href="'.$mainurl.'espacemembres.php">';
		$content_docpdfdl = '</td><td align="center" valign="middle" width="150">'.$pdf_link.'<img '.show_img_attr($mainurl.$editdocpdfdl[1]).' src="'.$mainurl.$editdocpdfdl[1].'" border="0" alt="'.$editdocpdfdl[2].'" vspace="5" hspace="5"><br /><img src="'.$mainurl.'images/pdflogo.gif" width="16" height="16" alt="'.$editdocpdfdl[2].'" vspace="5" hspace="5" border="0" /><b>'.$editdocpdfdl[2].'</b><br /><sup>T&eacute;l&eacute;charger le dernier num&eacute;ro de FORUM</sup></a>';
	}
//	$content_docpdfdl .= '</td></tr></table>';
return $content_docpdfdl;
*/
}
// ########## generate list of activites in the future

// ########## generate list of activities to come
function gen_activite($nlid) {
    global $connection, $mainurl,$dbtime,$urlclient,$tblevents,$eventsString,$tblgeneralnews,$generalnewsString;
	$generalnewsRead = mysqli_query($connection, "
		                           SELECT * FROM $tblgeneralnews
		                           WHERE generalnewsstatut='Y'
		                           AND generalnewsarchive='N'
		                           AND generalnewspublish='Y'
		                           ORDER BY generalnewsdate DESC
		                           LIMIT 5
		                          ");
	$generalnewsnRows = mysqli_num_rows($generalnewsRead);
	if ( (!isset($generalnewsnRows)) || ($generalnewsnRows == "0") ) {
		$content_generalnews = "";
	} else {
		$generalnews = '';
		for ($i=0; $i<$generalnewsnRows; $i++) {
			$generalnewsrow = mysqli_fetch_array($generalnewsRead);
			$mini_generalnews = substr($generalnewsrow["generalnewsentry"], 0, 600);
			if  (strlen($generalnewsrow["generalnewsentry"]) > 600)  $mini_generalnews .= "..."  ;
			$generalnews .= '<tr><td><hr /><a href="'.$mainurl.'?generalnewsId='.$generalnewsrow["generalnewsid"].'&amp;nlid='.$nlid.'"><b>'.$generalnewsrow["generalnewstitle"].'</b></a></td></tr>';//'.$mini_generalnews.'<br />
		}
	}
	if  (!isset($content_generalnews))
	$content_generalnews = '<table width="90%" align="center" border="0" cellpadding="0" cellspacing="0"><h2><i>'.$generalnewsString.'</i></h2>'.$generalnews.'</table><br />'  ;

	$eventsRead = mysqli_query($connection, "
		                           SELECT * FROM $tblevents
		                           WHERE eventsstatut='Y'
		                             AND eventsfrom>=$dbtime
		                           ORDER BY eventsfrom ASC
		                          ");
	$eventsnRows = mysqli_num_rows($eventsRead);
	if ( (!isset($eventsnRows)) || ($eventsnRows == "0") ) {
		$content_events = "";
	} else {
		$events = '';
		for ($i=0; $i<$eventsnRows; $i++) {
			$eventsrow = mysqli_fetch_array($eventsRead);
			$mini_events = substr($eventsrow["eventsentry"], 0, 600);
			if  (strlen($eventsrow["eventsentry"]) > 600)  $mini_events .= "..."  ;
			$events .= '<tr><td><hr /><a href="'.$mainurl.'?eventsId='.$eventsrow["eventsid"].'&amp;nlid='.$nlid.'"><b>'.human_date($eventsrow["eventsfrom"],NULL,'nl').' <i>'.sql_stringit('eventslocation',$eventsrow["eventslocation"]).' </i><br />'.$eventsrow["eventstitle"].'</b></a><br /></td></tr>';//'.$mini_events.'<br />
		}
	}
	if  (!isset($content_events))
	$content_events = '<table width="90%" align="center" border="0" cellpadding="0" cellspacing="0"><h2><i>Upcoming '.$eventsString.'</i></h2>'.$events.'</table>'  ;

return $content_generalnews.$content_events.'<br /> <br />';
}
// ########## generate list of activities to come

// ########## generate list of new galeriephoto since last sent newsletter
function gen_galerie($nlid) {
/*
    global $connection, $mainurl,$urlclient,$s,$tblnewsletter,$tblgalerie,$tblgaleriephoto;
	$editnewsletter = sql_get($tblnewsletter, " ORDER BY newsletterdate DESC ", "newsletterdate");
	$sql_galerie = " AND galeriedate>'$editnewsletter[0]' ";
	if  ( (!isset($editnewsletter[0])) || ($editnewsletter[0] == ".") )  $sql_galerie = ""  ;
	$galerieread = mysqli_query($connection, "
						  SELECT * FROM $tblgalerie
						  WHERE galeriestatut='Y'
						  $sql_galerie
						  ORDER BY galeriedate ASC
						 ");
	$galerienRows = mysqli_num_rows($galerieread);
	if ( (!isset($galerienRows)) || ($galerienRows == "0") ) {
		$content_galerie = "";
	} else {
		$galerie = "";
		for ($i=0; $i<$galerienRows; $i++) {
			$galerierow = mysqli_fetch_array($galerieread);
			$row_galerieid = $galerierow['galerieid'];
			$nYphoto = sql_nrows($tblgaleriephoto, " WHERE galeriephotogalerieid='$row_galerieid' AND galeriephotostatut='Y' ");
			if  ($nYphoto <= 1)  $s = ""  ;
			if ($nYphoto > 0) {
				$photo1 = sql_get($tblgaleriephoto, " WHERE galeriephotogalerieid='$row_galerieid' AND galeriephotostatut='Y' ", "galeriephotoimg");
				$galerie .= '<table width="90%" align="center" border="0" cellpadding="0" cellspacing="0"><tr><td><a href="http://'.$mainurl.'?x=5&y=2&galerieId='.$row_galerieid.'&nlid='.$nlid.'"><img  '.show_img_attr($mainurl.$photo1[0]).' src="'.$mainurl.$photo1[0].'" alt="'.$galerierow["galerietitre"].'" vspace="5" hspace="5" align="right" border="0" /><b>'.$galerierow["galerietitre"].' ( '.$nYphoto.' photo'.$s.' )</b></a><br />'.$galerierow["galeriedesc"].'</td></tr><tr><td><hr /></td></tr></table>';
			}
			$s = "s";
		}
	}
	if  (!isset($content_galerie))
		$content_galerie = '<br /><i>Les derni&egrave;res Galeries Photos</i><hr />'.$galerie  ;
return $content_galerie;
*/
}
// ########## generate list of new galeriephoto since last sent newsletter

function gen_newsletter($send,$nlid) {
  // template
  global $mail_headers,$mail_headers_text,$mail_headers_html,$_tpl_mail_newsletter,$personalized_newsletter;
	// Sitewide
	global $_POST,$notice,$error,$trace,$mainurl,$urlclient,$client,$lg,$x,$y,$form_methody,$send,$getcwd,$up,$urladmin,$slogan,$cosite,$coname,$cologo,$cologo_img,$clientemail,$default_pass,$coinfo,$optionselected,$tinyMCE,$text_style_js,$i_elm;
	// Strings
  global $codename,$cherString,$nomutilString,$motdepasseString,$envoyerString,$cologo,$class_conjugaison,$envoyeString,$parString,$sujetString,$emailString,$nonString,$membreString,$messageString,$newsletterString,$retourString,$copyrightnoticeString,$dejaString,$effectueString,$modifieString,$parString,$userString;
	// nl generated
  global $newsletterSujet,$newsletterMessage,$nlid,$membreId,$membreGendre,$membreUtil,$membrePass,$membreEmail_md5,$membreEmail,$membreStatut,$membrePriv,$membrePriv_astext,$membreTitle,$content_membre;
  // nl_strings
  global $nl_adhesionpreteString,$nl_copiercollerString,$nl_ouvrirsursiteString,$nl_messagepreviewString,$nl_sepadressesemailString;

  if (!isset($nl_adhesionpreteString))
  $nl_adhesionpreteString = "Your request for registration is ready, please use the following credentials";
  $procedure_adhesion = '';
	if ((!isset($membreUtil)) || ($membreUtil == '.') || ($membreUtil == '')) {
    $membreUtil = strtoupper($codename).' '.$class_conjugaison->plural(ucfirst($membreString),'M','2');
    $procedure_adhesion = '
    '.$nl_adhesionpreteString.'<br />
    If your username and password are not included, please contact us to be inserted in the system, you will then receive your credentials.
    <br /> <br />';
    /*
    - '.$nomutilString.' : <i>'.$nomutilString.'</i><br />
    - '.$motdepasseString.' : <i>'.$emailString.'</i>
    '  ;
  You will be able to access pages with the following privileges: $this_isPriv_astext
    */
  } else {
    $procedure_adhesion = '
    '.$nl_adhesionpreteString.' :
    <br /> <br />
    - '.$nomutilString.' : '.$membreUtil.'<br />
    - '.$motdepasseString.' : '.((($membreEmail != '') && ($membreEmail_md5 == $membrePass))?$membreEmail:'****** ('.$dejaString.' '.strtolower($modifieString.' '.$parString.' '.$membreString).')').'
  <br /> <br />'  ;
  }
  $chermembre = $class_conjugaison->plural(ucfirst($cherString),'M','2').' '.(isset($membreTitle)?$membreTitle:$membreUtil).',
  <br /> <br />';

	if ($send == $envoyerString) {
  	$nonhtml_content_generated = html_entity_decode(strip_tags($chermembre.$procedure_adhesion.$newsletterMessage));
  	//$content_membre.
  	$html_content_generated = $chermembre
                              .$procedure_adhesion
                              .$newsletterMessage
                              .gen_docpdfdl($nlid)
                              .gen_activite($nlid)
                              .gen_galerie($nlid);
  	$footer = "$newsletterString
".$class_conjugaison->plural($envoyeString,'F',1)."
, $parString <a href=\"$mainurl?nlid=$nlid\">$slogan</a><br /><sup>$copyrightnoticeString
<br /> <br />
$nl_ouvrirsursiteString $coname,<br />
$nl_copiercollerString : <a href=\"$mainurl?nlid=$nlid\">$mainurl?nlid=$nlid</a>";

    if  (($membreEmail != '') && ($membreEmail_md5 == $membrePass)) {
      // no procedure adhesion here, will be included with credentials template
      $this_isUtil = $membreUtil;
      $this_isPass = $membreEmail;
      $this_isPriv = $membrePriv;
      $this_isTitle = $class_conjugaison->plural(ucfirst($cherString),'M','1').' '.$membreTitle;
      $this_isPriv_astext = $membrePriv_astext;
      $nonhtml_content_generated = html_entity_decode(strip_tags($chermembre
                                                                  .$newsletterMessage));
      $html_content_generated = $newsletterMessage
                              .gen_docpdfdl($nlid)
                              .gen_activite($nlid)
                              .gen_galerie($nlid);
      ################################## IMPORT TEMPLATE
      if (file_exists($getcwd.$up.$safedir.'_tpl_mail_credentials.php'))
        include($getcwd.$up.$safedir.'_tpl_mail_credentials.php');
      else
        include($getcwd.$up.$urladmin.'defaults/_tpl_mail_credentials.php');
  		$newsletter_msg = $_tpl_mail_credentials;
    } else {
      ################################## IMPORT TEMPLATE
      if (file_exists($getcwd.$up.$safedir.'_tpl_mail_newsletter.php'))
        include($getcwd.$up.$safedir.'_tpl_mail_newsletter.php');
      else
        include($getcwd.$up.$urladmin.'defaults/_tpl_mail_newsletter.php');
  		$newsletter_msg = $_tpl_mail_newsletter;
    }
	} else { // show or new
	  $newsletter_msg = $nl_messagepreviewString.' :<br /> <br />';
	  $newsletter_msg .= gen_form($lg,$x,$y).($personalized_newsletter===false?'
<label for="newsletterCredentials"><b>Include credential informations?</b></label>
<input type="checkbox" name="newsletterCredentials" '.(isset($_POST['newsletterCredentials'])&&($_POST['newsletterCredentials']=='on')?$optionselected:'').'" />
<br /> <br />':'').'
<label for="newsletterSujet"><b>'.$sujetString.'</b></label><br />
<input type="text" name="newsletterSujet" size="60" maxlength="60" value="'.(isset($_POST['newsletterSujet'])?$_POST['newsletterSujet']:'').'" />
<br /> <br />
<label for="newsletterXmails"> <b>Extra '.$emailString.' ('.$nonString.' '.$membreString.')</b> '.$nl_sepadressesemailString.'</label><br />
<textarea name="newsletterXmails" rows="3" cols="40" style="width: 98%;">'.(isset($_POST['newsletterXmails'])?$_POST['newsletterXmails']:'').'</textarea>
<br />
<label for="newsletterMessage"> <b>'.$messageString.'</b> </label>
<br /> <br />
'.$content_membre.$chermembre.($tinyMCE===false?$text_style_js:'').'<br /><textarea id="elm'.($i_elm>=-1?$i_elm+=1:'').'" name="newsletterMessage" rows="20" cols="40" style="width: 97%; height: 300px; min-height: 300px;">'.format_edit((isset($_POST['newsletterMessage'])?$_POST['newsletterMessage']:''),'edit').'</textarea><br />';//'<textarea name="newsletterMessage" rows="5" cols="40" style="width: 98%;">'.(isset($_POST['newsletterMessage'])?$_POST['newsletterMessage']:'').'</textarea><br />';
	  $newsletter_msg .= gen_docpdfdl($nlid);
	  $newsletter_msg .= gen_activite($nlid);
	  $newsletter_msg .= gen_galerie($nlid);
	  $newsletter_msg .= '</td></tr></table><input type="submit" name="send" value="'.$envoyerString.'" /> | <a href="javascript:history.back()//">'.$retourString.'</a>
</form>';
	}
return $newsletter_msg;
}
