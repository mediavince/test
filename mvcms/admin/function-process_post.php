<?php #۞ #
if (stristr($_SERVER['PHP_SELF'], basename(__FILE__))){include '_security.php';Header("Location: $redirect");Die();}

if (!isset($array_modules_as_form)) $array_modules_as_form = array();
if (!isset($old_this)) $old_this = $this_is;
if (isset($that_is))
	if (!isset($old_that)) $old_that = $that_is;
if (!isset($old_dbtable)) $old_dbtable = $dbtable;

if (!isset($error_report)) $error_report = '';
if (!isset($error)) $error = '';

if (!isset($errors)) $errors = array();// stacks errors <this key> => <error type>
// following arrays stack simply as <this key>
if (!isset($errors_exists)) $errors_exists = array();// stacks multiplicity and uniqueness errors
if (!isset($errors_inv)) $errors_inv = array();// stacks invalid errors
if (!isset($errors_invmiss)) $errors_invmiss = array();// stacks invalid or missing errors

if (!isset($notice)) $notice = '';
if (!isset($error_img)) $error_img = '';// stacked as this key or inv, cf above errors arrays
if (!isset($notice_img)) $notice_img = '';
if (!isset($now_time)) $now_time = time();

$key_loop = '';

if (($send == 'new') || ($send == $envoyerString)) {
	if ($lg != $default_lg) {
		$default_lg = $lg;
		$array_lang = sql_array($tblenum,"WHERE enumstatut='Y'
											AND enumwhat='lang'
											AND enumtype!='$default_lg' ",
											"enumtype");
		if (!isset($array_lang[0]) || ($array_lang[0] == "")) {
			$array_lang = array($default_lg);
		} else {
			$array_lang = array_reverse($array_lang);
			$array_lang[] = $default_lg;
			$array_lang = array_reverse($array_lang);// so default_lg comes in first
		}
	}
}

