<?php #۞ #
if (stristr($_SERVER['PHP_SELF'], basename(__FILE__))){include '_security.php';Header("Location: $redirect");Die();}

if (!isset($priv_for_admin))
$priv_for_admin = '4';

$typeString = ucfirst($destinataireString);

if (!isset($sender)) {
  if (stristr($_SERVER['PHP_SELF'],$urladmin)) {
    $sender = $admin_name;
  } else {
    if (isset($user_id)) {
      $read = sql_get($tblmembre,"WHERE membrerid='$user_id' AND membrelang='$lg' ","membregendre,membreprenom,membrenom");
      $sender = sql_stringit("gendre",$read[0])." ".$read[1]." ".$read[2];
    } else
    $sender = $user_name;
  }
}

$array_communications_mail = sql_array($tblenum,"WHERE enumwhat='priv' AND enumtitre!='1' ","enumtitre");
//$dbtable = "$tbladmin,$tbluser";
$dbtable = "$tbluser";
/*
$sql_admin = " (adminstatut='Y'
						  AND admindate!='0000-00-00 00:00'
						  AND (adminemail!='$default_email' OR adminemail!='')) OR ";
*/
$sql_user = " (userstatut='Y'
						  AND userdate!='0000-00-00 00:00'
						  AND (useremail!='$default_email' OR useremail!='')) ";

$read = mysqli_query($connection, "SELECT * FROM $dbtable WHERE userlang='$lg' ");// $sql_admin $sql_user
$nRows = mysqli_num_rows($read);

