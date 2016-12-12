<?php if (stristr($_SERVER['PHP_SELF'],'_mod_form.php') {include '_security.php';Header("Location: $redirect");Die();}

  $form_mod = '';
	session_start();
if ( !isset($send) ) {
  $form_mod .= gen_form($lg,$x).'<div style="width:80%;margin: 0 auto;">
<div style="float:left;padding:15px;">
	<label for="name"><b>> '.$nomString.'</b></label><br />
	<input class="text" name="name" type=text /><br />
	<label for="fname"><b>> '.$prenomString.'</b></label><br />
	<input class="text" name="fname" type=text /><br />
	<label for="organisation"><b>> '.$organisationString.'</b></label><br />
	<input class="text" name="organization" type=text /><br />
	<label for="email"><b>> '.$emailString.' </b></label><br />
	<input class="text" name="email" type=text /><br />
	<label for="code"><b>> anti-spam </b></label><br />
	<input class="text" name="code" type=text /><br />
	<img src="'.$domain.'blog/_captcha.php" style="padding: 1px 8px 0px 8px;" />
</div>
<div style="padding:15px;">
	<label for="subject"><b>> '.$sujetString.' </b></label><br />
	<select class="text" name="subject" width="150">
		<option value="">:: '.$achoisirString.' ::</option>
		<option value="openposition">Open positions</option>
		<option value="informations">Informations about us</option>
		<option value="other">Other</option>
	</select><p>
	<label for="message"><b>> '.$messageString.'</b></label><br />
	<textarea name="message" cols="25" rows="11" style="width:230px;height:100px" maxlength="500"></textarea></p>
</div>
<div style="text-align:left">
	<input type="submit" name="send" value="'.$envoyerString.'" /> | <input type=reset value="Reset" />
</div>
</div>
<br clear="all" />
</form>';

} else if ( $send == $envoyerString ) {
	$array_form = array("name","fname","organisation","email","code","subject","message");
	$gen_message = '<table style="width:80%" align="center" border="1" cellpadding="10" cellspacing="5">';
	for ($i=0;$i<count($array_form);$i++) {
		$this_form = $array_form[$i];
		if	( isset($_REQUEST[$this_form]) )	${$this_form} = nl2br(stripslashes(html_encode($_REQUEST[$this_form])))	;
		if ($this_form !== 'code')
		$gen_message .= '<tr><td align="right"><b>'.$this_form.'</b>&nbsp;</td><td>'.${$this_form}.'&nbsp;</td></tr>';
	}
	$gen_message .= '</table>';
//	$trace .= $gen_message;
	$name = stripslashes(strip_tags($name));
	$email = is_valid_email(strip_tags($email));
	$subject = strip_tags($subject);
	if (!$name || ($name == '') || preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $name) || 
		!$email || ($email == '') || !is_email($email) ||
		preg_match("/^[~%*#§|}{°]+\$/", $organisation) ||
		preg_match("/^[~%*#§|}{°]+\$/", $message) || !$message || ($message == '') || (strlen($message) > 500) ||
		((md5($_POST['code']) !== $_SESSION['antispam_key']))
	) {
		if ($lg == 'it') $form_mod .= '<p>Per favore compila la scheda a seguire:<ul>';
		else $form_mod .= '<p>Please check the following errors :<ul>';
		if (!$name || ($name == '') || preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $name) )
		$form_mod .= '<li>'.$nomString.' > '.$error_invmiss.$name.'</li>'	;
    	if (!$email || ($email == '') || !is_email($email) )
		$form_mod .= '<li>'.$emailString.' > '.$error_invmiss.$email.'</li>'	;
		if ( preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $organisation) )
		$form_mod .= '<li>'.$organisationString.' > '.$error_inv.'<br /> <br /></li>'	;
		if ( !$message || preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/", $message) )
		$form_mod .= '<li>'.$messageString.' > '.$error_invmiss.'<br /> <br /></li>'	;
		if (  (strlen($message) > 500) )
		$form_mod .= '<li>'.$messageString.' > '.$error_inv.' :: max. length 500 chars.<br /> <br /></li>'	;
		if ( (md5($_POST['code']) !== $_SESSION['antispam_key']) )
		$form_mod .= '<li>Code > '.$error_invmiss.'<br /> <br /></li>'	;
		if (preg_match("/^[~%*#§|}{°]+\$/", $message))
		$form_mod .= '<li>'.$messageString.' > '.$error_invmiss.'</li>'	;
		$form_mod .= '</ul><br /> <br /><p><a href="javascript:history.back()//">'.$retourString.'</a></p>';
	} else {
		$message = '<html><body>'.$gen_message.'</body></html>';
		$confirm_sub = $codename.' :: '.$subject;	// .' :: '.$messageString.' '.$envoyeString.' !';
		$confirm_msg = '<html><body><a href='.$mainurl.'>'.$cologo.'</a>'.$name.',<!-- <br /> <br />Grazie --><br /> <br />'.$codename.' '.$repondraString.' '.$bientotString.' '.$surString.' "<i>'.$subject.'</i>"<br /> <br /><a href='.$mainurl.'>'.$cosite.'</a></body></html>';
		$this_email = $coinfo;
//	Email that gets sent to you.
		$sendmail = mail($this_email, $subject, $message, "From: $email <$email>".$mail_headers);
			mail($email, $confirm_sub, $confirm_msg, "From: $coname <$coinfo>".$mail_headers);
		if (!$sendmail) {
			$form_mod .= $error_request.'<p><a href="javascript:history.back()//">'.$retourString.'</a></p>';
		} else {
// Email that gets sent to them.
			mail($email, $confirm_sub, $confirm_msg, "From: $coname <$coinfo>".$mail_headers);
			$form_mod .= '<font color="Green"><b>'.$messageString.' '.$envoyeString.'</b></font><br /> <br />';
		}
	}
}
