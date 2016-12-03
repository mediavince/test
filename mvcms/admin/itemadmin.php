<?php #۞ #
if (strstr($_SERVER["PHP_SELF"],'itemadmin.php') || !isset($this_is)) {
	include '_security.php';
	Header("Location: $redirect");Die();
}
//	$notice .= mvtrace(__FILE__,__LINE__)." $x<br />";

if (stristr($_SERVER['PHP_SELF'],$urladmin))
$content .= $admin_menu.'<div style="float:left;width:99%;">';

if (!isset($local_url))
$local_url = $local.'?lg='.$lg.'&amp;x='.$x.'&amp;y='.$y;

$dbtable = ${"tbl".$this_is};

if (!isset($admin_priv)) $admin_priv = array();

if (isset($that_is)) {
	$that_dbtable = ${"tbl".$that_is};
	$that_array_fields = sql_fields($that_dbtable,'array');
	$that_id_rid = (in_array( $that_is."rid" , $that_array_fields ));
	if (!isset($that_array_fields[1])) {
		//	unset($that_is);
		$that_is = NULL;// better than unset
	} else {
		$old_that = $that_is;
		$old_thatdbtable = $that_dbtable;
	}
}

if (!isset($array_csv_list))
$array_csv_list = array('user','membre');
if (!isset($array_modules_as_form))
$array_modules_as_form = array();
if (!isset($array_preferred_fields))
$array_preferred_fields = array('title','titre','name','nom','util','id');

$mediumtext_array = array(); // textarea no formatting: eg meta desc & keyw
$longtext_array = array(); // textarea with tinyMCE: word style UI
$enumYN_array = array(); // either Y or NO: produces selectable option code
$enumtype_array = array();// int(11) unsigned, produces selectable code for all possibilities
// taken from enum and assign string, then allows creation of new
// type, further options apply like - for deleting the selected item
$int3_array = array(); // int(3) unsigned, flag for fetching items from referenced table
$datetime_array = array(); // datetime, flag for showing calendar
$array_fields_type = array();//lists all types for a given table

$result = @mysql_query("SHOW FIELDS FROM $dbtable");
if (!$result) {Header("Location: $redirect");Die();} // no table or no connection...
while($row=@mysql_fetch_array($result)) {
	$array_fields_type[$row['Field']] = $row['Type'];
	if ($row['Type'] == 'mediumtext')
	$mediumtext_array[] = $row['Field'];
	if ($row['Type'] == 'longtext')
	$longtext_array[] = $row['Field'];
	if ($row['Type'] == "enum('N','Y')")
	$enumYN_array[] = $row['Field'];
	if ($row['Type'] == 'int(11) unsigned')
	$enumtype_array[] = $row['Field'];
	if ($row['Type'] == 'int(3) unsigned')
	$int3_array[] = $row['Field'];
	if ($row['Type'] == 'datetime')
	$datetime_array[] = $row['Field'];
}
@mysql_free_result($result);

if (!isset($array_mandatory_fields))
$array_mandatory_fields = array();
if (!isset($params_array))
$params_array = array();

if (isset(${"_".$this_is."_params_array"}))
$params_array = array_unique(array_merge($params_array,${"_".$this_is."_params_array"}));
if (isset(${"_".$this_is."_array_mandatory_fields"}))
$array_mandatory_fields = array_unique(array_merge($array_mandatory_fields,
${"_".$this_is."_array_mandatory_fields"}));

if (isset($that_is)) {
	if (isset(${"_".$that_is."_params_array"}))
	$params_array = array_unique(array_merge($params_array,${"_".$that_is."_params_array"}));
	if (isset(${"_".$that_is."_array_mandatory_fields"}))
	$array_mandatory_fields = array_unique(array_merge($array_mandatory_fields,
	${"_".$that_is."_array_mandatory_fields"}));
}

$array_fields = sql_fields($dbtable,'array');
$this_id_rid = (in_array( $this_is."rid" , $array_fields ));
if (!isset(${$this_is.'_referrerfield'})) {
	foreach($array_preferred_fields as $k)
	if (!isset(${$this_is.'_referrerfield'}))
	if (in_array($this_is.$k,$array_fields)) ${$this_is.'_referrerfield'} = $this_is.$k;
}
if (!isset(${$this_is.'_referrerfield'}))
${$this_is.'_referrerfield'} = $array_fields[5];

if ((!in_array($this_is."lang",$array_fields) && !isset($that_is))
		|| (!in_array($this_is."lang",$array_fields) && isset($that_is)
			&& !in_array($that_is."lang",$that_array_fields)))
$basic_array = array_diff($basic_array,array('lang','rid'));
$params_array = array_merge($basic_array,$params_array);

if (in_array($this_is,$editable_by_membre) && !stristr($_SERVER['PHP_SELF'],$urladmin)
		&& ($logged_in === true)) {
	foreach($editable_by_membre_needed_params as $ebmnp) {
		$membreId = $user_id;
		if (in_array($ebmnp,$array_interconnected_byid))
		${$this_is.ucfirst($ebmnp)} = $user_id;
		else {
			${$this_is.ucfirst($ebmnp)} = sql_getone($tblmembre,
													"WHERE membrerid='$user_id' ",
													"membre".$ebmnp);
			${$ebmnp."Id"} = ${$this_is.ucfirst($ebmnp)};
		}
	}
}
foreach($array_fields as $key) {
	$empty_array_fields[] = ($key=='statut'?'Y':(isset(${$key."Id"})
	&&in_array($this_is,$editable_by_membre)
	&&!stristr($_SERVER['PHP_SELF'],$urladmin)
	&&($logged_in === true)?"'".${$key."Id"}."'":''));
	$list_array_fields = isset($list_array_fields)?$list_array_fields.','.$key:$key;
}

${"nRows".ucfirst($this_is)} = sql_nrows($dbtable,"".(in_array($this_is."lang",$array_fields)?
													" WHERE ".$this_is."lang='$lg' ":''));
${"nRows".ucfirst($this_is)."y"} = sql_nrows($dbtable," WHERE ".$this_is."statut='Y' "
													.(in_array($this_is."lang",$array_fields)?
													" AND ".$this_is."lang='$lg' ":''));
${"nRows".ucfirst($this_is)."n"} = sql_nrows($dbtable," WHERE ".$this_is."statut='N' "
													.(in_array($this_is."lang",$array_fields)?
													" AND ".$this_is."lang='$lg' ":''));

/////////////////////// ENREGISTREMENT DU DEFAUT DES VARIABLES, CF APRES LOOP ////////////////
$old_this = $this_is;
$old_dbtable = $dbtable;
/////////////////////// ENREGISTREMENT DU DEFAUT DES VARIABLES, CF APRES LOOP ////////////////
if (isset($that_is)) {
	$that_array_fields = sql_fields($that_dbtable,'array');
	if (!isset(${$that_is.'_referrerfield'})) {
		foreach($array_preferred_fields as $k)
		if (!isset(${$that_is.'_referrerfield'}))
		if (in_array($that_is.$k,$that_array_fields))
		${$that_is.'_referrerfield'} = $that_is.$k;
	}
	if (!isset(${$that_is.'_referrerfield'}))
	${$that_is.'_referrerfield'} = $that_array_fields[5];
	$array_fields = array_merge($array_fields,$that_array_fields);
	foreach($that_array_fields as $key) {
		$that_empty_array_fields[] = ($key=='statut'?'Y':'');
		$that_list_array_fields = isset($that_list_array_fields)?
		$that_list_array_fields.','.$key:$key;
	}
	$result = @mysql_query("SHOW FIELDS FROM $that_dbtable");
	while($row=@mysql_fetch_array($result)) {
		$array_fields_type[$row['Field']] = $row['Type'];
		if ($row['Type'] == 'mediumtext')
		$mediumtext_array[] = $row['Field'];
		if ($row['Type'] == 'longtext')
		$longtext_array[] = $row['Field'];
		if ($row['Type'] == "enum('N','Y')")
		$enumYN_array[] = $row['Field'];
		if ($row['Type'] == 'int(11) unsigned')
		$enumtype_array[] = $row['Field'];
		if ($row['Type'] == 'int(3) unsigned')
		$int3_array[] = $row['Field'];
		if ($row['Type'] == 'datetime')
		$datetime_array[] = $row['Field'];
	}
	@mysql_free_result($result);
	${"nRows".ucfirst($that_is)} = sql_nrows($that_dbtable,
											(in_array($that_is."lang",$that_array_fields)?
											"WHERE ".$that_is."lang='$lg' ":''));
	${"nRows".ucfirst($that_is)."y"} = sql_nrows($that_dbtable,"WHERE ".$that_is."statut='Y' "
											.(in_array($that_is."lang",$that_array_fields)?
											" AND ".$that_is."lang='$lg' ":''));
	${"nRows".ucfirst($that_is)."n"} = sql_nrows($that_dbtable,"WHERE ".$that_is."statut='N' "
											.(in_array($that_is."lang",$that_array_fields)?
											" AND ".$that_is."lang='$lg' ":''));
}

if (isset(${$this_is."Id"})) {
	if (${$this_is."Id"} == 'new') {
		//	unset(${$this_is."Id"});
		$send = 'new';
	} else {
		if (!preg_match("/^[0-9]+\$/",${$this_is."Id"}))
		{Header("Location: $redirect");Die();}
		else {
			//	!check
			if ($lg != $default_lg)
			${$this_is."Id"} = sql_getone(${"tbl".$this_is},
										"WHERE ".$this_is."id='".${$this_is."Id"}
										."' ",$this_is.($this_id_rid===true?'r':'')."id");
			$this_id = ${$this_is."Id"};
		}
	}
}