if ($nRows == '0') {
  $communications_content = $nRows.' '.$membreString.' '.$enregistreString.': '.$pasdeString.' '.$communicationsString.' '.$aenvoyerString.' !<br />';
} else {
  $communications_content = $nRows.' '.$class_conjugaison->plural($membreString,'',$nRows).' '.$class_conjugaison->plural($enregistreString,'',$nRows).'<hr /><br /> <br />';
  if (isset($send) && ($send == $envoyerString) && ($_SERVER["REQUEST_METHOD"] == "POST")) {
		$communicationsSender = strip_tags(stripslashes($_POST['communicationsSender']));
		$communicationsSujet = strip_tags(stripslashes($_POST['communicationsSujet']));
  	$communicationsSender = html_entity_decode($communicationsSender);
  	$communicationsSujet = html_entity_decode($communicationsSujet);
		//$communications2who = strip_tags(stripslashes($_POST['communications2who']));
    $communications2who = array();
		if ($array_communications_mail[0] !== "") {
		  foreach($array_communications_mail as $key)
		    if (isset($_POST["priv".$key]) && ($_POST["priv".$key] == 'on'))
		      $communications2who[] = $key;
		      /*
		  if (isset($communications2who[0])&&($communications2who[0]!=''))
      $sql_communications2who = implode("%' OR userpriv LIKE '%",$communications2who);
      else
      $communications2who[] = "1";
          */
    } else {
      $communications2who[] = "1";
    }
		$communicationsMessage = nl2br(strip_tags(html_encode(stripslashes($_POST['communicationsMessage']))));
		if (!isset($communicationsSender) || !is_email($communicationsSender) || (!isset($communicationsSujet)) || preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $communicationsSujet) || ($communicationsSujet == "") || (strlen($communicationsSujet) > 60) || !isset($communications2who) || ($communications2who[0] == "1") || (!isset($communicationsMessage)) || preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $communicationsMessage) || ($communicationsMessage == "")
		   ) {
  $error .= '<font color="Red"><b>'.$erreurString.'!</b></font><br />'.$listecorrectionString.'<ul>';
			if ( !$communicationsSender || !is_email($communicationsSender) )  
  $error .= '<li>'.$fromString.' > '.$error_invmiss.'</li>'  ;
			if ( !$communicationsSujet  ||  preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $communicationsSujet) || (strlen($communicationsSujet) > 60) )  
  $error .= '<li>'.$sujetString.' > '.$error_invmiss.' (max: 60 c.)</li>'  ;
			if ( !$communications2who || ($communications2who[0] == "1") )  
  $error .= '<li>'.$typeString.' > '.$error_invmiss.'</li>'  ;
			if ( !$communicationsMessage  ||  preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $communicationsMessage) )  
  $error .= '<li>'.$messageString.' > '.$error_invmiss.'</li>'  ;
  $error .= '</ul>';//<a href="javascript:history.back()//">'.$retourString.'</a>';
		} else {
    	$nonhtml_content_generated = html_entity_decode(strip_tags(str_replace("<br />",$CRLF,$communicationsMessage)));
    	$html_content_generated = $communicationsMessage;
    	$footer = "$communicationsString ".$class_conjugaison->plural($envoyeString,'F',1).", $parString <a href=\"$mainurl\">$slogan</a><br /><sup>$copyrightnoticeString</sup><br /> <br />";
      ################################## IMPORT TEMPLATE
      if (@file_exists($getcwd.$up.$safedir.'_tpl_mail_communications.php'))
        include($getcwd.$up.$safedir.'_tpl_mail_communications.php');
      else
        include($getcwd.$up.$urladmin.'defaults/_tpl_mail_communications.php');
  		$communications_msg = $_tpl_mail_communications;
	//		$communicationsXmails = array_unique(sql_array($dbtable,"WHERE userpriv LIKE '%".(isset($sql_communications2who)?$sql_communications2who:$communications2who)."%' ","useremail"));
			$communicationsXmails = array_unique(sql_array($dbtable,"WHERE userpriv LIKE '%".implode("%' OR userpriv LIKE '%",$communications2who)."%' ","useremail"));
			/*
			foreach($communicationsXmails as $k)
			echo $k."<br />";
			die("SELECT useremail FROM $dbtable WHERE userpriv LIKE '%".implode("%' OR userpriv LIKE '%",$communications2who)."%' ");
			*/
			if (in_array($priv_for_admin,$communications2who))
			$communicationsXmails = array_unique(array_merge(sql_array($tbladmin,"WHERE adminpriv='0' ","adminemail"),$communicationsXmails));

		//	$communicationsXmails = array_map('is_valid_email',$communicationsXmails);// not needed
			$communicationsXmails = is_valid_email($communicationsXmails);

  //  echo "WHERE userpriv LIKE '%".(isset($sql_communications2who)?$sql_communications2who:$communications2who)."%' ";Die();
			$countemails = count($communicationsXmails);
      $codename = html_entity_decode($codename);
			$communications_msg = wordwrap($communications_msg,70,$CRLF,true);
    //  contains_bad_str($communications_msg);// uncomment if not using multi-part which contains content-type info
      contains_bad_str($communicationsSujet);
      contains_newlines($communicationsSujet);
      html_entity_decode($communicationsSujet);
      if (!isset($bcc_max))
      $bcc_max = '20';
      if (($communicationsXmails != '') && ($countemails > $bcc_max)) {
  		  $mail_this ='';
        $i = 0;
        $batchemails = array();
        foreach($communicationsXmails as $key){
          if ($i<$bcc_max)
          $batchemails[]=$key;
          $i++;
          if($i==$bcc_max){
            $mail_this = html_entity_decode(implode(", ",$batchemails));
            if (stristr($_SERVER['HTTP_HOST'],"localhost"))
            $mail_conf = true;
            else
            $mail_conf = mail($communicationsSender, $communicationsSujet, $communications_msg, "From: $sender <$communicationsSender>" . $CRLF . "Bcc: $mail_this".$mail_headers);
            if ($mail_conf === true) {
              $notice .= '<font color="Green"><b>'.$messageString.' <!--g&eacute;n&eacute;rique--> '.$class_conjugaison->plural($envoyeString,'M',1).' > </b></font> '.$communicationsSender.' + extra '.$emailString.' : '.$mail_this.'<br />'.(stristr($_SERVER['HTTP_HOST'],"localhost")?" #122<br />$communicationsSender, $communicationsSujet, $communications_msg, \"From: $sender &lt;$communicationsSender&gt;\"" . "<br />\r\n" . "Bcc: $mail_this".$mail_headers.'<hr /><br />':'');
            } else {
              $error .= '<font color="Red"><b>'.strtoupper($erreurString).' ! : </b></font><b>'.$messageString.' <!--g&eacute;n&eacute;rique--> '.$nonString.' '.$class_conjugaison->plural($envoyeString,'M',1).' > </b></font> '.$communicationsSender.' + extra '.$emailString.' : '.$mail_this.'<br />'.(stristr($_SERVER['HTTP_HOST'],"localhost")?" #126<br />$communicationsSender, $communicationsSujet, $communications_msg, \"From: $sender &lt;$communicationsSender&gt;\"" . "<br />\r\n" . "Bcc: $mail_this".$mail_headers.'<hr /><br />':'');
            }
            $i=0;
            $batchemails = array();
          }
        }
      } else {
        $mail_this = html_entity_decode(implode(", ",$communicationsXmails));
        if (stristr($_SERVER['HTTP_HOST'],"localhost"))
        $mail_conf = true;
        else
        $mail_conf = mail($communicationsSender, $communicationsSujet, $communications_msg, "From: $sender <$communicationsSender>" . $CRLF . "Bcc: $mail_this".$mail_headers);
        if ($mail_conf === true) {
          $notice .= '<font color="Green"><b>'.$messageString.' <!--g&eacute;n&eacute;rique--> '.$class_conjugaison->plural($envoyeString,'M',1).' > </b></font> '.$communicationsSender.' + extra '.$emailString.' : '.$mail_this.'<br />'.(stristr($_SERVER['HTTP_HOST'],"localhost")?"<br />$communicationsSender, $communicationsSujet, $communications_msg, \"From: $sender &lt;$communicationsSender&gt;\"" . "<br />\r\n" . "Bcc: $mail_this".$mail_headers.'<hr /><br />':'');
        } else {
          $error .= '<font color="Red"><b>'.strtoupper($erreurString).' ! : </b></font><b>'.$messageString.' <!--g&eacute;n&eacute;rique--> '.$nonString.' '.$class_conjugaison->plural($envoyeString,'M',1).' > </b></font> '.$communicationsSender.' + extra '.$emailString.' : '.$mail_this.'<br />'.(stristr($_SERVER['HTTP_HOST'],"localhost")?"#141<br />$communicationsSender, $communicationsSujet, $communications_msg, \"From: $sender &lt;$communicationsSender&gt;\"" . "<br />\r\n" . "Bcc: $mail_this".$mail_headers.'<hr /><br />':'');
        }
      }
    }
    if ($error=='') {
      $_SESSION['mv_notice'] = $notice;
      Header("Location: ".$_SERVER['REQUEST_URI']);Die();
    } else {
    //  $_SESSION['mv_error'] = $error; // commented otherwise presents again error
    //  Header("Location: ./");Die();
    }
  }
