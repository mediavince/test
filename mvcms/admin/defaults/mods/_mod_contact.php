<?php if (stristr($_SERVER['PHP_SELF'],'_mod_contact.php')) {include '_security.php';Header("Location: $redirect");Die();}
//  session_start();
$tinyMCE = false;
  
$_mod_content = "";
if (!isset($max_session_mail_count)) $max_session_mail_count = '3';
if (!isset($max_msglen)) $max_msglen = '1500';

if (($lg != $default_lg) && isset(${"array_subject_".$lg}))
$array_subject = ${"array_subject_".$lg};

if ( isset($send) && ( $send == $envoyerString ) && isset($_SESSION['mail_count']) && isset($_SESSION['antispam_key']) ) {

  if($_SERVER['REQUEST_METHOD'] != "POST"){
     Header("Location: $redirect");Die();
  }
  
  contains_bad_str($email);
  contains_bad_str($subject);
  contains_bad_str($message);
  
  contains_newlines($email);
  contains_newlines($subject);

	$gen_message = '<table style="width:80%" align="center" border="1" cellpadding="10" cellspacing="5">';
	for ($i=0;$i<count($array_form);$i++) {
		$this_form = $array_form[$i];
		if ($this_form !== 'code') {
  		if	( isset($_REQUEST[$this_form]) ) {
    		if	($this_form !== 'subject')	${$this_form} = html_encode($_REQUEST[$this_form])	;
      	${$this_form} = nl2br(stripslashes($_REQUEST[$this_form]))	;
      }
  		if ($this_form == 'email')
  		$email = $_REQUEST[$this_form];
  		$gen_message .= '<tr><td align="right"><b>'.$this_form.'</b> </td><td>'.${$this_form}.' </td></tr>';
  	}
	}
	$gen_message .= '</table>';
	
	$name = stripslashes(strip_tags($name));
	$email = is_valid_email(strip_tags($email));
	$subject = strip_tags(html_encode(stripslashes($subject)));
	$message = nl2br(strip_tags(html_encode(stripslashes($message))));
	$md5_post_code = md5($_POST['code']);
	
	if ( !$name || ($name == '') || preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $name) || 
    !$email || ($email == '') || !is_email($email) || !is_valid_email($email) ||
    !$subject || !in_array($subject,$array_subject) ||
		!$message || ($message == '') || preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $message) || (strlen($message) > $max_msglen) ||
		($md5_post_code !== $_SESSION['antispam_key']) ||
		($_SESSION['mail_count'] >= $max_session_mail_count)
	) {
		$error .= $listecorrectionString.'<ul>';
		if ($md5_post_code !== $_SESSION['antispam_key'])
		$error .= '<ul><li>Anti-Sp@m > '.$error_invmiss.'</li></ul>'	;
		if (!$name || ($name == '') || preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $name))
		$error .= '<li>'.$nomString.' > '.$error_invmiss.$name.'</li>'	;
		if (!$email || ($email == '') || !is_email($email) || !is_valid_email($email))
		$error .= '<li>'.$emailString.' > '.$error_invmiss.$email.'</li>'	;
		if (preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $organisation))
		$error .= '<li>'.$organisationString.' > '.$error_inv.'</li>'	;
		if (!$subject || !in_array($subject,$array_subject))
		$error .= '<li>'.$sujetString.' > '.$error_invmiss.'</li>'	;
		if (!$message || preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $message))
		$error .= '<li>'.$messageString.' > '.$error_invmiss.'</li>'	;
		if (strlen($message) > $max_msglen)
		$error .= '<li>'.$messageString.' '.$troplongString.' > '.$error_inv.' :: max. '.$max_msglen.'</li>'	;
		if ($_SESSION['mail_count'] >= $max_session_mail_count)
		$error .= '<li>'.ucfirst($envoyerString).' '.$max_session_mail_count.' max. > '.$error_invmiss.'</li>'	;
		if ($md5_post_code !== $_SESSION['antispam_key'])
		$error .= '</ul>';//<a href="'.$_SERVER['REQUEST_URI'].'">'.$retourString.'</a><br />';
		else
		$error .= '</ul>';//<a href="javascript:history.back()//">'.$retourString.'</a><br />';
	} else {
    $_SESSION['mail_count']++;
	//	$message = '<html><body>'.$gen_message.'</body></html>';
    // In case any of our lines are larger than 70 characters, we should use wordwrap()
    $communicationsMessage = wordwrap($message,70,$CRLF,true);
		$this_email = is_valid_email($coinfo);
//	Email that gets sent to you.
  	$subject = html_entity_decode($subject);
    $nonhtml_content_generated = $communicationsMessage;
    $html_content_generated = $communicationsMessage;
    $footer = "$communicationsString ".$class_conjugaison->plural($envoyeString,'F',1).", $parString <a href=\"$mainurl\">$codename</a><br /><sup>$copyrightnoticeString</sup><br /> <br />";
    ################################## IMPORT TEMPLATE
    if (@file_exists($getcwd.$up.$safedir.'_tpl_mail_communications.php'))
      include($getcwd.$up.$safedir.'_tpl_mail_communications.php');
    else
      include($getcwd.$up.$urladmin.'defaults/_tpl_mail_communications.php');
  	$communications_msg = $_tpl_mail_communications;
  		
    if (stristr($_SERVER['HTTP_HOST'],"localhost"))
    $mail_conf = true;
    else
    $mail_conf = mail($this_email, $subject, $communications_msg, "From: $name <$email>".$mail_headers);
    if ($mail_conf === true) {
      $notice .= '<font color="Green"><b>'.$messageString.' <!--générique--> '.$class_conjugaison->plural($envoyeString,'M',1).' > </b></font><br />'.(stristr($_SERVER['HTTP_HOST'],"localhost")?"<br />$this_email, $subject, $communications_msg, \"From: $name <$email>\"" . "<br />\r\n".$mail_headers.'<hr /><br />':'');
    } else {
      $error .= '<font color="Red"><b>'.strtoupper($erreurString).' ! : </b></font><b>'.$messageString.' <!--générique--> '.$nonString.' '.$class_conjugaison->plural($envoyeString,'M',1).' > </b></font><br />';
    }
           
    if ($error=='') {
      $_SESSION['mv_notice'] = $notice;
      Header("Location: ".$_SERVER['REQUEST_URI']);Die();
    } 
            
	}
} else {
}