if (isset($that_is)) {
	if (isset(${$that_is."Id"}) && preg_match("/^[0-9]+\$/",${$that_is."Id"})) {
		${$this_is."Id"} = ${$that_is."Id"};
		$this_id = ${$that_is."Id"};
	}
}
//**//
if ((isset($rqst) || isset($toggle)) && isset(${$this_is."Id"})) {
	${$this_is."Id"} = strip_tags(${$this_is."Id"});
	$rqst = (isset($rqst)?strip_tags($rqst):'');
	if ((isset($toggle) || ($rqst == 'conf') || ($rqst == 'canc'))
		&& preg_match("/^[0-9]+\$/",${$this_is."Id"}) && (${$this_is."Id"} !== '')) {
		if ($this_is == 'admin') {
			if (${$this_is."Id"} == $admin_id) {
				$error = $error_invmiss.' => '.$sortirString.' !';
			} else if (${"nRows".ucfirst($this_is)} == 1) {
				$error = $error_invmiss.' => <b>'.${"nRows".ucfirst($this_is)}.'</b> '
						.$class_conjugaison->plural($enregistrementString,'M',${"nRows"
						.ucfirst($this_is)}).' '.$surString.' '.${$this_is."String"}.' !';
			}
		}
		if ($error=='') {
			foreach($array_fields as $k_check) {
				$field_check = substr($k_check,strlen($this_is));
				if (isset(${$field_check."Id"})) {
					${$field_check."Id"} = strip_tags(${$field_check."Id"});
					$notice .= '<b>'.$enregistrementString.' '.$pourString.' '.$field_check
							.' '.$class_conjugaison->plural((mv_toggle(${"tbl".$field_check},
							$field_check.(in_array($field_check.$toggle,$array_fields)?
							$toggle:"statut")," ".$field_check.(in_array($field_check."rid",
							$array_fields)?'r':'')."id='".${$field_check."Id"}."' AND "
							.$this_is.$field_check."='".${$this_is."Id"}."' ")=='Y'?
							$confirmeString:$enattenteString),'M',1).'</b>';
					$found_check_for_toggle = true;
					break;
				}
			}
			if (!isset($found_check_for_toggle))
			$notice .= '<b>'.$enregistrementString.' '.$pourString.' '.$this_is.' '
					.$class_conjugaison->plural((mv_toggle($dbtable,$this_is
					.(in_array($this_is.$toggle,$array_fields)?$toggle:"statut")," "
					.$this_is.($this_id_rid===true?'r':'')."id='".${$this_is."Id"}
					."' ")=='Y'?$confirmeString:$enattenteString),'M',1).'</b>';
			if (isset($that_is) && isset(${"connected_byid_".$this_is."_".$that_is}))
			$notice .= '<br /><b>'.$enregistrementString.' '.$pourString.' '.$that_is.' '
					.$class_conjugaison->plural((mv_toggle($that_dbtable,$that_is
					.(in_array($that_is.$toggle,$that_array_fields)?$toggle:"statut")," "
					.$that_is.($that_id_rid===true?'r':'')."id='".${$that_is."Id"}."' ")=='Y'?
					$confirmeString:$enattenteString),'M',1).'</b>';
			$_SESSION['mv_notice'] = $notice;
			${"nRows".ucfirst($this_is)} = sql_nrows($dbtable,"".(in_array($this_is."lang",
										$array_fields)?" WHERE ".$this_is."lang='$lg' ":''));
			${"nRows".ucfirst($this_is)."y"} = sql_nrows($dbtable," WHERE ".$this_is
											."statut='Y' ".(in_array($this_is."lang",
											$array_fields)?"AND ".$this_is."lang='$lg' ":''));
			${"nRows".ucfirst($this_is)."n"} = sql_nrows($dbtable," WHERE ".$this_is
											."statut='N' ".(in_array($this_is."lang",
											$array_fields)?"AND ".$this_is."lang='$lg' ":''));
		} else {
			$_SESSION['mv_error'] = $error;
		}
		Header("Location: ".html_entity_decode($local_url
							.(isset($send)&&isset(${$this_is."Id"})?'&amp;send='.$send
							.'&amp;'.$this_is.'Id='.${$this_is."Id"}:'')));
		Die();
	} else
	{Header("Location: $redirect");Die();}
}

////////////////////////////////////
$nRowsThis_is = ${"nRows".ucfirst($this_is)};
$nRowsThis_isy = ${"nRows".ucfirst($this_is)."y"};
$nRowsThis_isn = ${"nRows".ucfirst($this_is)."n"};
if (isset($that_is)) {
	$nRowsThat_is = ${"nRows".ucfirst($that_is)};
	$nRowsThat_isy = ${"nRows".ucfirst($that_is)."y"};
	$nRowsThat_isn = ${"nRows".ucfirst($that_is)."n"};
}
////////////////////////////////////

if (!isset($array_int3_have_to_match)) // for selection inter-dependancy
$array_int3_have_to_match = array();