//	if (!isset($send)||($error!='')) {
  	if ($error!=''){
      $new_communications2who = array();
      if (isset($communications2who[0]))
      foreach($communications2who as $k)
      $new_communications2who[] = $k;
      $communicationsMessage = trim(stripslashes(str_replace("'","&acute;",$_POST['communicationsMessage'])));// same routine as what is in incdb
			$send = 'new';// reinitializes tinymce
    }

    $inputs = gen_inputcheck($tblenum,(isset($new_communications2who)?implode("|",$new_communications2who):''), "%' AND enumtitre!='1' AND enumtype LIKE '%", 'priv');

    $part_comm_to_who = '<br /><div style="float:left;width:97%;"><label for="communications2who"><b> '.$typeString.' </b></label><br />'.$inputs.'</div>';
    $communications_content .= (stristr($_SERVER['PHP_SELF'],$urladmin)?gen_form($lg,$x,$y):gen_form($lg,$x).'<input type="hidden" name="do" value="tabcontent4" />').'
  <label for="communicationsSender"><b> '.$fromString.' </b></label> 
  <input type="hidden" name="communicationsSender" value="'.(isset($admin_email)?$admin_email:(isset($user_email)?$user_email:'')).'" /><u>'.$sender.' &lt;'.(isset($admin_email)?$admin_email:(isset($user_email)?$user_email:'')).'&gt;</u>
  <br />
  <label for="communicationsSujet"><b> '.$sujetString.' </b></label><br />
  <input class="text" type="text" name="communicationsSujet" size="60" maxlength="60" value="'.(isset($communicationsSujet)?$communicationsSujet:'').'" />
  '.$part_comm_to_who.'
  <hr /><br />
  <label for="communicationsMessage"><b> '.$messageString.' </b></label>
  '.($tinyMCE===false?$text_style_js:'').'<br /><textarea id="elm'.($i_elm>=-1?$i_elm+=1:'').'" name="communicationsMessage" rows="20" cols="40" style="width: 97%; height: 300px; min-height: 300px;">'.(isset($communicationsMessage)?format_edit($communicationsMessage,'edit'):'').'</textarea>
  <hr /><br /><input type="submit" name="send" value="'.$envoyerString.'" />
  </form>';// | <a href="javascript:history.back()//">'.$retourString.'</a>
//  }
########################################################### ENVOYER
########################################################### ENVOYER
}
if (stristr($_SERVER['PHP_SELF'],$urladmin))
$content .= $admin_menu.$communications_content;