$loop_array_lang = $array_lang;
//	if (!in_array($this_is,array("user")))
if (in_array($this_is."lang",$array_fields) && ($lg == $default_lg)) {
	if ($send == $sauverString) {
		$loop_array_lang = array($default_lg);
		$this_identifier = (in_array($this_is.'gendre',$array_fields)?$this_is.'date':
							(in_array($this_is."title",$array_fields)?$this_is."title":
							(in_array($this_is."titre",$array_fields)?$this_is."titre":
							(in_array($this_is."util",$array_fields)?$this_is."util":
							(in_array($this_is."title",$array_fields)?$this_is."title":
							$array_fields[count($basic_array)])))));
        foreach(sql_array($dbtable,"WHERE ".$this_is."lang!='$default_lg'
									AND ".$this_identifier."='"
									.sql_getone($dbtable,"WHERE ".$this_is."lang='$default_lg'
													AND ".$this_is.($this_id_rid===true?'r':'')
													."id='".${$this_is."Id"}."'",
													$this_identifier)."'",$this_is."lang")
				as $k)
			if (in_array($k,$array_lang))
			$loop_array_lang[] = $k;
		}// else $loop_array_lang = $array_lang;
    } else {//    $loop_array_lang = array($lg);
		if ((!in_array($this_is."lang",$array_fields) && !isset($that_is))
	  	|| (isset($that_is) && !in_array($that_is."lang",$that_array_fields)))
		$loop_array_lang = array($lg);
		else $loop_array_lang = array($lg);
	}
	if (isset($moderate) && ($moderate === true)) { // enables the moderation in mods like inscrire
		$fpp = true;
		include $getcwd.$up.$urladmin.'_moderator_captcha.php';
		if ($error!=='') $error_report = $error;
		$error = '';
		$fpp = NULL;
	}
    foreach($loop_array_lang as $loop_lg) {
		for($i=0;$i<count($array_fields);$i++) {
	//	if (($this_is == "user") && ($loop_lg != $default_lg)) // prevent repeat of user routine
	//	continue;
		if (substr($array_fields[$i],0,strlen($dbtable)-1) != $this_is) {
			$this_is = $that_is;
			$dbtable = $that_dbtable;
		}
		$key = substr($array_fields[$i],strlen($dbtable)-1);
		if (in_array($key,$array_mandatory_fields))
		if (!in_array("$this_is$key",$enumtype_array)
		&& ((!stristr($_SERVER['PHP_SELF'],$urladmin) && ($user_can_add_types===false))
		|| stristr($_SERVER['PHP_SELF'],$urladmin)))
		if (!isset(${$this_is.ucfirst($key)})
		|| (isset(${$this_is.ucfirst($key)}) && (${$this_is.ucfirst($key)} == ''))) {
			if ($lg == $loop_lg) {
				$errors[$this_is.$key] = $error_invmiss;
				$errors_invmiss[] = $this_is.$key;
				$error_report .= '<li>'.${$key."String"}.': '.$error_invmiss.'</li>';
			}
		}
	//	global ${$key."String"},${$this_is.ucfirst($key)};
		if (in_array($key,$basic_array) && stristr($_SERVER['PHP_SELF'],$urladmin)) {
			if ($send == $envoyerString) {
				if ($key == 'statut')
				${$this_is.ucfirst($key)} = $editthis[$i];
			}
			if (!isset($master_item) && (($key == 'id') || ($key == 'rid'))) {
				if (isset(${$this_is.ucfirst($key)})
				&& preg_match("/^[0-9]+\$/",${$this_is.ucfirst($key)})) {
					$this_id = ${$this_is.ucfirst($key)};
					if ($loop_lg == $default_lg)
					$master_item = $this_id;
					else {
						if ($key != 'rid') {
							$master_item = sql_getone($tblhtaccess,
												"WHERE htaccesslang='$default_lg'
												AND htaccessitem='".sql_getone($tblhtaccess,
																		"WHERE htaccesslang='$loop_lg'
																		AND htaccessid='$this_id' ",
																		"htaccessitem")." ",
												"htaccessentry");
						} else $master_item = $this_id;
					}
				}
			}
			if ($key == 'statut') {
				if (${$this_is.ucfirst($key)} && !preg_match("/^[YN]\$/",${$this_is.ucfirst($key)}))
				${$this_is.ucfirst($key)} = (stristr($_SERVER['PHP_SELF'],$urladmin)?'Y':'N');
				if (isset($moderate) && ($moderate === true))
				${$this_is.ucfirst($key)} = 'N';
			}
			if (($key == 'date') && in_array($key,$array_mandatory_fields))
			if (${$this_is.ucfirst($key)}
			&& preg_match("/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}[ ]{0,1}[0-9]{0,2}[:]{0,1}[0-9]{0,2}[:]{0,1}[0-9]{0,2}\$/",
				${$this_is.ucfirst($key)}))
			${$this_is.ucfirst($key)} = db_date(${$this_is.ucfirst($key)});
			else {
				if ($lg == $loop_lg) {
					$errors[$this_is.$key] = $error_inv;
					$errors_inv[] = $this_is.$key;
					$error_report .= '<li>'.${$key."String"}.': '.$error_inv.'</li>';
				}
			}
			if ($key == 'lang')
			${$this_is.ucfirst($key)} = $loop_lg;
		} else {
/////////// routine for dynamic processing
			if (($key == 'title') || ($key == 'titre')) {
				${$this_is.ucfirst($key)} = strip_tags(html_encode(${$this_is.ucfirst($key)}));
				$this_titre = ${$this_is.ucfirst($key)};
				$countread = 0;
				if (!in_array($key,$array_mandatory_fields))
				if (!$this_titre || ($this_titre == '')) {
					if ($lg == $loop_lg) {
						$errors[$this_is.$key] = $error_invmiss;
						$errors_invmiss[] = $this_is.$key;
						$error_report .= '<li>'.${$key."String"}.': '.$this_titre.' > '.$error_invmiss.'</li>';
					}
					$countread = sql_nrows($dbtable,"WHERE ".$this_is.$key."='".$this_titre."' "
									.(in_array("lang",$basic_array)?" AND ".$this_is."lang='$loop_lg' ":''));
				}
				if ($send == $sauverString)
				if (stristr($this_titre,$editthis[$i]))
				$countread -= ($this_id_rid===true?count($array_lang):1);// 1
				if ($countread > 0) {
					if ($lg == $loop_lg) {
						$errors[$this_is.$key] = $error_exists;
						$errors_exists[] = $this_is.$key;
						$error_report .= '<li>'.${$key."String"}.': '.$this_titre.' > '.$error_exists.'</li>';
					}
				}
			} else if ($key == 'util') {
				${$this_is.ucfirst($key)} = strip_tags(html_encode(${$this_is.ucfirst($key)}));
				if (in_array($key,$array_mandatory_fields) && (${$this_is.ucfirst($key)} != '')) {
					if (($this_is == 'admin') && !preg_match("/^[a-zA-Z0-9_ -]+\$/",${$this_is.ucfirst($key)})) {
						if ($lg == $loop_lg) {
							$errors[$this_is.$key] = $error_inv;
							$errors_inv[] = $this_is.$key;
							$error_report .= '<li>'.${$key."String"}.': '.$error_inv.'</li>';
						}
					}
					$countread = sql_nrows($dbtable,"WHERE ".$this_is.$key."='".${$this_is.ucfirst($key)}."' ");
					if ($send == $sauverString) {
						if ($editthis[$i] == ${$this_is.ucfirst($key)})
						$countread -= ($this_id_rid===true?count($array_lang):1);// 1
					}
					if ($countread > 0) {
						if ($lg == $loop_lg) {
							$errors[$this_is.$key] = $error_exists;
							$errors_exists[] = $this_is.$key;
							$error_report .= '<li>'.${$key."String"}.': '.${$this_is.ucfirst($key)}.' > '
											.$error_exists.'</li>';
						}
					}
				}
			} else if ($key == 'email') {
				if (in_array($key,$array_mandatory_fields) && (${$this_is.ucfirst($key)} != '')) {
					if (!is_email(${$this_is.ucfirst($key)})) {
						if ($lg == $loop_lg) {
							$errors[$this_is.$key] = $error_inv;
							$errors_inv[] = $this_is.$key;
							$error_report .= '<li>'.${$key."String"}.': '.$error_inv.'</li>';
						}
					} else
					${$this_is.ucfirst($key)} = strip_tags(html_encode(strtolower(${$this_is.ucfirst($key)})));
					$countread = sql_nrows($dbtable,"WHERE ".$this_is.$key."='".${$this_is.ucfirst($key)}."' ");
					if ($send == $sauverString) {
						if ($editthis[$i] == ${$this_is.ucfirst($key)})
						$countread -= ($this_id_rid===true?count($array_lang):1);// 1
					}
					if ($countread > 0) {
						if ($lg == $loop_lg) {
							$errors[$this_is.$key] = $error_exists;
							$errors_exists[] = $this_is.$key;
							$error_report .= '<li>'.${$key."String"}.': '.${$this_is.ucfirst($key)}.' > '.$error_exists.'</li>';
						}
					}
				}
			} else if ($key == 'website') {
				${$this_is.ucfirst($key)} = (!stristr(${$this_is.ucfirst($key)},"http://")?"http://":'')
											.strip_tags(html_encode(${$this_is.ucfirst($key)}));
				if ((${$this_is.ucfirst($key)} != '') && !is_url(${$this_is.ucfirst($key)})) {
					if ($lg == $loop_lg) {
						$errors[$this_is.$key] = $error_inv;
						$errors_inv[] = $this_is.$key;
						$error_report .= '<li>'.${$key."String"}.': '.$error_inv.'</li>';
					}
				}
			} else if ($key == 'pass') {
				if (($send == $envoyerString) && ($lg == $loop_lg)) {
					if (${$this_is.ucfirst($key)} == "") {
						if ($this_is == 'admin') {
							if ($lg == $loop_lg) {
								$errors[$this_is.$key] = $error_invmiss;
								$errors_invmiss[] = $this_is.$key;
								$error_report .= '<li>'.${$key."String"}.': '.$error_invmiss.'</li>';
							}
						} else {
			                if (isset($_POST[$this_is."Email"]) && is_email($_POST[$this_is."Email"]))
			                ${$this_is.ucfirst($key)} = md5(strip_tags(html_encode(strtolower(trim($_POST[$this_is."Email"])))));
						}
					} else {
						${$this_is.ucfirst($key)} = md5(strip_tags(html_encode(${$this_is.ucfirst($key)})));
					}
				}
				if (($send == $sauverString) && (${$this_is.ucfirst($key)} != ""))
				${$this_is.ucfirst($key)} = md5(strip_tags(html_encode(${$this_is.ucfirst($key)})));
			} else if ($key == 'priv') {
				if ($this_is == 'admin') {
					if (in_array($_POST['privilege'],$array_admin_priv)) {
						$adminPriv = strip_tags(html_encode($_POST['privilege']));
						$valid_adminpriv = true;
					}
				} else {
					${$this_is.ucfirst($key)} = "";
					${$this_is.ucfirst($key)."_astext"} = "";
					$array_priv = sql_array($tblenum,"WHERE enumwhat='privilege' AND enumstatut='Y' ","enumtitre");
					if ($array_priv[0] !== "") {
						foreach($array_priv as $k_priv)
						if (isset($_POST["privilege".$k_priv]) && ($_POST["privilege".$k_priv] == 'on')) {
							${$this_is.ucfirst($key)} .= $k_priv."|";
							${$this_is.ucfirst($key)."_astext"} .= sql_stringit('privilege',$k_priv).",";
						}
						${$this_is.ucfirst($key)} = substr(${$this_is.ucfirst($key)},0,-1);
						${$this_is.ucfirst($key)."_astext"} = substr(${$this_is.ucfirst($key)."_astext"},0,-1);
					} else {
						${$this_is.ucfirst($key)} == "1";
						${$this_is.ucfirst($key)."_astext"} = sql_stringit('privilege',${$this_is.ucfirst($key)});
					}
				}
			} else if (($lg == $loop_lg) && (($key == 'img') || ($key == 'doc'))) {
				if ($array_fields_type[$this_is.$key] == 'text') {
					$editthis[$i] = explode("|",(isset(${$key."_default_lg_passing_desc_for_insert_if_dbtable"})?
													${$key."_default_lg_passing_desc_for_insert_if_dbtable"}:$editthis[$i]));
				} else {
					if (isset($this_id)) {
						if ($key == 'doc')
						$editthis[$i] = sql_array($tblcontdoc,"WHERE contdoclang='$loop_lg' AND contdoccontid='$this_id' ","contdoc");
						if ($key == 'img')
						$editthis[$i] = sql_array($tblcontphoto,"WHERE contphotolang='$loop_lg' AND contphotocontid='$this_id' ","contphotoimg");
					} else {
						$editthis[$i] = array();
					}
				}
				$this_nof = $nof;// reset nof
				$count_explode = count($editthis[$i]);
				$this_nof = ($count_explode<$nof||$send=='new'?$nof:($count_explode+1));
				$this_nof = (count($_FILES)>$this_nof?count($_FILES):$this_nof);
				if ($error_report == '')
				for ($ii=0;$ii<$this_nof;$ii++) {
					if (!isset($_FILES[$this_is.ucfirst($key)]["name"][$ii]))
						break;
					$userfile_name = $_FILES[$this_is.ucfirst($key)]["name"][$ii];
					$userfile_tmp = $_FILES[$this_is.ucfirst($key)]["tmp_name"][$ii];
					foreach($array_lang as $keylg)
					if (($keylg == $default_lg) && ($userfile_name == "") && (isset($editthis[$i][$ii]) && ($editthis[$i][$ii] != ''))) {
						if ($array_fields_type[$this_is.$key] == 'text') {
							$new_upload = $editthis[$i][$ii];
							if (isset(${$key."_passing_desc_for_insert_if_dbtable"})
								&& is_array(${$key."_passing_desc_for_insert_if_dbtable"})) {
								if (!in_array($new_upload,${$key."_passing_desc_for_insert_if_dbtable"}))
								${$key."_passing_desc_for_insert_if_dbtable"}[] = $new_upload;
							} else
							${$key."_passing_desc_for_insert_if_dbtable"} = array($new_upload);
						}
					}
					if (!@file_exists($userfile_tmp))
					continue;
					if (($lg == $loop_lg) && ($userfile_name == "") && (isset($editthis[$i][$ii]) && ($editthis[$i][$ii] != ''))) {
					} else {
						if (($send == 'new')
						|| (!isset($_POST[$this_is."Desc"][$ii])
						|| (isset($_POST[$this_is."Desc"][$ii]) && ($_POST[$this_is."Desc"][$ii] == '')))) {
							if (isset($this_titre) && ($this_titre != ''))
							${$this_is."Desc"} = space2underscore($this_titre).($ii>0?"_$ii":'');
							else {
								if ($this_is == 'membre')
								${$this_is."Desc"} = space2underscore(${$this_is."Nom"}."-".${$this_is."Prenom"}).($ii>0?"_$ii":'');
								else
								${$this_is."Desc"} = space2underscore((isset($this_titre)?
									$this_titre:(isset(${$this_is."Util"})?${$this_is."Util"}:$_POST[$this_is."Id"])))
										.($ii>0?"_$ii":'');
							}
						} else {
							${$this_is."Desc"} = stripslashes(str_replace("'","´",html_encode($_POST[$this_is."Desc"][$ii])))
												.($ii>0?"_$ii":'');
						}
						$userfile_size = $_FILES[$this_is.ucfirst($key)]["size"][$ii];
						$userfile_type = $_FILES[$this_is.ucfirst($key)]["type"][$ii];
						$ext = explode('.',strrev($userfile_name),2);
						$ext = strtolower(strrev($ext[0]));
						if ($key == "img") {
							if (in_array($ext,array_merge($array_img_ext,$array_swf_ext)))
							$valid_ext = "true";
							$media_line = "contphoto";// $key added for contphotoimg field
						}
						if ($key == "doc") {
							if (in_array($ext,$array_doc_ext))
							$valid_ext = "true";
							$media_line = "contdoc";// $key NOT added for contdoc field
						}
						$sql = "WHERE ".$media_line."
								LIKE '%".strtoupper(space2underscore($this_is))."_".space2underscore(${$this_is."Desc"})."%'
								OR ".$this_is.$key."
								LIKE '%".strtoupper(space2underscore($this_is))."_".space2underscore(${$this_is."Desc"})."%' ";
						$check = sql_nrows("$dbtable,".${"tbl".$media_line},$sql);
						if ($check > 0) {
							$to_del_file = sql_getone($dbtable,"WHERE ".$this_is.$key."
								LIKE '%".strtoupper(space2underscore($this_is))."_".space2underscore(${$this_is."Desc"})."%' ",
								$this_is.$key);
							if ($to_del_file == '')
							$to_del_file = sql_getone(${"tbl".$media_line},"WHERE ".$media_line.($key=='img'?$key:'')."
								LIKE '%".strtoupper(space2underscore($this_is))."_".space2underscore(${$this_is."Desc"})."%' ",
								"contphotodesc");
							sql_del(${"tbl".$media_line},"WHERE ".$media_line.($key=='img'?$key:'')."
														LIKE '%".${$this_is."Desc"}."%' ");
							sql_update($dbtable,"SET ".$this_is.$key."='' ",
												"WHERE ".$this_is.$key." LIKE '%".${$this_is."Desc"}."%' ","");
							delete_imgdoc($this_is,space2underscore($to_del_file));
							$check = sql_nrows("$dbtable,".${"tbl".$media_line},$sql);
						}
	        			if (!isset($valid_ext)) $valid_ext = "false";
	        			if (!${$this_is."Desc"} || (${$this_is."Desc"} == '')
	        			|| !preg_match("/^[a-zA-Z0-9_-]+\$/",strtoupper(space2underscore($this_is))."_".space2underscore(${$this_is."Desc"}))
	        			|| ($userfile_name == '') || ($valid_ext !== 'true') || ($valid_ext == 'false')
	        			|| ($userfile_size > $max_filesize)
	        			|| ($check > 0)
	        				) {
							if (!${$this_is."Desc"}
							|| !preg_match("/^[a-zA-Z0-9_-]+\$/",strtoupper(space2underscore($this_is))."_".space2underscore(${$this_is."Desc"}))
							|| (${$this_is."Desc"} == ''))
		$error_img .= '<li>'.$nomString.' > '.$error_invmiss.'</li>';
							if (($userfile_name == '') || ($valid_ext !== 'true') || ($valid_ext == 'false'))
		$error_img .= '<li>'.$fichierString.' > '.$error_invmiss.'</li>';
							if ($userfile_size > $max_filesize)
		$error_img .= '<li>'.$fichierString.' > '.$error_inv.' ('.$max_filesizeString.')</li>';
							if ($check > 0)
		$error_img .= '<li>'.($key=="img"?$photoString:$documentString).' '.$error_exists.'</li>';
						} else {
							if (in_array($ext,$array_img_ext)) { // img
								$filename = $filedir.strtoupper(space2underscore($this_is))."_".space2underscore(${$this_is."Desc"});
								$location = upload_image($userfile_tmp,$filename,$ext);
							} else { // doc
								$userfile_name = strtoupper(space2underscore($this_is))."_".space2underscore(${$this_is."Desc"}).'.'.$ext;
								$location = (in_array($this_is,$array_safedir)?$safedir:$filedir)."$userfile_name";
								$movelocation = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.$location;
								move_uploaded_file($userfile_tmp,$movelocation);
							}
							if ($location == "error-too_small") {
		$error_img .= '<p style="text-align:center">'.$erreurString.'<br />'.$max_filesizeString.'</p>';
							} else if ($location == "error-sqrt") {
		$error_img .= '<p style="text-align:center">'.$erreurString.'<br />'.$max_filesizeString.' (max sqrt <= '.$max_sqrt.')</p>';
							} else {
								$new_upload = $location;
								if ($array_fields_type[$this_is.$key] == 'text') { // update done after
									$insertquery = true;
									if (isset(${$key."_passing_desc_for_insert_if_dbtable"})
									&& is_array(${$key."_passing_desc_for_insert_if_dbtable"}))
										${$key."_passing_desc_for_insert_if_dbtable"}[] = $new_upload;
									else
										${$key."_passing_desc_for_insert_if_dbtable"} = array($new_upload);
								} else { ////////////
									if ($key == "img") {
										$insertquery = @mysql_query("INSERT INTO $tblcontphoto
(`contphotoid`,`contphotostatut`,`contphotodate`,`contphotolang`,`contphotorid`,`contphotoutil`,`contphotocontid`,`contphotodesc`,`contphotoimg`)
																	VALUES
('','Y',$dbtime,'$default_lg','','$admin_name','".(!empty(${$this_is."Id"})?${$this_is."Id"}:$now_time)."','".${$this_is."Desc"}."','$new_upload')
																	");
									} else {
										$insertquery = @mysql_query("INSERT INTO $tblcontdoc
(`contdocid`,`contdocstatut`,`contdocdate`,`contdoclang`,`contdocrid`,`contdocutil`,`contdoccontid`,`contdocdesc`,`contdoc`)
																	VALUES
('','Y',$dbtime,'$default_lg','','$admin_name','".(!empty(${$this_is."Id"})?${$this_is."Id"}:$now_time)."','".${$this_is."Desc"}."','$new_upload')
																	");
									}
								}
								if (!$insertquery) {
		$error_img .= '<p style="text-align:center">'.$error_request.': '.($key=="img"?$photoString:$documentString);
								} else {
									if ($key == "img") {
										$insertread = sql_get($tblcontphoto,
																"WHERE contphotocontid='".(!empty(${$this_is."Id"})?${$this_is."Id"}:$now_time)."'
																	AND contphotodesc='".${$this_is."Desc"}."'
																	AND contphotolang='$default_lg' AND contphotorid='' ",
																"contphotoid,contphotoutil,contphotocontid,contphotoimg,contphotodesc");
										$values = "";
										foreach($array_lang as $keylg) {
											if ($keylg == $default_lg) {
												sql_updateone($tblcontphoto,"SET contphotorid=contphotoid ",
																			"WHERE contphotoid='".$insertread[0]."'
																			AND contphotolang='$default_lg' ","");
											} else {
												$values .= ($values==''?'':',')."(NULL,'Y',$dbtime,'$keylg','".$insertread[0]
														."','$admin_name','".(!empty(${$this_is."Id"})?${$this_is."Id"}:$now_time)
														."','".${$this_is."Desc"}."','$new_upload')";
											}
										}
										if ($values!='') {
											$insertquery = @mysql_query("INSERT INTO $tblcontphoto
(`contphotoid`,`contphotostatut`,`contphotodate`,`contphotolang`,`contphotorid`,`contphotoutil`,`contphotocontid`,`contphotodesc`,`contphotoimg`)
																		VALUES
																		$values
																		");
										}
									} else {
										$insertread = sql_get($tblcontdoc,
																"WHERE contdoccontid='".(!empty(${$this_is."Id"})?${$this_is."Id"}:$now_time)."'
																	AND contdocdesc='".${$this_is."Desc"}."'
																	AND contdoclang='$default_lg' AND contdocrid='' ",
																"contdocid,contdocutil,contdoccontid,contdoc,contdocdesc");
										$values = "";
										foreach($array_lang as $keylg) {
											if ($keylg == $default_lg) {
												sql_updateone($tblcontdoc,"SET contdocrid=contdocid ",
																		"WHERE contdocid='".$insertread[0]."' AND contdoclang='$default_lg' ","");
											} else {
												$values .= ($values==''?'':',')."(NULL,'Y',$dbtime,'$keylg','".$insertread[0]
														."','$admin_name','".(!empty(${$this_is."Id"})?${$this_is."Id"}:$now_time)
														."','".${$this_is."Desc"}."','$new_upload')";
											}
										}
										if ($values!='') {
											$insertquery = @mysql_query("INSERT INTO $tblcontdoc
(`contdocid`,`contdocstatut`,`contdocdate`,`contdoclang`,`contdocrid`,`contdocutil`,`contdoccontid`,`contdocdesc`,`contdoc`)
																		VALUES
																		$values
																		");
											if (!$insertquery)
												mvtrace(__FILE__, __LINE__, "insert query not caught !!");
										}
									}
									${$this_is.ucfirst($key)} = $new_upload;
									$notice_img .= '<div><p style="text-align:center"><b>'.($key=="img"?$photoString:$documentString).' '
												.$enregistreString.'</b></p>';
									if ($key == "img") {
										$notice_img .= '<div class="saved_img"><img '.show_img_attr($getcwd.$up.$new_upload)
													.' src="'.$mainurl.$new_upload.'?'.time().'" align="right" vspace="5" hspace="5" alt="'
													.${$this_is."Desc"}.'" border="0" /></div>';
									}
									$this_newupload = substr($new_upload,0,-(strlen($ext)+1)).'.'.$ext;
									$notice_img .= '<h1>'.$rapportString.'</h1><a href="'.$mainurl.(stristr($this_newupload,$safedir)?
												$urlsafe.'?file='.base64_encode($this_newupload):$this_newupload).'" target="_blank">'
												.$this_newupload.'</a>';
									if (in_array($ext,$array_img_ext))
									$notice_img .= '<br /><a href="'.$mainurl.substr($new_upload,0,-(strlen($ext)+1)).'_big.'.$ext
												.'" target="_blank">'.substr($new_upload,0,-(strlen($ext)+1)).'_big.'.$ext
												.'</a><br /><a href="'.$mainurl.substr($new_upload,0,-(strlen($ext)+1)).'_ori.'.$ext
												.'" target="_blank">'.substr($new_upload,0,-(strlen($ext)+1)).'_ori.'.$ext.'</a><br /> <br />';
									$notice_img .= '</div>';
								}
							}
						}
					}
				}
				if ($lg == $loop_lg && $error_img !== '') {
					$errors[$this_is.$key] = $error_invmiss;
					$errors_invmiss[] = $this_is.$key;
					$error_report .= $error_img;
				}
				if (!isset(${$this_is.ucfirst($key)}))
				${$this_is.ucfirst($key)} = '';
			} else if (in_array("$this_is$key",$datetime_array)) {
				if (isset(${$this_is.ucfirst($key)})
				&& preg_match("/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}[ ]{0,1}[0-9]{0,2}[:]{0,1}[0-9]{0,2}[:]{0,1}[0-9]{0,2}\$/",
					${$this_is.ucfirst($key)}))
				${$this_is.ucfirst($key)} = db_date(${$this_is.ucfirst($key)});
				else {
					if (in_array($key,$array_mandatory_fields)) {
						if ($lg == $loop_lg) {
							$errors[$this_is.$key] = $error_invmiss;
							$errors_invmiss[] = $this_is.$key;
							$error_report .= '<li>'.${$key."String"}.': '.$error_invmiss.'</li>';
						}
					}
				}
			} else if (in_array("$this_is$key",$mediumtext_array)) {
				${$this_is.ucfirst($key)} = ${$this_is.ucfirst($key)};
			} else if (in_array("$this_is$key",$longtext_array)) {
				${$this_is.ucfirst($key)} = ${$this_is.ucfirst($key)};
			} else if (in_array("$this_is$key",$enumYN_array)) {
				if (isset(${$this_is.ucfirst($key)}) && !preg_match("/^[NY]\$/",${$this_is.ucfirst($key)}))
				${$this_is.ucfirst($key)} = 'N';
			} else if (in_array("$this_is$key",$enumtype_array)) {
				${$this_is.ucfirst($key)} = strip_tags(html_encode(${$this_is.ucfirst($key)}));
				$valid_type = true;
				if (stristr($_SERVER['PHP_SELF'],$urladmin) || (!stristr($_SERVER['PHP_SELF'],$urladmin) && ($user_can_add_types===true))) {
					$newtype = strip_tags(html_encode(${"new_".$this_is.ucfirst($key)}));
					if ($newtype !== "") {
						if ((($newtype[0] == '-') || ($newtype[0] == '>'))
						&& in_array(${$this_is.ucfirst($key)},sql_array($tblenum,"WHERE enumwhat='$this_is$key' ","enumtitre"))) {
							if ($newtype[0] == '-') {
								if (sql_nrows(${"tbl".$this_is},"WHERE ".$this_is."type='".${$this_is.ucfirst($key)}."' ") == 0) {
									$old_type_str = sql_stringit($this_is.'type',${$this_is.ucfirst($key)});
									sql_del($tblenum,"WHERE enumwhat='$this_is$key' AND enumtitre='".${$this_is.ucfirst($key)}."' ");
									sql_del($tblstring,"WHERE stringtype='$this_is$key' AND stringtitle='".${$this_is.ucfirst($key)}."' ");
									$notice .= $typeString.' <i>'.$old_type_str.'</i> '.$class_conjugaison->plural($effaceString,'','1').'!<br />';
								}
								${$this_is.ucfirst($key)} = $editthis[$i];
							}
							if ($newtype[0] == '>') {
								$setq = "SET stringentry='".substr($newtype,1)."' ";
								$whereq = "WHERE stringlang='$loop_lg'
										AND stringtype='$this_is$key' AND stringtitle='".${$this_is.ucfirst($key)}."' ";
								if (sql_nrows($tblstring,$whereq) > 0)
								$notice .= $typeString.' <s>'.sql_stringit($this_is.'type',${$this_is.ucfirst($key)}).'</s> '
										.$class_conjugaison->plural($modifieString,'','1').' > <i>'
										.sql_updateone($tblstring,$setq,$whereq,"stringentry").'</i>!<br />';
							}
						} else {
							if (sql_nrows($tblstring,"WHERE stringtype='$this_is$key' AND stringentry='$newtype' ") == 0) {
								$uniqueidentifier = time();
								$insertq = "INSERT INTO $tblenum
												VALUES
									('','Y','$this_is$key','$uniqueidentifier','".(sql_nrows($tblenum,"WHERE enumwhat='$this_is$key' ")+1)."') ";
								$insertquery = @mysql_query($insertq);
								if (!$insertquery) {
									$error .= $error_request.' '.$typeString.'<br />'.mvtrace(__FILE__, __LINE__,$insertq);
									$valid_type = false;
								} else {
									$newlycreatedtitre = sql_getone($tblenum,"WHERE enumwhat='$this_is$key'
																			AND enumtype='$uniqueidentifier' ","enumtitre");
									$valid_type = true;
									$array_lang = sql_array($tblenum,"WHERE enumstatut='Y' AND enumwhat='lang' ","enumtype");
									foreach($array_lang as $key_lg) {
										$insertq = "INSERT INTO $tblstring
														VALUES
											('','','$key_lg','$this_is$key','$newlycreatedtitre','$newtype') ";
										$insertquery = @mysql_query($insertq);
										if (!$insertquery) {
											$error .= $error_request.' '.$typeString.' ('.$key_lg.')<br />'.mvtrace(__FILE__, __LINE__,$insertq);
											$valid_type = false;
										}
									}
									${$this_is.ucfirst($key)} = $newlycreatedtitre;
								}
								if ($valid_type === true)
								$notice .= $typeString.' <i>'.$newtype.'</i> '.$class_conjugaison->plural($enregistreString,'','1').'!<br />';
							}
						}
					} else {
						if ((${$this_is.ucfirst($key)} == "") && in_array($key,$array_mandatory_fields)) {
							if ($lg == $loop_lg) {
								$errors[$this_is.$key] = $error_invmiss;
								$errors_invmiss[] = $this_is.$key;
								$error_report .= '<li>'.${$key."String"}.': '.$error_invmiss.'</li>';
							}
						}
					}
				}
			} else { // all other cases including extra_routines overwrites
				if (!isset(${$this_is.ucfirst($key)}))
					${$this_is.ucfirst($key)} = '';
				else
					${$this_is.ucfirst($key)} = (is_string(${$this_is.ucfirst($key)})
						?strip_tags(html_encode(${$this_is.ucfirst($key)}))
						:${$this_is.ucfirst($key)});
				// include here extra_routines
				if (@file_exists($getcwd.$up.$safedir.'_extra_routines.php'))
					require $getcwd.$up.$safedir.'_extra_routines.php';
				if (!isset($array_routines) || (isset($array_routines) && !in_array($key,$array_routines)))
				if (($key == "nom") && ($this_is == 'membre') && isset(${$this_is."Prenom"}) && isset(${$this_is."Nom"})) {
					if ($editthis[array_search($this_is."prenom",$array_fields)]." ".$editthis[array_search($this_is."nom",$array_fields)]
						!= ${$this_is."Prenom"}." ".${$this_is."Nom"})
					if (sql_nrows($tblhtaccess,"WHERE htaccessurl='".space2underscore(${$this_is."Prenom"}." ".${$this_is."Nom"})."' ")>0) {
						if ($countread > 0) {
							if ($lg == $loop_lg) {
								$errors[$this_is.$key] = $error_exists;
								$errors_exists[] = $this_is.$key;
								$error_report .= '<li>'.${$key."String"}.': '.${$this_is.ucfirst($key)}.' > '.$error_exists.'</li>';
							}
						}
					}
				}
			}
	/////////// routine for dynamic processing /
		}
	}
	if (isset($that_is)) {
///////////////////// REMISE A DEFAUT DES VARIABLES ////////////////////
		$this_is = $old_this;
		$dbtable = $old_dbtable;
///////////////////// REMISE A DEFAUT DES VARIABLES ////////////////////
	}

	if ($error_report !== '') {
		$error .= ($lg==$loop_lg?'<b>'.$erreurString.'!</b> : '.$listecorrectionString.'<br />'.$error_report:'');
	} else {
		if ($send == $sauverString) {
			$update_dbtable = $dbtable;
			$sql_Instruction = '';
			$docimg_where = $this_is.($this_id_rid===true?'r':'')."id='".${$this_is."Id"}."' ";
			if (isset($that_is)) {
				$docimg_where .= " AND ".$that_is.($that_id_rid===true?'r':'')."id='".${$this_is."Id"}."' ";
			}
			$where = $docimg_where.(in_array($this_is."lang",$array_fields)?" AND ".$this_is."lang='$loop_lg' ":'');
			$setdate = $this_is."date=$dbtime";
			if (in_array('date',$array_mandatory_fields) && isset(${$this_is."Date"}))
			$setdate = $this_is."date='".${$this_is."Date"}."'";
			if (isset($that_is)) {
				$where .= (in_array($that_is."lang",$that_array_fields)?"' AND ".$that_is."lang='$loop_lg' ":'');
				if (in_array('date',$array_mandatory_fields) && isset(${$that_is."Date"}))
				$setdate .= $that_is.'date='.${$that_is."Date"};
				else
				$setdate .= ",".$that_is."date=$dbtime";
				$update_dbtable .= ",".$that_dbtable;
			}
			for($i=0;$i<count($array_fields);$i++) {
				if (substr($array_fields[$i],0,strlen($dbtable)-1) != $this_is) {
					$this_is = $that_is;
					$dbtable = $that_dbtable;
				}
				$key = substr($array_fields[$i],strlen($dbtable)-1);
				if (!in_array($key,array('id','date','lang','rid'))) {
					if (in_array($this_is.ucfirst($key),$array_multilingual)) {
						if ($editthis[$i] != ${$this_is.ucfirst($key)}) {
							if (count($array_lang) == 1) {
								$sql_Instruction .= $this_is.$key."='".(in_array($key,array("title","titre"))?
																$this_titre:${$this_is.ucfirst($key)})."', ";
							} else {
								if ($lg == $loop_lg)
								$notice .= ${$key."String"}." > ".(sql_updateone($update_dbtable,"SET ".$this_is.$key."='"
										.(in_array($key,array("title","titre"))?$this_titre:${$this_is.ucfirst($key)})."' ",
										"WHERE ".$this_is."rid='".${$this_is."Id"}."' "
										.($lg==$default_lg?" AND ".$this_is.$key."='".$editthis[$i]."' "
										:" AND ".$this_is."lang='$loop_lg' "),$this_is.$key)==${$this_is.ucfirst($key)}?'':$NONString)
										." ".$modifieString."<br />";
							}
						}
					} else if (($key == 'doc') || ($key == 'img')) {
						if ($lg == $loop_lg) {
							if (isset(${$key."_passing_desc_for_insert_if_dbtable"}) && is_array(${$key."_passing_desc_for_insert_if_dbtable"}))
								${$key."_passing_desc_for_insert_if_dbtable"} = implode("|",${$key."_passing_desc_for_insert_if_dbtable"});
							if (isset(${$key."_passing_desc_for_insert_if_dbtable"}) && (${$key."_passing_desc_for_insert_if_dbtable"} != '')
							&& (${$key."_passing_desc_for_insert_if_dbtable"} != $editthis[$i])) {
								$notice .= sql_updateone($update_dbtable,"SET ".$this_is.$key."='".${$key."_passing_desc_for_insert_if_dbtable"}."'",
										" WHERE $docimg_where ",$this_is.$key)." = new photo<br />";
								${$key."_default_lg_passing_desc_for_insert_if_dbtable"} = ${$key."_passing_desc_for_insert_if_dbtable"};
								${$key."_passing_desc_for_insert_if_dbtable"} = NULL;
							}
						}
					} else if ($key == 'pass') {
						if (${$this_is.ucfirst($key)} !== '')
						$sql_Instruction .= $this_is.$key."='".${$this_is.ucfirst($key)}."', ";
					} else {
						if (@file_exists($getcwd.$up.$safedir.'_extra_routines.php'))
						require $getcwd.$up.$safedir.'_extra_routines.php';
						if (!isset($array_routines) || (isset($array_routines) && !in_array($key,$array_routines)))
						if (isset(${$this_is.ucfirst($key)}) && ($editthis[$i] != ${$this_is.ucfirst($key)}))
						$sql_Instruction .= $this_is.$key."='".${$this_is.ucfirst($key)}."', ";
					}
				}
			}
			if	(($sql_Instruction == '') && !(in_array('date',$array_mandatory_fields) && isset(${$this_is."Date"})))
			$notice .= ($loop_lg==$lg?'<b>'.$enregistrementString.' '.$nonString.' '.$class_conjugaison->plural($modifieString,'F',1)
					.(!in_array('1',$admin_priv)?(stristr($_SERVER['PHP_SELF'],$urladmin)?
						' '.$pourString.' '.${$this_is."String"}.', '
					.$numidString.' : '.$this_id.', '.$statutString.' : <u>'.${$this_is."Statut"}.'</u></b><!-- <br /> <br /><a href="'
					.$local_url.'">'.$verslisteString.' '.${$this_is."String"}.'</a> -->':'</b>').'<br />':''):'');
			else {
				$updatequery = sql_update($update_dbtable,"SET $sql_Instruction $setdate ",
														" WHERE ".(count($array_lang)>1?$docimg_where:$where)." ",$list_array_fields);
				if ($updatequery[0] == '.') {
					if ($loop_lg == $lg)
						$error .= $error_request.mvtrace(__FILE__, __LINE__, 'bad [u] '.$loop_lg);
				}
				else {
					if (!isset($sent_email)) // so it send only one email
					if (in_array($this_is,$array_email_conf) && (isset($_POST["email"]) && ($_POST["email"]=='on'))) {
////////////////////////////
						if ($this_is=='membre') {
							$this_isUtil = $userUtil;
							if ($userPass!='') {
								$this_isPass = $_POST['userPass'];
							} else {
								$this_isPass = $userEmail;
								sql_update($update_dbtable,"SET userpass='".md5($this_isPass)."' "," WHERE $where ",$list_array_fields);
							}
							if (isset(${$this_is."Gendre"}))
							$this_isTitle = ${$this_is."Prenom"}." ".${$this_is."Nom"};
						} else {
							$this_isUtil = ${$this_is."Util"};
							if ($userPass!='') {
								$this_isPass = $_POST[$this_is."Pass"];
							} else {
								$this_isPass = ${$this_is."Email"};
								sql_update($update_dbtable,"SET userpass='".md5($this_isPass)."' "," WHERE $where ",$list_array_fields);
							}
						}
						$mail_email = is_valid_email($_POST[$old_this.'Email']);// no encoding here
						$mail_subject = html_entity_decode("$codename : $accesString $membreString $confirmeString");
						$this_isPriv_astext = $userPriv_astext;
						$footer = "$messageString $envoyeString $parString $coinfo<hr /><br />$slogan";
						if ($loop_lg == 'fr')
						$mail_message = "<html><body>$cologo<br />".($this_is=='membre'?$userUtil:${$this_is."Util"})
									."<br /> <br />Votre compte a &eacute;t&eacute; cr&eacute;&eacute; sur le site de $coname"
									."<br /> <br />Visitez $mainurl et ins&eacute;rez:<br /> - le nom d`utilisateur : "
									.($this_is=='membre'?$userUtil:${$this_is."Util"})."<br /> - avec le mot de passe : "
									.($this_is=='membre'?($userPass!=''?$_POST['userPass']:$userEmail):
									(${$this_is."Pass"}!=''?$_POST[$this_is."Pass"]:${$this_is."Email"}))
									."<br /> <br />Vous b&eacute;n&eacute;ficierez des privil&egrave;ges suivants : $userPriv_astext"
									."<br /> <br />Pour acc&eacute;der au contenu privil&eacute;gi&eacute; : veuillez <b>changer le mot "
									."de passe</b>.<br />Vous aurez aussi la possibilit&eacute; d`indiquer d`autres informations."
									."<br />(Si vous souhaitez changer le nom d`utilisateur, merci de nous contacter.)"
									."<br />Ensuite, sauvez et identifiez vous de nouveau.<br /> <br />$footer</body></html>";
						else
						$mail_message = "<html><body><div style=\"width:650px;\"><div style=\"width:650px;background:transparent url("
									.$mainurl."images/toplogor_bg.jpg) repeat-y;\">$cologo</div><br />"
									.($this_is=='membre'?$userUtil:${$this_is."Util"})
									."<br /> <br />Your account has been created on the $coname website"
									."<br /> <br />Visit $mainurl and login with the following credentials:"
									."<br /> - username : ".($this_is=='membre'?$userUtil:${$this_is."Util"})
									."<br /> - password : ".($this_is=='membre'?($userPass!=''?$_POST['userPass']:$userEmail)
									:(${$this_is."Pass"}!=''?$_POST[$this_is."Pass"]:${$this_is."Email"}))
									."<br /> <br />You will be able to access pages with the following privileges: $userPriv_astext"
									."<br /> <br />To access the privileged content, please follow the instructions upon your first login:"
									." mainly <b>change the password</b>.<br />You may also indicate further information by visiting the "
									."\"my profile\" page.<br />Upon each completion update of your password, you will have to login again."
									."<br /> <br />$footer</div></body></html>";
						contains_bad_str($mail_message);
################################## IMPORT TEMPLATE
						if (@file_exists($getcwd.$up.$safedir.'_tpl_mail_credentials.php'))
						require $getcwd.$up.$safedir.'_tpl_mail_credentials.php';
						else
						require $getcwd.$up.$urladmin.'defaults/_tpl_mail_credentials.php';
						if (isset($array_mail_credentials)) {
							$mail_message = '';
							foreach($array_mail_credentials as $tpl_l)
							$mail_message .= ($tpl_l[0]=="$"?${substr($tpl_l,1)}:$tpl_l);
						} else {
							$mail_message = $_tpl_mail_credentials;
						}
						$mail_message = wordwrap($mail_message,70,$CRLF,true);
						contains_bad_str($mail_email);
						contains_bad_str($mail_subject);
						contains_newlines($mail_email);
						contains_newlines($mail_subject);
						$codename = html_entity_decode($codename);
						if (stristr($_SERVER["HTTP_HOST"], "localhost") || stristr($_SERVER["HTTP_HOST"], "192.168.3.")) {
							$sent_email = TRUE;
							$notice .= "<hr />".$mail_message."<hr /><textarea cols='100' rows='30'>$mail_message</textarea>";
						} else
						$sent_email = mail($mail_email, $mail_subject, $mail_message, "From: $codename <$coinfo>".$mail_headers);
						$notice .= '<b>'.$enregistrementString.' '.$class_conjugaison->plural($modifieString,'F',1)
							.(stristr($_SERVER['PHP_SELF'],$urladmin)?' '.$pourString.' '.$updatequery[3].' , '.$numidString
							.' : '.$updatequery[0].', '.$statutString.' : <u>'.$updatequery[1].'</u></b><br />'
							.(isset($sent_email)?($sent_email?'<span style="color:green;">'.$messageString.' '.$envoyeString
							.'</span>':'<span style="color:red">'.$messageString.' '.$nonString.' '.$envoyeString.'</span>'):'')
							.'<!-- <br /> <br /><a href="'.$local_url.'">'.$verslisteString.' '.${$this_is."String"}.'</a> -->':
								'</b>').'<br />';
					}
				}
			}
			if (isset($tblhtaccess) && in_array($this_is,$array_modules)) {
				$row_date = (isset($array_rss_dateindex[$this_is])?"'${$this_is.ucfirst($array_rss_dateindex[$this_is])}'":$dbtime);
				$row_statut = ${$this_is."Statut"};
				$keylg = ($lg==$default_lg?$loop_lg:$lg);
				$row_lang = $keylg;
				$row_type = $this_is;
				$row_entry = sql_getone($dbtable,"WHERE ".$this_is.($this_id_rid===true?'r':'')."id='$this_id'
													AND ".$this_is."lang='$keylg' ",$this_is."id");
				$row_item = $this_id;
				if (isset(${$this_is."Gendre"})) $row_title = ${$this_is."Prenom"}." ".${$this_is."Nom"};
				else $row_title = (isset($this_titre)?$this_titre:$row_item);
				$row_url = space2underscore($row_title);
				$row_metadesc = (isset(${$this_is."Metadesc"})&&(${$this_is."Metadesc"}!=$default_desc_keyw[0])?${$this_is."Metadesc"}:'');
				$row_metakeyw = (isset(${$this_is."Metakeyw"})&&(${$this_is."Metakeyw"}!=$default_desc_keyw[1])?${$this_is."Metakeyw"}:'');
				$getthis = sql_get($tblhtaccess,"WHERE htaccesslang='$keylg' AND htaccessitem='$row_item' AND htaccesstype='$row_type'
												ORDER BY htaccessdate DESC ",
									"htaccessid,htaccessstatut,htaccesstitle,htaccessentry,htaccessurl,htaccessmetadesc,htaccessmetakeyw");
				if ($getthis[0] == '.') {
					$insertq = "INSERT INTO $tblhtaccess ".sql_fields($tblhtaccess,'list')."
									VALUES
						('',$row_date,'$row_statut','$row_lang','$row_item','$row_title','$row_entry','$row_url','$row_type','$row_metadesc','$row_metakeyw') ";
					$insertquery = @mysql_query($insertq);
					if (!$insertquery)
						$error .= $error_request." [i] ".$loop_lg." htaccess > <b>$row_type</b> : <i>$row_title</i><br />".mvtrace(__FILE__, __LINE__,$insertq);
					else
						$notice .= $effectueString." [i] ".$loop_lg." htaccess > <b>$row_type</b> : <i>$row_title</i><br />";
				} else {
					$setq = "";
					if ($row_statut != $getthis[1]) $setq .= "htaccessstatut='$row_statut', ";
					if ($row_title != $getthis[2]) $setq .= "htaccesstitle='$row_title', ";
					if ($row_url != $getthis[4]) $setq .= "htaccessurl='$row_url', ";
					if ($loop_lg == $default_lg) {
						$default_lg_metadesc = $getthis[5];
						$default_lg_metakeyw = $getthis[6];
					}
					if ((isset($default_lg_metadesc)&&($default_lg_metadesc == $getthis[5]))
					|| (!isset($default_lg_metadesc) && (($row_metadesc != $getthis[5]) || ($row_metadesc != $default_desc_keyw[0]))))
					$setq .= "htaccessmetadesc='$row_metadesc', ";
					if ((isset($default_lg_metakeyw)&&($default_lg_metakeyw == $getthis[6]))
					|| (!isset($default_lg_metadesc) && (($row_metakeyw != $getthis[6]) || ($row_metakeyw != $default_desc_keyw[1]))))
					$setq .= "htaccessmetakeyw='$row_metakeyw', ";
					if ($setq != '')
					$notice .= $effectueString." [u] ".$loop_lg." htaccess > <b>$row_type</b> : <i>"
	 						.sql_updateone($tblhtaccess,"SET htaccessdate=$row_date, $setq htaccessid='".$getthis[0]."' ",
	 													"WHERE htaccessid='".$getthis[0]."' ","htaccesstitle")."</i><br />";
				}
			}
		} else {
//////////////////////////// END SAVE
			$values = "";
			$this_is = $old_this;
			$dbtable = $old_dbtable;
			for($i=0;$i<count($array_fields);$i++) {
				if (substr($array_fields[$i],0,strlen($dbtable)-1) != $this_is) {
					$insertq = "INSERT INTO $dbtable ".sql_fields($dbtable,'list')." VALUES ( $values ) ";
					$insertquery = @mysql_query($insertq);
					if (!$insertquery) {
						if ($loop_lg == $lg)
							$error .= $error_request.mvtrace(__FILE__, __LINE__, $insertq.' bad [i] '.$loop_lg);
						break;
					} else {
						if (!isset($newly_inserted_id))
						if ($loop_lg == $default_lg) {
							$new_id = mysql_insert_id();
							if (!preg_match("/^[0-9]+\$/",$new_id)) {
								if ($loop_lg == $lg)
									$error .= $error_request.mvtrace(__FILE__, __LINE__, ' bad [i] '.$loop_lg."not an id");
								break;
							} else {
								$update_rid = sql_updateone($dbtable,"SET ".$this_is.($this_id_rid===true?"lang='$default_lg', ".$this_is."r":'')
											."id='$new_id' ","WHERE ".$this_is."id='$new_id' ",$this_is.($this_id_rid===true?'r':'')."id");
								if ($update_rid != $new_id) {
									if ($loop_lg == $lg)
										$error .= $error_request.mvtrace(__FILE__, __LINE__, ' bad [i] '.$loop_lg."update id != new id");
									break;
								} else
								$newly_inserted_id = $new_id;
							}
						} else {
							$newly_inserted_id = mysql_insert_id();
						}
					}
					$this_is = $that_is;
					$dbtable = $that_dbtable;
				}
				$key = substr($array_fields[$i],strlen($dbtable)-1);
				if (($key == 'id') || ($key == 'rid')) {
					if ($key == 'rid')
					$values .= ", '".(isset($newly_inserted_id)?$newly_inserted_id:'')."'";
					else
					$values = "''";
				} else if ($key == 'date') {
					if (in_array($key,$array_mandatory_fields) && isset(${$this_is.ucfirst($key)}))
					$values .= ", '".${$this_is.ucfirst($key)}."'";
					else
					$values .= ", $dbtime";
				} else if ($key == 'statut')
					$values .= ", '".(!preg_match("/^[YN]\$/",${$this_is.ucfirst($key)})?'Y':${$this_is.ucfirst($key)})."'";
				else if ($key == 'lang')
					$values .= ", '$loop_lg'";
				else if (($key == 'doc') || ($key == 'img')) {
					if ($lg == $loop_lg) {
						if (isset(${$key."_passing_desc_for_insert_if_dbtable"}) && is_array(${$key."_passing_desc_for_insert_if_dbtable"}))
						${$key."_passing_desc_for_insert_if_dbtable"} = implode("|",${$key."_passing_desc_for_insert_if_dbtable"});
						if (isset(${$key."_passing_desc_for_insert_if_dbtable"}) && (${$key."_passing_desc_for_insert_if_dbtable"} != '')
						&& (${$key."_passing_desc_for_insert_if_dbtable"} != $editthis[$i])) {
							$notice .= sql_updateone($update_dbtable,"SET ".$this_is.$key."='".${$key."_passing_desc_for_insert_if_dbtable"}
									."' "," WHERE $docimg_where ",$this_is.$key)." = new photo<br />";
							${$key."_default_lg_passing_desc_for_insert_if_dbtable"} = ${$key."_passing_desc_for_insert_if_dbtable"};
							${$key."_passing_desc_for_insert_if_dbtable"} = NULL;
						}
					}
					$values .= ", '".(isset(${$key."_default_lg_passing_desc_for_insert_if_dbtable"})?
									${$key."_default_lg_passing_desc_for_insert_if_dbtable"}:'')."' ";
				} else if (($key == 'title') || ($key == 'titre'))
					$values .= ", '$this_titre'";
				else if ($key == 'pass') {
					if (${$this_is.ucfirst($key)} != '')
					$values .= ", '".${$this_is.ucfirst($key)}."'";
					else
					$values .= ", '".md5(strtolower(${$this_is."Email"}))."'";
				} else
				$values .= ", '".${$this_is.ucfirst($key)}."'";
				if (in_array($key,array('nom','title','util'))) {
					if (isset($that_dbtable) && ($dbtable == $that_dbtable)) {
						if ((($key == 'title') || ($key == 'titre')))
						$where = "WHERE ".$this_is.$key."='$this_titre' ";
						else
						$where = (isset(${$this_is.ucfirst($key)})?"WHERE ".$this_is.$key."='".${$this_is.ucfirst($key)}."' ":'');
					} else {
						if (($key == 'title') || ($key == 'titre'))
						$where = "WHERE ".$this_is.$key."='$this_titre' "
								.(in_array($this_is."lang",$array_fields)?" AND ".$this_is."lang='$loop_lg' ":'');
						else
						$where = "WHERE ".$this_is.$key."='".${$this_is.ucfirst($key)}."' "
								.(in_array($this_is."lang",$array_fields)?" AND ".$this_is."lang='$loop_lg' ":'');
						$old_where = $where;
					}
				}
			}
			$insertq = '';
			if (($error=='') && ($values != ''))
			{
				$insertq = "INSERT INTO $dbtable ".sql_fields($dbtable,'list')." VALUES ( $values ) ";
				$insertquery = @mysql_query($insertq);
			} else $insertquery = false;
			if (!$insertquery) {
				if ($loop_lg == $lg)
					$error .= $error_request.mvtrace(__FILE__, __LINE__, $insertq.' bad [i] '.$loop_lg);
			}
			else {
				$notice .= ' [i] '.$loop_lg.'<br />';
				if (!isset($newly_inserted_id))
				if ($loop_lg == $default_lg) {
					$new_id = mysql_insert_id();
					if (!preg_match("/^[0-9]+\$/",$new_id)) {
						if ($loop_lg == $lg)
							$error .= $error_request.mvtrace(__FILE__, __LINE__, ' bad [i] '.$loop_lg."new_id not valid");
						break;
					} else {
						$update_rid = sql_updateone($dbtable,"SET ".$this_is.($this_id_rid===true?'r':'')."id='$new_id' ",
											"WHERE ".$this_is."id='$new_id' ",$this_is.($this_id_rid===true?'r':'')."id");
						if ($update_rid != $new_id) {
							if ($loop_lg == $lg)
								$error .= $error_request.mvtrace(__FILE__, __LINE__, ' bad [i] '.$loop_lg."bad update_rid");
							break;
						} else
						$newly_inserted_id = $new_id;
					}
				} else {
					$newly_inserted_id = sql_getone($dbtable,$where,$this_is."id");
				}
				if (isset($that_is)) {
					$dbtable = $old_dbtable;
					$this_is = $old_this;
				}
				$readinsert = sql_get($dbtable,$old_where,$list_array_fields);
				if ($readinsert[0]!='.') {
					sql_updateone($tblcontphoto,"SET contphotocontid='$new_id' ","WHERE contphotocontid='$now_time' ","contphotocontid");
					sql_updateone($tblcontdoc,"SET contdoccontid='$new_id' ","WHERE contdoccontid='$now_time' ","contdoccontid");
					if (isset($that_dbtable) && ($that_dbtable == $tblmembre)) {
////////////////////////////
						if (isset($tblhtaccess) && in_array($that_is,$array_modules)) {
							$htaccess_values = '';
							foreach(sql_fields($tblhtaccess,'array') as $htaccess_k) {
								$htaccess_k = substr($htaccess_k,strlen('htaccess'));
								if (in_array($htaccess_k,$basic_array)) {
									if ($htaccess_k == 'id') $htaccess_values .= "''";
									if ($htaccess_k == 'date') $htaccess_values .= ",$dbtime";
									if ($htaccess_k == 'statut') $htaccess_values .= ",'Y'";
									if ($htaccess_k == 'lang') $htaccess_values .= ",'$loop_lg'";
								} else {
									if ($htaccess_k == 'lang') $htaccess_values .= ",'$loop_lg'";
									else if ($htaccess_k == 'type') $htaccess_values .= ",'$that_is'";
									else if ($htaccess_k == 'item') $htaccess_values .= ",'$new_id'";
									else if ($htaccess_k == 'title') {
										if (isset(${$that_is."Gendre"}))
										$htaccesstitle = ${$that_is."Prenom"}." ".${$that_is."Nom"};
										else
										$htaccesstitle = $this_titre;
										$htaccess_values .= ",'$htaccesstitle'";
									} else if ($htaccess_k == 'entry')
									$htaccess_values .= ",'".sql_getone($that_dbtable,"WHERE ".$that_is."gendre='".${$that_is."Gendre"}."'
										AND ".$that_is."prenom='".${$that_is."Prenom"}."' AND ".$that_is."lang='$loop_lg' ",$that_is."id")."'";
									else if ($htaccess_k == 'url')
									$htaccess_values .= ",'".space2underscore($htaccesstitle)."'";
									else if ($htaccess_k == 'metadesc')
									$htaccess_values .= ",'".(isset(${$that_is."Metadesc"})&&(${$that_is."Metadesc"}!=$default_desc_keyw[0])?
																${$that_is."Metadesc"}:'')."'";
									else if ($htaccess_k == 'metakeyw')
									$htaccess_values .= ",'".(isset(${$that_is."Metakeyw"})&&(${$that_is."Metakeyw"}!=$default_desc_keyw[1])?
																${$that_is."Metakeyw"}:'')."'";
									else
									$htaccess_values .= ",'".(isset(${$that_is.ucfirst($htaccess_k)})?${$that_is.ucfirst($htaccess_k)}:'')."'";
								}
							}
							$insertq = " INSERT INTO $tblhtaccess ".sql_fields($tblhtaccess,'list')." VALUES ($htaccess_values) ";
							$insertquery = @mysql_query($insertq); // no comma after lg!!!
							if (!$insertquery) {
								if ($loop_lg == $lg)
									$error .= $error_request." [i] ".$loop_lg." > <b>$that_is</b> : <i>$htaccesstitle</i><br />".mvtrace(__FILE__, __LINE__,$insertq);
							}
							else
								$notice .= $effectueString." [i] ".$loop_lg." > <b>$that_is</b> : <i>$htaccesstitle</i><br />";
						}
						if (!isset($sent_email)) // so it sends only one email
						if (in_array($that_is,$array_email_conf)
							&& (!isset($_POST["email"]) || (isset($_POST["email"]) && ($_POST["email"]=='on')))
						) {
////////////////////////////
							$this_isUtil = ${$old_this."Util"};
							$this_isPass = ($_POST[$old_this."Pass"]==""?
											html_encode(strtolower(trim($_POST[$old_this."Email"]))):$_POST[$old_this."Pass"]);
							$this_isPriv_astext = $userPriv_astext;
							$mail_email = is_valid_email($_POST[$old_this.'Email']); // no encoding here
							$mail_subject = html_entity_decode("$codename : $accesString $membreString $confirmeString");
							$footer = "$messageString $envoyeString $parString $coinfo<hr /><br />$codename";
							if ($loop_lg == 'fr')
							$mail_message = "<html><body>$cologo<br />".($this_is=='membre'?$userUtil:${$this_is."Util"})
								."<br /> <br />Votre compte a &eacute;t&eacute; cr&eacute;&eacute; sur le site de $coname"
								."<br /> <br />Visitez $mainurl et ins&eacute;rez:<br /> - le nom d`utilisateur : "
								.($this_is=='membre'?$userUtil:${$this_is."Util"})."<br /> - avec le mot de passe : "
								.($this_is=='membre'?($userPass!=''?$_POST['userPass']:$userEmail):
								(${$this_is."Pass"}!=''?$_POST[$this_is."Pass"]:${$this_is."Email"}))
								."<br /> <br />Vous b&eacute;n&eacute;ficierez des privil&egrave;ges suivants : $userPriv_astext"
								."<br /> <br />Pour acc&eacute;der au contenu privil&eacute;gi&eacute; : veuillez <b>changer le mot de passe</b>."
								."<br />Vous aurez aussi la possibilit&eacute; d`indiquer d`autres informations."
								."<br />(Si vous souhaitez changer le nom d`utilisateur, merci de nous contacter.)"
								."<br />Ensuite, sauvez et identifiez vous de nouveau.<br /> <br />$footer</body></html>";
							else
							$mail_message = "<html><body><div style=\"width:650px;\"><div style=\"width:650px;background:transparent url("
								.$mainurl."images/toplogor_bg.jpg) repeat-y \">$cologo</div><br />".($this_is=='membre'?$userUtil:
								${$this_is."Util"})."<br /> <br />Your account has been created on the $coname website"
								."<br /> <br />Visit $mainurl and login with the following credentials:<br /> - username : "
								.($this_is=='membre'?$userUtil:${$this_is."Util"})."<br /> - password : ".($this_is=='membre'?
								($userPass!=''?$_POST['userPass']:$userEmail):(${$this_is."Pass"}!=''?$_POST[$this_is."Pass"]:
								${$this_is."Email"}))."<br /> <br />You will be able to access pages with the following privileges: "
								."$userPriv_astext<br /> <br />To access the privileged content, please follow the instructions upon your "
								."first login: mainly <b>change the password</b>.<br />You may also indicate further information by visiting "
								."the \"my profile\" page.<br />Upon each completion update of your password, you will have to login again."
								."<br /> <br />$footer</div></body></html>";
							contains_bad_str($mail_message);
################################## IMPORT TEMPLATE
							if (@file_exists($getcwd.$up.$safedir.'_tpl_mail_credentials.php'))
							require $getcwd.$up.$safedir.'_tpl_mail_credentials.php';
							else
							require $getcwd.$up.$urladmin.'defaults/_tpl_mail_credentials.php';
							if (isset($array_mail_credentials)) {
								$mail_message = '';
								foreach($array_mail_credentials as $tpl_l)
								$mail_message .= ($tpl_l[0]=="$"?${substr($tpl_l,1)}:$tpl_l);
							} else {
								$mail_message = $_tpl_mail_credentials;
							}
							$mail_message = wordwrap($mail_message,70,$CRLF,true);
							contains_bad_str($mail_email);
							contains_bad_str($mail_subject);
							contains_newlines($mail_email);
							contains_newlines($mail_subject);
							$codename = html_entity_decode($codename);
							if (stristr($_SERVER["HTTP_HOST"], "localhost") || stristr($_SERVER["HTTP_HOST"], "192.168.3.")) {
								$sent_email = TRUE;
								$notice .= "<hr />".$mail_message."<hr />$mail_email, $mail_subject, <textarea cols='100' rows='30'>"
										."$mail_message</textarea>From: $codename <$coinfo>";
							} else
							$sent_email = mail($mail_email, $mail_subject, $mail_message, "From: $codename <$coinfo>".$mail_headers);
							if ($loop_lg == $lg)
							$notice = '<b>'.$enregistrementString.' '.$class_conjugaison->plural($effectueString,'M',1)
								.(stristr($_SERVER['PHP_SELF'],$urladmin)?' '.$pourString.' '.$readinsert[3].' , ID : '
								.$readinsert[0].', '.$statutString.' : '.$readinsert[1].' [i] '.$loop_lg.'</b><br />'
								.(isset($sent_email)?($sent_email?'<span style="color:green;">'.$messageString.' '.$envoyeString
								.'</span>':'<span style="color:red">'.$messageString.' '.$nonString.' '.$envoyeString.'</span>'):'')
								.'<!-- <br /> <br /><a href="'.$local_url.'">'.$verslisteString.' '.$membreString.'</a> -->':'</b>')
								.'<br />'
							.$notice;
						}
					} else {
////////////////////////////
						if (isset($tblhtaccess) && in_array($this_is,$array_modules) && !in_array($this_is,$array_modules_as_form)) {
							$htaccess_values = '';
							foreach(sql_fields($tblhtaccess,'array') as $htaccess_k) {
								$htaccess_k = substr($htaccess_k,strlen('htaccess'));
								if (in_array($htaccess_k,$basic_array)) {
									if ($htaccess_k == 'id') $htaccess_values .= "''";
									if ($htaccess_k == 'date')
									$htaccess_values .= (isset($array_rss_dateindex)&&in_array($this_is,$array_rss_dateindex)?
														",'${$this_is.ucfirst($array_rss_dateindex[$this_is])}'":",$dbtime");
									if ($htaccess_k == 'statut')
									$htaccess_values .= ",'".(!preg_match("/^[YN]\$/",${$this_is.ucfirst($htaccess_k)})?
																'Y':${$this_is.ucfirst($htaccess_k)})."'";
	    				        	if ($htaccess_k == 'lang') $htaccess_values .= ",'$loop_lg'";
								} else {
									if ($htaccess_k == 'lang') $htaccess_values .= ",'$loop_lg'";
									else if ($htaccess_k == 'type') $htaccess_values .= ",'$this_is'";
									else if ($htaccess_k == 'item') $htaccess_values .= ",'$new_id'";
									else if ($htaccess_k == 'title') {
										if (isset(${$this_is."Gendre"}))
										$htaccesstitle = ${$this_is."Prenom"}." ".${$this_is."Nom"};
										else
										$htaccesstitle = $this_titre;
										$htaccess_values .= ",'$this_titre'";
									} else if ($htaccess_k == 'entry') $htaccess_values .= ",'".$readinsert[0]."'";
									else if ($htaccess_k == 'url') $htaccess_values .= ",'".space2underscore($htaccesstitle)."'";
									else if ($htaccess_k == 'metadesc')
									$htaccess_values .= ",'".(isset(${$this_is."Metadesc"})&&(${$this_is."Metadesc"}!=$default_desc_keyw[0])?
																${$this_is."Metadesc"}:'')."'";
									else if ($htaccess_k == 'metakeyw')
									$htaccess_values .= ",'".(isset(${$this_is."Metakeyw"})&&(${$this_is."Metakeyw"}!=$default_desc_keyw[1])?
																${$this_is."Metakeyw"}:'')."'";
									else $htaccess_values .= ",'".(isset(${$this_is.ucfirst($htaccess_k)})?
																${$this_is.ucfirst($htaccess_k)}:'')."'";
								}
							}
							$insertq = " INSERT INTO $tblhtaccess ".sql_fields($tblhtaccess,'list')." VALUES ($htaccess_values) ";
							$insertquery = @mysql_query($insertq); // no comma after lg!!!
							if (!$insertquery) {
								if ($loop_lg == $lg)
									$error .= $error_request.(stristr($_SERVER['PHP_SELF'],$urladmin)
										? " [i] ".$loop_lg." htaccess > <b>$this_is</b> : <i>$htaccesstitle</i><br />"
										: '').mvtrace(__FILE__, __LINE__,$insertq);
							} else {
								$notice .= (stristr($_SERVER['PHP_SELF'],$urladmin)
									? "$effectueString [i] ".$loop_lg." htaccess > <b>$this_is</b> : <i>$htaccesstitle</i><br />"
									: '');
								if ($loop_lg == $lg)
								if ((!stristr($_SERVER['PHP_SELF'],$urladmin) && (isset(${"moderate_".$this_is})
								&& (${"moderate_".$this_is} === true))) || (isset($moderate) && ($moderate === true))) {
									$notice = $moderateurverifString."<br />"
										.$notice;
									$communicationsMessage = "
										$enregistrementString&nbsp;$pourString&nbsp;${$this_is."String"}&nbsp;$effectueString!
										$cliquericiString&nbsp;$pourString&nbsp;$ouvrirString:
										$mainurl{$urladmin}?lg={$lg}&amp;x=z&amp;y={$this_is}&amp;send=edit&{$this_is}Id=$new_id
										$entrerString&nbsp;>&nbsp;$adminString&nbsp;!";
									$subject = $codename.' :: '.$nouveauString.' '.${$this_is."String"};
									$this_email = $coinfo;
									$subject = html_entity_decode($subject);
									$nonhtml_content_generated = $communicationsMessage;
									$html_content_generated = nl2br($communicationsMessage);
									$footer = "$communicationsString ".$class_conjugaison->plural($envoyeString,'F',1).", $parString "
											."<a href=\"$mainurl\">$codename</a><br /><sup>$copyrightnoticeString</sup><br /> <br />";
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
										$notice .= '<font color="Green"><b>'.$messageString.' '.$class_conjugaison->plural($envoyeString,'M',1)
											.' > </b></font><br />'.(stristr($_SERVER['HTTP_HOST'],"localhost")?
											"<br />$this_email, $subject, $communications_msg, \"From: $coinfo\"" . "<br />".$mail_headers
											.'<hr /><br />':'');
									}
								}
							}
						}
//////////////////////////////
						if ($loop_lg == $lg)
							$notice = '<b>'.$enregistrementString.' '.$class_conjugaison->plural($effectueString,'M',1)
								.(stristr($_SERVER['PHP_SELF'],$urladmin)?' '.$pourString.' '.$readinsert[3].' , '.$numidString.' : '
								.$readinsert[0].', '.$statutString.' : '.$readinsert[1].' [i] '.$loop_lg.'</b><!-- <br /> <br /><a href="'
								.$local_url.'">'.$verslisteString.' '.${$this_is."String"}.'</a> -->':'</b>').'<br />'
							.$notice;
					}
				} else {
					if ($loop_lg == $lg)
						$error .= $error_request.'<br />'.mvtrace(__FILE__, __LINE__, "bad readinsert");
				}
			}
		}
	}
}
$notice .= $notice_img;
if (stristr($_SERVER['PHP_SELF'],$urladmin)
	|| (!stristr($_SERVER['PHP_SELF'],$urladmin)
		&& (!in_array($this_is,$array_modules_as_form) && !in_array($this_is,$array_fixed_modules))))
// with its own redirect and session checks
if ($error == '') {
	$_SESSION['mv_notice'] = $notice;
	Header("Cache-Control: no-cache");
	Header("Pragma: no-cache");
	Header("Location: ".html_entity_decode($local_url));Die();
}