$javascript_for_dependancy_head = '';
$javascript_for_dependancy = '';
foreach($array_int3_have_to_match as $key) {
	$key = explode(",",$key);
	foreach($key as $element)
	if (in_array($this_is.$element,$array_fields))
	$int3_interdependant[] = $this_is.$element;
	$javascript_for_dependancy_head .= $key[0]."Array = [];\n";
	$sql = @mysql_query("SELECT * FROM ".${"tbl".$key[0]}
						." WHERE ".$key[0]."lg='$lg' ORDER BY ".$key[0].(isset($row[$key[0]
						."rid"])?'r':'')."id ASC ");
	while($row = @mysql_fetch_array($sql)) {
		if (!isset($current_element_id))
		$current_element_id = $row[$key[0].(isset($row[$key[0]."rid"])?'r':'')."id"];
		else {
			if ($row[$key[0].(isset($row[$key[0]."rid"])?'r':'')."id"]!=$current_element_id)
			$current_element_id = $row[$key[0].(isset($row[$key[0]."rid"])?'r':'')."id"];
		}
		$javascript_for_dependancy_head .= $key[0]."Array[".$row[$key[0].(isset($row[$key[0]
										."rid"])?'r':'')."id"]."] = \'".html_entity_decode(
										(isset($row[$key[0]."gendre"])?sql_stringit('gendre',
										$row[$key[0]."gendre"]).' '.$row[$key[0]."prenom"].' '
										.$row[$key[0]."nom"]:(isset($row[$key[0]."titre"])?
										$row[$key[0]."titre"]:(isset($row[$key[0]."title"])?
										$row[$key[0]."title"]:(isset($row[$key[0]."util"])?
										$row[$key[0]."util"]:(isset($row[$key[0]."type"])?
										sql_stringit($key[0]."type",$row[$key[0]."type"]):
										$row[$key[0].(isset($row[$key[0]."rid"])?'r':'')."id"]
										))))))."\';\n";
		for($i=1;$i<count($key);$i++) {
			$element = $key[$i];
			$javascript_for_dependancy .= "Array_$current_element_id = [];\n";
			$key_sql = @mysql_query("SELECT * FROM ".${"tbl".$element}." ORDER BY "
									.$element.(in_array($element."rid",sql_fields(${"tbl"
									.$element},'array'))?'r':'')."id ASC ");
			while($row_key = @mysql_fetch_array($key_sql))
			if ($row_key[$element.$key[$i-1]] == $current_element_id)
			$javascript_for_dependancy .= "Array_".$current_element_id."[".$row_key[$element
										.(isset($row[$element."rid"])?'r':'')."id"]."] = \'"
										.html_entity_decode((isset($row_key[$element
										."gendre"])?sql_stringit('gendre',$row_key[$element
										."gendre"]).' '.$row_key[$element."prenom"].' '
										.$row_key[$element."nom"]:(isset($row_key[$element
										."titre"])?$row_key[$element."titre"]:
										(isset($row_key[$element."title"])?$row_key[$element
										."title"]:(isset($row_key[$element."util"])?
										$row_key[$element."util"]:(isset($row_key[$element
										."type"])?sql_stringit($element."type",
										$row_key[$element."type"]):$row_key[$element
										.(isset($row[$element."rid"])?'r':'')."id"]
										))))))."\';\n";
		}
	}
}
if (!isset($int3_interdependant)||(count($int3_interdependant)==1)) {
	$int3_interdependant = array();
} else {
	$stylesheet .= '
<script type="text/javascript">
'.$javascript_for_dependancy_head.'
'.$javascript_for_dependancy.'
</script>
<script type="text/javascript" src="'.$mainurl.'lib/mvdyn_selectable_options.js"></script>
';
}
##############################################################################################
if (isset($send)) {
	if ($send == ucfirst($envoyerString).' ZIP') {
		include $getcwd.$up.$urladmin.'galleryzip.php';
	}
	if (($forum_commenting === true) &&
			(($send == $envoyerString.' '.$reponseString)
			|| ($send == $ajouterString.' '.$commentString)
			|| (($send == 'delete') && isset($commentId))
				)) {
		include $getcwd.$up.$urladmin.'commentmanager.php';
		die();
	}
	if (($send == 'edit') || ($send == $sauverString) || ($send == 'delete')) {
		if (!isset(${$this_is."Id"}))
		{Header("Location: $redirect");Die();}
		${$this_is."Id"} = strip_tags(${$this_is."Id"});
		$editthis = sql_get($dbtable,"WHERE ".(in_array($this_is."lang",$array_fields)?
					" ".$this_is.($this_id_rid===true?'r':'')."id='".${$this_is."Id"}
					."' AND ".$this_is."lang='$lg' ":$this_is."id='".${$this_is."Id"}."' "),
					$list_array_fields);
		if (isset($that_is))
		$editthat = sql_get($that_dbtable,"WHERE ".(in_array($that_is."lang",
					$that_array_fields)?" ".$that_is.($that_id_rid===true?'r':'')."id='"
					.${$this_is."Id"}."' AND ".$that_is."lang='$lg' ":$that_is
					.($that_id_rid===true?'r':'')."id='".${$this_is."Id"}."' "),
					$that_list_array_fields);
		if (!preg_match("/^[YN]\$/",$editthis[1]))
		{Header("Location: $redirect");Die();}
	} else { // send == envoyer or new !!
		$editthis = $empty_array_fields;//array('','','','');
		if (isset($that_is))
		$editthat = $that_empty_array_fields;//array('','','','');
	}
	if (isset($that_is))
	$editthis = array_merge($editthis,$editthat);
##############################################################################################
	if (!in_array('1',$admin_priv) || (in_array('1',$admin_priv) && ($send == $sauverString)))
	if ((($send == $envoyerString) || ($send == $sauverString))
		&& ($_SERVER["REQUEST_METHOD"] == "POST")) {
		include $getcwd.$up.$urladmin.'function-process_post.php';
// 		include 'function-process_post.php';
	}
	$this_is = $old_this;
	$dbtable = $old_dbtable;
##############################################################################################
	if (!in_array('1',$admin_priv) ||
		(in_array('1',$admin_priv) && in_array($send,array('edit',$sauverString))))
	if (($send == 'new') || ($send == 'edit')
		|| (($error != '') && (($send == $sauverString) || ($send == $envoyerString)))) {
		// style="clear:both;"
		//  if (($send == 'new') && ($lg != $default_lg)) $lg = $default_lg;
		// other langs cannot add new entry
		if (in_array($this_is,$array_galleries) && (($send == 'new') || ($send == 'edit'))) {
			include $getcwd.$up.$urladmin.'galleryzip.php';
		}
		if (isset($datetime_array[0])) {
			$javascript .= '<script src="'.$mainurl.$urladmin.'jscripts/calendarpopup.js" '
						.'type="text/javascript" language="javascript"></script>'
						.$CRLF.'<script src="'.$mainurl.$urladmin.'dates.php?lg='.$lg.'" '
						.'type="text/javascript" language="javascript"></script>';
				//<script>var cal = new CalendarPopup(\'calpop\');
				//cal.setWeekStartDay(1); cal.autoHide();</script>
		}
		$content .= '<table align="center" border="0" cellpadding="2" cellpspacing="0">'
					.$CRLF.'<tr><td align="center">';
		$content .= ($send=='new'?$ajoutString:(isset(${$this_is."Id"})
						&&preg_match("/^[0-9]+\$/",${$this_is."Id"})
						&&!in_array('1',$admin_priv)?'<a href="'.$local_url
						.'&amp;send=delete&amp;'.$this_is.'Id='.${$this_is."Id"}
						.'" onclick="return confirm(\''.$confirmationeffacementString
						.'\');"><img src="'.$mainurl.'images/delete.gif" width="10" '
						.'height="10" alt="'.$effacerString.'" title="'.$effacerString
						.'" align="right" border="0" /></a> '.(in_array($this_is.'archive',
						$array_fields)?'<a href="'.$local_url.(isset($send)?'&amp;send='
						.$send:'').'&amp;'.$this_is.'Id='.${$this_is."Id"}
						.'&amp;toggle=archive" onclick="return confirm(\''
						.($editthis[array_search($this_is.'archive',$array_fields)]=='Y'?
						$nonString.' '.$archiveString.' ?\');"><img src="'.$mainurl
						.'images/archive_y.png" width="24" height="24" border="0" title="'
						.$archivedString.'" alt="'.$archivedString:$archiveString
						.' ?\');"><img src="'.$mainurl.'images/archive_n.png" width="24" '
						.'height="24" border="0" title="'.$nonString.' '.$archivedString
						.'" alt="'.$nonString.' '.$archivedString).'" align="right" /></a> ':
						'').(in_array($this_is.'publish',$array_fields)?'<a href="'.$local_url
						.(isset($send)?'&amp;send='.$send:'').'&amp;'.$this_is.'Id='
						.${$this_is."Id"}.'&amp;toggle=publish" onclick="return confirm(\''
						.($editthis[array_search($this_is.'publish',$array_fields)]=='Y'?
						$nonString.' '.$publishString.' ?\');"><img src="'.$mainurl
						.'images/publish_y.png" width="12" height="12" border="0" title="'
						.$publishedString.'" alt="'.$publishedString:$publishString
						.' ?\');"><img src="'.$mainurl.'images/publish_n.png" width="12" '
						.'height="12" border="0" title="'.$nonString.' '.$publishedString
						.'" alt="'.$nonString.' '.$publishedString).'" align="right" /></a> ':
						'').$modifierString:'')).' '.${$this_is."String"}.'</td>'
						.$CRLF.'</tr><tr>'.$CRLF.'<td align="left">';
		$form_content = array();
		$form_content['_form'] = (stristr($_SERVER['PHP_SELF'],$urladmin)?gen_form($lg,$x,$y):
							'<form enctype="multipart/form-data" name="f'.$x.'" action="'
							.($full_url===true?$mainurl.lgx2readable($lg,$x):'').'" method="'
							.$postgetmethod.'"><input type="hidden" name="send" value="'
							.$sauverString.'" />');
		$content .= $form_content['_form'];
		for($i=0;$i<count($array_fields);$i++) {
			if (substr($array_fields[$i],0,strlen($dbtable)-1) != $this_is) {
				$this_is = $that_is;
				$dbtable = $that_dbtable;
				$content .= '<hr /><h2 style="text-align:center;">'.$paramsString.' '
						.${$this_is."String"}.'</h2>';
			}
			$key = substr($array_fields[$i],strlen($dbtable)-1);
			if (isset(${$this_is.ucfirst($key)}))
			$editthis[$i] = ${$this_is.ucfirst($key)};
			if (in_array($key,$array_mandatory_fields))
			//	|| (isset(${"_".$this_is."_array_mandatory_fields"})
			//	&& in_array($key,${"_".$this_is."_array_mandatory_fields"})))
			${$key."String"} .= ' <span class="mandatory">* '.$obligatoireString.'</span> ';
			if (in_array($key,$basic_array)) {
				if (isset(${$this_is."Id"})) {
					if (in_array($key,array('id','rid'))) {
						$this_id = $editthis[$i];
						//  ${$this_is."Id"} = $this_id;
						if (($send == $envoyerString) && ($error != ''))
						$form_content[$this_is.$key] = '';
						else
						$form_content[$this_is.$key] = '<input name="'.$this_is.ucfirst($key)
							.'" type="hidden" value="'.$editthis[$i].'" />'
							.(stristr($_SERVER['PHP_SELF'],$urladmin)?'> '.${$key."String"}
							.' <i>'.$editthis[$i].'</i><br />':'');
						$content .= $form_content[$this_is.$key];
					}
					if ($key == 'date') {
						if (in_array($key,$array_mandatory_fields)) { // edit
							$javascript .= '<script>'.$CRLF.'var cal'.$i
										.' = new CalendarPopup(\'calpop'.$i.'\');  cal'.$i
										.'.setWeekStartDay(1); cal'.$i.'.autoHide();'
										.$CRLF.'</script>';
							$form_content[$this_is.$key] = '<label for="'.$this_is
								.ucfirst($key).'">'.ucfirst(${$key."String"}).'</label> '
								.'<input type="text" name="'.$this_is.ucfirst($key)
								.'" value="'.($editthis[$i]!=''?edit_date($editthis[$i]):
								'DD/MM/YYYY').'" size="12" /> <div class="cal_sml"><a '
								.'class="toggle" onclick="cal'.$i.'.select(document.forms[\'f'
								.$x.'\'].'.$this_is.ucfirst($key).',\'anchor'.$i
								.'\',\'dd/MM/yyyy\'); return false;" name="anchor'.$i
								.'" id="anchor'.$i.'">'.$calendrierString.'</a></div><div '
								.'id="calpop'.$i.'" class="calpop" style="visibility:hidden;"'
								.'>&nbsp;</div><br />';
						} else
						$form_content[$this_is.$key] = '<input name="'.$this_is.ucfirst($key)
							.'" type="hidden" value="'.$editthis[$i].'" />'
							.(stristr($_SERVER['PHP_SELF'],$urladmin)?'> '.${$key."String"}
							.' <i>'.$editthis[$i].'</i><br />':'');
						$content .= $form_content[$this_is.$key];
					}
				} else {
					if ($key == 'date')
					if (in_array($key,$array_mandatory_fields)) {// new
						$javascript .= '<script>'.$CRLF.'var cal'.$i
									.' = new CalendarPopup(\'calpop'.$i.'\');  cal'.$i
									.'.setWeekStartDay(1); cal'.$i.'.autoHide();'
									.$CRLF.'</script>';
						$form_content[$this_is.$key] = '<label for="'.$this_is.ucfirst($key)
							.'">'.ucfirst(${$key."String"}).'</label> '
							.'<input type="text" name="'.$this_is.ucfirst($key).'" value="'
							.($editthis[$i]!=''?edit_date($editthis[$i]):'DD/MM/YYYY')
							.'" size="12" /> <div class="cal_sml"><a class="toggle" '
							.'onclick="cal'.$i.'.select(document.forms[\'f'.$x.'\'].'
							.$this_is.ucfirst($key).',\'anchor'.$i.'\',\'dd/MM/yyyy\'); '
							.'return false;" name="anchor'.$i.'" id="anchor'.$i.'">'
							.$calendrierString.'</a></div><div id="calpop'.$i.'" '
							.'class="calpop" style="visibility:hidden;">&nbsp;</div><br />';
						$content .= $form_content[$this_is.$key];
					}
				}
				if ($key == 'statut') {
					if ($editthis[$i] == '')
					$editthis[$i] = (stristr($_SERVER['PHP_SELF'],$urladmin)?'Y':'N');
					if (!in_array('1',$admin_priv)) {
						$form_content[$this_is.$key] = (stristr($_SERVER['PHP_SELF'],
							$urladmin)?'<label for="'.$this_is.ucfirst($key).'">'
							.ucfirst(${$key."String"}).'</label> <!--<br />-->'
							.'<select name="'.$this_is.ucfirst($key).'">'.gen_selectoption(
							$tblenum,$editthis[$i],'','statut').'</select><br />':
							'<input name="'.$this_is.ucfirst($key)
							.'" type="hidden" value="'.$editthis[$i].'" />');
						$content .= $form_content[$this_is.$key];
					}
				}
				// lg is always set
				if ($key == 'lang') {
					$form_content[$this_is.$key] = '<input type="hidden" name="'.$this_is
												.ucfirst($key).'" value="'.$lg.'" />';
					$content .= $form_content[$this_is.$key];
				}
			} else if (!stristr($_SERVER['PHP_SELF'],$urladmin)
						&& in_array($key,$array_hidden)) {
				$form_content[$this_is.$key] = '<input name="'.$this_is.ucfirst($key)
					.'" type="hidden" value="'.$editthis[$i].'" />'
					.(stristr($_SERVER['PHP_SELF'],$urladmin)?'> '.ucfirst(${$key."String"}).' <i>'
					.$editthis[$i].'</i><br />':'');
				$content .= $form_content[$this_is.$key];
			} else {
				///////////// routine for dynamic processing
				if ($key == 'util') {
					if (($this_is == 'admin'))
					$form_content[$this_is.$key] = '<label for="'.$this_is.ucfirst($key)
						.'">'.ucfirst(${$key."String"}).' "'.ucfirst(${$this_is."String"})
						.'"</label> (a-z A-Z 0-9 _ -)'.'<br />'.$CRLF.'<input name="'
						.$this_is.ucfirst($key).'" type="text"'.'value="'.$editthis[$i]
						.'" autocomplete="off" /><br />';
					else
					$form_content[$this_is.$key] = '<label for="'.$this_is.ucfirst($key)
						.'">'.ucfirst(${$key."String"}).' "'.ucfirst(${$this_is."String"})
						.'"</label><br /><input name="'.$this_is.ucfirst($key)
						.'" type="text" value="'.$editthis[$i]
						.'" autocomplete="off" /><br />';
					$content .= $form_content[$this_is.$key];
				} else if ($key == 'pass') {
					$form_content[$this_is.$key] = '<label for="'.$this_is.ucfirst($key)
						.'">'.$motdepasseString.'</label>'.($send=='edit'?' > <b>'
						.$laisservidepourgarderString.'</b> ':'').($this_is!='admin'?
						' > <b>'.ucfirst(strtolower($voirString.' '.$confirmationString.' '
						.$emailString)).'</b>':'').'<input name="'.$this_is.ucfirst($key)
						.'" type="password" value="" /><br />';
					$content .= $form_content[$this_is.$key];
				} else if ($key == 'email') {
					$form_content[$this_is.$key] = '<label for="'.$this_is.ucfirst($key)
						.'">'.ucfirst(${$key."String"}).'</label>'.(in_array($this_is,
						$array_email_conf)?(stristr($_SERVER['PHP_SELF'],$urladmin)?' > <b>'
						.ucfirst(strtolower($envoyerString.' '.$confirmationString))
						.'?</b> <input name="'.$key.'" type="checkbox" /> ':''):'')
						.'<input name="'.$this_is.ucfirst($key).'" type="text" value="'
						.$editthis[$i].'" /><br />';
						//<input name="'.$key.'" type="hidden" '.$inputchecked.' value="on" />
					$content .= $form_content[$this_is.$key];
				} else if ($key == 'priv') {
					if (($this_is == 'admin')) {
						if (!in_array('1',$admin_priv))
						$form_content[$this_is.$key] = '<label for="privilege">'
							.ucfirst($privilegeString).'</label><br /><img src="'.$mainurl
							.'images/spacer.gif" width="30" height="0" border="0" alt="" />'
							.'<label for="privilege">'.$pageString.'</label> > '
							.'<input type="radio" name="privilege" value="1" '
							.($editthis[$i]==1?'checked="checked"':'').' /> '
							.'<input type="radio" name="privilege" value="0" '
							.($editthis[$i]==0?'checked="checked"':'').' /> &nbsp;&nbsp;< '
							.'<label for="privilege">'.$toutString.'</label><br />';
						$content .= $form_content[$this_is.$key];
					} else {
						if ($editthis[$i] == '') $editthis[$i] = '1';
						if ($mod_priv === true)
						$form_content[$this_is.$key] = '<br /><div><div style="float:left;">'
							.'<label for="'.$this_is.ucfirst($key).'">'
							.ucfirst($privilegeString).' : </label> &nbsp;</div>'
							.gen_inputcheck($tblenum,$editthis[$i],'','privilege',true)
							."</div><br /> <br />";
							//gen_inputcheck last param true for hiding first item
						$content .= $form_content[$this_is.$key];
					}
				} else if (($key == 'title') || ($key == 'titre')) {
					$form_content[$this_is.$key] = '<label for="'.$this_is.ucfirst($key)
						.'">'.ucfirst(${$key."String"}).'</label><br /><input name="'.$this_is
						.ucfirst($key).'" type="text" value="'.$editthis[$i]
						.'" /><br />';
					$content .= $form_content[$this_is.$key];
				} else if (($key == 'website') || ($key == 'url')) {
					$form_content[$this_is.$key] = '<label for="'.$this_is.ucfirst($key)
						.'">'.ucfirst(${$key."String"}).'</label><br /><input name="'.$this_is
						.ucfirst($key).'" type="text" value="'
						.(!stristr($editthis[$i],"http://")?"http://":'').$editthis[$i]
						.'" /><br />';
					$content .= $form_content[$this_is.$key];
				} else if ($key == 'gendre') {
					$form_content[$this_is.$key] = '<label for="'.$this_is.ucfirst($key)
						.'">'.ucfirst(${$key."String"}).'</label> <!--<br />--><select name="'
						.$this_is.ucfirst($key).'">'.gen_selectoption($tblenum,
						(isset(${$this_is.ucfirst($key)})?${$this_is.ucfirst($key)}:
						$editthis[$i]),'','gendre').'</select><br />';
					$content .= $form_content[$this_is.$key];
				} else if ($key == 'comment') {
					if ((isset($send) && ($send != 'new')) && ($admin_viewing === false)) {
						include $getcwd.$up.$urladmin.'commentmanager.php';
						$form_content[$this_is.$key] = $show_comment;
						$content .= $form_content[$this_is.$key];
					} else {
						$show_comment = '';
					}
				} else if (($key == 'img') || ($key == 'doc')) {
					if ($array_fields_type[$this_is.$key] == 'text') {
						if (!is_array($editthis[$i]))
						$editthis[$i] = explode("|",$editthis[$i]);
						if ((count($editthis[$i])==1)&&($editthis[$i][0]==''))
						$editthis[$i] = array();
					} else {
						if (isset($this_id)) {
							if ($key == "img")
							$editthis[$i] = sql_array($tblcontphoto,"WHERE contphotolang='$lg'
											AND contphotocontid='$this_id'
											AND contphotoimg LIKE '%".strtoupper($this_is)."%'
											ORDER BY contphotosort,contphotoid ASC ",
										"contphotoimg");
							if ($key == "doc")
							$editthis[$i] = sql_array($tblcontdoc,"WHERE contdoclang='$lg'
											AND contdoccontid='$this_id'
											AND contdoc LIKE '%".strtoupper($this_is)."%' ",
										"contdoc");
						} else
						$editthis[$i] = array();
					}
					if ($key == 'img') {
						$form_content[$this_is.$key] = '<div class="form_image">';
						$content .= $form_content[$this_is.$key];
					}
					$count_explode = count($editthis[$i]);
					$this_nof = $nof;//reset nof
					$this_nof = ($count_explode<$nof||$send=='new'?$nof:$count_explode+1);
					$count_explode = count($editthis[$i]);
					if ($count_explode<$nof)
					if (!isset($editthis[$i][$nof]))
					for($ii=$count_explode;$ii<$nof;$ii++)
					$editthis[$i][] = '';
					for($ii=0;$ii<$this_nof;$ii++) {
						if ($key == 'img') {
							$contphoto_img_by_id = sql_getone($tblcontphoto,
								"WHERE contphotolang='$lg' AND  contphotorid='".$editthis[$i]
								."' ","contphotoimg");
							if (isset($this_id) && isset($editthis[$i][$ii]))
							$contphoto_desc_by_this_id = sql_getone($tblcontphoto,
											"WHERE contphotolang='$lg'
												AND contphotocontid='$this_id'
												AND contphotoimg='".$editthis[$i][$ii]."' ",
											"contphotodesc");
							$form_content[$this_is.$key[$ii]] = '<div class="form_image_half">'
								.(isset($editthis[$i][$ii])&&$editthis[$i][$ii]!=''?
								'<br /><img src="'.$mainurl.(is_int($editthis[$i])?
								$contphoto_img_by_id.'?'.$now_time.'" '.show_img_attr(
								$contphoto_img_by_id):$editthis[$i][$ii].'?'.$now_time.'" '
								.show_img_attr($editthis[$i][$ii])).' border="0" /><br />':'')
								.'<br />'.'<label for="'.$this_is.ucfirst($key).'[]">'
								.ucfirst(${$key."String"}).($ii>0?$ii:'').' '
								.(isset($editthis[$i][$ii])&&$editthis[$i][$ii]!=''?
								'<a href="'.$mainurl.(stristr($editthis[$i][$ii],$safedir)?
								$urlsafe.'?file='.base64_encode($editthis[$i][$ii]):
								$editthis[$i][$ii]).'" target="_new" class="extlink"><!--'
								.$editthis[$i][$ii].'--></a> | <a href="'.$local_url
								.'&amp;send=delete&amp;'.$this_is.'Id='.$editthis[4].'&amp;'
								.$this_is.ucfirst($key).'='.$editthis[$i][$ii]
								.'" onclick="return confirm(\''.$confirmationeffacementString
								.'\');"><img src="'.$mainurl.'images/delete.gif" width="10" '
								.'height="10" alt="'.$effacerString.'" title="'.$effacerString
								.'" border="0" /></a> ':'').'</label> '.(in_array($this_is,
								$array_galleries)?'<input class="text" type="text" name="'
								.$this_is."Desc".'[]" value="'.(isset($that_is)?
								(in_array($this_is.'nom',$that_array_fields)?
								$editthat[array_search($this_is.'nom',$that_array_fields)]." "
								.$editthat[array_search($this_is.'prenom',$that_array_fields)]
								:$editthat[5]):(isset($contphoto_desc_by_this_id)
								&&$contphoto_desc_by_this_id!=''?str_replace("-"," ",
								$contphoto_desc_by_this_id):$editthis[5])).'" /><br />':'')
								.'<input type="file" name="'.$this_is.ucfirst($key)
								.'[]" /><br /></div>';
						} else {
							$form_content[$this_is.$key[$ii]] = '<label for="'.$this_is
								.ucfirst($key).'[]">'.ucfirst(${$key."String"}).($ii>0?$ii:'')
								.'</label><input type="file" name="'.$this_is.ucfirst($key)
								.'[]" />'.(isset($editthis[$i][$ii])&&$editthis[$i][$ii]!=''?
								'<a href="'.$mainurl.(stristr($editthis[$i][$ii],$safedir)?
								$urlsafe.'?file='.base64_encode($editthis[$i][$ii]):
								$editthis[$i][$ii]).'" target="_new" class="extlink">'
								.$editthis[$i][$ii].'</a> | <a href="'.$local_url
								.'&amp;send=delete&amp;'.$this_is.'Id='.$editthis[4].'&amp;'
								.$this_is.ucfirst($key).'='.$editthis[$i][$ii]
								.'" onclick="return confirm(\''.$confirmationeffacementString
								.'\');"><img src="'.$mainurl.'images/delete.gif" width="10" '
								.'height="10" alt="'.$effacerString.'" title="'.$effacerString
								.'" border="0" /></a>':'').'<br />';
						}
						$form_content[$this_is.$key] .= $form_content[$this_is.$key[$ii]];
					}
					if ($key == 'img') {
						$form_content[$this_is.$key] = '</div>';
					}
					$content .= $form_content[$this_is.$key];
					//  if ($key == 'img')
					//  $content .= $contentimg.'</div>';
				} else if ($key == 'coords') {
					if ($editthis[$i]!='') {
						$star_img_coords = explode("|",$editthis[$i]);
						if (isset($star_img_coords[1])) {
							$form_content[$this_is.$key] = '<style>#star_img_coords{'
								.'position:absolute;z-index:9999;margin-left:'
								.($star_img_coords[0]>7?$star_img_coords[0]-7:
								$star_img_coords[0]).'px;margin-top:'.($star_img_coords[1]>7?
								$star_img_coords[1]-7:$star_img_coords[1]).'px;}</style>';
							$content .= $form_content[$this_is.$key];
						}
					}
					$form_content[$this_is.$key] = '<label for="'.$this_is.ucfirst($key)
						.'">'.ucfirst(${$key."String"}).'</label><br /><input name="'.$this_is
						.ucfirst($key).'" id="'.$this_is.ucfirst($key).'" type="text" value="'
						.$editthis[$i].'" /><br />';
					$content .= $form_content[$this_is.$key];
					include $getcwd.$up.$urladmin.'_coords_from_map.php';
				} else if (isset($that_is) && ($key == $old_this)) {
					$form_content[$this_is.$key] = '<label for="'.$this_is.ucfirst($key)
						.'">'.ucfirst(${$key."String"}).'</label> <!--<br />--><select name="'
						.$this_is.ucfirst($key).'">'.gen_selectoption($old_dbtable,
						$editthis[$i],'',$old_this).'</select><br />';
					$content .= $form_content[$this_is.$key];
				} else if (isset(${"tbl".$key})
							&& !in_array($key,$basic_array)
							&& !in_array($this_is.$key,$int3_array)) {
					if (in_array($this_is,$editable_by_membre)
						&& !stristr($_SERVER['PHP_SELF'],$urladmin)
						&& ($logged_in === true)
						&& isset(${$key."Id"})) {
						$form_content[$this_is.$key] = '';
				//'<input type="hidden" name="'.$key."Id".'" value="'.${$key."Id"}.'" />';
					} else {
						$form_content[$this_is.$key] = '<label for="'.$this_is.ucfirst($key)
							.'">'.ucfirst(${$key."String"}).'</label> <!--<br />-->'
							.'<select name="'.$this_is.ucfirst($key).'"'.(in_array($this_is
							.$key,$int3_interdependant)?' id="'.$this_is.$key.'"'
							.($this_is.$key==$int3_interdependant[0]?
							' onchange="changed(this,\''.$int3_interdependant[1].'\')"':
							' onchange="check_selected(\''.$int3_interdependant[1].'\',\''
							.$int3_interdependant[0].'\')"'):'').'>'.
							gen_selectoption(${"tbl$key"},$editthis[$i],(isset($this_id)
							&&in_array($this_is.$key,$array_uniqueselection)?
							" AND $key$this_is='$this_id' ":(in_array($this_is.$key,
							$array_uniqueselection)?" AND ($key$this_is=''
							OR $key$this_is=NULL)":(isset($send)&&($send=='edit')
							&&in_array($this_is.$key,$int3_interdependant)&&($this_is
							.$key==$int3_interdependant[1])?" AND $key".substr(
							$int3_interdependant[0],strlen($this_is))."='"
							.$editthis[$int3_interdependant[0]]."' ":''))).(in_array($key
							."lang",sql_fields(${"tbl$key"},"array"))?" AND ".$key
							."lang='$lg' ":''),$key).'</select><br />';
					}
					$content .= $form_content[$this_is.$key];
				} else {
					if (in_array("$this_is$key",$mediumtext_array)) {
						$form_content[$this_is.$key] = '<label for="'.$this_is.ucfirst($key)
							.'">'.ucfirst(${$key."String"}).'</label><br /><textarea name="'
							.$this_is.ucfirst($key).'" rows="20" cols="40" '
							.'style="width:97%;height:100px;">'.format_edit($editthis[$i],
							'edit').'</textarea><br />';
						$content .= $form_content[$this_is.$key];
					} else if (in_array("$this_is$key",$longtext_array)) {
						$form_content[$this_is.$key] = '<label for="'.$this_is.ucfirst($key)
							.'">'.ucfirst(${$key."String"}).'</label>'.($tinyMCE===false?
							$text_style_js:'').'<br /><textarea id="elm'
							.($i_elm>=-1?$i_elm+=1:'').'" name="'.$this_is.ucfirst($key)
							.'" rows="20" cols="40" '
							.'style="width:97%;height:300px;min-height:300px;">'
							.format_edit($editthis[$i],'edit').'</textarea><br />';
						$content .= $form_content[$this_is.$key];
					} else if (in_array("$this_is$key",$enumYN_array)) {
						$form_content[$this_is.$key] = '<label for="'.$this_is.ucfirst($key)
							.'">'.ucfirst(${$key."String"}).'</label> <!--<br />-->'
							.'<select name="'.$this_is.ucfirst($key).'">'.gen_selectoption(
							array('N','Y'),$editthis[$i],'','').'</select><br />';
						$content .= $form_content[$this_is.$key];
					} else if (in_array("$this_is$key",$enumtype_array)) {
						$form_content[$this_is.$key] ='<label for="'.$this_is.ucfirst($key)
							.'">'.ucfirst(${$key."String"}).'</label> <select name="'.$this_is
							.ucfirst($key).'">'.gen_selectoption($tblenum,$editthis[$i],'',
							$this_is.$key).'</select>'.(!stristr($_SERVER['PHP_SELF'],
							$urladmin)&&($user_can_add_types===false)?'':' <input name="new_'
							.$this_is.ucfirst($key).'" value="" /> ('.$ajouterString.')')
							.'<br />';
						$content .= $form_content[$this_is.$key];
					} else if (in_array("$this_is$key",$int3_array)) {
						if (isset($this_id)) {
							$whereq = "WHERE $key$this_is='$this_id' AND ".$key."lang='$lg' ";
							$sql = @mysql_query("SELECT * FROM ".${"tbl".$key}." $whereq ");
							$form_content[$this_is.$key] = '<label>'.ucfirst(${$key."String"})
								.'</label>: '.@mysql_num_rows($sql).' '.$class_conjugaison->
								plural($enregistrementString,'',@mysql_num_rows($sql))
								.'<br />';
							while($row = @mysql_fetch_array($sql)) {
								$form_content[$this_is.$key] .= $row[$key."title"]
									.' <a href="'.$mainurl.(stristr($_SERVER['PHP_SELF'],
									$urladmin)?"$urladmin?lg=$lg&amp;send=edit&amp;x=z&amp;'
									.'y=$key&amp;".$key."Id=".$row[$key.(isset($row[$key
									."rid"])?'r':'')."id"]:lgx2readable($lg,'',$key,
									$row[$key.(isset($row[$key."rid"])?'r':'')."id"])).'">'
									.$modifierString.'</a><br />';
							}
							$content .= $form_content[$this_is.$key];
						}
					} else if (in_array("$this_is$key",$datetime_array)) {
						$javascript .= '<script>'.$CRLF.'var cal'.$i
							.' = new CalendarPopup(\'calpop'.$i.'\');  cal'.$i
							.'.setWeekStartDay(1); cal'.$i.'.autoHide();'
							.$CRLF.'</script>';
						$form_content[$this_is.$key] = '<label for="'.$this_is.ucfirst($key)
							.'">'.ucfirst(${$key."String"}).'</label> '.$CRLF
							.'<input type="text" name="'.$this_is.ucfirst($key).'" value="'
							.($editthis[$i]!=''?edit_date($editthis[$i]):'DD/MM/YYYY')
							.'" size="12" /> <div class="cal_sml"><a class="toggle" '
							.'onclick="cal'.$i.'.select(document.forms[\'f'.$x.'\'].'
							.$this_is.ucfirst($key).',\'anchor'.$i.'\',\'dd/MM/yyyy\'); '
							.'return false;" name="anchor'.$i.'" id="anchor'.$i.'">'
							.$calendrierString.'</a></div><div id="calpop'.$i.'" '
							.'class="calpop" style="visibility:hidden;">&nbsp;</div><br />';
						$content .= $form_content[$this_is.$key];
					} else {
						//  if (isset(${$this_is.ucfirst($key)}))
						if (@file_exists($getcwd.$up.$safedir.'_extra_routines.php'))
						require $getcwd.$up.$safedir.'_extra_routines.php';
						if (!isset($array_routines) || !isset($form_content[$this_is.$key])
							|| (isset($array_routines) && !in_array($key,$array_routines))
						) {
							$form_content[$this_is.$key] = '<label for="'.$this_is
								.ucfirst($key).'">'.ucfirst(${$key."String"})
								.'</label><br /><input name="'.$this_is.ucfirst($key)
								.'" type="text" value="'.$editthis[$i].'" /><br />';
						}
						$content .= $form_content[$this_is.$key];
					}
				}
				/////////////
			}
		}
		if ($moderate === true)
		include $getcwd.$up.$urladmin.'_moderator_captcha.php';

		// to study because this_is is that_is if that_is is set but it works for membre...
		if (isset($tblhtaccess) && in_array($this_is,$array_modules) && !in_array($this_is,$array_modules_as_form)) {
			if ($send == 'edit')
			$gethtaccess = sql_get($tblhtaccess,"WHERE htaccessitem='$this_id'
													AND htaccesstype='$this_is'
													AND htaccesslang='$lg'
													ORDER BY htaccessdate DESC ",
												"htaccessmetadesc,htaccessmetakeyw");
			else
			$gethtaccess = array('','');
			if ($gethtaccess[0] == '.') $gethtaccess[0] = '';
			if (!isset($suppress_metas) || ($suppress_metas === false)) {
				$form_content[$this_is.$key] = '<hr style="float:left;" />'
					.'<div id="offerhtaccess" style="display:block;"><a class="toggle" '
					.'onclick="javascript:getelbyid(\'htaccessfields\').style.display='
					.'\'block\';getelbyid(\'offerhtaccess\').style.display=\'none\';">'
					.$ajouterString.' '.$metadescString.' &amp; '.$metakeywString
					.'</a></div><div id="htaccessfields" style="display:none;">'
					.strtoupper($metadescString.' &amp; '.$metakeywString)
					.'<br /><label for="'.$this_is.'Metadesc">'.ucfirst($metadescString)
					.'</label><br /><textarea name="'.$this_is
					.'Metadesc" rows="3" cols="40" style="width:97%;height:50px;'
					.'min-height:50px;">'.(isset(${$this_is."Metadesc"})?${$this_is
					."Metadesc"}:($gethtaccess[0]==''?$desc:$gethtaccess[0]))
					.'</textarea><br /><label for="'.$this_is.'Metakeyw">'
					.ucfirst($metakeywString).'</label><br /><textarea name="'.$this_is
					.'Metakeyw" rows="3" cols="40" style="width:97%;height:50px;'
					.'min-height:50px;">'.(isset(${$this_is."Metakeyw"})?${$this_is
					."Metakeyw"}:($gethtaccess[1]==''?$keyw:$gethtaccess[1]))
					.'</textarea></div>';
				$content .= $form_content[$this_is.$key];
			}
		}
		$this_is = $old_this;
		$dbtable = $old_dbtable;

		if ($error != '')
		if ($send == $envoyerString) $send = 'new';
		else $send = 'edit';
		$form_content['_submit'] = '<input name="send" type="submit" value="'.($send=='new'?
			$envoyerString:$sauverString).'" />'.(stristr($_SERVER['PHP_SELF'],$urladmin)&&(
			sql_getone($tbladmin,"WHERE adminpriv LIKE '%0%' LIMIT 1 ","adminutil") ==
			$admin_name)&&($send!='new')?' | <input name="send" type="submit" value="'
			.$envoyerString.'" />':'')
			.(!isset($form_noreset)?' | <input type="reset" value="Reset" />':'');
		$content .= '<br />'.$form_content['_submit'].'</form>'.'</td></tr></table>';
