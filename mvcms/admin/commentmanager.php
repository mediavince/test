<?PHP
if (stristr($_SERVER['PHP_SELF'],'commentmanager.php')) {
  include '_security.php';
  Header("Location: $redirect");Die();
}
//          $notice .= mvtrace(__FILE__,__LINE__)." $x<br />";

  if (isset($forumId)) $show_id = $forumId;
  if (isset($show_id)) $forumId = $show_id;
  if (!isset($show_id) && !isset($forumId))
  {Header("Location: $redirect");Die();}
  
  if (!isset($moderate_forum)) $moderate_forum = true;
  
  if (isset($send) && ($send == $ajouterString.' '.$commentString) && ($forum_commenting === true)) {
    if($_SERVER['REQUEST_METHOD'] != "POST"){
       Die("Unauthorized attempt to access page.");
    }
		$commentForum = $forumId;
		$commentMembre = (isset($user_id)&&$user_id>0?$user_id:$anonymeString);
		$commentEntry = nl2br(strip_tags(html_encode($commentEntry)));
		$valid_Entry = true;
		if	(sql_nrows($tblcomment,"WHERE commentforum='$commentForum' AND commententry='$commentEntry' AND commentlang='$lg' ") > 0)
		$valid_Entry = false;
		if (!$commentMembre || preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $commentMembre) ||
			!$commentEntry || preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $commentEntry) || (strlen($commentEntry) > 3000) ||
			($valid_Entry === false) ||
			(($send == $sauverString) && (!$commentStatut || !preg_match("/^[0-9]+\$/", $commentId))) ||
			(($moderate_forum === true) && (md5($_POST['code']) !== $_SESSION['antispam_key']))
			) {
  $error .= '<b>'.$erreurString.'!</b>, '.$listecorrectionString.'<ul>';
			if ( !$commentMembre || preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $commentMembre) )
  $error .= '<li>'.$nomString.' > '.$error_invmiss.'<br /> <br /></li>'	;
			if ( !$commentEntry || preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $commentEntry) )
  $error .= '<li>'.$commentString.' > '.$error_invmiss.'<br /> <br /></li>'	;
			if (  (strlen($commentEntry) > 3000) )
  $error .= '<li>'.$commentString.' > '.$error_inv.' :: max. 3000 chrs.<br /> <br /></li>'	;
			if ( ($moderate_forum === true) && (md5($_POST['code']) !== $_SESSION['antispam_key']) )
  $error .= '<li>Code > '.$error_invmiss.'<br /> <br /></li>'	;
			if ($valid_Entry === false)
  $error .= '<li>'.$commentString.' > '.$error_inv.' (<a href="'.$local_url.'&amp;forumId='.sql_getone($tblcomment,"WHERE commententry='$commentEntry' ","commentforum").'#c'.sql_getone($tblcomment,"WHERE commententry='$commentEntry' ","commentrid").'">'.$dejaString.' '.$existantString.'</a>)<br /> <br /></li>'	;
      session_unregister('key');
      $md5 = md5(microtime() * mktime());
      $string = substr($md5,0,rand(5,8));
      $_SESSION['antispam_key'] = md5($string);
      if (!isset($_SESSION['mail_count'])) $_SESSION['mail_count'] = 0;
      
      /*
      $_SESSION['mv_error'] = $error;
      $_SESSION['mv_notice'] = $notice;
      Header("Cache-Control: no-cache");
      Header("Pragma: no-cache");
      Header("Location: ".html_entity_decode($local_url));Die();
      */
      
    } else {
    
    
			$commentip = $_SERVER['REMOTE_ADDR'];
			if (in_array($commentip,$banned_ips)	||
					stristr($commentEntry, "viagra")	||
					stristr($commentEntry, "phentermine")	||
					stristr($commentEntry, "porn")		||
					stristr($commentEntry, "p0rn")		|| 
					stristr($commentEntry, "xxx")		||
					($commentMembre == "Nikolasvipam")	) { // CATCHING IPS IN BANNED ARRAY, REF EMAILS FOR INFO

        session_unregister('key');
        $md5 = md5(microtime() * mktime());
        $string = substr($md5,0,rand(5,8));
        $_SESSION['antispam_key'] = md5($string);
        if (!isset($_SESSION['mail_count'])) $_SESSION['mail_count'] = 0;
      
				$addquery = "CAPTCHA"; // ALLOWS CONTENT TO BE SHOWN AS IF THE comment HAD BEEN RECORDED
        $notice .= '<b>'.$commentString.' '.$class_conjugaison->plural($enregistreString,'M','1').'</b></font><br />';
        if ($moderate_forum == true)
        $notice .= ' <br /><i>'.$moderateurverifString.'</i><img src="'.$mainurl.'images/_spacer.php?string='.base64_encode($string).'" width="0" height="0" border="0" alt="sp@mcaptcha" /><br />'; // TRICKS THE SPAMMER INTO THINKING HE ACTUALLY SUCCEEDED, LOSER!
			} else {
        $sqlq = "
										INSERT INTO $tblcomment
										(`commentid`, `commentstatut`, `commentdate`, `commentlang`, `commentrid`, `commentforum`, `commentmembre`, `commententry`, `commentresponse`,`commentip`)
										VALUES
										(NULL, '".($moderate_forum===true?'N':'Y')."', $dbtime, '$default_lg', '', '$forumId', '$commentMembre', '$commentEntry', '','$commentip')
										";
				$addquery = @mysql_query("$sqlq");
				if (!$addquery) 
  $error .= $error_request.'<br />';//<a href="javascript:history.back()//">'.$retourString.'</a><br />';
				else {
  $notice .= '<b>'.$commentString.' '.$class_conjugaison->plural($enregistreString,'M','1').'</b><br />'.($moderate_forum===true?'<i>'.$moderateurverifString.'</i><br />':"");
					$comment_id = sql_updateone($tblcomment,"SET commentrid=commentid ","WHERE commentmembre='$commentMembre' AND commententry='$commentEntry' AND commentlang='$default_lg' ","commentid");
					foreach($array_lang as $loop_lg)
  					if ($loop_lg != $default_lg)
  					$addquery = @mysql_query("
										INSERT INTO $tblcomment
										(`commentid`, `commentstatut`, `commentdate`, `commentlang`, `commentrid`, `commentforum`, `commentmembre`, `commententry`, `commentresponse`,`commentip`)
										VALUES
										(NULL, '".($moderate_forum===true?'N':'Y')."', $dbtime, '$loop_lg', '$comment_id', '$forumId', '$commentMembre', '$commentEntry', '','$commentip')
										");
					$mail_subject = html_entity_decode("new comment on ".$codename." blog #$comment_id :: forum #$forumId ".date('Y-m-d H:i:s',$now_time)); // date('l \t\h\e jS \o\f F Y \a\t H:i:s',(time()+(60*60*6))); // shows server local time: date("r")
					$mail_message = $_SERVER['HTTP_REFERER'].(strstr($_SERVER['HTTP_REFERER'],'?')?'':'?').'forumId='.$show_id.'#c'."$comment_id
          
          $datecreeString : ".date('l \t\h\e jS \o\f F Y \a\t H:i:s',$now_time)."
          
          IP address: ".str_replace('.','&#046;',$commentip)."
          
          Log in to see that entry.";
          contains_bad_str($mail_message);
       //   $mail_headers = $CRLF.'Bcc: '.$clientemail.$mail_headers;
        	$nonhtml_content_generated = $mail_message;
        	$html_content_generated = nl2br($mail_message);
        	$footer = "$communicationsString ".$class_conjugaison->plural($envoyeString,'F',1).", $parString <a href=\"$mainurl\">$codename</a><br /><sup>$copyrightnoticeString<br /> <br />";
          ################################## IMPORT TEMPLATE
          if (@file_exists($getcwd.$up.$safedir.'_tpl_mail_communications.php'))
            include($getcwd.$up.$safedir.'_tpl_mail_communications.php');
          else
            include($getcwd.$up.$urladmin.'defaults/_tpl_mail_communications.php');
      		$communications_msg = wordwrap($_tpl_mail_communications,70,$CRLF,true);
      		if (isset($user_id)&&$user_id>0) {
  					$user_email = sql_getone("$tbluser,$tblforum"," WHERE userrid=forummembre AND forumrid='$forumId' ","useremail");
  					if (is_email($user_email)) $user_email = is_valid_email($user_email);
  				}
          contains_bad_str($user_email);
          contains_bad_str($mail_subject);
        //  contains_bad_str($communications_msg); // uncomment and comment above, either use or not conte-type boundaries
          contains_newlines($user_email);
          contains_newlines($mail_subject);
          if (stristr($_SERVER['HTTP_HOST'],"localhost")) {
            $notice .= "$user_email, <br />$mail_subject, <br />$communications_msg, <br />From: $codename new comment adviser <$coinfo> <br />$mail_headers";
          } else {
  					$mail_newcomment = mail($user_email, $mail_subject, $communications_msg, "From: ".$codename." new comment adviser <$coinfo>".$mail_headers);
          }
          
					$mail_message = $mainurl.$urladmin.'?lg='.$lg.'&x='.sql_getone($tblcont,"WHERE conttype='$this_is' ","contpg").'&forumId='.$show_id.'#c'."$comment_id
          
          $datecreeString : ".date('l \t\h\e jS \o\f F Y \a\t H:i:s',$now_time)."
          
          IP address: ".str_replace('.','&#046;',$commentip)."
          
          Log in to see that entry.";
          contains_bad_str($mail_message);
          $mail_headers = $CRLF.'Bcc: '.$clientemail.$mail_headers;
        	$nonhtml_content_generated = $mail_message;
        	$html_content_generated = nl2br($mail_message);
        	$footer = "$communicationsString ".$class_conjugaison->plural($envoyeString,'F',1).", $parString <a href=\"$mainurl\">$codename</a><br /><sup>$copyrightnoticeString<br /> <br />";
          ################################## IMPORT TEMPLATE
          if (@file_exists($getcwd.$up.$safedir.'_tpl_mail_communications.php'))
            include($getcwd.$up.$safedir.'_tpl_mail_communications.php');
          else
            include($getcwd.$up.$urladmin.'defaults/_tpl_mail_communications.php');
      		$communications_msg = wordwrap($_tpl_mail_communications,70,$CRLF,true);
          contains_bad_str($clientemail);
          contains_bad_str($mail_subject);
        //  contains_bad_str($communications_msg); // uncomment and comment above, either use or not conte-type boundaries
          contains_newlines($clientemail);
          contains_newlines($mail_subject);
          if (stristr($_SERVER['HTTP_HOST'],"localhost")) {
            $notice .= "$clientemail, <br />$mail_subject, <br />$communications_msg, <br />From: $codename new comment adviser <$coinfo> <br />$mail_headers";
          } else {
  					$mail_newcomment = mail($clientemail, $mail_subject, $communications_msg, "From: ".$codename." new comment adviser <$coinfo>".$mail_headers);
          }
				}
			}
    }
    if ($error == '') {// && stristr($_SERVER['REQUEST_URI'],$urladmin)) {
      $_SESSION['mv_notice'] = $notice;
      Header("Cache-Control: no-cache");
      Header("Pragma: no-cache");
      Header("Location: ".html_entity_decode($local_url));Die();
    }
  }
  
	if (isset($send) && ($send == 'delete') && isset($commentId)) {
    if (isset($forumId) && preg_match("/^[0-9]+\$/", $forumId) && isset($commentId) && preg_match("/^[0-9]+\$/", $commentId)) {
    } else {
       Die("Unauthorized attempt to access page.");
    }
		if (sql_del($tblcomment,"WHERE commentrid='$commentId' AND commentforum='$forumId' ") > 0)
		$error .= $erreurString.' : '.$commentString.' '.$nonString.' '.$class_conjugaison->plural($effaceString,'M','1').'<br />';
		else
		$notice .= $commentString.' '.$class_conjugaison->plural($effaceString,'M','1').'<br />';
//    if ($error == '') {// && stristr($_SERVER['REQUEST_URI'],$urladmin)) {
      $_SESSION['mv_error'] = $error;
      $_SESSION['mv_notice'] = $notice;
      Header("Cache-Control: no-cache");
      Header("Pragma: no-cache");
      Header("Location: ".html_entity_decode($local_url));Die();
//    }
  }
  
	if (isset($send) && ($send == $envoyerString.' '.$reponseString)) {
    if (isset($forumId) && preg_match("/^[0-9]+\$/", $forumId) && isset($commentId) && preg_match("/^[0-9]+\$/", $commentId) && isset($commentResponse) && ($commentResponse != '') && ($_SERVER['REQUEST_METHOD'] == "POST")) {
    } else {
       Die("Unauthorized attempt to access page.");
    }
		$updatequery = sql_update($tblcomment," SET commentresponse='$commentResponse' ","WHERE commentrid='$commentId' AND commentforum='$forumId' ","commentresponse");// AND commentmembre='".(isset($user_id)?$user_id:'0')."'
		if (in_array($updatequery[0],array('','.')))
		$error .= $erreurString.' : '.$reponseString.' '.$nonString.' '.$class_conjugaison->plural($enregistreString,'F','1').'<br />'." SET commentresponse='$commentResponse' , WHERE commentrid='$commentId' AND commentforum='$forumId' ";
		else
		$notice .= $reponseString.' '.$class_conjugaison->plural($enregistreString,'F','1').'<br />';
  //  if ($error == '') {// && stristr($_SERVER['REQUEST_URI'],$urladmin)) {
      $_SESSION['mv_error'] = $error;
      $_SESSION['mv_notice'] = $notice;
      Header("Cache-Control: no-cache");
      Header("Pragma: no-cache");
      Header("Location: ".html_entity_decode($local_url));Die();
  //  }
  }
  
  // show comment
              if (isset($commentorder) && in_array($commentorder,array('ASC','DESC')))
              $sql_commentorder = "ORDER BY commentdate $commentorder ";
              else $sql_commentorder = "ORDER BY commentdate ".(isset($filter_commentorder)&&in_array($filter_commentorder,array('ASC','DESC'))?$filter_commentorder:"ASC")." ";
              $get_comment = sql_array($tblcomment,"WHERE ".($admin_viewing===true||$user_id==sql_getone(${"tbl".$this_is},"WHERE ".$this_is."rid='$show_id' ",$this_is."membre")?'':" commentstatut='Y' AND ")." commentlang='$lg' AND comment".$this_is."='$show_id' $sql_commentorder ","commentrid");//
              if ($get_comment[0] != '') {
                $show_comment = '<hr /><br />'.count($get_comment).' '.$class_conjugaison->plural($commentString,'M',count($get_comment)).'! ('.$ordreString.': <a href="'.$local_url.'&amp;commentorder=ASC" target="_self">'.$ASCString.'</a> | <a href="'.$local_url.'&amp;commentorder=DESC" target="_self">'.$DESCString.'</a>)<br />';
                foreach($get_comment as $comment) {
                  $this_comment = sql_get("$tblcomment,$tblmembre","WHERE commentrid='$comment' AND commentlang='$lg' AND commentmembre=membrerid ","commentmembre,commententry,commentresponse,commentdate,commentstatut,membregendre,membrenom,membreprenom,membrerid");
                  if ($this_comment[0]=='.')
                  $this_comment = sql_get($tblcomment,"WHERE commentrid='$comment' AND commentlang='$lg' ","commentmembre,commententry,commentresponse,commentdate,commentstatut");
                  $can_be_deleted = false;
                  if ($logged_in===true&&($admin_viewing===true||(isset($user_id)&&(sql_getone(${"tbl".$this_is},"WHERE ".$this_is."rid='$show_id' ",$this_is."membre")==$user_id)&&(($this_comment[0]==$user_id)&&($this_comment[2]==''))||(sql_getone(${"tbl".$this_is},"WHERE ".$this_is."rid='$show_id' ",$this_is."membre")==$user_id))))
                  $can_be_deleted = true;
                  if (($can_be_deleted === true) || ($this_comment[4]=='Y'))
                  $show_comment .= '<br /><div class="comment"><div style="float:right;">'.($can_be_deleted===true?' <a href="'.$local_url.'&amp;toggle=statut&amp;forumId='.$forumId.'&amp;commentId='.$comment.'" onclick="return confirm(\''.($this_comment[4]=='Y'?$nonString.' '.$publishString.' ?\');"><img src="'.$mainurl.'images/publish_y.png" width="12" height="12" border="0" title="'.$publishedString.'" alt="'.$publishedString:$publishString.' ?\');"><img src="'.$mainurl.'images/publish_n.png" width="12" height="12" border="0" title="'.$nonString.' '.$publishedString.'" alt="'.$nonString.' '.$publishedString).'" /></a> | <a href="'.$local_url.'&amp;send=delete&amp;commentId='.$comment.'" onclick="return confirm(\''.$confirmationeffacementString.'\');"> <img src="'.$mainurl.'images/delete.gif" width="10" height="10" border="0" title="'.$effacerString.'" alt="'.$effacerString.'" /></a>':'').' <sup><i>'.human_date($this_comment[3],true).'</i></sup></div><b>'.$redacteurString.' > <i>'.(isset($this_comment[5])?sql_stringit('gendre',$this_comment[5]).' '.$this_comment[6].' '.$this_comment[7]:$anonymeString).'</i></b><br />'.$this_comment[1].'</div>';
                  if ($this_comment[2] != '')
                  $show_comment .= '<div class="response"><sup><i><u>'.$reponseString.'</u></i></sup><br />'.$this_comment[2].'</div>';
        					else {
                  //  if ($this_comment[6] == (isset($user_id)?$user_id:'0'))
                  //  if ($forumId == (isset($user_id)&&$user_id>0?$user_id:'0'))
                    if ($logged_in===true&&($this_comment[2]=='')&&($admin_viewing===true||(isset($user_id)&&in_array(sql_getone(${"tbl".$this_is},"WHERE ".$this_is."rid='$show_id' ",$this_is."membre"),array('0',$user_id)))))
                	  $show_comment .= '<div id="addresponse_'.$comment.'" style="text-align:center;display:block;"><a class="toggle" onclick="javascript:getelbyid(\'responseform_'.$comment.'\').style.display=\'block\';getelbyid(\'addresponse_'.$comment.'\').style.display=\'none\';">'.$envoyerString.' '.$reponseString.'</a></div><div id="responseform_'.$comment.'" style="display:none;"><form enctype="multipart/form-data" action="'.lgx2readable($lg,$x,$this_is,$show_id).'" method="'.$postgetmethod.'"><input type="hidden" name="'.$this_is.'Id" value="'.$show_id.'" /><input type="hidden" name="commentId" value="'.$comment.'" /><label for="commentResponse"> <b>'.$reponseString.'</b> </label><br /><textarea name="commentResponse" rows="30" cols="40" style="width: 97%; height: 100px; min-height: 100px;">'.(isset($commentResponse)?$commentResponse:'').'</textarea><input type="submit" name="send" value="'.$envoyerString.' '.$reponseString.'" /><br /></form></div>';
                	}
                }
              } else {
                $show_comment = "<hr /><br />0 ".$commentString."!<br />";
              }
              if (${$this_is."_commenting"} === true) {
                if ($admin_viewing === true) {
                //  $show_comment .= $gestionString.'<br />';
                } else {
                  if (!isset($send) || (isset($send) && ($send != 'edit'))) {
                    session_unregister('key');
                    $md5 = md5(microtime() * mktime());
                    $string = substr($md5,0,rand(5,8));
                    $_SESSION['antispam_key'] = md5($string);
                    $comment_offer_top = '<div id="addcommenttop" style="text-align:center;display:block;"><a class="toggle" onclick="javascript:getelbyid(\'commentformtop\').style.display=\'block\';getelbyid(\'addcommenttop\').style.display=\'none\';">'.$ajouterString.' '.$commentString.'</a> | <a href="'.lgx2readable($lg,'',$this_is,'?send=new').'">'.$ajoutString.' '.$forumString.'</a></div><div id="commentformtop" style="display:none;"><form enctype="multipart/form-data" action="'.lgx2readable($lg,$x,$this_is,$show_id).'" method="'.$postgetmethod.'"><input type="hidden" name="'.$this_is.'Id" value="'.$show_id.'" /><label for="commentMembre"> <b>'.$nomString.'</b> *</label> <input type="hidden" name="commentMembre" value="'.(isset($user_id)&&$user_id>0?$user_id:'0').'" style="width: 97%" />'.(isset($user_name)?$user_name:(isset($admin_name)?$admin_name:$anonymeString)).'<br /><label for="commentEntry"> <b>'.$commentString.'</b> *</label><br /><textarea id="elm1" name="commentEntry" rows="30" cols="40" style="width: 97%; height: 100px; min-height: 100px;">'.(isset($commentEntry)?format_edit($commentEntry,'edit'):'').'</textarea>';
                    if ($moderate_forum === true)
                    $comment_offer_top .= '<br /><label for="code"> <b>'.$recopiercodeString.'</b> </label><br /><img src="'.$mainurl.'images/_captcha.php?string='.base64_encode($string).'" border="0" /> <input type="text" name="code" value="" /><br /> <br /><i>'.$moderateurverifString.'</i>';
                    $comment_offer_top .= '<br /><b>'.$accordusageString.'</b><br /><input type="submit" name="send" value="'.$ajouterString.' '.$commentString.'" /><br /></form></div>';
                    $comment_offer_bot = '<div id="addcommentbot" style="text-align:center;display:block;"><a class="toggle" onclick="javascript:getelbyid(\'commentformbot\').style.display=\'block\';getelbyid(\'addcommentbot\').style.display=\'none\';">'.$ajouterString.' '.$commentString.'</a></div><div id="commentformbot" style="display:none;"><form enctype="multipart/form-data" action="'.lgx2readable($lg,$x,$this_is,$show_id).'" method="'.$postgetmethod.'"><input type="hidden" name="'.$this_is.'Id" value="'.$show_id.'" /><label for="commentMembre"> <b>'.$nomString.'</b> *</label> <input type="hidden" name="commentMembre" value="'.(isset($user_id)&&$user_id>0?$user_id:'0').'" style="width: 97%" />'.(isset($user_name)?$user_name:(isset($admin_name)?$admin_name:$anonymeString)).'<br /><label for="commentEntry"> <b>'.$commentString.'</b> *</label><br /><textarea id="elm1" name="commentEntry" rows="30" cols="40" style="width: 97%; height: 100px; min-height: 100px;">'.(isset($commentEntry)?format_edit($commentEntry,'edit'):'').'</textarea>';
                    if ($moderate_forum === true)
                    $comment_offer_bot .= '<br /><label for="code"> <b>'.$recopiercodeString.'</b> </label><br /><img src="'.$mainurl.'images/_captcha.php?string='.base64_encode($string).'" border="0" /> <input type="text" name="code" value="" /><br /> <br /><i>'.$moderateurverifString.'</i>';
                    $comment_offer_bot .= '<br /><b>'.$accordusageString.'</b><br /><input type="submit" name="send" value="'.$ajouterString.' '.$commentString.'" /><br /></form></div>';
                    $show_comment = $comment_offer_top.$show_comment.($get_comment[0]!=''?"<hr />".$comment_offer_bot:'');
                    if (!isset($_SESSION['mail_count'])) $_SESSION['mail_count'] = 0;
                    $show_comment .= '';
                  }
                }
              }
              
?>