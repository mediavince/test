<?php #۞ #
if (stristr($_SERVER['PHP_SELF'], basename(__FILE__))){include '_security.php';Header("Location: $redirect");Die();}

if (!isset($tpldir))
$tpldir = "lib/templates/"	;
  
$up = "../";

if (isset($google_jquery)&&$google_jquery) {
	$check_img = $tpldir.$active_template.'/arrow-circle-double.png';
	$code_div_jquery = '<div id="jquery-captcha-refresh-example"><img src="'.$mainurl
		.(@file_exists($check_img)?$check_img:'images/arrow-circle-double.png')
		.'" title="Rafraichir" alt="Rafraichir" class="refreshcaptcha" /></div>';
} else $code_div_jquery = '';
$form_content['code'] = '<label for="code">Anti-Sp@m</label><br />
			<img src="'.$mainurl.'images/_captcha.php" class="imgspam" /><br />'
			.$code_div_jquery.'
			<input class="text" name="code" type="text" /><br />';
			
if (isset($send) && in_array($send,array($envoyerString,$sauverString))
	&& isset($_SESSION['antispam_key'])
	) {
	if($_SERVER['REQUEST_METHOD'] !== "POST"){
		Header("Location: $redirect");Die();
	}
	if (!isset($form_content)) $form_content = array();//used with mod_inscrire
	$md5_post_code = md5($_POST['code']);
	if (isset($fpp) && ($fpp === true)) {
	// set un unset in function-process_post to error out on captcha
		if ($md5_post_code !== $_SESSION['antispam_key']) {
			$errors['code'] = $error_invmiss;
			$errors_invmiss[] = 'code';
			$error .= '<li>Anti-Sp@m : '.$error_invmiss.'</li></ul>';
		}
	//	$error .= '<ul><li>Anti-Sp@m > '.$error_invmiss.'</li></ul>';
		// double ul encapsulation for antispam code and on top of list
	} else {//align
		if ($md5_post_code !== $_SESSION['antispam_key']) {
				session_unregister('antispam_key');
				$errors['code'] = $error_invmiss;
				$errors_invmiss[] = 'code';
				$error .= '<ul><li>'.${"codeString"}.': '.$error_invmiss.'</li></ul>';
		} else {
			if ($error !== '')
			session_unregister('antispam_key');
		}
		$content .= $form_content['code'];
	}//align
} else {
	session_unregister('antispam_key');
	$content .= $form_content['code'];
}
