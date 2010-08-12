<?PHP #۞ # ADMIN
if (stristr($_SERVER['PHP_SELF'],'newslettersend.php')) {
  include '_security.php';
  Header("Location: $redirect");Die();
}

if (!isset($personalized_newsletter))
$personalized_newsletter = false;

include 'function-gen_newsletter.php';

$dbtable = $tblnewsletter;

$thisyear = date('Y');
$lastyear = $thisyear-1;
	/*
  $sql_membre =  " AND membrecotisation='$thisyear' ";
  if  (date('m') < '04')  $sql_membre = " AND membrecotisation>='$lastyear' "  ;
  */
$sql_membre = " WHERE userstatut='Y' "; // not working with cotisation on this one yet

// THIS IS FOR ONLY CONFIRMED
/****************************************************************************
$membreread = @mysql_query("
						  SELECT * FROM $tblmembre
						  WHERE membrestatut='Y'
						  AND membredate!='0000-00-00 00:00'
						  AND membreemail!='adresse@changer.ctt'
						  $sql_membre
						 ");
****************************************************************************/

$membreread = @mysql_query("
						  SELECT * FROM $tbluser
						  $sql_membre
						 ");

$nRowsUser = @mysql_num_rows($membreread);

if ($nRowsUser == '0') {
//  $content .= 'Aucun membre n&#039;est actif, pas de newsletter &agrave; envoyer !<br />';
  $content .= $nRowsUser.' '.$membreString.' '.$enregistreString.': '.$pasdeString.' '.$newsletterString.' '.$aenvoyerString.' !<br />';
} else {
/*
	if (($send == 'new') || ($send != $envoyerString)) {
		if ($nRowsUser == '1') {
	  $content .= $nRowsUser.' membre va recevoir cette newsletter.<br />';
		} else {
	  $content .= $nRowsUser.' membres vont recevoir cette newsletter<!--  (confirmes donc ayant fait la demarche de confirmation en changeant leur mot de passe et cotisants de 2007 et 2006 si ce mois-ci est avant avril) -->.<br />';
		}
	} else {
		if ($nRowsUser == '1') {
	  $content .= $nRowsUser.' membre a re&ccedil;u cette newsletter.<br /> <br />';
		} else {
	  $content .= $nRowsUser.' membres ont re&ccedil;u cette newsletter<!--  (confirmes donc ayant fait la demarche de confirmation en changeant leur mot de passe et cotisants de 2007 et 2006 si ce mois-ci est avant avril) -->.<br /> <br />';
		}
	}
*/
  $content .= $nRowsUser.' '.$class_conjugaison->plural($membreString,'',$nRowsUser).' '.$class_conjugaison->plural($enregistreString,'',$nRowsUser).'<br />';
	$content_membre = '<table width="90%" align="center" border="0" cellpadding="0" cellspacing="0"><tr><td>';
	$nlid = "0";
	if ($send == 'new') {
    $content .= gen_newsletter($send,$nlid);
########################################################### ENVOYER
########################################################### ENVOYER
	} else if (($send == $envoyerString) && ($_SERVER["REQUEST_METHOD"] == "POST")) {
    $personalized_newsletter = (isset($_POST['newsletterCredentials'])&&($_POST['newsletterCredentials']=='on')?true:$personalized_newsletter);
		$newsletterSujet = strip_tags(stripslashes($newsletterSujet));
		$sql_newsletterSujet = html_encode($newsletterSujet);
		if ($tinyMCE === false)
		$newsletterMessage = nl2br(strip_tags(html_encode(stripslashes($newsletterMessage))));
		$newsletterXmails = is_valid_email($newsletterXmails);
		$checkmail = '';
		if ($personalized_newsletter === false) {
  		if (isset($rev_newsletterXmails[0]))
			if  ($rev_newsletterXmails[0] == ',')//  $checkmail .= '<font color="Red">Erreur : la liste ne peut se terminer par ,</font><br />';
        $rev_newsletterXmails[0] = substr($rev_newsletterXmails[0],0,-1);
		  while($row = mysql_fetch_array($membreread))
		  $newsletterXmails .= ($newsletterXmails==''?'':", ").is_valid_email($row["useremail"]);
    }
		if ($newsletterXmails != '') {
  		$rev_newsletterXmails = strrev($newsletterXmails);
  		if (isset($rev_newsletterXmails[0]))
			if  ($rev_newsletterXmails[0] == ',')//  $checkmail .= '<font color="Red">Erreur : la liste ne peut se terminer par ,</font><br />';
        $rev_newsletterXmails[0] = substr($rev_newsletterXmails[0],0,-1);
			$countemails = substr_count($newsletterXmails, ',');
			if ($countemails>0) {
			  $batch_emails = explode(',', $newsletterXmails);
			  for ($i=0;$i<$countemails;$i++) {
					if  (!is_email(trim($batch_emails[$i])))  $checkmail .= '<font color="Red">'.$error_inv.' '.$emailString.' : '.$batch_emails[$i].'</font><br />';
				}
			} else {
				if  (!is_email($newsletterXmails))  $checkmail .= '<font color="Red">'.$error_inv.' '.$emailString.' : '.$newsletterXmails.'</font><br />';
			}
		}
		$nrowsnewsletter = sql_nrows($tblnewsletter, " WHERE newslettersujet='$sql_newsletterSujet' ");
		if ( (!isset($newsletterSujet)) || preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $newsletterSujet) || ($newsletterSujet == "") || (strlen($newsletterSujet) > 60) || (!isset($newsletterMessage)) || preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $newsletterMessage) || ($newsletterMessage == "") || ($nrowsnewsletter > '0')
		   ) {
  $error .= '<font color="Red"><b>'.$erreurString.'!</b></font> '.$listecorrectionString.'<ul>';
			if ( !$newsletterSujet  ||  preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $newsletterSujet) || (strlen($newsletterSujet) > 60) )  
  $error .= '<li>'.$sujetString.' > '.$error_invmiss.' (max: 60 c.)</li>'  ;
			if ( !$newsletterMessage  ||  preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $newsletterMessage) )  
  $error .= '<li>'.$messageString.' > '.$error_invmiss.'</li>'  ;
			if ($nrowsnewsletter > '0')  
  $error .= '<li>'.$sujetString.' > '.$error_exists.'</li>'  ;
			if ($checkmail != '')  
  $error .= '<li>'.$erreurString.' '.$emailString.''.$checkmail.'</li>'  ;
  $error .= '</ul><a href="javascript:history.back()//">'.$retourString.'</a><br />';
		$send = "new";
  $content .= gen_newsletter($send,$nlid);
		} else {
			$newsletterContent = $content_membre.$newsletterMessage
                                          .gen_docpdfdl($nlid)
                                          .gen_activite($nlid)
                                          .gen_galerie($nlid);
			$insertquery = @mysql_query("
										 INSERT INTO $tblnewsletter 
										 (`newsletterid`, `newsletterstatut`, `newsletterdate`, `newslettersujet`, `newslettercontent`, `newslettersent`, `newslettererror`, `newsletterread`)
										 VALUES 
										 ('', 'Y', $dbtime, '$sql_newsletterSujet', '$newsletterContent', '', '', '')
										");
			if (!$insertquery) {
  $error .= $error_request.'<br />';
			} else {
        $codename = html_entity_decode($codename);
				$editnewsletter = sql_get($tblnewsletter, " WHERE newslettersujet='$sql_newsletterSujet' ", "newsletterid");
				$newsletterid = $editnewsletter[0];
				$nlid = $newsletterid.'-0';
				$newsletter_msg = gen_newsletter('show',$nlid);
				$newsletter_msg = wordwrap($newsletter_msg,70,$CRLF,true);
        //  contains_bad_str($newsletter_msg);// uncomment if not using multi-part which contains content-type info
          contains_bad_str($newsletterSujet);
          contains_newlines($newsletterSujet);
        $bcc_max = '20';
				if (($newsletterXmails != '') && ($countemails > $bcc_max)) {
					$batchemails = explode(', ', $newsletterXmails);
					$mail_this ='';
					for ($i=0;$i<=$countemails;$i++) {
						if ($i == '0') {
							$mail_this .= $batchemails[$i];
						} else if ($i<$bcc_max) {
							$mail_this .= ', '.$batchemails[$i];
						} else if ($i == $bcc_max) {
              if (stristr($_SERVER['HTTP_HOST'],"localhost"))
              $mail_conf = true;
              else
							$mail_conf = mail($coinfo, html_entity_decode($newsletterSujet), $newsletter_msg, "From: $codename <$coinfo>" . $CRLF . "Bcc: ".$mail_this.$mail_headers);
							if ($mail_conf === true) {
  $notice .= '<font color="Green"><b>'.$messageString.' <!--g&eacute;n&eacute;rique--> '.$class_conjugaison->plural($envoyeString,'M',1).' > </b></font> '.$coinfo.' + extra '.$emailString.' : '.$mail_this.'<br />'.(stristr($_SERVER['HTTP_HOST'],"localhost")?'<br /><textarea rows="30" cols="40" style="width: 98%;">'."$coinfo, ".html_entity_decode($newsletterSujet).", $newsletter_msg<br />, \"From: $codename <$coinfo>" . "<br />\r\n" . "Bcc: $mail_this".$mail_headers.'</textarea>':'');
							} else {
  $error .= '<font color="Red"><b>'.strtoupper($erreurString).' ! : </b></font><b>'.$messageString.' <!--g&eacute;n&eacute;rique--> '.$nonString.' '.$class_conjugaison->plural($envoyeString,'M',1).' > </b></font> '.$coinfo.' + extra '.$emailString.' : '.$mail_this.'<br />';
							}
							$mail_this = $batchemails[$i];
						} else if ($i == $countemails) {
							$mail_this .= ', '.$batchemails[$i];
              if (stristr($_SERVER['HTTP_HOST'],"localhost"))
              $mail_conf = true;
              else
							$mail_conf = mail($coinfo, html_entity_decode($newsletterSujet), $newsletter_msg, "From: $codename <$coinfo>" . $CRLF . "Bcc: ".$mail_this.$mail_headers);
							if ($mail_conf === true) {
  $notice .= '<font color="Green"><b>'.$messageString.' <!--g&eacute;n&eacute;rique--> '.$class_conjugaison->plural($envoyeString,'M',1).' > </b></font> '.$coinfo.' + extra '.$emailString.' : '.$mail_this.'<br />'.(stristr($_SERVER['HTTP_HOST'],"localhost")?'<br /><textarea rows="30" cols="40" style="width: 98%;">'."$coinfo, ".html_entity_decode($newsletterSujet).", $newsletter_msg<br />, \"From: $codename <$coinfo>" . "<br />\r\n" . "Bcc: $mail_this".$mail_headers.'</textarea>':'');
							} else {
  $error .= '<font color="Red"><b>'.strtoupper($erreurString).' ! : </b></font><b>'.$messageString.' <!--g&eacute;n&eacute;rique--> '.$nonString.' '.$class_conjugaison->plural($envoyeString,'M',1).' > </b></font> '.$coinfo.' + extra '.$emailString.' : '.$mail_this.'<br />';
							}
						} else {
							if (is_float($i/$bcc_max)) {
								$mail_this .= ', '.$batchemails[$i];
							} else {
                if (stristr($_SERVER['HTTP_HOST'],"localhost"))
                $mail_conf = true;
                else
								$mail_conf = mail($coinfo, html_entity_decode($newsletterSujet), $newsletter_msg, "From: $codename <$coinfo>" . $CRLF . "Bcc: ".$mail_this.$mail_headers);
								if ($mail_conf === true) {
  $notice .= '<font color="Green"><b>'.$messageString.' <!--g&eacute;n&eacute;rique--> '.$class_conjugaison->plural($envoyeString,'M',1).' > </b></font> '.$coinfo.' + extra '.$emailString.' : '.$mail_this.'<br />'.(stristr($_SERVER['HTTP_HOST'],"localhost")?'<br /><textarea rows="30" cols="40" style="width: 98%;">'."$coinfo, ".html_entity_decode($newsletterSujet).", $newsletter_msg<br />, \"From: $codename <$coinfo>" . "<br />\r\n" . "Bcc: $mail_this".$mail_headers.'</textarea>':'');
  							} else {
  $error .= '<font color="Red"><b>'.strtoupper($erreurString).' ! : </b></font><b>'.$messageString.' <!--g&eacute;n&eacute;rique--> '.$nonString.' '.$class_conjugaison->plural($envoyeString,'M',1).' > </b></font> '.$coinfo.' + extra '.$emailString.' : '.$mail_this.'<br />';
								}
								$mail_this = $batchemails[$i];
							}
						}
					}

				} else if (($newsletterXmails != '') && ($countemails <= $bcc_max)){
          if (stristr($_SERVER['HTTP_HOST'],"localhost"))
          $mail_conf = true;
          else
					$mail_conf = mail($coinfo, html_entity_decode($newsletterSujet), $newsletter_msg, "From: $codename <$coinfo>" . $CRLF . "Bcc: ".$newsletterXmails.$mail_headers);
					if ($mail_conf === true) {
  $notice .= '<font color="Green"><b>'.$messageString.' <!--g&eacute;n&eacute;rique--> '.$class_conjugaison->plural($envoyeString,'M',1).' > </b></font> '.$coinfo.' + extra '.$emailString.' : '.$newsletterXmails.'<br />'.(stristr($_SERVER['HTTP_HOST'],"localhost")?'<br /><textarea rows="30" cols="40" style="width: 98%;">'."$coinfo, ".html_entity_decode($newsletterSujet).", $newsletter_msg<br />, \"From: $codename <$coinfo>" . "<br />\r\n" . "Bcc: $newsletterXmails".$mail_headers.'</textarea>':'');
					} else {
  $error .= '<font color="Red"><b>'.strtoupper($erreurString).' ! : </b></font><b>'.$messageString.' <!--g&eacute;n&eacute;rique--> '.$nonString.' '.$class_conjugaison->plural($envoyeString,'M',1).' > </b></font> '.$coinfo.' + extra '.$emailString.' : '.$newsletterXmails.'<br />';
					}
				} else {
          if (stristr($_SERVER['HTTP_HOST'],"localhost"))
          $mail_conf = true;
          else
					$mail_conf = mail($coinfo, html_entity_decode($newsletterSujet), $newsletter_msg, "From: $codename <$coinfo>".$mail_headers);
					if ($mail_conf === true) {
  $notice .= '<font color="Green"><b>'.$messageString.' <!--g&eacute;n&eacute;rique--> '.$class_conjugaison->plural($envoyeString,'M',1).' > </b></font> '.$coinfo.'<br />'.(stristr($_SERVER['HTTP_HOST'],"localhost")?'<br /><textarea rows="30" cols="40" style="width: 98%;">'."$coinfo, ".html_entity_decode($newsletterSujet).", $newsletter_msg<br />, \"From: $codename <$coinfo>".$mail_headers.'</textarea>':'');
					} else {
  $error .= '<font color="Red"><b>'.strtoupper($erreurString).' ! : </b></font><b>'.$messageString.' <!--g&eacute;n&eacute;rique--> '.$nonString.' '.$class_conjugaison->plural($envoyeString,'M',1).' > </b></font> '.$coinfo.'<br />';
					}
				}
				
				
				
				
				
				
				
				
				
				$emailsent = "";
				$emailsent_count = 0;
				$emailerror = "";
				$emailerror_count = 0;
  $content .= $rapportString.' > '.$newsletterString.' !<hr /><br />';
  
  
  
  
  
  if ($personalized_newsletter === true)
				while($row = mysql_fetch_array($membreread)) {
					$membreId = $row["userid"];
					$get_membre = sql_get($tblmembre,"WHERE membreid='$membreId' ","membregendre,membreprenom,membrenom");
					$membreGendre = $get_membre[0];
					$membreTitle = sql_stringit('gendre',$membreGendre)." ".$get_membre[1]." ".$get_membre[2];
					$membreUtil = $row["userutil"];
					$membrePass = $row["userpass"];
					$membreEmail_md5 = md5($row["useremail"]);
					$membreEmail = is_valid_email($row["useremail"]);
					$membreStatut = $row["userstatut"];
          $membrePriv = $row["userpriv"];
          $membrePriv_astext = "";
        	if ($row["userpriv"] !== "") {
        		foreach(explode("|",$row["userpriv"]) as $k_priv)
        		$membrePriv_astext .= ($membrePriv_astext==''?'':",").sql_stringit('privilege',$k_priv);
          } else {
            $membrePriv == "1";
            $membrePriv_astext = sql_stringit('privilege',$membrePriv); 
          }
					$nlid = $newsletterid.'-'.$membreId;
					$newsletter_msg = gen_newsletter($send,$nlid);
					$newsletter_msg = wordwrap($newsletter_msg,70,$CRLF,true);
        //  contains_bad_str($newsletter_msg);// uncomment if not using multi-part which contains content-type info
          contains_newlines($newsletterSujet);
          contains_bad_str($membreEmail);
          contains_newlines($membreEmail);
          if (stristr($_SERVER['HTTP_HOST'],"localhost"))
          $mail_conf = true;
          else
					$mail_conf = mail($membreEmail, html_entity_decode($newsletterSujet), $newsletter_msg, "From: $codename <$coinfo>".$mail_headers);
					if ($mail_conf === true) {
						if  ($emailsent != '')  $emailsent .= ', '.$membreEmail  ;
						if  ($emailsent == '')  $emailsent .= $membreEmail  ;
						$emailsent_count += 1;
        	  $notice .= '<font color="Green"><b>'.$messageString.' '.$class_conjugaison->plural($envoyeString,'M',1).' > </b></font> '.$membreUtil.' ('.$membreId.')<br />';
            if (stristr($_SERVER['HTTP_HOST'],"localhost"))
            $notice .= '<br /><textarea rows="30" cols="40" style="width: 98%;">'.$membreEmail.', '.html_entity_decode($newsletterSujet).', '."From: $codename <$coinfo>"."<br />\r\n".$mail_headers."<br />\r\n###########################################################"."<br />\r\n".$newsletter_msg.'</textarea>';
					} else {
						if  ($emailerror != '')  $emailerror .= ', '.$membreEmail  ;
						if  ($emailerror == '')  $emailerror .= $membreEmail  ;
						$emailerror_count += 1;
        	  $error .= '<font color="Red"><b>'.strtoupper($erreurString).'! : </b></font>'.$membreUtil.' ('.$membreId.')<br />';
					}
				}
        if ($emailsent_count>0)
        $notice .= '<hr /><br />'.$class_conjugaison->plural($emailString.' '.$envoyeString,'',$emailsent_count).' ('.$emailsent_count.' / '.$nRowsUser.')<br /> <br/>'.$emailsent.'<hr /><br />';
        if ($emailerror_count>0)
        $error .= '<hr /><br />'.$class_conjugaison->plural($emailString.' '.$nonString.' '.$envoyeString,'',$emailerror_count).'. ('.$emailerror_count.' / '.$nRowsUser.')<br /> <br/>'.$emailerror.'<hr /><br />';
        $emailsent .= '<hr /><br />Extra '.$emailString.' :<br />'.$newsletterXmails.'<hr /><br />';
				$newsletteraddrapport = sql_update($tblnewsletter, "SET newsletterdate=$dbtime, newslettersent='$emailsent', newslettererror='$emailerror' ", "WHERE newsletterid='$newsletterid' ", "newslettersent, newslettererror");
				if ( ($emailsent == $newsletteraddrapport[0]) && ($emailerror == $newsletteraddrapport[1]) ) {
  $notice .= '<br /><font color="Green"><b>'.$rapportString.' '.$enregistreString.' !</b></font><br />';
				} else {
  $error .= '<br /><font color="Red"><b>'.strtoupper($erreurString).' : '.$rapportString.' '.$nonString.' '.$enregistreString.' !</b></font><br />';
				}
			} // insertquery
		}
	} else {
		Header("Location: $redirect");Die();
	}
}
?>