#######################################################Delete
	} else if ($send == 'delete') {
		if (isset(${$this_is."Id"}) && preg_match("/^[0-9]+\$/",${$this_is."Id"})) {
			${$this_is."Id"} = strip_tags(${$this_is."Id"});
			if (isset($commentId) && !preg_match("/^[0-9]+\$/",$commentId))
			{Header("Location: $redirect");Die();}
			$old_this = $this_is;
			$error_img = '';
			$notice_img = '';
			foreach($editthis as $key => $value) {
				if (($key != '0') &&
					(($key == $this_is."comment")
					|| (isset($that_is) && ($key == $that_is."comment")) ||
						(($key == $this_is."doc")
						|| (isset($that_is) && ($key == $that_is."doc")) ||
						($key == $this_is."img")
						|| (isset($that_is) && ($key == $that_is."img")))
					)) {
					if ($value != "") {
						$deletequery = 0;
						if (($key == $this_is."comment")
							|| (isset($that_is) && ($key == $that_is."comment")))
						$this_is = substr($key,0,-(strlen('comment')));
						else
						$this_is = substr($key,0,-3);
						if ($old_this!=$this_is)
						${$this_is."Id"} = ${$old_this."Id"};
						if (substr($key,-(strlen('comment'))) == "comment") {
							$deletequery = sql_del($tblcomment,"WHERE ".(isset($commentId)?" commentrid='$commentId' AND ":'')." comment".$this_is."='".${$this_is."Id"}."' ");
							if ($deletequery > 0)
							$error .= $commentString.(isset($commentId)?' (id#'.$commentId.')':'').' '.$nonString.' '.$effaceString.'!<br />';
							else
							$notice .= $commentString.(isset($commentId)?' (id#'.$commentId.')':'').' '.$effaceString.'!<br />';
						} else { // doc or img
							if (substr($key,-3) == "doc")
							$deletequery = sql_del($tblcontdoc,"WHERE contdoc='$value' ");
							if (substr($key,-3) == "img")
							$deletequery = sql_del($tblcontphoto,"WHERE contphotoimg='$value' ");
							//	$deletequery = sql_del($tblcontphoto, " WHERE contphotoid='$contphotoId' ");
							if ($deletequery > 0) {
								$error_img .= $error_img_request.': '.($key==$this_is."img"?$photoString:$documentString).' '.$nonString.' '.$effaceString;//.'<p><a href="javascript:history.back()//">'.$retourString.'</a></p>'; // uncomment if form is not included afterwards and redirect is fixed so you can see the fields
							} else {
								$newvalue_imgdoc = '';
								if ($array_fields_type[$key] == 'text') {
									$value = explode("|",$value);
									if (!is_array($value))
									$value = array($value);
								} else {
									if (substr($key,-3) == "doc")
									$value = sql_array($tblcontdoc,"WHERE contdoccontid='".${$this_is."Id"}."' ","contdoc");
									if (substr($key,-3) == "img")
									$value = sql_array($tblcontphoto,"WHERE contphotocontid='".${$this_is."Id"}."' ORDER BY contphotosort,contphotoid ASC ","contphotoimg");
									if (!is_array($value))
									$value = array($value);
								}
								$del_nof = count($value);

								if (!isset($value[1]) || ($del_nof != $nof))
								for($inof=0;$inof<$nof;$inof++)
								$value[] = (isset($value[$inof])?$value[$inof]:'');

								for($ii=0;$ii<$del_nof;$ii++) {
									if (isset(${$this_is."Img"}) || isset(${$this_is."Doc"}) || (isset($that_is) && (isset(${$that_is."Img"}) || isset(${$that_is."Doc"})))) {
										if ((isset(${$this_is."Img"}) && (${$this_is."Img"} == $value[$ii])) || (isset(${$this_is."Doc"}) && (${$this_is."Doc"} == $value[$ii])) || (isset($that_is) && ((isset(${$that_is."Img"}) && (${$that_is."Img"} == $value[$ii])) || (isset(${$that_is."Doc"})) && (${$that_is."Img"} == $value[$ii])))) {
											if (delete_imgdoc($this_is,$value[$ii]) === true) {
												if (substr($key,-3) == "doc")
												$deletequery = sql_del($tblcontdoc,"WHERE contdoc='".$value[$ii]."' ");
												if (substr($key,-3) == "img")
												$deletequery = sql_del($tblcontphoto,"WHERE contphotoimg='".$value[$ii]."' ");
												$notice_img .= " | ".$value[$ii]." ; ".($key==$this_is."img"?$photoString:$documentString).' '.$effaceString.'!<br />';
												$newvalue_imgdoc .= ($ii>0?"|":'');
											} else {
												$error_img .= " | ".$value[$ii]." ; ".($key==$this_is."img"?$photoString:$documentString).' '.$nonString.' '.$effaceString.'!<br />';
												$newvalue_imgdoc .= ($ii>0?"|":'').$value[$ii];
											}
										} else {
											$newvalue_imgdoc .= ($ii>0?"|":'').$value[$ii];
											//		echo $value[$ii].' = bump<br/>';
											//Header("Location: $redirect");Die();
										}
									} else {
										if (delete_imgdoc($this_is,$value[$ii]) === true) {
											if (substr($key,-3) == "doc")
											$deletequery = sql_del($tblcontdoc,"WHERE contdoc='".$value[$ii]."' ");
											if (substr($key,-3) == "img")
											$deletequery = sql_del($tblcontphoto,"WHERE contphotoimg='".$value[$ii]."' ");
											$notice_img .= " | ".($key==$this_is."img"?$photoString:$documentString).' '.$effaceString.'!'.($deletequery>0?' not [d]':' [d]').'<br />';
											$newvalue_imgdoc .= ($ii>0?"|":'');
										} else {
											$error_img .= " | ".$value[$ii]." ; ".($key==$this_is."img"?$photoString:$documentString).' '.$nonString.' '.$effaceString.'!<br />';
											$newvalue_imgdoc .= ($ii>0?"|":'').$value[$ii];
										}
									}
								}
								if ($array_fields_type[$key] == 'text') {
									$updatequery = sql_update(${"tbl".$this_is},"SET $key='$newvalue_imgdoc' ","WHERE ".$this_is.($this_id_rid===true?'r':'')."id='".${$this_is."Id"}."' ","$key");
									if ($updatequery[0]!='.') {
										$notice_img .= ($key==$this_is."img"?$photoString:$documentString).' '.$modifieString.' [u]<br />';
									} else {
										$error_img .= " | ".$error_img_request.': '.($key==$this_is."img"?$photoString:$documentString).' '.$nonString.' '.$modifieString.' [u]<br />';//.'<p><a href="javascript:history.back()//">'.$retourString.'</a></p>'; // uncomment if form is not included afterwards and redirect is fixed so you can see the fields
									}
								}
							}
						}
					}
				} else {
				}
			}
			if (isset(${$this_is."Img"}) || isset(${$this_is."Doc"}) ||
					(isset($that_is) && (isset(${$that_is."Img"}) || isset(${$that_is."Doc"}))) ||
					isset($commentId)) {
				$_SESSION['mv_error'] = $error.$error_img;
				$_SESSION['mv_notice'] = $notice.$notice_img;
				Header("Location: ".html_entity_decode($local_url));
				Die();
			} else {
				if (sql_del($dbtable,"WHERE ".$old_this.(in_array($old_this."rid",$array_fields)?"r":'')."id='".${$this_is."Id"}."' ") > 0)
				$error .= $error_request.' 1'.'<span style="display:none;"><br />'.mvtrace(__FILE__,__LINE__).'</span>';//.'<p><a href="javascript:history.back()//">'.$retourString.'</a></p>'; // uncomment if form is not included afterwards and redirect is fixed so you can see the fields
				else {
					$notice .= '<b>'.${$old_this."String"}.': '.$enregistrementString.' '.$effaceString.'</b>';//<br /><a href="'.$local_url.'">'.$verslisteString.' '.${$this_is."String"}.'</a><br />'; // uncomment if form is not included afterwards and redirect is fixed so you can see the fields
					if (isset($tblhtaccess) && in_array($this_is,$array_modules))
					if (sql_del($tblhtaccess,"WHERE htaccessitem='".${$this_is."Id"}."' AND htaccesstype='$this_is'  ")==0)
					$notice .= ' | <b>htaccess: '.$enregistrementString.' '.$effaceString.'</b>';
					if (isset($that_is)) {
						if (sql_del($that_dbtable,"WHERE ".$that_is.($that_id_rid===true?"r":'')."id='".${$this_is."Id"}."' ") > 0)
						$error .= $error_request.' 2';//.'<p><a href="javascript:history.back()//">'.$retourString.'</a></p>'; // uncomment if form is not included afterwards and redirect is fixed so you can see the fields
						else {
							if (isset($tblhtaccess) && in_array($that_is,$array_modules))
							if (sql_del($tblhtaccess,"WHERE htaccessitem='".${$that_is."Id"}."' AND htaccesstype='$that_is'  ")==0)
							$notice .= ' | <b>htaccess: '.$enregistrementString.' '.$effaceString.'</b>';
						}
					}
				}
				$error .= $error_img;
				$notice .= $notice_img;
				if ($error == '') {
					$_SESSION['mv_notice'] = $notice;
					if (in_array($this_is,$editable_by_membre) && !stristr($_SERVER['PHP_SELF'],$urladmin) && ($logged_in === true) && isset(${$this_is."Id"}))
					Header("Location: ".html_entity_decode($mainurl.lgx2readable($lg,'',$this_is,'')));
					else
					Header("Location: ".html_entity_decode($local_url));Die();
				}
			}
		} else {
			Header("Location: $redirect");Die();
		}
	} else {
		Header("Location: $redirect");Die();
	}
