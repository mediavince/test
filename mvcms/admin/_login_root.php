<?php #۞ # USER
if (stristr($_SERVER['PHP_SELF'],'_login_root.php')) {
	include '_security.php';
	Header("Location: $redirect");Die();
}
//          $notice .= mvtrace(__FILE__,__LINE__)." $x<br />";
function user_logout()
{
    global $COOKIE,$cookie_codename,$redirect,$loginform,$logged_in,$lg,$form_error,$form_login,
        $error,$notice,$redirURL,$sortirString,$class_conjugaison,$effectueString,$login,
        $url_profil,$entrerString;
	if (isset($_COOKIE[$cookie_codename."user"])) {
		setcookie($cookie_codename."user", false);
		$_COOKIE[$cookie_codename."user"] = "";
		$_COOKIE[$cookie_codename."user"] = NULL;
		unset($_COOKIE[$cookie_codename."user"]);
	}
	$loginform = gen_form($lg).$notice.$form_login;//.$form_error
	$logged_in = false;
	$_SESSION['mv_notice'] = $sortirString.' '
	    .$class_conjugaison->plural($effectueString,'M','1').' ! <a href="'.$url_profil.'">'
	    .ucfirst($entrerString).'</a>';//.' '.$loginform;
	Header("Location: $redirect".(isset($redirURL)?$redirURL:''));Die();
}

$dbtable = $tbluser;
if (!isset($forgotpassString))
$forgotpassString = 'I forgot my password';
if (!isset($forgotpasshelpString))
$forgotpasshelpString = '
	<b>Follow these instructions:</b><br />
	> Input your username<br />
	> Check that box<br />
	> Click login<br />
	A new password will be sent to the email address on file!';
$div_forgotpass = '<div id="forgotpasshelp" class="helpbox">'.$forgotpasshelpString.'</div>';
if (!isset($nouveaumdpachangerString))
$nouveaumdpachangerString = 'Your new password has been sent to your email address,
	please update it upon your next login!';

$get_profil = sql_get(
    $tblcont,
    "WHERE contlang='$lg' AND conttype='profil' ",
	"conturl,conttitle,contpg"
);
$url_profil = $mainurl.$get_profil[0];
$get_inscrire = sql_get(
    $tblcont,
	"WHERE contlang='$lg' AND conttype='inscrire' ",
	"conturl,conttitle,contpg"
);
$url_inscrire = $mainurl.$get_inscrire[0];
if ($htaccess4sef === true) {
    $url_profil = $mainurl.lgx2readable($lg,$get_profil[2]);
    $url_inscrire = $mainurl.lgx2readable($lg,$get_inscrire[2]);
}

$form_login = '<input type="hidden" name="redirURL" value="'.substr($local_uri,1).'" />
	<label for="userName"> '.$nomutilString.' </label>
	<input class="inputtext" name="userName" type="text" autocomplete="off" /><br />
	<label for="passWord"> '.$motdepasseString.' </label>
	<input class="inputtext" name="passWord" type="password" autocomplete="off" /><br />
	<label for="forgotpass"> '.$forgotpassString.' </label>
	<input name="forgotpass" type="checkbox" id="forgotpasscheckbox" />
	<img src="'.$mainurl.'images/help.gif"
		onmouseover="javascript:getelbyid(\'forgotpasshelp\').style.display=\'block\';"
		onmouseout="javascript:getelbyid(\'forgotpasshelp\').style.display=\'none\';"
		onclick="javascript:getelbyid(\'forgotpasscheckbox\').checked=\'true\';" border="0" />
	<br />'.$div_forgotpass.'<input name="login" type="submit" value="'.$entrerString.'" />'
    .($get_inscrire[0]!='.'?' <a href="'.$url_inscrire.'">'.$get_inscrire[1].'</a>':'')
    .'</form>';

$form_error = ' * '.$accesString.' '.$refuseString.' * ';

$form_logout = '<input type="hidden" name="redirURL" value="'.substr($local_uri,1).'" />
	<input name="login" type="submit" value="'.$sortirString.'" />
	<a href="'.$url_profil.'">'.$get_profil[1].'</a></form>';

$user_array_fields = sql_fields($tbluser,'array');