//  session_unregister('antispam_key'); // bugs in new php versions
/*
  $md5 = md5(microtime(1) * mktime(0,0,0,0,0,0));
  $string = substr($md5,0,rand(5,8));
  $_SESSION['antispam_key'] = md5($string);
*/
  if (!isset($_SESSION['mail_count'])) $_SESSION['mail_count'] = 0;
  $_mod_content .= gen_form($lg,$x).'<div class="contactform"><div class="leftfields"><label for="name"><b>> '.$nomString.'</b></label><br /><input class="text" name="name" type="text" value="'.(isset($name)?$name:'').'" /><br /><label for="fname"><b>> '.$prenomString.'</b></label><br /><input class="text" name="fname" type="text" value="'.(isset($fname)?$fname:'').'" /><br /><label for="organisation"><b>> '.$organisationString.'</b></label><br /><input class="text" name="organisation" type="text" value="'.(isset($organisation)?$organisation:'').'" /><br /><label for="email"><b>> '.$emailString.' </b></label><br /><input class="text" name="email" type="text" value="'.(isset($email)?$email:'').'" /><br /><label for="code"><b>> anti-spam </b></label><br /><input class="text" name="code" type="text" /><br /><img src="'.$mainurl.'images/_captcha.php'
	//	.'?string='.base64_encode($string)
	.'" class="imgspam" /></div><div class="rightfields"><label for="subject"><b>> '.$sujetString.' </b></label><br /><select class="text" name="subject" width="150">'.gen_selectoption($array_subject,(isset($subject)?$subject:''),'','').'</select><p><label for="message"><b>> '.$messageString.'</b></label><br /><textarea name="message" cols="25" rows="11" maxlength="'.$max_msglen.'">'.format_edit((isset($message)?$message:''),'edit').'</textarea></p></div><div style="text-align:left"><input type="submit" name="send" value="'.$envoyerString.'" /> | <input type="reset" value="Reset" /></div></div><div class="clear"></div></form>';

$_mod_contact = $_mod_content;