#######################################################show
} else {
	if (!in_array('1',$admin_priv)) {
		if (in_array($this_is,$array_csv_list))//($admin_viewing === true) &&
		$content .= ' <a href="csvliste.php?action=list&amp;what='.$this_is.'" target="_blank"><img src="'.$mainurl.'images/xlslogo.gif" width="16" height="16" title="CSV" alt="CSV" align="right" border="0" /></a>';
		$content .= '<a href="'.$local_url.'&amp;send=new" target="_self"><img src="'.$mainurl.'images/card_f2.png" '.show_img_attr($mainurl.'images/card_f2.png').' title="'.$ajoutString.' '.${$this_is."String"}.'" alt="'.$ajoutString.' '.${$this_is."String"}.'" align="left" border="0" /> '.$ajoutString.' </a>';
	}
	// lists the registered items
	if ( (!isset($statut)) || ($statut != 'tout') || ($statut != 'Y') || ($statut != 'N') ||
			(!isset($par)) || !in_array($par,$params_array) ||
			(!isset($ordre)) || ($ordre != 'ASC') || ($ordre != 'DESC')
			) {
		if	(!isset($statut))	$statut = $toutString	;
		if	(!isset($par))		$par = 'date'	;
		if	(!isset($ordre))	$ordre = 'DESC'	;
		$nextordre = ($ordre=='DESC'?'ASC':'DESC');
		if	(!isset($q))	$q = ''	;
		if	($statut == $toutString) $statutstr = $toutString	;
		if	($statut == 'Y')	$statutstr = $confirmeString	;
		if	($statut == 'N')	$statutstr = $enattenteString	;
		$selectHead = '<div class="selectHead">'.(stristr($_SERVER['PHP_SELF'],$urladmin)?gen_form($lg,$x,$y):gen_form($lg)).'<label for="statut">'.$statutString.' : </label><select name="statut">'.gen_selectoption($tblenum,$statut,'','statut','count').'</select> | <label for="par">'.$parString.' : </label><select name="par">'.gen_selectoption($params_array,$par,'','').'</select> | <label for="ordre">'.$ordreString.' : </label><select name="ordre">'.gen_selectoption(array('ASC','DESC'),$ordre,'','').'</select> || <input type="submit" value="'.$voirString.'" /><br /><label for="q">'.$rechercherString.' : </label> <input type="text" name="q" value="'.stripslashes($q).'" style="width:50%"> || <input type="submit" value="'.$rechercherString.'"></form></div>';
		$tableHead = '<table align="center" class="tableHead" border="1" cellspacing="2" cellpadding="2"><tr><th>'.$infodbString.'</th><th>'.$paramsString.' '.${$this_is."String"}.'</th>'.(isset($that_is)?'<th>'.$paramsString.' '.${$that_is."String"}.'</th>':'').'<th>'.$optionsString.'</th></tr><tr><td>';
		if (isset($that_is) && isset($array_mandatory_fields))
		$params_array = array_unique(array_merge($params_array,$array_mandatory_fields));
		for($i=0;$i<count($basic_array);$i++)
		if (isset($basic_array[$i]) && ($basic_array[$i] != 'lang') && ($basic_array[$i] != ''))
		$tableHead .= (in_array($basic_array[$i],$array_mandatory_fields)?'<b>'.${$basic_array[$i]."String"}.'</b>':'<a href="'.$local_url.'&amp;statut='.$statut.'&amp;par='.$basic_array[$i].'&ordre='.$nextordre.'">'.${$basic_array[$i]."String"}.'</a>').'<br />';
		for($i=0;$i<count($array_fields);$i++) {
			if (substr($array_fields[$i],0,strlen($dbtable)-1) != $this_is) {
				if ($this_is == $old_this) {
					$content .= '</td><td>';
					$tableHead .= '</td><td>';
				}
				$this_is = $that_is;
				$dbtable = $that_dbtable;
			}
			$key = substr($array_fields[$i],strlen($dbtable)-1);
			if (($i==count($basic_array)) || (isset($array_mandatory_fields[0]) && ($key == $array_mandatory_fields[0]) && !in_array($key,$basic_array)))
			$tableHead .= '</td><td>';
			if (!in_array($key,$basic_array) && in_array($key,$params_array)) {
				if (isset($key) && ($key != 'lang') && ($key != '')) {
					$tableHead .= (in_array($key,$array_mandatory_fields)?'<b>'.ucfirst(${$key."String"}).'</b>':'<a href="'.$local_url.'&amp;statut='.$statut.'&amp;par='.$key.'&amp;ordre='.$nextordre.'">'.ucfirst(${$key."String"}).'</a>');//.'<br />';
					if (!in_array($key,array("gendre","prenom")))
					$tableHead .= '<br />';
					else
					$tableHead .= ' ';
				}
			}
		}
		$tableHead .= '</td><td>&nbsp;</td></tr>';
		$this_is = $old_this;// reinstate this_is if that_is was processed
		$dbtable = $old_dbtable;
		if (isset($that_is)) {
			$that_is = $old_that;
			$that_dbtable = $old_thatdbtable;
			$dbtable .= ",$that_dbtable";
		}
		$statutList = "";
		if	($statut != $toutString)	$statutList = " WHERE ".$this_is."statut='$statut' "	;
		if	(isset($q))	$q = strip_tags(stripslashes(html_encode($q)))	;
		if	(($q != '') && !preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/",$q))	$statutList .= ($statutList==''?"WHERE ":" AND ").${$this_is.'_referrerfield'}." LIKE '%$q%' ";
		$statutList .= (in_array($this_is."lang",$array_fields)?($statutList==''?"WHERE ":" AND ")." ".$this_is."lang='$lg' ":'');
		if (isset($that_is))
		$statutList .= ($statutList==''?"WHERE ":" AND ").(in_array($that_is."lang",$that_array_fields)?$this_is.($this_id_rid===true?'r':'')."id=".$that_is.($that_id_rid===true?'r':'')."id AND ".$that_is."lang='$lg' ":$this_is."id=".$that_is."id ")." ";
		$fullread = @mysql_query("
						SELECT * FROM $dbtable
						$statutList
						ORDER BY ".(isset($that_is)?$that_is:$this_is)."$par $ordre
						");
		$fullnRows = @mysql_num_rows($fullread);
		$read = @mysql_query("
					SELECT * FROM $dbtable
					$statutList
					ORDER BY ".(isset($that_is)?$that_is:$this_is)."$par $ordre
					$queryLimit
					");
		$nRows = @mysql_num_rows($read);
		$totalpg = ceil( $fullnRows / $listPerpg );
		for ($i=1;$i<=$totalpg;$i++) {
			if ($i == $pg) {
				$pages .= ' <b> '.$i.' </b> ';
			} else {
				$pages .= ' <a href="'.$local_url.'&amp;statut='.$statut.'&amp;par='.$par.'&amp;ordre='.$ordre.'&amp;pg='.$i.'">'.$i.'</a> ';
			}
		}
		if (!isset(${"nRows".ucfirst($this_is)}) || (${"nRows".ucfirst($this_is)} == 0)) {
			$content .= '<p style="text-align:left;float:left;">&nbsp;0 '.$enregistrementString.' '.$surString.' '.${$this_is."String"}.'.</p>';
		} else if (in_array('1',$admin_priv)) {
			$content .= '<p style="text-align:left;">&nbsp;</p>';
		} else {
			$content .= $selectHead;
			$count_content = '';
			if (${"nRows".ucfirst($this_is)} == '1') {
				$count_content .= '<p style="text-align:left;float:left;">&nbsp;'.${"nRows".ucfirst($this_is)}.' '.$class_conjugaison->plural($enregistrementString,'M',${"nRows".ucfirst($this_is)}).' '.$surString.' '.${$this_is."String"}.' '.(${"nRows".ucfirst($this_is)."y"} == '1'?$confirmeString:$enattenteString).'</p>';
			} else {
				$count_content .= '<p style="text-align:left;float:left;">&nbsp;<a href="'.$local_url.'&amp;statut='.$toutString.'&amp;par='.$par.'&amp;ordre='.$ordre.'">'.${"nRows".ucfirst($this_is)}.' '.$class_conjugaison->plural(${$this_is."String"},'M',${"nRows".ucfirst($this_is)}).' '.$class_conjugaison->plural($enregistreString,'M',${"nRows".ucfirst($this_is)}).'</a>, <a href="'.$local_url.'&amp;statut=Y&par='.$par.'&amp;ordre='.$ordre.'">'.${"nRows".ucfirst($this_is)."y"}.' '.$class_conjugaison->plural($confirmeString,'M',${"nRows".ucfirst($this_is)."y"}).'</a>, <a href="'.$local_url.'&amp;statut=N&amp;par='.$par.'&amp;ordre='.$ordre.'">'.${"nRows".ucfirst($this_is)."n"}.' '.$class_conjugaison->plural($enattenteString,'M',${"nRows".ucfirst($this_is)."n"}).'</a></p>';
			}
			$content .= $count_content;
			if	(isset($q) && ($q != '') && !preg_match("/^[@&!?,.:;'`~%*#§|}{°]+\$/",$q))
			$content .= '<p style="text-align:left;float:left;">&nbsp;'.$fullnRows.' '.$class_conjugaison->plural($enregistrementString,'M',$fullnRows).' '.$surString.' '.${$this_is."String"}.' ('.${substr(${$this_is.'_referrerfield'},strlen($this_is))."String"}.') '.$pourString.': <i>"'.$q.'"</i></p>';
			if	($listPerpg < $fullnRows)
			$content .= $pages.' << &nbsp;</p>'	;
			if ($nRows > 0)
			$content .= $tableHead;
			if ($read)
			while($row = mysql_fetch_array($read)) {
				$this_is = $old_this;// reinstate this_is if that_is was processed
				$dbtable = $old_dbtable;
				if (isset($that_is) && ($that_is == $this_is)) {
					$that_is = $old_that;
					$that_dbtable = $old_thatdbtable;
				}
				$content .= '<tr><td>'.$row[$old_this.$params_array[0]].'<br />'.$row[$old_this.$params_array[1]].'<br />'.sql_stringit('statut',$row[$old_this.$params_array[2]]).($this_id_rid===true?'<br />'.$row[$old_this.$params_array[4]]:'').'</td><td>';
				for($i=0;$i<count($array_fields);$i++) {
					if (substr($array_fields[$i],0,strlen($dbtable)-1) != $this_is) {
						if ($this_is == $old_this)
						$content .= '</td><td>';
						$this_is = $that_is;
						$dbtable = $that_dbtable;
					}
					$key = substr($array_fields[$i],strlen($dbtable)-1);
					if (!in_array($key,$basic_array) && in_array($key,$params_array)) {
						if ($key == 'priv') {
							if ($this_is=="admin") {
								$content .= ($row[$this_is.$key]=='1'?$pageString:$toutString);
							} else {
								$walk_keys = explode("|",$row[$this_is.$key]);
								foreach($walk_keys as $key)
								$content .= ($key!==$walk_keys[0]?", ":'').sql_stringit('privilege',$key);
							}
							$content .= '<br />';
							//  } else if (($key == 'resp') && isset(${$key."_con"})) {
						} else if ($key == 'entry') {
							$content .= strip_tags(substr($row[$this_is.$key],0,100));
						} else if (($key == 'type') || in_array($this_is.$key,$enumtype_array)) {
							$content .= '<b>'.sql_stringit($this_is.$key,$row[$this_is.$key]).'</b><br />';
						} else if (in_array($key,$array_modules) || in_array($key,array("gendre","prenom","nom"))) {// && isset(${$key."_con"})) {
							if (in_array($key,$array_modules)) {
								$content .= '<a href="'.$local.'?lg='.$lg.'&amp;send=edit&amp;x='.$x.'&amp;y='.(isset(${$key."_con_y"})?${$key."_con_y"}:$key).'&amp;'.$key."Id".'='.$row[$this_is.$key].'" target="_self">';
								$this_getitem = sql_get(${"tbl".$key}," WHERE ".(in_array($key."rid",sql_fields(${"tbl".$key},'array'))?$key."rid='".$row[$this_is.$key]."' AND ".$key."lg='$lg' ":$key."id='".$row[$this_is.$key]."' "),"*");
								if (isset($this_getitem[$key."title"])) {
									$content .= $this_getitem[$key."title"];
								} else {
									if (isset($this_getitem[$key."gendre"]))
									$content .= sql_stringit('gendre',$this_getitem[$key."gendre"]).' '.$this_getitem[$key."prenom"].' '.$this_getitem[$key."nom"];
								}
								$content .= '</a><br />';
							} else {
								//  $content .= '<a href="'.$local.'?lg='.$lg.'&amp;send=edit&amp;x='.$x.'&amp;y='.$this_is.'&amp;'.$this_is."Id".'='.$row[$this_is.($this_id_rid===true?'r':'')."id"].'" target="_self">';
								if (($key=="gendre") || (!isset($row[$this_is."gendre"]) && in_array($key,array("prenom","nom")))) {
									$content .= '<a href="'.$local.'?lg='.$lg.'&amp;x='.sql_getone($tblcont,"WHERE $where_statut_lang conttype='$this_is' ","contpg").'&amp;y='.$this_is.'&amp;'.$this_is."Id".'='.$row[$this_is.($this_id_rid===true?'r':'')."id"].'" target="_self">';
									$content .= (isset($row[$this_is."gendre"])?sql_stringit('gendre',$row[$this_is."gendre"]):'').(isset($row[$this_is."prenom"])?' '.$row[$this_is."prenom"]:'').(isset($row[$this_is."nom"])?' '.$row[$this_is."nom"]:'').'</a><br />';
								}
							}
						} else {
							if (in_array($this_is.$key,$datetime_array))
							$row[$this_is.$key] = human_date($row[$this_is.$key]);
							$content .= (($key=='website')||($key=='email')?'<a href="'.($key=='email'?'mailto:':'').$row[$this_is.$key].'" target="_new">'.$row[$this_is.$key].'</a>':($key=='gendre'?sql_stringit('gendre',$row[$this_is.$key]):(isset($row[$this_is.$key])?$row[$this_is.$key]:'')));
							if (!in_array($key,array("gendre","prenom"))) // no line feed
							$content .= '<br />';
							else
							$content .= ' ';
						}
					}
				}
				$type_page = sql_getone($tblcont,"WHERE $where_statut_lang conttype='$this_is' ","contpg");
				$_params_array_0 = (in_array('rid',$params_array)?$params_array[array_search('rid',$params_array)]:$params_array[0]);
				$content .= '&nbsp;</td><td><a href="'.$local_url.'&amp;send=delete&amp;'.$this_is.'Id='.$row[$this_is.$_params_array_0].'" onclick="return confirm(\''.$confirmationeffacementString.'\');"><img src="'.$mainurl.'images/delete.gif" width="10" height="10" align="right" title="'.$effacerString.'" alt="'.$effacerString.'" border="0" /></a>'.(in_array($this_is.'archive',$array_fields)?'<a href="'.$local_url.(isset($send)?'&amp;send='.$send:'').'&amp;'.$this_is.'Id='.$row[$this_is.$_params_array_0].'&amp;toggle=archive" onclick="return confirm(\''.($row[$this_is."archive"]=='Y'?$nonString.' '.$archiveString.' ?\');"><img src="'.$mainurl.'images/archive_y.png" width="24" height="24" border="0" title="'.$archivedString.'" alt="'.$archivedString:$archiveString.' ?\');"><img src="'.$mainurl.'images/archive_n.png" width="24" height="24" border="0" title="'.$nonString.' '.$archivedString.'" alt="'.$nonString.' '.$archivedString).'" align="right" /></a>':'').(in_array($this_is.'publish',$array_fields)?'<a href="'.$local_url.(isset($send)?'&amp;send='.$send:'').'&amp;'.$this_is.'Id='.$row[$this_is.$_params_array_0].'&amp;toggle=publish" onclick="return confirm(\''.($row[$this_is."publish"]=='Y'?$nonString.' '.$publishString.' ?\');"><img src="'.$mainurl.'images/publish_y.png" width="12" height="12" border="0" title="'.$publishedString.'" alt="'.$publishedString:$publishString.' ?\');"><img src="'.$mainurl.'images/publish_n.png" width="12" height="12" border="0" title="'.$nonString.' '.$publishedString.'" alt="'.$nonString.' '.$publishedString).'" align="right" /></a>':'').'<br />'.($type_page!=''?'<a href="'.$local.'?lg='.$lg.'&amp;x='.sql_getone($tblcont,"WHERE $where_statut_lang conttype='$this_is' ","contpg").'&amp;y='.$this_is.'&amp;'.$this_is."Id".'='.$row[$this_is.$_params_array_0].'" target="_self">'.$ouvrirString.'</a><br />':'').'<a href="'.$local_url.'&amp;send=edit&amp;'.$this_is.'Id='.$row[$this_is.$_params_array_0].'">'.$modifierString.'</a><br /><div style="float:right;"><a href="'.$local_url.'&amp;toggle=1&amp;'.$this_is.'Id='.$row[$this_is.$_params_array_0].'">'.($row[$this_is.$params_array[2]]=='Y'?$mettreenattenteString:$confirmerString).'</a></div></td></tr>';//.($this_is=='admin'?'&amp;'.$this_is.'Util='.$row[$this_is.$params_array[4]]:'')
				if (isset($that_is)) // reset this and that
				if (isset($row[$that_is.$key]) && ($i == count($params_array)-1)) {
					//  $content .= '<br />voila';
					$this_is = $old_this;
					$dbtable = $old_dbtable;
					$that_is = $old_that;
					$that_dbtable = $old_thatdbtable;
				}
			}
			$content .= '</table><br />';
			if	($listPerpg < $fullnRows)
			$content .= $pages.' << &nbsp;</p>'	;
			$content .= $count_content;
		}
	} else {
		Header("Location: $redirect");Die();
	}
}
if (stristr($_SERVER['PHP_SELF'],$urladmin))
$content .= '</div>';
/*
echo (isset($notice)&&($notice!='')?'<textarea rows="10" cols="300">'.$notice.'</textarea>':'');
echo (isset($error)&&($error!='')?'<textarea rows="10" cols="300">'.$error.'</textarea>':'');
*/