#####################################
if (!isset($login)) {
	if (isset($_COOKIE[$cookie_codename."user"])) {
		$cookie = $_COOKIE[$cookie_codename."user"];
		$read_cookie = explode("^", base64_decode($cookie));
		$user_name = $read_cookie[0];
		$pass_word = $read_cookie[1];
		$user_priv = $read_cookie[2];
//		$user_idurl = $read_cookie[3];
		$user_priv = explode("|", $user_priv);
		if (!is_array($user_priv))	{
            $user_priv = array($user_priv);
            $sql_priv = " AND userpriv='$user_priv' ";
		} else {
		    $sql_priv = "AND (";
		    foreach($user_priv as $key)
		    $sql_priv .= " userpriv LIKE '%$key%' OR";
		    $sql_priv = substr($sql_priv,0,-2).") ";
        }
        $read = sql_get(
            $tbluser,
        	"WHERE userutil='$user_name' ",
        	"user".(in_array("userrid",$user_array_fields)?"r":'')."id,useremail"
        );
        $user_id = $read[0];
        $user_email = $read[1];
        if (sql_nrows($dbtable," WHERE userutil='$user_name' AND userpass='$pass_word' $sql_priv AND userstatut='Y' ") > 0) {
            $loginform = gen_form($lg).$user_name.'<br />'.$form_logout;
      	    $logged_in = true;
            if ((md5(sql_getone($tbluser,"WHERE userutil='$user_name' ","useremail")) == $pass_word)
                && !stristr($url_profil,$_SERVER["REQUEST_URI"])
            ) {
                Header("Location: $url_profil");Die();
    	  	}
    	} else {
    		user_logout();
    		$loginform = gen_form($lg).$form_login;
    		$logged_in = false;
    	}
###!!! add else if session is started in case cookies don't work !!!###
	} else {
		$loginform = gen_form($lg).$form_login;
		$logged_in = false;
	}
#####################################
} else if ( ($login == $entrerString) && (isset($userName)) && (isset($passWord)) ) {
	$user_name = strip_tags(html_encode($userName));
	$pass_word = strip_tags(html_encode($passWord));
	$pass_word_md5 = md5($pass_word);
	if (($user_name != '') && isset($_POST['forgotpass']) && ($_POST['forgotpass'] == 'on')) {
    	$this_isPass = substr(md5(substr(md5(microtime() * mktime()),0,rand(5,8))),0,rand(6,9));
        $this_isPass = substr(base64_encode(md5(microtime() * mktime())),0,rand(6,9));
		$read = sql_update(
			$dbtable,
			"SET userpass='".md5($this_isPass)."' ",
			" WHERE userutil='$user_name' AND userstatut='Y' ",
			"userutil,useremail,userpriv"
		);
		// SEND RESET PASSWORD
		if (($read[0] == $user_name) && is_email($read[1])) {
		  	$this_is = "forgotpass";
		  	$mail_email = is_valid_email($read[1]);
		  	$mail_subject = html_entity_decode("$slogan : $accesString $membreString $modifieString");
		  	contains_bad_str($mail_email);
		  	contains_bad_str($mail_subject);
		  	contains_newlines($mail_email);
		  	contains_newlines($mail_subject);
		  	$this_isUtil = $user_name;
		  	$read_priv = array();
		  	foreach(explode("|",$read[2]) as $k_priv)
		  	$read_priv[] = sql_stringit('privilege',$k_priv);
		  	$this_isPriv_astext = implode(", ",$read_priv);
		  	$footer = "$messageString $envoyeString $parString $coinfo<hr /><br />$slogan";
		  	################################## IMPORT TEMPLATE
		  	if (file_exists($getcwd.$up.$safedir.'_tpl_mail_credentials.php'))
		  		include $getcwd.$up.$safedir.'_tpl_mail_credentials.php';
		  	else
		  		include $getcwd.$up.$urladmin.'defaults/_tpl_mail_credentials.php';
		  	if (isset($array_mail_credentials)) {
		  		$mail_message = '';
		  		foreach($array_mail_credentials as $tpl_l)
		  		$mail_message .= ($tpl_l[0]=="$"?${substr($tpl_l,1)}:$tpl_l);
		  	} else {
		  		$mail_message = $_tpl_mail_credentials;
		  	}
// 		  	contains_bad_str($mail_message);// uncomment if not using multi-part which contains content-type info
		  	$mail_message = wordwrap($mail_message,70,$CRLF,true);
		  	$codename = html_entity_decode($codename);
		  	if (stristr($_SERVER["HTTP_HOST"], "localhost") || stristr($_SERVER["HTTP_HOST"], "192.168.3.")) {
		  		$sent_email = 0;
		  		$notice .= "<hr />".$mail_message."<hr /><textarea cols='100' rows='30'>$mail_message</textarea>";
		  	} else
		  		$sent_email = mail($mail_email, $mail_subject, $mail_message, "From: $codename <$coinfo>".$mail_headers);
		  	$notice .= $nouveaumdpachangerString;
		  	$_SESSION['mv_notice'] = $notice;
		  	Header("Location: $redirect".(isset($redirURL)?$redirURL:''));Die();
    	} else {
  			$loginform = gen_form($lg).$form_login;//.$form_error
  			$logged_in = false;
  			$error .= $form_error;
    	}
		// SEND RESET PASSWORD
	} else if (($user_name != '') && ($pass_word != '')) {// || !preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $user_name)
		if (!$connection) {
			$loginform = gen_form($lg).$form_login;//.$form_error
			$logged_in = false;
  			$error .= $form_error;
		} else if ($connection) {
			$read = sql_get(
				$dbtable,
				"WHERE userutil='$user_name' AND userpass='$pass_word_md5' AND userstatut='Y' ",
				"userutil,userpass,userdate,userpriv,useremail,user".(in_array("userrid",$user_array_fields)?"r":'')."id"
			);
			$user_email = $read[4];
			$user_id = $read[5];
			$user_priv = explode("|",$read[3]);
			if (!is_array($user_priv))
			$user_priv = array($user_priv);
			$countread = sql_nrows($dbtable, " WHERE userutil='$user_name' AND userpass='$pass_word_md5' AND userstatut='Y' ");
			if ( (!$read) || ($read[0] == '.') || ($countread == 0) ) {
			// ($countread != (in_array("userrid",$user_array_fields)?count($array_lang):'1'))
				$loginform = gen_form($lg).$form_login;//.$form_error
				$logged_in = false;
    			$error .= $form_error;
			} else {
			// if connection succeeded get info from DB and compare variables
				if ( !(($user_name == $read[0]) && ($pass_word_md5 == $read[1])) ) {
					$loginform = gen_form($lg).$form_login;//.$form_error
					$logged_in = false;
      				$error .= $form_error;
				} else if ( ($pass_word_md5 == $read[1]) && ($user_name == $read[0]) ) {
					$info = base64_encode("$user_name^$pass_word_md5^".implode("|",$user_priv)."^");//."^".$idurl_constructor);
					setcookie($cookie_codename."user","$info",0);
					//,'/'.$urlclient ???last double quote needed for assuring persistency of login
					// checked and not needed as long as action goes root
					$update = sql_update(
						$dbtable,
						"SET userdate=$dbtime",
						"WHERE userutil='$user_name' AND userpass='$pass_word_md5' ",
						"userdate"
					);
					$loginform = $bienvenueString.' '.$user_name.' !<br />
						<sup>'.$dernierString.' '.$accesString.' : "'.$read[2].'"</sup><br />';
				  	$logged_in = true	;
          			$notice = ''.$loginform;
          	//.'<div style="background-color: #CF9;border: 1px solid green;padding: 5px;margin: 5px;text-align:center;"></div>';
					$loginform = gen_form($lg).$loginform.$form_logout;
					if ($read[1] == md5($read[4])) {
						$pwdtochange = true;
            			$notice = '<div
            				style="background-color:#FA5;width:90%;border:1px solid red;padding:5px;margin:5px;text-align:center;">'
            				.strtoupper($modifierString.' '.$motdepasseString).' !'
            				.(!stristr($_SERVER["PHP_SELF"],$get_profil[0])?' >> <a href="'.$url_profil.'">'.$get_profil[1].'</a>':'')
            				.'</div>';
          			}
		  			$_SESSION['mv_notice'] = $notice;
		  			Header("Location: $redirect".(isset($redirURL)?$redirURL:''));Die();
				}
			}
		}
	} else {
		$loginform = gen_form($lg).$form_login;//.$form_error
		$logged_in = false;
		$error .= $form_error;
		$_SESSION['mv_error'] = $error;
		Header("Location: $redirect".(isset($redirURL)?$redirURL:''));Die();
	}
#####################################
} else if ($login == $sortirString) {
  	user_logout();
	$loginform = gen_form($lg).$form_login;
	$logged_in = false;
	$notice = $sortirString.' '.$class_conjugaison->plural($effectueString,'M','1').' '.$loginform;
	Header("Location: $redirect".(isset($redirURL)?$redirURL:''));Die();
#####################################
} else {
	Header("Location: $redirect#$login");Die();
}
