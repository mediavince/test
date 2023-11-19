<?php if (stristr($_SERVER['PHP_SELF'],'_mod_inscrire.php')) {include '_security.php';Header("Location: $redirect");Die();}

if (!stristr($_SERVER['PHP_SELF'],$urladmin)) {
	$_mod_content = "";
	if (in_array($this_is,$array_modules_as_form) && ($logged_in === false)) {

		$moderate = true;
		$sendlink = false;
		$local_url = $local.substr($local_uri,1).(isset($q)?'?q='.$q:'?');
		$this_is = 'user';
		$that_is = 'membre';
		if (!isset($send))
		$send = 'new';
		$suppress_metas = true;
		if (empty($array_hidden))
		$array_hidden = array('pass', 'img', 'profession', 'adresse', 'ville', 'codpost',
							'pays', 'numtel', 'skype', 'numfax', 'marketing1', 'event', 'forum');
		if (empty($array_mandatory_fields))
		$array_mandatory_fields = array('','util','privilege','email',
										'profession', 'ville', 'codpost',
										'gendre','nom','prenom','code');

		if ($error!=='') $_SESSION['inscriredone'] = NULL;

		if (isset($send) && ($send == $envoyerString)
			&& isset($_SESSION['mail_count']) && isset($_SESSION['antispam_key'])
		) {
			if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
				Header("Location: $redirect");Die();
			}
		} else {
			/*
		session_unregister('antispam_key');
		$md5 = md5(microtime() * mktime(0,0,0,0,0,0));
		$string = substr($md5,0,rand(5,8));
		$_SESSION['antispam_key'] = md5($string);
			*/
		}

		if (!isset($membreGendre) || (isset($membreGendre) && ($membreGendre == "")))
		$membreGendre = "1";

		if (!isset($userPriv) || (isset($userPriv) && ($userPriv == "")))
		$userPriv = "1";

		$error_inv = str_replace('*','',$error_inv);
		$error_invmiss = str_replace('*','',$error_invmiss);
		$error_exists = str_replace('*','',$error_exists);

		if ($lg == 'fr')
		$array_input_hints = array('code'=>" Recopier le code i&ccedil;i");
		else
		$array_input_hints = array('code'=>" Copy the code here");

		$old_content = $content;

		if (!isset($_SESSION['inscriredone'])) {
			if (@file_exists($getcwd.$up.$urladmin.'itemadmin.php'))
			include $getcwd.$up.$urladmin.'itemadmin.php';
			if (isset($form_array_tpl)) {
				$content = "";
				foreach($form_content as $kc=>$fc) {
					${"form_inscrire_$kc"} = (!stristr($kc,"gendre")?
										'<div class="input_wrapper">'.$fc
										.(isset($errors[$kc])?
											'<div class="error_input">'.$errors[$kc].'</div>'
										:'').'</div>'
									:$fc);
					if (isset($array_input_hints[$kc]))
					${"form_inscrire_$kc"} = str_replace('<input ',
												'<input title="'.$array_input_hints[$kc].'" ',
												${"form_inscrire_$kc"}
											);
				}
				foreach($form_array_tpl as $tpl) {
					$content .= (isset($tpl[0])&&$tpl[0]=="$"?${substr($tpl,1)}:$tpl);
				}
			}
		} else $notice .= $_SESSION['inscriredone'];

		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		if (($error == '') && ($notice !== '') && isset($array_fields)) {
			$_SESSION['inscriredone'] .= $enregistrementString.' '
									.$class_conjugaison->plural($effectueString,'',1)
									.'!<br />'.$admincheckconfString;
			$notice .= $_SESSION['inscriredone']."<br />";
			$message = $enregistrementString.' '.$pourString.' '.$membreString.' '
					.$enregistreString.'!<br />'.$cliquericiString.' '.$pourString.' '
					.$ouvrirString.':<br />'.$mainurl.$urladmin
					.'?x=z&amp;y=membre'//&amp;membreId='.$this_id
					.'<br />'.$entrerString.' > '.$adminString.'!';
			$subject = $codename.' :: '.$nouveauString.' '.$membreString;
			$communicationsMessage = wordwrap($message,70,$CRLF,true);
			$this_email = $coinfo;
			//	Email that gets sent to you.
			$subject = html_entity_decode($subject);
			$nonhtml_content_generated = $communicationsMessage;
			$html_content_generated = $communicationsMessage;
			$footer = "$communicationsString ".$class_conjugaison->plural($envoyeString,'F',1)
					.", $parString <a href=\"$mainurl\">$slogan</a>
					<br /><sup>$copyrightnoticeString</sup><br /> <br />";
			################################## IMPORT TEMPLATE
			if (@file_exists($getcwd.$up.$safedir.'_tpl_mail_communications.php'))
			include($getcwd.$up.$safedir.'_tpl_mail_communications.php');
			else
			include($getcwd.$up.$urladmin.'defaults/_tpl_mail_communications.php');
			$communications_msg = $_tpl_mail_communications;

			if (stristr($_SERVER['HTTP_HOST'],"localhost"))
			$mail_conf = true;
			else
			$mail_conf = mail($this_email,$subject,$communications_msg,"From: $coinfo".$mail_headers);
			if ($mail_conf === true) {
				$notice .= '<font color="Green"><b>'.$messageString.' '
						.$class_conjugaison->plural($envoyeString,'M',1).' !</b></font><br />'
						.(stristr($_SERVER['HTTP_HOST'],"localhost")?
							"<br />$this_email, $subject, $communications_msg, \"From: $coinfo\""
							."<br />\r\n".$mail_headers.'<hr /><br />':'');
			}
		}


	} else {
		if ($logged_in === true)
		$_mod_content .= ucfirst($dejaString).' '.$membreString.'!<br />';
	}
	$_mod_inscrire = $_mod_content;
	// module_parser routine to place module where [] really is
	$content = str_replace("[inscrire]",$_mod_inscrire.$content,$old_content);

	$_SESSION['inscriredone'] = NULL;
}
