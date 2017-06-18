<?php #۞ #
if (stristr($_SERVER['PHP_SELF'], basename(__FILE__))){include '_security.php';Header("Location: $redirect");Die();}

if (isset(${$this_is."Id"}) && !preg_match("/^[0-9]+\$/",${$this_is."Id"}))
{Header("Location: $redirect");die();}

if (!isset($basic_array))
$basic_array = array('id','date','statut','lang','rid');
if (isset($params_array))
$basic_array = array_unique(array_merge($basic_array,$params_array));
else $params_array = $basic_array;

if (!isset($logged_in))
$logged_in = false;
if (!isset($protected_show))
$protected_show = false;
if (!isset($view_see_all_button))
$view_see_all_button = true;
if (!isset($lightbox))
$lightbox = (isset($js_lightbox)&&is_bool($js_lightbox)?$js_lightbox:false);
// when showing list of items, should only show one img
if (!isset($show_oneimg_only))
$show_oneimg_only = true;

$sql_q = '';
$q_count = 0;
if (isset($q) && (!preg_match("/^[[:alpha:]]+\$/",$q) || isset(${$this_is."Id"})))
$q = '';

$dbtable = ${"tbl".$this_is};
$array_fields = sql_fields($dbtable,'array');
if (isset($array_fields[0])) {
	$this_id_rid = (in_array( $this_is."rid" , $array_fields ));
	foreach($array_fields as $key) {
		if (!isset($filter_searchfield))
		// search only on this
		if (isset($q) && ($q != '')) {
			if (($key != $this_is."id") && ($key != $this_is."date") && ($key != $this_is."statut")) {
				$array_q = explode(" ",$q);
				if (!isset($array_q[1])) $array_q = ($sql_q!=''?" OR ":'').$key." LIKE '%".$q."%' ";
				else $array_q = implode("%' OR ".$key." LIKE '%",$array_q);
				if ($array_q != '') $sql_q .= $array_q;
			}
			$q = $q;
		}
		// end of search
		$empty_array_fields[] = ($key=='statut'?'Y':'');
		$list_array_fields = isset($list_array_fields)?$list_array_fields.','.$key:$key;
		if (isset(${"filter_".$key}))
		if (is_bool(${"filter_".$key}))
		$this_isSQL = "$this_is$key='".(${"filter_".$key}===true?'Y':'N')."' AND ";
		else
		$this_isSQL = "$this_is$key='".${"filter_".$key}."' AND ";
	}
}
if (isset($that_is)) {
	$that_dbtable = ${"tbl".$that_is};
	$that_array_fields = sql_fields($that_dbtable,'array');
	if (isset($that_array_fields[0])) {
		$that_id_rid = (in_array( $that_is."rid" , $that_array_fields ));
		foreach($that_array_fields as $key) {
			$that_empty_array_fields[] = ($key=='statut'?'Y':'');
			$that_list_array_fields = isset($that_list_array_fields)?$that_list_array_fields.','.$key:$key;
			if (isset(${"filter_".$key}))
			if (is_bool(${"filter_".$key}))
			$that_isSQL = "$that_is$key='".(${"filter_".$key}===true?'Y':'N')."' AND ";
			else
			$that_isSQL = "$that_is$key='".${"filter_".$key}."' AND ";
		}
	}
}

if ($sql_q != '') $sql_q = " AND ( ".$sql_q." ) ";

$local_url = $local.substr($local_uri,1).(isset($q)?'?q='.$q:'?');
if ($admin_viewing === true) {
	$full_url = false;
	$local_url = $local.'?lg='.$lg.'&amp;x='.$x;//.'&amp;y='.$this_is;
	$local_url .= (isset($q)&&($q!='')?'&amp;q='.$q:'');
}

//if (!isset($mediumtext_array)) {// check if needed elsewhere, was deleted because of index filter loop
$array_fields_type = array();//lists all types for a given table
$mediumtext_array = array(); // textarea no formatting: eg meta desc & keyw
$longtext_array = array(); // textarea with tinyMCE: word style UI
$enumYN_array = array(); // either Y or NO: produces selectable option code
$enumtype_array = array();  // int(11) unsigned, produces selectable code for all possibilities taken from enum and assign string,
							// then allows creation of new type, further options apply like - for deleting the selected item
$int3_array = array(); // int(3) unsigned, flag for fetching items from referenced table
$datetime_array = array(); // datetime, flag for showing calendar
$result = @mysql_query("SHOW FIELDS FROM $dbtable");
if (!$result) {
	if ($admin_viewing === true) {
		$_SESSION['mv_error'] = $error_inv." module ".$this_is;
		Header("Location: ".html_entity_decode($local_url)."&send=edit");Die();
	} else {
		Header("Location: $redirect");Die();
	}
} // no table or no connection...
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
if (isset($that_is)) {
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
}
//}// see above

$_mod_content = '';
if (($logged_in === true) && isset($send) && in_array($this_is,$editable_by_membre)
	&& (lgx2readable($lg,$x) == lgx2readable($lg,'',$this_is)) && ($admin_viewing === false)
) {
	$edit_text = true;
	include $getcwd.$up.$urladmin.'itemadmin.php';
} else {

	if (!isset($filter_map) && !isset($filter_index))
	$_mod_content .= '<hr /><div class="generic_wrapper"><div class="centered">';

	//if ($admin_viewing === false)
	if ((isset($rqst) || isset($toggle)) && isset(${$this_is."Id"})) {
		${$this_is."Id"} = strip_tags(${$this_is."Id"});
		$rqst = (isset($rqst)?strip_tags($rqst):'');
		if ((isset($toggle) || ($rqst == 'conf') || ($rqst == 'canc'))
			&& preg_match("/^[0-9]+\$/",${$this_is."Id"}) && (${$this_is."Id"} !== '')
		) {
			if ($this_is == 'admin') {
				if (${$this_is."Id"} == $admin_id) {
					$error = $error_invmiss.' => '.$sortirString.' !';
				} else if (${"nRows".ucfirst($this_is)} == 1) {
					$error = $error_invmiss.' => <b>'.${"nRows".ucfirst($this_is)}.'</b> '
						.$class_conjugaison->plural($enregistrementString,'M',${"nRows".ucfirst($this_is)})
						.' '.$surString.' '.${$this_is."String"}.' !';
				}
			}
			if ($error=='') {
				foreach($array_fields as $k_check) {
					$field_check = substr($k_check,strlen($this_is));
					if (isset(${$field_check."Id"})) {
						${$field_check."Id"} = strip_tags(${$field_check."Id"});
						$notice .= '<b>'.$enregistrementString.' '.$pourString.' '.$field_check.' '.$class_conjugaison->plural(
							(mv_toggle(${"tbl".$field_check},
								$field_check.(in_array($field_check.$toggle,$array_fields)?$toggle:"statut"),
								" ".$field_check.(in_array($field_check."rid",sql_fields(${"tbl".$field_check},'array'))?'r':'')
								."id='".${$field_check."Id"}."' AND ".$field_check.$this_is."='".${$this_is."Id"}."' "
							)=='Y'?$confirmeString:$enattenteString),'M',1).'</b>';
						$found_check_for_toggle = true;
						break;
					}
				}
				if (!isset($found_check_for_toggle))
				$notice .= '<b>'.$enregistrementString.' '.$pourString.' '.$this_is.' '.$class_conjugaison->plural(
					(mv_toggle($dbtable,
						$this_is.($toggle!=''?$toggle:"statut"),
						" ".$this_is.($this_id_rid===true?'r':'')."id='".${$this_is."Id"}."' "
					)=='Y'?$confirmeString:$enattenteString),'M',1).'</b>';
				if (isset($that_is) && isset(${"connected_byid_".$this_is."_".$that_is}))
				$notice .= '<br /><b>'.$enregistrementString.' '.$pourString.' '.$that_is.' '.$class_conjugaison->plural(
					(mv_toggle($that_dbtable,
						$that_is.($toggle!=''?$toggle:"statut"),
						" ".$that_is.($that_id_rid===true?'r':'')."id='".${$that_is."Id"}."' "
					)=='Y'?$confirmeString:$enattenteString),'M',1).'</b>';
				$_SESSION['mv_notice'] = $notice;
				/*
				${"nRows".ucfirst($this_is)} = sql_nrows($dbtable,"");
				${"nRows".ucfirst($this_is)."y"} = sql_nrows($dbtable," WHERE ".$this_is."statut='Y' ");
				${"nRows".ucfirst($this_is)."n"} = sql_nrows($dbtable," WHERE ".$this_is."statut='N' ");
				*/
			} else {
				$_SESSION['mv_error'] = $error;
			}
			Header("Location: ".html_entity_decode(
				$local_url.'&amp;'.$this_is.'Id='.${$this_is."Id"}
				.(isset(${$field_check."Id"})?'&amp;'.$field_check.'Id='.${$field_check."Id"}:'')
				.(isset($send)?'&amp;send='.$send:'')
			));Die();
		} else
			{Header("Location: $redirect");Die();}
	}

	if (isset(${$this_is."Id"}) && preg_match("/^[0-9]+\$/",${$this_is."Id"})) {

		//	if (sql_nrows($dbtable,"WHERE ".$this_is."lang='$lg' AND ".$this_is."rid='".${$this_is."Id"}."' ") == 0)
		//echo ${$this_is."Id"}." $dbtable,"."WHERE ".$this_is."id='".${$this_is."Id"}."' ";


		if (sql_nrows($dbtable,"WHERE ".$this_is."id='".${$this_is."Id"}."' ") == 0)//($this_id_rid===true?'r':'').
		{Header("Location: $redirect");die();}

		if (!isset($array_sortable))
		$array_sortable = array('gallery');
		if (in_array($this_is,$array_sortable) && ($admin_viewing === true) && !isset($jquery_sort_included)) {
			// ORDER BY contphotosort,contphotoid ASC
			$jquery_sort_included = true;
		}

		$show_id = ${$this_is."Id"};
		if (($lg != $default_lg) && $this_id_rid===true)
		$show_id = sql_getone(${"tbl".$this_is},"WHERE ".$this_is."id='$show_id' ",$this_is."rid");
		$current = sql_get(${"tbl".$this_is},"WHERE ".$this_is.($this_id_rid===true?'r':'')."id='$show_id' ", "*");
		$get_metas = sql_get(
			$tblhtaccess,
			"WHERE htaccesstype='$this_is' AND htaccessitem='$show_id' AND htaccesslang='$lg' ORDER BY htaccessdate DESC ",
			"htaccessmetadesc,htaccessmetakeyw"
		);
		if ($get_metas[0]!='.') {
			$desc = $get_metas[0];
			$keyw = $get_metas[1];
		}
		if (isset($show_array_by_id))
		foreach($show_array_by_id as $key) {
			if (strstr($key,":")) {
				$key = explode(":",$key);
				if (isset(${"connected_byid_".$this_is."_".$key[0]}))
				{
					${"show_".$key[0]."_".$key[1]} = sql_getone(${"tbl".$key[0]},
					" WHERE ".$key[0].(in_array($key[0]."rid",sql_fields(${"tbl".$key[0]},'array'))?"lang='$lg' AND ".$key[0]."r":'')
						."id='".$current[$this_is.$key[0]]."' ",$key[0].$key[1]);
				} elseif (isset(${"connected_byid_".$key[0]."_".$this_is})) {
					${"show_".$key[0]."_".$key[1]} = sql_getone(${"tbl".$key[0]},
					" WHERE ".$key[0].(in_array($key[0]."rid",sql_fields(${"tbl".$key[0]},'array'))?"lang='$lg' AND ".$key[0]."r":'')
						."id='$show_id' ",$key[0].$key[1]);
				} elseif ($key[1] == 'cut') {
					${"show_".$key[0]} = substr(strip_tags(sql_getone(${"tbl".$this_is},
						" WHERE ".$this_is.(in_array($this_is."rid",sql_fields(${"tbl".$this_is},'array'))?"lang='$lg' AND ".$this_is."r":'')
							."id='$show_id' ",$this_is.$key[0])),0,300);
				} else {
					${"show_".$key[0]."_".$key[1]} = '';
					error_log(__LINE__." : show_".$key[0]."_".$key[1]." = ".${"show_".$key[0]."_".$key[1]});
					if (isset(${"show_".$key[0]."_".$key[1]."_array_linked_by_id"})) {
					error_log(__LINE__." isset : show_".$key[0]."_".$key[1]."_array_linked_by_id");
					error_log(__LINE__." q : WHERE ".$key[0].$this_is."='$show_id' AND ".$key[0].$key[1]."='Y' AND ".$key[0]."lang='$lg' "
						." select: ".$key[0]."rid");
						$loop = sql_array(${"tbl".$key[0]},
							" WHERE ".$key[0].$this_is."='$show_id' AND ".$key[0].$key[1]."='Y' AND ".$key[0]."lang='$lg' "
						,$key[0]."rid");
						if ($loop[0]!='')
						foreach($loop as $rel_id){
							$getitem = sql_get(${"tbl".$key[0]},
								" WHERE ".$key[0]."rid='$rel_id' AND ".$key[0]."lang='$lg' ",
								$key[0]."rid,".$key[0].implode(",".$key[0],${"show_".$key[0]."_".$key[1]."_array_linked_by_id"})
							);
							if ($getitem[0]!='.') {
								$link_id = $getitem[0];
								for($i=0;$i<count(${"show_".$key[0]."_".$key[1]."_array_linked_by_id"});$i++) {
									${"link_".${"show_".$key[0]."_".$key[1]."_array_linked_by_id"}[$i]} =
										(${"show_".$key[0]."_".$key[1]."_array_linked_by_id"}[$i]=='gendre'?
										 	sql_stringit("gendre",$getitem[$i+1])
										 :
										 	$getitem[$i+1]);//there is the id as getitem[0]
								}
								${"loop_show_".$key[0]."_".$key[1]} = '';
								foreach(${"array_".$key[0]."_".$key[1]."_tpl_linked_by_id"} as $tpl)
								${"loop_show_".$key[0]."_".$key[1]} .= (isset($tpl[0])&&$tpl[0]=="$"?${substr($tpl,1)}:$tpl);
								if (($full_url===true)||($admin_viewing===true))
								${"loop_show_".$key[0]."_".$key[1]} = str_replace($local_url.'&amp;'.$key[0]."Id=$link_id",lgx2readable($lg,'',$key[0],$link_id),${"loop_show_".$key[0]."_".$key[1]});
								${"show_".$key[0]."_".$key[1]} .= ${"loop_show_".$key[0]."_".$key[1]};
							}
						}
					} else {
						$count = sql_array(${"tbl".$key[0]}," WHERE ".$key[0].$this_is."='$show_id' AND ".$key[0]."lang='$lg' ",$key[0]."rid");//sql_nrows(${"tbl".$key[0]}," WHERE ".$key[0].$this_is."='$show_id' ",$key[0]."id");
						//  if ($count>0){
						if (isset($count[0])&&($count[0]!='')) {
							foreach($count as $loop_k)
							${"show_".$key[0]."_".$key[1]} .= sql_getone(${"tbl".$key[0]}," WHERE ".$key[0].$this_is."='$show_id' AND ".$key[0]."lang='$lg' ",$key[0].$key[1])."<br />";
						} else {
							${"show_".$key[0]."_".$key[1]} .= sql_getone(${"tbl".$key[0]}," WHERE ".$key[0]."rid='$show_id' AND ".$key[0]."lang='$lg' ",$key[0].$key[1]);
						}
					}
				}
			}
		}

		//  $_mod_content .= ' | <a href="'.$local_url.'" target="_self">'.$voirString.' '.$toutString.'</a><hr />';
		$getitem = sql_get($dbtable,"WHERE ".$this_is."rid='$show_id' AND ".$this_is."lang='$lg' ",$list_array_fields);
		if (($logged_in === true) && (!isset($filter_map) || isset($filter_index)) && ((isset($membre_id) && isset($user_name) && (sql_getone($tbluser,"WHERE userutil='$user_name' ","userid") == $membre_id)) || (($admin_viewing === true) && !in_array('1',$admin_priv))))
		$_mod_content .= '<div style="float:right;">'.(in_array($this_is.'publish',$array_fields)?'<a href="'.$local_url.(isset($send)?'&amp;send='.$send:'').'&amp;'.$this_is.'Id='.$show_id.'&amp;toggle=publish" onclick="return confirm(\''.($getitem[array_search($this_is.'publish',$array_fields)]=='Y'?$nonString.' '.$publishString.' ?\');"><img src="'.$mainurl.'images/publish_y.png" width="12" height="12" border="0" title="'.$publishedString.'" alt="'.$publishedString:$publishString.' ?\');"><img src="'.$mainurl.'images/publish_n.png" width="12" height="12" border="0" title="'.$nonString.' '.$publishedString.'" alt="'.$nonString.' '.$publishedString).'" /></a>':'').(in_array($this_is.'archive',$array_fields)?'<a href="'.$local_url.(isset($send)?'&amp;send='.$send:'').'&amp;'.$this_is.'Id='.$show_id.'&amp;toggle=archive" onclick="return confirm(\''.($getitem[array_search($this_is.'archive',$array_fields)]=='Y'?$nonString.' '.$archiveString.' ?\');"><img src="'.$mainurl.'images/archive_y.png" width="24" height="24" border="0" title="'.$archivedString.'" alt="'.$archivedString:$archiveString.' ?\');"><img src="'.$mainurl.'images/archive_n.png" width="24" height="24" border="0" title="'.$nonString.' '.$archivedString.'" alt="'.$nonString.' '.$archivedString).'" /></a>':'').'</div>';
		$show_mod_content = "";
		for($i=0;$i<count($array_fields);$i++) {
			$key = substr($array_fields[$i],strlen($dbtable)-1);
			$value = $getitem[$i];
			if (in_array($key,$basic_array)) {
				if (!isset($show_array_by_id))
				$_mod_content .= "<b>".$key."</b> == ".$value."<br />";
				if ($key == 'date')
				$show_date = human_date($value);
			}
			if (isset($show_array_by_id)) {
				if (in_array($key,$show_array_by_id)) {
					if ($key == "gendre") {
						${"show_".$key} = sql_stringit('gendre',$value);
					} else if (($key == "img") || ($key == "doc")) {
						${"show_".$key} = '';
						if ($array_fields_type[$this_is.$key] != 'text') {
							if ($key == "doc")
							$value = sql_array($tblcontdoc,"WHERE contdoclang='$lg' AND contdoccontid='$show_id' AND contdoc LIKE '%".strtoupper($this_is)."_%' ","contdoc");
							if ($key == "img")
							$value = sql_array($tblcontphoto,"WHERE contphotolang='$lg' AND contphotocontid='$show_id' AND contphotoimg LIKE '%".strtoupper($this_is)."_%' ORDER BY contphotosort,contphotoid ASC ","contphotoimg");
							$value = implode("|",$value);
						}
						if ($value != '') {
							if ($key == 'img') ${"show_".$key} .= '<div id="ajax_response"></div><div id="sortable"><ul>';
							$array_imgdoc = explode("|",$value);
							foreach($array_imgdoc as $array_imgdoc_k)
							if ($key == "img") {
								if ($array_imgdoc_k != '') {
									$ext = explode(".",strrev($array_imgdoc_k),2);
									$idn = strrev($ext[1]);
									$ext = strrev($ext[0]);
									$readable_name = ucwords(str_replace("_"," ",str_replace("-"," ",str_replace($filedir.(stristr($idn,$this_is)?strtoupper($this_is):''),"",$idn))));
									${"show_".$key} .= (in_array($ext,$array_img_ext)?'<li id="recordsArray_'.sql_getone($tblcontphoto,"WHERE contphotolang='$lg' AND contphotocontid='$show_id' AND contphotoimg='$array_imgdoc_k' ","contphotorid").'" class="ui-state-default"><div class="image"><a href="'.$mainurl.$idn.'_big.'.$ext.'" rel="lightbox['.$this_is.$key.']" target="_blank" title="&lt;a href=\''.$mainurl.$idn.'_ori.'.$ext.'\'&gt;&lt;img src=\''.$mainurl.'images/downloads_f2.png\' style=\'border:none;float:left;\' /&gt;&lt;/a&gt;'.$readable_name.'"><img src="'.$mainurl.$array_imgdoc_k.'" '.show_img_attr($mainurl.$array_imgdoc_k).' style="padding:2px;" align="left" title="'.$readable_name.'" alt="'.$readable_name.'" border="0" /></a></div></li> ':'<a href="'.$mainurl.$array_imgdoc_k.'" target="_new"><img src="'.$mainurl.'images/'.$ext.'logo.gif" width="16" height="16" vspace="5" hspace="5" title="'.$readable_name.'" alt="'.$readable_name.'" border="0" />'.str_replace($filedir,"",substr($array_imgdoc_k,0,-(strlen($ext)+1))).'</a> ');
								} else
								${"show_".$key} .= '';
							} else if ($key == "doc") {
								if ($array_imgdoc_k != '') {
									$ext = explode(".",strrev($array_imgdoc_k),2);
									$idn = strrev($ext[1]);
									$ext = strrev($ext[0]);
									$readable_name = ucwords(str_replace("_"," ",str_replace("-"," ",str_replace($filedir.(stristr($idn,$this_is)?strtoupper($this_is):''),"",$idn))));
									$download_value = (stristr($array_imgdoc_k,$safedir)?$urlsafe.'?file='.base64_encode($array_imgdoc_k):$array_imgdoc_k);
									${"show_".$key} .= '<a href="'.$mainurl.$download_value.'" target="_blank"><img src="'.$mainurl.'images/'.$ext.'logo.gif" width="16" height="16" vspace="5" hspace="5" title="'.(isset($show_title)?$show_title:$array_imgdoc_k).'" alt="'.(isset($show_title)?$show_title:$readable_name).'" border="0" />'.(isset($show_title)?(stristr($readable_name,space2underscore($show_title))?$show_title:''):$readable_name).'</a> ';
								} else
								${"show_".$key} .= '';
							}
							if ($key == 'img') ${"show_".$key} .= '</ul></div>';
						}
					} else if (($key == "type") || in_array($this_is.$key,$enumtype_array)) {
						${"show_".$key} = sql_stringit($this_is.$key,$value);
					} else if ($key == "comment") {

						include $getcwd.$up.$urladmin.'commentmanager.php';

					} else if ($key == "coords") {
						${"show_".$key} = $value;
						$value = explode("|",$value);
						if (($value != '') && isset($value[1])) {
							${"show_".$key."_x"} = ($value[0]>7?$value[0]-7:$value[0]);
							${"show_".$key."_y"} = ($value[1]>7?$value[1]-7:$value[1]);
						} else {
							${"show_".$key."_x"} = "0";
							${"show_".$key."_y"} = "0px;display:none;padding:0";//hides it if is empty
						}
					} else if (($key == "metadesc") || ($key == "metakeyw")) {
						${"show_".$key} = sql_getone($tblhtaccess,"WHERE htaccesstype='$this_is' AND htaccessitem='$show_id' AND htaccesslang='$lg' ORDER BY htaccessdate DESC ","htaccess$key");
					} else if (in_array($key,$array_modules)) {
						if (($value == '') || ($value == NULL) || ($value == 0)) {
							$sql_q = '';
							$sql_q .= filter_sql_q($key);
							$get_all = sql_array(${"tbl".$key}," WHERE ".$key.$this_is."='".${$this_is."Id"}."' $sql_q ".(isset(${$key."_ordered_by"})?"ORDER BY ".$key.${$key."_ordered_by"}.(isset(${$key."_ordre"})?" ".${$key."_ordre"}:''):(isset(${$key."_ordre"})?"ORDER BY ".$key."date ".${$key."_ordre"}." ":'')),$key."rid");
						} else {
							$get_all = array($value);
						}
						get_types($key);
						$loop_linked_content = '';
						foreach($get_all as $newvalue) {
							${$key."_getitem"} = sql_get(${"tbl".$key}," WHERE ".$key."rid='$newvalue' AND ".$key."lang='$lg' ","*");
							if (isset($show_array_linked_by_id) || isset(${"show_".$key."_array_linked_by_id"})) {
								if (${$key."_getitem"}[0] == '.') {
									$_linked_content = '';
								} else {
									foreach((isset(${"show_".$key."_array_linked_by_id"})?${"show_".$key."_array_linked_by_id"}:$show_array_linked_by_id) as $row) {
										if ($row == 'gendre') {
											${"link_".$row} = sql_stringit('gendre',${$key."_getitem"}[$key.$row]);
										} else if (($row == "img") || ($row == "doc")) {
											${"link_".$row} = '';
											if (${$key."_getitem"}[$key.$row] != '') {
												$array_imgdoc = explode("|",${$key."_getitem"}[$key.$row]);
												foreach($array_imgdoc as $array_imgdoc_k)
												if ($row == "img") {
													if ($array_imgdoc_k != '') {
														$ext = explode(".",strrev($array_imgdoc_k),2);
														$ext = strrev($ext[0]);
														${"link_".$row} .= (in_array($ext,$array_img_ext)?'<div class="image"><img src="'.$mainurl.$array_imgdoc_k.'" '.show_img_attr($mainurl.$array_imgdoc_k).' style="padding:2px;" border="0" /></div> ':'<a href="'.$mainurl.$array_imgdoc_k.'" target="_new"><img src="'.$mainurl.'images/'.$ext.'logo.gif" width="16" height="16" vspace="5" hspace="5" title="'.$array_imgdoc_k.'" alt="'.$array_imgdoc_k.'" border="0" />'.str_replace($filedir,"",substr($array_imgdoc_k,0,-(strlen($ext)+1))).'</a> ');
													} else
													${"link_".$row} .= '';
												} else if ($row == "doc") {
													if ($array_imgdoc_k != '') {
														$ext = explode(".",strrev($array_imgdoc_k),2);
														$ext = strrev($ext[0]);
														$download_value = (stristr($array_imgdoc_k,$safedir)?$urlsafe.'?file='.base64_encode($array_imgdoc_k):$array_imgdoc_k);
														${"link_".$row} .= '<a href="'.$mainurl.$download_value.'" target="_blank"><img src="'.$mainurl.'images/'.$ext.'logo.gif" width="16" height="16" vspace="5" hspace="5" title="'.(isset($link_title)?$link_title:$array_imgdoc_k).'" alt="'.(isset($link_title)?$link_title:$array_imgdoc_k).'" border="0" />'.(isset($link_title)?(stristr($array_imgdoc_k,space2underscore($link_title))?$link_title:''):$array_imgdoc_k).'</a> ';
													} else
													${"link_".$row} .= '';
												}
											}
										} else if (($row == 'type') || in_array("$key$row",$enumtype_array)) {
											${"link_".$row} = sql_stringit($key.$row,${$key."_getitem"}[$key.$row]);
										} else if ($key == "coords") {
											${"link_".$row} = ${$key."_getitem"}[$key.$row];
											${$key."_getitem"}[$key.$row] = explode("|",${$key."_getitem"}[$key.$row]);
											if ((${$key."_getitem"}[$key.$row] != '') && isset(${$key."_getitem"}[$key.$row][1])) {
												${"link_".$row."_x"} = (${$key."_getitem"}[$key.$row][0]>7?${$key."_getitem"}[$key.$row][0]-7:${$key."_getitem"}[$key.$row][0]);
												${"link_".$row."_y"} = (${$key."_getitem"}[$key.$row][1]>7?${$key."_getitem"}[$key.$row][1]-7:${$key."_getitem"}[$key.$row][1]);
											} else {
												${"link_".$row."_x"} = "0";
												${"link_".$row."_y"} = "0px;display:none;padding:0";//hides it if is empty
											}
										} else {
											if (in_array("$key$row",$datetime_array) || ($row == 'date'))
											${$key."_getitem"}[$key.$row] = human_date(${$key."_getitem"}[$key.$row]);
											${"link_".$row} = ${$key."_getitem"}[$key.$row];
										}
									}
									// include template
									$link_id = ${$key."_getitem"}[$key.'rid'];
									$get_metas = sql_get($tblhtaccess,"WHERE htaccesstype='$key' AND htaccessitem='$link_id' AND htaccesslang='$lg' ORDER BY htaccessdate DESC ","htaccessmetadesc,htaccessmetakeyw");
									if ($get_metas[0]!='.') {
										$link_metadesc = $get_metas[0];
										$link_metakeyw = $get_metas[1];
									} else {
										$link_metadesc = '';
										$link_metakeyw = '';
									}
									$_linked_content = '';
									foreach((isset(${"array_".$key."_tpl_linked_by_id"})?${"array_".$key."_tpl_linked_by_id"}:$array_tpl_linked_by_id) as $tpl_k)
									$_linked_content .= ($tpl_k[0]=="$"?${substr($tpl_k,1)}:$tpl_k);
									if (($full_url===true)||($admin_viewing===true)) {
										$_linked_content = str_replace($local_url.'&amp;'.$key.'Id='.$newvalue,lgx2readable($lg,'',$key,$newvalue),$_linked_content);
									}
								}
								//  ${"show_".$key} =
								$loop_linked_content .= $_linked_content;
							} else {
								//${"show_".$key}
								$_linked_content = (isset(${$key."_getitem"}[$key."nom"])&&(${$key."_getitem"}[$key."nom"]!='')?sql_stringit('gendre',${$key."_getitem"}[$key."gendre"]).' '.${$key."_getitem"}[$key."prenom"].' '.${$key."_getitem"}[$key."nom"].(sql_getone($tblcont,"WHERE conttype='$key' ","contpg")==""?''&&$admin_viewing===false:'</a>'):((isset(${$key."_getitem"}[$key."title"])&&(${$key."_getitem"}[$key."title"]!=''))||(isset(${$key."_getitem"}[$key."titre"])&&(${$key."_getitem"}[$key."titre"]!=''))?${$key."_getitem"}[$key."title"].'</a><br />':''));//<br />'.(isset(${$key."_getitem"}[$key."profession"])&&(${$key."_getitem"}[$key."profession"]!='')?'<i>'.${$key."_getitem"}[$key."profession"].'</i><br />':'')
								//  if (${"show_".$key} != '')
								if ($_linked_content != '')
								//${"show_".$key}
								$_linked_content = (sql_getone($tblcont,"WHERE conttype='$key' ","contpg")==""?'':'<a href="'.($full_url===true||$admin_viewing===true?lgx2readable($lg,'',$key,$newvalue):$local_url.'&amp;'.$key.'Id='.$newvalue).'">').$_linked_content;//${"show_".$key};
								//&&$admin_viewing===false
								else
								$_linked_content = ' <img src="'.$mainurl.'images/help.gif" width="13" height="14" border="0" title="?" alt="?" /> '.ucfirst(${$key."String"}).' '.$nonString.' '.$existantString;
								//  ${"show_".$key} =
								$loop_linked_content .= $_linked_content;
							}
						}
						${"show_".$key} = $loop_linked_content;
					} else {
						if (in_array("$this_is$key",$datetime_array) || ($key == 'date'))
						$value = human_date($value);
						if (@file_exists($getcwd.$up.$safedir.'_extra_routines.php'))
						require $getcwd.$up.$safedir.'_extra_routines.php';
						if (!isset($array_routines)
							|| (isset($array_routines) && !in_array($key,$array_routines))
						) {
							${"show_".$key} = $value;
						}
					}
				} else {
					/*
			if (!in_array($key,$basic_array) && ($value != ''))
			$show_mod_content .= '<br /><b>'.(isset(${$key."String"})?${$key."String"}:$key).'</b><br />'.(isset(${"tbl".$key})?sql_getone(${"tbl".$key}," WHERE ".$key."rid='$value' AND ".$key."lang='$lg' ",$key."title"):$value)."<br />";
		*/
				}
			} else {
			}
			if (isset($that_is) && ($i==count($array_fields)-1)) {
				$getthat_item = sql_get($that_dbtable,"WHERE ".$that_is.$this_is."='$keys' AND ".$that_is."lang='$lg' ",$that_list_array_fields);
				if ($getthat_item[0] != '.') {
					//  $_mod_content .= '<div style="border:1px solid gray;padding:2px;">';
					for($ii=0;$ii<count($that_array_fields);$ii++) {
						$that_key = substr($that_array_fields[$ii],strlen($that_dbtable)-1);
						$that_value = $getthat_item[$ii];
						$_linked_content = '';
						if (in_array($that_key,$basic_array)) {
							if (in_array($that_key,array('id','rid'))) $link_id = $that_value;
							if (!isset($show_array_linked))
							$_linked_content .= "<b>".$that_key."</b> == ".$that_value."<br />";
						}
						if (isset($show_array_linked)) {
							if (in_array($that_key,$show_array_linked)) {
								if ($that_key == "gendre") {
									${"link_".$that_key} = sql_stringit('gendre',$that_value);
								} else if ($that_key == "img") {
									${"link_".$that_key} = '';
									if ($that_value != '') {
										$array_imgdoc = explode("|",$that_value);
										foreach($array_imgdoc as $array_imgdoc_k)
										if ($that_key == "img") {
											if ($array_imgdoc_k != '') {
												$ext = explode(".",strrev($array_imgdoc_k),2);
												$ext = strrev($ext[0]);
												${"link_".$that_key} = (in_array($ext,$array_img_ext)?'<div class="image"><img src="'.$mainurl.$array_imgdoc_k.'" '.show_img_attr($mainurl.$array_imgdoc_k).' style="padding:2px;" align="right" border="0" /></div> ':'<a href="'.$mainurl.$array_imgdoc_k.'" target="_new"><img src="'.$mainurl.'images/'.$ext.'logo.gif" width="16" height="16" vspace="5" hspace="5" title="'.$array_imgdoc_k.'" alt="'.$array_imgdoc_k.'" border="0" />'.str_replace($filedir,"",substr($array_imgdoc_k,0,-(strlen($ext)+1))).'</a> ');
											} else
											${"link_".$that_key} .= '';
										} else if ($that_key == "doc") {
											if ($array_imgdoc_k != '') {
												$ext = explode(".",strrev($array_imgdoc_k),2);
												$ext = strrev($ext[0]);
												$download_value = (stristr($array_imgdoc_k,$safedir)?$urlsafe.'?file='.base64_encode($array_imgdoc_k):$array_imgdoc_k);
												${"link_".$that_key} .= '<a href="'.$mainurl.$download_value.'" target="_blank"><img src="'.$mainurl.'images/'.$ext.'logo.gif" width="16" height="16" vspace="5" hspace="5" title="'.(isset($link_title)?$link_title:$array_imgdoc_k).'" alt="'.(isset($link_title)?$link_title:$array_imgdoc_k).'" border="0" />'.(isset($link_title)?(stristr($array_imgdoc_k,space2underscore($link_title))?$link_title:''):$array_imgdoc_k).'</a> ';
											} else
											${"link_".$that_key} .= '';
										}
									}
								} else {
									${"link_".$that_key} = (isset($q)?str_ireplace($q,'\<span class=\"qhighlight\"\>'.$q.'\<\/span\>',html_entity_decode($that_value),${"link_".$that_key."_count"}):$that_value);
									if (isset(${"link_".$that_key."_count"})) $q_count += ${"link_".$that_key."_count"};
								}
							} else {
								/*
						if (!in_array($that_key,$basic_array) && ($that_value != ''))
						$_linked_content .= '<br /><b>'.(isset(${$that_key."String"})?${$that_key."String"}:$that_key).'</b><br />'.(isset(${"tbl".$that_key})?sql_getone(${"tbl".$that_key}," WHERE ".$that_key."rid='$that_value' AND ".$that_key."lang='$lg' ",$that_key."title"):$that_value)."<br />";
				*/
							}
						} else {
						}
					}
					if (isset($show_array_linked))
					if (isset($array_tpl_linked))
					foreach($array_tpl_linked as $tpl_l)
					$_linked_content .= ($tpl_l[0]=="$"?${substr($tpl_l,1)}:$tpl_l);
					if (($full_url===true)||($admin_viewing===true)) {
						$_linked_content = str_replace($local_url.'&amp;'.$key.'Id='.$value,lgx2readable($lg,'',$key,$value),$_linked_content);
					}
					${$key."_linked_content"} = $_linked_content;
					//   $_mod_content .= '</div><hr />'.$ii.' > here<br />';
				}
			}
		}
		if (isset($array_tpl_by_id))
		foreach($array_tpl_by_id as $tpl) //  by id
		$_mod_content .= (isset($tpl[0])&&$tpl[0]=="$"?${substr($tpl,1)}:$tpl);
		if (($full_url===true)||($admin_viewing===true)) {
			$_mod_content = str_replace($local_url.'&amp;'.$this_is."Id=$show_id",lgx2readable($lg,'',$this_is,$show_id),$_mod_content);
			//  $_mod_content = ereg_replace(addslashes($local_url).'\&amp;'.$this_is."Id=([^]]*)\"",lgx2readable($lg,'',$this_is,"\1").'"',$_mod_content);

			//  $_mod_content = preg_replace($local_url.'&amp;'.$this_is."Id=(\d+)",'lgx2readable($lg,"",$this_is,"$1")',$_mod_content);
		}
		$_mod_content .= $show_mod_content;






	} else { // no id, shows list







		//  $_mod_content .= '<hr /><div style="width:100%;float:left;"><div style="width:648px;margin:0 auto;">';

		if (isset(${$this_is."Type"}))
		$sql_q .= " AND ".$this_is."type='".${$this_is."Type"}."' ";

		if ((count($array_lang)>1) && in_array($this_is."lang",$array_fields))
		$sql_q .= " AND ".$this_is."lang='$lg' ";

		$_linked_content = '';
		$_ordered_mod_content = '';
		$sql_q .= filter_sql_q($this_is);

		if	(!isset($par) || (isset($par) && !in_array($this_is.$par,$array_fields)))
		$par = (isset($select_array)?$select_array[0]:'date');
		if ((isset($par) && ($par == 'date')) && !isset($ordre))
		$ordre = 'DESC';
		if	(!isset($ordre))	$ordre = 'ASC'	;
		if (isset($select_array) && is_array($select_array)) {
			$nextordre = ($ordre=='DESC'?'ASC':'DESC');
			$selectHead = '<div style="width:98%;clear:right;text-align:center;">'.($admin_viewing===true?gen_form($lg,$x,$y):gen_form($lg,$x)).'<label for="par">'.$parString.' : </label><select name="par">'.gen_selectoption($select_array,$par,'','').'</select> | <label for="ordre">'.$ordreString.' : </label><select name="ordre">'.gen_selectoption(array('ASC','DESC'),$ordre,'','').'</select> || <input type="submit" value="'.$voirString.'" /></form></div>';
			$sql_q .= " ORDER BY ".$this_is.$par." $ordre ";
		} else {
			if (isset($ordered_by)) {
				if (strstr($ordered_by,",")) {
					$exploded_ordered_by = explode(",",$ordered_by);
					$sql_ordered_by = $this_is.implode(",$this_is",$exploded_ordered_by);
					$ordered_by = $exploded_ordered_by[0];
				} else
				$sql_ordered_by = $this_is.$ordered_by;
				$sql_q .= " ORDER BY $sql_ordered_by $ordre ";
			}
		}
		//if ((isset($q) && ($q != '')) &&
		if ( isset($that_is) && isset($that_dbtable) && isset($filter_searchfield) && in_array($that_is.$filter_searchfield,$that_array_fields)) {
			$where = " WHERE ".$this_is."statut='Y' AND ".$that_is."statut='Y' AND ".$that_is.$this_is."=".$this_is."rid AND ".$this_is."lang='$lg' AND ".$that_is."lang='$lg' $sql_q ";
			$array_getitems = array_unique(sql_array("$dbtable,$that_dbtable",$where,$this_is."rid"));
		} else {
			$where = " WHERE ".$this_is."statut='Y' AND ".$this_is."lang='$lg' $sql_q ";
			$array_getitems = array_unique(sql_array($dbtable,$where,$this_is."rid"));
		}
		//$array_getitems = sql_array($dbtable,"WHERE ".$this_is."statut='Y' $sql_q ",$this_is."id");
		$types_array_indexes = array();
		if (isset($array_getitems[0]) && ($array_getitems[0] != '')) {
			$ii = 0;
			foreach($array_getitems as $keys) {
				$getitem = sql_get($dbtable,"WHERE ".$this_is."rid='$keys' AND ".$this_is."lang='$lg' ",'*');//$list_array_fields = *
				if ($getitem[0] != '.') {

					if (isset($index_by_alpha) && isset($ordered_by) && (!isset(${"filter_index_by_alpha"}) || (isset(${"filter_index_by_alpha"}) && (${"filter_index_by_alpha"}===true)))) {
						if (isset($this_index)) $past_index = $this_index;
						$this_index = $getitem[$this_is.$ordered_by][0];
						if (!in_array($this_index,$types_array_indexes)) {
							$types_array_indexes[] = $this_index;
							if ((isset($past_index) && ($past_index != $this_index)) || !isset($past_index))
							if (!isset(${$this_is."Type"}))
							//  $_ordered_mod_content .= '<span class="alphaorder">'.$this_index.'</span>';
							$_ordered_mod_content .= $_pre_css_bgimg.'<a href="'.$local_url.($ordered_by=='type'?'&amp;'.$this_is.ucfirst($ordered_by).'='.(($htaccess4sef===true)&&($type2htaccess===true)?space2underscore(sql_stringit($this_is.$ordered_by,$this_index)):$this_index):'#'.$this_index).'">&nbsp;'.(sql_stringit($this_is.$ordered_by,$this_index)!=''?sql_stringit($this_is.$ordered_by,$this_index):$this_index).'&nbsp;</a>'.$_post_css_bgimg;
						}
					}
					for($i=0;$i<count($array_fields);$i++) {
						//  $_linked_content = 'voided linked content i='.$i.'<hr />';
						$key = substr($array_fields[$i],strlen($dbtable)-1);
						$value = $getitem[$i];
						if (in_array($key,$basic_array)) {
							if (in_array($key,array('id','rid'))) {
								$show_id = $value;
								$current = sql_get(${"tbl".$this_is}, "WHERE ".$this_is.($this_id_rid===true?'r':'')."id='$show_id' ", "*");
								$get_metas = sql_get($tblhtaccess,"WHERE htaccesstype='$this_is' AND htaccessitem='$show_id' AND htaccesslang='$lg' ORDER BY htaccessdate DESC ","htaccessmetadesc,htaccessmetakeyw");
								if ($get_metas[0]!='.') {
									$show_metadesc = $get_metas[0];
									$show_metakeyw = $get_metas[1];
								} else {
									$show_metadesc = '';
									$show_metakeyw = '';
								}
								// 20090526 placed below in key=array_modules, to confirm (needed for inputing content of resp where yinvs was fetched)
								if (isset($show_array))
								foreach($show_array as $show_array_key) {
									if (strstr($show_array_key,":")) {
										$show_array_key = explode(":",$show_array_key);
										if (isset(${"connected_byid_".$this_is."_".$show_array_key[0]}))
										{
											if (isset($current[$this_is.$show_array_key[0]]))
											${"show_".$show_array_key[0]."_".$show_array_key[1]} = sql_getone(${"tbl".$show_array_key[0]}," WHERE ".$show_array_key[0]."rid='".$current[$this_is.$show_array_key[0]]."' AND ".$show_array_key[0]."lang='$lg' ",$show_array_key[0].$show_array_key[1]);
										} elseif (isset(${"connected_byid_".$show_array_key[0]."_".$this_is})) {
											${"show_".$show_array_key[0]."_".$show_array_key[1]} = sql_getone(${"tbl".$show_array_key[0]}," WHERE ".$show_array_key[0]."rid='$show_id' AND ".$show_array_key[0]."lang='$lg' ",$show_array_key[0].$show_array_key[1]);
										} elseif ($show_array_key[1] == 'cut') {
											${"show_".$show_array_key[0]} = substr(strip_tags(sql_getone($dbtable," WHERE ".$this_is."rid='$show_id' AND ".$this_is."lang='$lg' ",$this_is.$show_array_key[0])),0,300);
										} else {
											${"show_".$show_array_key[0]."_".$show_array_key[1]} = sql_getone(${"tbl".$show_array_key[0]}," WHERE ".$show_array_key[0].$this_is."='$show_id' AND ".$show_array_key[0]."lang='$lg' ",$show_array_key[0].$show_array_key[1]);
										}
										if ($show_array_key[1] == 'gendre')
										$show_array_key[1] = sql_stringit('gendre',$show_array_key[1]);
									}
								}
							}
							if ($key == "date") {
								$par_old_date[$ii] = $value;
								$show_date = human_date($value);
							}
							if ($key == "statut") $show_statut = $value;
							if (!isset($show_array))
							$_mod_content .= ($key=='id'?'<a href="'.($full_url===true||$admin_viewing===true?lgx2readable($lg,'',$this_is,$value):$local_url.'&amp;'.$this_is.'Id='.$value).'" target="_self">'."<b>".$key."</b> == ".$value."<br /></a>":"<b>".$key."</b> == ".$value."<br />");
						} else {
							if (isset($show_array) && in_array($key,$show_array)) {
								if ($key == "gendre") {
									${"show_".$key} = sql_stringit('gendre',$value);
								} else if (($key == "img") || ($key == "doc")) {
									// when showing list of items, should only show one img
									${"show_".$key} = '';
									if ($array_fields_type[$this_is.$key] != 'text') {
										if ($key == 'img')
										$value = sql_array($tblcontphoto,"WHERE contphotolang='$lg' AND  contphotocontid='$show_id' AND contphotoimg LIKE '%".strtoupper($this_is)."_%' ORDER BY contphotosort,contphotoid ASC ","contphotoimg");
										if ($key == 'doc')
										$value = sql_array($tblcontdoc,"WHERE contdoclang='$lg' AND  contdoccontid='$show_id' AND contdoc LIKE '%".strtoupper($this_is)."_%' ","contdoc");
										$value = implode("|",$value);
									}
									if ($value != '') {
										$array_imgdoc = explode("|",$value);
										foreach($array_imgdoc as $array_imgdoc_k)
										if ($key == "img") {
											if ($array_imgdoc_k != '') {
												$ext = explode(".",strrev($array_imgdoc_k),2);
												$ext = strrev($ext[0]);
												if (($show_oneimg_only === true) && (${"show_".$key} == ''))
												${"show_".$key} .= (in_array($ext,$array_img_ext)?'<div class="image"><img src="'.$mainurl.$array_imgdoc_k.'" '.show_img_attr($mainurl.$array_imgdoc_k).' style="padding:2px;" align="right" border="0" '.($show_oneimg_only===true?' class="show_oneimg_only" ':'').' /></div> ':'<a href="'.$mainurl.$array_imgdoc_k.'" target="_new"><img src="'.$mainurl.'images/'.$ext.'logo.gif" width="16" height="16" vspace="5" hspace="5" title="'.$array_imgdoc_k.'" alt="'.$array_imgdoc_k.'" border="0" />'.str_replace($filedir,"",substr($array_imgdoc_k,0,-(strlen($ext)+1))).'</a> ');
											} else
											${"show_".$key} .= '';
										} else if ($key == "doc") {
											if ($array_imgdoc_k != '') {
												$ext = explode(".",strrev($array_imgdoc_k),2);
												$ext = strrev($ext[0]);
												$download_value = (stristr($array_imgdoc_k,$safedir)?$urlsafe.'?file='.base64_encode($array_imgdoc_k):$array_imgdoc_k);
												${"show_".$key} .= '<a href="'.$mainurl.$download_value.'" target="_blank"><img src="'.$mainurl.'images/'.$ext.'logo.gif" width="16" height="16" vspace="5" hspace="5" title="'.(isset($show_title)?$show_title:$array_imgdoc_k).'" alt="'.(isset($show_title)?$show_title:$array_imgdoc_k).'" border="0" />'.(isset($show_title)?(stristr($array_imgdoc_k,space2underscore($show_title))?$show_title:''):$array_imgdoc_k).'</a> ';
											} else
											${"show_".$key} .= '';
										}
									}
								} else if (($key == "type") || in_array($this_is.$key,$enumtype_array)) {
									${"show_".$key} = sql_stringit($this_is.$key,$value);
								} else if ($key == "comment") {
									$get_comment = sql_array($tblcomment,"WHERE commentstatut='Y' AND commentlang='$lg' AND comment".$this_is."='$show_id' ","commentrid");
									if ($get_comment[0] != '')
									$show_comment = " (".count($get_comment)." ".$class_conjugaison->plural($commentString,'M',count($get_comment)).") ";
									else
									$show_comment = " (0 ".$commentString.") ";
								} else if ($key == "coords") {
									${"show_".$key} = $value;
									$value = explode("|",$value);
									if (($value != '') && isset($value[1])) {
										${"show_".$key."_x"} = ($value[0]>7?$value[0]-7:$value[0]);
										${"show_".$key."_y"} = ($value[1]>7?$value[1]-7:$value[1]);
									} else {
										${"show_".$key."_x"} = "0";
										${"show_".$key."_y"} = "0px;display:none;padding:0";//hides it if is empty
									}
								} else if (in_array($key,$array_modules)) {
									if (isset($show_array))
									foreach($show_array as $show_array_key) {
										if (strstr($show_array_key,":")) {
											$show_array_key = explode(":",$show_array_key);
											if (isset(${"connected_byid_".$show_array_key[0]."_".$this_is}))
											${"show_".$show_array_key[0]."_".$show_array_key[1]} = sql_getone(${"tbl".$show_array_key[0]}," WHERE ".$show_array_key[0]."rid='$show_id' AND ".$show_array_key[0]."lang='$lg' ",$show_array_key[0].$show_array_key[1]);
											else
											${"show_".$show_array_key[0]."_".$show_array_key[1]} = sql_getone(${"tbl".$show_array_key[0]}," WHERE ".$show_array_key[0]."rid='$value' AND ".$show_array_key[0]."lang='$lg' ",$show_array_key[0].$show_array_key[1]);
											if ($show_array_key[1] == 'gendre')
											$show_array_key[1] = sql_stringit('gendre',$show_array_key[1]);
										}
									}
									${"not_".$key."_id"} = $value;
									if ((isset($q) && ($q != '')) && isset($that_is) && isset($that_dbtable) && isset($filter_searchfield) && in_array($that_is.$filter_searchfield,$that_array_fields)) {
										//  $key_sql_q = filter_sql_q($key);
										${$key."_getitem"} = sql_get(${"tbl".$key}," WHERE ".$that_is.$this_is."='$show_id' AND ".$key."rid='$value' AND ".$key."lang='$lg' ","*");//$key_sql_q
									} else {
										$key_sql_q = filter_sql_q($key);
										${$key."_getitem"} = sql_get(${"tbl".$key}," WHERE ".$key."rid='$value' AND ".$key."lang='$lg' $key_sql_q ","*");
									}
									$bypass_this = false;
									if (${$key."_getitem"}[0] != '.')
									//&&$admin_viewing===false
									${"show_".$key} = (sql_getone($tblcont,"WHERE conttype='$key' ","contpg")==""?'':'<a href="'.($full_url===true||$admin_viewing===true?lgx2readable($lg,'',$key,$value):$local_url.'&amp;'.$key.'Id='.$value).'">').(isset(${$key."_getitem"}[$key."title"])?${$key."_getitem"}[$key."title"].'</a><br />':(isset(${$key."_getitem"}[$key."nom"])?sql_stringit('gendre',${$key."_getitem"}[$key."gendre"]).' '.${$key."_getitem"}[$key."prenom"].' '.${$key."_getitem"}[$key."nom"].(sql_getone($tblcont,"WHERE conttype='$key' ","contpg")==""?'':'</a>').'<br />':'')).'';//<div style="float:left;width:48%;margin:2px;padding:2px;"></div>//.(isset(${$key."_getitem"}[$key."profession"])&&($key_sql_q=='')?'<i>'.${$key."_getitem"}[$key."profession"].'</i><br />':'')
									//&&$admin_viewing===false
									else {
										$reason_exception = ucfirst(${$key."String"}).' '.$nonString.' '.$existantString;
										${"show_".$key} = ' <img src="'.$mainurl.'images/help.gif" width="13" height="14" border="0" title="'.$reason_exception.'" alt="'.$reason_exception.'" /> ';//$bypass_this = true;
									}
									if (isset($that_is)) {
										$that_sql_q = " AND ".$that_is.$this_is."='$show_id' AND ".$that_is."statut='Y' ";
										$that_sql_q .= filter_sql_q($that_is);
										$where = "WHERE ".(isset($q)&&($q!='')?$that_is.$this_is."!='' ":$that_is.$this_is."='$keys' AND ".$that_is."rid!='".${"not_".$that_is."_id"}."'")." $that_sql_q ORDER BY ".(isset(${$key."_getitem"}[$key."title"])?$key."title":$key."nom")." ASC ";
										$array_that_items = sql_array($that_dbtable,$where,$that_is."id");
										$_linked_content = '';
										if (isset($_linked_count_items) && ($_linked_count_items === true))
										$_linked_content = count($array_that_items).' '.$class_conjugaison->plural($enregistrementString,'',count($array_that_items));
										foreach($array_that_items as $that_id) {
											if ($that_id == '') {
												if (isset($_linked_count_items) && ($_linked_count_items === true))
												$_linked_content = '';//0 '.$enregistrementString;
											} else {
												$getthat_item = sql_get($that_dbtable,"WHERE ".$that_is."rid='$that_id' AND ".$that_is."lang='$lg' ",$that_list_array_fields);
												if ($getthat_item[0] != '.') {
													//  $_mod_content .= '<div style="border:1px solid gray;padding:2px;">';
													for($ii=0;$ii<count($that_array_fields);$ii++) {
														$that_key = substr($that_array_fields[$ii],strlen($that_dbtable)-1);
														$that_value = $getthat_item[$ii];
														/*
							if (in_array($that_key,$basic_array)) {
							$_linked_content .= ($that_key=='id'?'<a href="'.$local_url.'&amp;'.$that_is.'Id='.$that_value.'" target="_self">'."<b>".$that_key."</b> == ".$that_value."<br /></a>":"<b>".$that_key."</b> == ".$that_value."<br />");

							} else {
							}
							*/
														if (in_array($that_key,$basic_array)) {
															if (in_array($that_key,array('id','rid'))) {
																$link_id = $that_value;
																$get_metas = sql_get($tblhtaccess,"WHERE htaccesstype='$that_is' AND htaccessitem='$link_id' AND htaccesslang='$lg' ORDER BY htaccessdate DESC ","htaccessmetadesc,htaccessmetakeyw");
																if ($get_metas[0]!='.') {
																	$link_metadesc = $get_metas[0];
																	$link_metakeyw = $get_metas[1];
																} else {
																	$link_metadesc = '';
																	$link_metakeyw = '';
																}
															}
															if (!isset($show_array_linked))
															$_linked_content .= "<b>".$that_key."</b> == ".$that_value."<br />";
														}
														if (isset($show_array_linked)) {
															if (in_array($that_key,$show_array_linked)) {
																if ($that_key == "gendre") {
																	${"link_".$that_key} = sql_stringit('gendre',$that_value);
																} else if (($that_key == "img") || ($that_key == "doc")) {
																	${"link_".$that_key} = '';
																	if ($that_value != '') {
																		$array_imgdoc = explode("|",$that_value);
																		foreach($array_imgdoc as $array_imgdoc_k)
																		if ($that_key == "img") {
																			if ($array_imgdoc_k != '') {
																				$ext = explode(".",strrev($array_imgdoc_k),2);
																				$ext = strrev($ext[0]);
																				${"link_".$that_key} .= (in_array($ext,$array_img_ext)?'<div class="image"><img src="'.$mainurl.$array_imgdoc_k.'" '.show_img_attr($mainurl.$array_imgdoc_k).' style="padding:2px;" align="right" border="0" /></div> ':'<a href="'.$mainurl.$array_imgdoc_k.'" target="_new"><img src="'.$mainurl.'images/'.$ext.'logo.gif" width="16" height="16" vspace="5" hspace="5" title="'.$array_imgdoc_k.'" alt="'.$array_imgdoc_k.'" border="0" />'.str_replace($filedir,"",substr($array_imgdoc_k,0,-(strlen($ext)+1))).'</a> ');
																			} else
																			${"link_".$that_key} .= '';
																		} else if ($that_key == "doc") {
																			if ($array_imgdoc_k != '') {
																				$ext = explode(".",strrev($array_imgdoc_k),2);
																				$ext = strrev($ext[0]);
																				$download_value = (stristr($array_imgdoc_k,$safedir)?$urlsafe.'?file='.base64_encode($array_imgdoc_k):$array_imgdoc_k);
																				${"link_".$that_key} .= '<a href="'.$mainurl.$download_value.'" target="_blank"><img src="'.$mainurl.'images/'.$ext.'logo.gif" width="16" height="16" vspace="5" hspace="5" title="'.(isset($link_title)?$link_title:$array_imgdoc_k).'" alt="'.(isset($link_title)?$link_title:$array_imgdoc_k).'" border="0" />'.(isset($link_title)?(stristr($array_imgdoc_k,space2underscore($link_title))?$link_title:''):$array_imgdoc_k).'</a> ';
																			} else
																			${"link_".$that_key} .= '';
																		}
																	}
																} else if (($that_key == "type") || in_array($that_is.$that_key,$enumtype_array)) {
																	${"link_".$that_key} = sql_stringit($that_is.$that_key,$that_value);// was show???
																} else if ($key == "coords") {
																	${"link_".$that_key} = $that_value;
																	$that_value = explode("|",$that_value);
																	if (($that_value != '') && isset($that_value[1])) {
																		${"link_".$that_key."_x"} = ($that_value[0]>7?$that_value[0]-7:$that_value[0]);
																		${"link_".$that_key."_y"} = ($that_value[1]>7?$that_value[1]-7:$that_value[1]);
																	} else {
																		${"link_".$that_key."_x"} = "0";
																		${"link_".$that_key."_y"} = "0px;display:none;padding:0";//hides it if is empty
																	}
																} else {
																	if (in_array("$that_is$that_key",$datetime_array) || ($that_key == 'date'))
																	$that_value = human_date($that_value);
																	${"link_".$that_key} = (isset($q)?str_ireplace($q,'\<span class=\"qhighlight\"\>'.$q.'\<\/span\>',html_entity_decode($that_value),${"link_".$that_key."_count"}):$that_value);
																	if (isset(${"link_".$that_key."_count"})) $q_count += ${"link_".$that_key."_count"};
																}
															} else {
																/*
								if (!in_array($that_key,$basic_array) && ($that_value != ''))
								$_linked_content .= '<br /><b>'.(isset(${$that_key."String"})?${$that_key."String"}:$that_key).'</b><br />'.(isset(${"tbl".$that_key})?sql_getone(${"tbl".$that_key}," WHERE ".$that_key."rid='$that_value' AND ".$that_key."lang='$lg' ",$that_key."title"):$that_value)."<br />";
								*/
															}
														} else {
														}
													}
													if (isset($show_array_linked))
													if (isset($array_tpl_linked)) {
														//  foreach($array_tpl_linked as $tpl_l) {
														for($li=0;$li<count($array_tpl_linked);$li++) {
															$tpl_l = $array_tpl_linked[$li];
															if (($li>0) && ($array_tpl_linked[$li-1] == '') && ($array_tpl_linked[$li] == '<br />'))
															$tpl_l = '';
															$_linked_content .= ($tpl_l[0]=="$"?${substr($tpl_l,1)}:$tpl_l);
														}
													}
													if (($full_url===true)||($admin_viewing===true))
													$_linked_content = str_replace($local_url.'&amp;'.$key.'Id='.$link_id,lgx2readable($lg,'',$key,$link_id),$_linked_content);
												}
											}
										}//end foreach($array_that_items as $that_id)
									}
									/*
				} else if ($key == 'resp') {
				$resp_getitem = sql_get($tblmembre," WHERE membreid='$value' ","membregendre,membrenom,membreprenom,membreprofession");
				${"show_".$key} = '<a href="'.$local_url.'&amp;membreId='.$value.'">'.sql_stringit('gendre',$resp_getitem["membregendre"]).' '.$resp_getitem["membrenom"].' '.$resp_getitem["membreprenom"].'</a><br />'.$resp_getitem["membreprofession"].'<br />';
				*/
								} else {
									if (in_array("$this_is$key",$datetime_array) || ($key == 'date')) {
										${"par_old_".$key}[$ii] = $value;
										$value = human_date($value);
									}

									if (@file_exists($getcwd.$up.$safedir.'_extra_routines.php'))
										require $getcwd.$up.$safedir.'_extra_routines.php';
									if (!isset($array_routines) || (isset($array_routines) && !in_array($key,$array_routines)))
										${"show_".$key} = (isset($q)?str_ireplace($q,'\<span class=\"qhighlight\"\>'.$q.'\<\/span\>',html_entity_decode($value),${"show_".$key."_count"}):$value);
									if (isset(${"show_".$key."_count"})) 
										$q_count += ${"show_".$key."_count"};
								}
							}
							//  $_mod_content .= $key." == ".$value."<br />";
							if (($key == 'membre') && (in_array($this_is,$editable_by_membre) || in_array($this_is,$deletable_by_membre)))
								$membre_id = $value; // allows logged_in members to edit own content according to array $editable_by_membre in tpl
						}
						if (isset($select_array) && in_array($key,$select_array) && isset(${"show_".$key}))
							${"par_".$key}[$ii] = ${"show_".$key};
					}
					if (isset($show_array)) {
						//  $_mod_content .= '<table><tr><td>';
						if (isset($select_array)) {
							if (isset($par) && isset(${"show_".$par})) {
								//  if (!in_array("$this_is$par",$datetime_array) && ($par != 'date'))
								//  ${"show_".$par}[$ii] = ${"show_".$par};
								//  ${"par_old_".$par}[$ii] = ${"par_old_".$par};
								if ($ii > 0) {
									if (in_array("$this_is$par",$datetime_array) || ($par == 'date')) {
										if (substr(${"par_old_".$par}[$ii],0,11) != substr(${"par_old_".$par}[$ii-1],0,11))
										$_mod_content .= '<hr /><br /><div style="text-align:center;"><span class="arrow">'.human_date(${"par_old_".$par}[$ii]).'</span></div><hr /><br />';
									} else {
										if (${"par_".$par}[$ii] != ${"par_".$par}[$ii-1])
										$_mod_content .= '<hr /><br /><div style="text-align:center;"><span class="arrow">'.${"par_".$par}[$ii].'</span></div><hr /><br />';
									}
								} else {
									if (in_array("$this_is$par",$datetime_array) || ($par == 'date')) {
										$_mod_content .= '<hr /><br /><div style="text-align:center;"><span class="arrow">'.human_date(${"par_old_".$par}[$ii]).'</span></div><hr /><br />';
									} else {
										$_mod_content .= '<hr /><br /><div style="text-align:center;"><span class="arrow">'.${"par_".$par}[$ii].'</span></div><hr /><br />';
									}
								}
							}
						}
						if (isset($ordered_by) && isset($this_index))
						if ((isset($past_index) && ($past_index != $this_index)) || !isset($past_index))
							$_mod_content .= '<span class="alphaorder"><a name="'.$this_index.'"></a>&nbsp;'.(sql_stringit($this_is.$ordered_by,$this_index)!=''?sql_stringit($this_is.$ordered_by,$this_index):$this_index).'&nbsp;</span><br />';

						if (!isset($filter_map) && (!isset($bypass_this) || (isset($bypass_this) && ($bypass_this === false))))
							$_mod_content .= '<div class="show_array">';//width:48%;

						$_membre_owned = (isset($membre_id) && isset($user_name) && (sql_getone($tbluser,"WHERE userutil='$user_name' ","userid") == $membre_id));
						if (($logged_in === true)
						 && ((!isset($filter_map) && (!isset($bypass_this) || (isset($bypass_this) && ($bypass_this === false)))) || isset($filter_index))
						 && ($_membre_owned || (($admin_viewing === true) && !in_array('1',$admin_priv))
						))
						$_mod_content .= '<div style="float:right;clear:right;">'.(in_array($this_is.'publish',$array_fields)?'<a href="'.($full_url===true||$admin_viewing===true?($admin_viewing===true?$local:$mainurl).lgx2readable($lg,'',$this_is,$show_id).($admin_viewing===true?'':"?"):$local_url.'&amp;'.$this_is.'Id='.$show_id).(isset($send)?'&amp;send='.$send:'').'&amp;toggle=publish" onclick="return confirm(\''.($getitem[array_search($this_is.'publish',$array_fields)]=='Y'?$nonString.' '.$publishString.' ?\');"><img src="'.$mainurl.'images/publish_y.png" width="12" height="12" border="0" title="'.$publishedString.'" alt="'.$publishedString:$publishString.' ?\');"><img src="'.$mainurl.'images/publish_n.png" width="12" height="12" border="0" title="'.$nonString.' '.$publishedString.'" alt="'.$nonString.' '.$publishedString).'" /></a> ':'').(in_array($this_is.'archive',$array_fields)?'<a href="'.$local_url.'&amp;'.$this_is.'Id='.$show_id.(isset($send)?'&amp;send='.$send:'').'&amp;toggle=archive" onclick="return confirm(\''.($getitem[array_search($this_is.'archive',$array_fields)]=='Y'?$nonString.' '.$archiveString.' ?\');"><img src="'.$mainurl.'images/archive_y.png" width="24" height="24" border="0" title="'.$archivedString.'" alt="'.$archivedString:$archiveString.' ?\');"><img src="'.$mainurl.'images/archive_n.png" width="24" height="24" border="0" title="'.$nonString.' '.$archivedString.'" alt="'.$nonString.' '.$archivedString).'" /></a>':'')
							.($admin_viewing||(in_array($this_is,$editable_by_membre)&&$_membre_owned)
								?'<a href="'.($admin_viewing===true?$local.'?lg='.$lg.'&amp;x=z&amp;y='.$this_is.'&amp;'.$this_is."Id=$show_id&amp;":$mainurl.lgx2readable($lg,'',$this_is,$show_id).($full_url===true?'?':'&amp;')).'send=edit">'.$modifierString.'</a> ':'')
							.($admin_viewing||(in_array($this_is,$deletable_by_membre)&&$_membre_owned)
								?'<a href="'.($admin_viewing===true?$local.'?lg='.$lg.'&amp;x=z&amp;y='.$this_is.'&amp;'.$this_is."Id=$show_id&amp;":$mainurl.lgx2readable($lg,'',$this_is,$show_id).($full_url===true?'?':'&amp;')).'send=delete" onclick="return confirm(\''.$confirmationeffacementString.'\');"><img src="'.$mainurl.'images/delete.gif" width="10" height="10" title="'.$effacerString.'" alt="'.$effacerString.'" border="0" /></a>':'')
							.'</div>'; // allows logged_in members to edit own content according to array $editable_by_membre in tpl($admin_viewing===true?$local.'?lg='.$lg.'&amp;x=z&amp;y='.$this_is.'&amp;'.$this_is."Id=$show_id&amp;send=edit":($full_url===true?$mainurl.lgx2readable($lg,'',$this_is,$show_id)

						//.'<a href="'.($full_url===true&&$admin_viewing===false?$mainurl.lgx2readable($lg,'',$this_is,$show_id)."?send=edit":($admin_viewing===true?$local.'?lg='.$lg.'&amp;x=z&amp;y='.$this_is.'&amp;'.$this_is.'Id='.$show_id.'&amp;send=delete" onclick="return confirm(\''.$confirmationeffacementString.'\');"><img src="'.$mainurl.'images/delete.gif" width="10" height="10" title="'.$effacerString.'" alt="'.$effacerString.'" border="0" /></a>'.'<br /><a href="'.$local.'?lg='.$lg.'&amp;x=z&amp;y='.$this_is:$local_url).'&amp;'.$this_is.'Id='.$show_id.'&amp;send=edit').'">'.$modifierString.'</a></div>';

						if (isset($array_tpl) && (!isset($bypass_this) || (isset($bypass_this) && ($bypass_this === false))))
						//  foreach($array_tpl as $tpl) {
						for($li=0;$li<count($array_tpl);$li++) {
							$tpl = $array_tpl[$li];
							//$_mod_content .= (isset($tpl[0])&&$tpl[0]=="$"?${substr($tpl,1)}:$tpl);
							if (isset($tpl[0]) && ($tpl[0]=="$")) {
								$_mod_content .= ${substr($tpl,1)};
								if (($li>0) && (${substr($tpl,1)} == '') && (isset($array_tpl[$li+1]) && ($array_tpl[$li+1] == '<br />')))
								$array_tpl[$li+1] = '<br />';
							} else
								$_mod_content .= $tpl;
						}

						if (($full_url===true)||($admin_viewing===true)) {
							$_mod_content = str_replace($local_url.'&amp;'.$this_is."Id=$show_id",lgx2readable($lg,'',$this_is,$show_id),$_mod_content);
							if (isset($that_is))
							$_mod_content = str_replace($local_url.'&amp;'.$that_is."Id=$show_id",lgx2readable($lg,'',$that_is,$show_id),$_mod_content);
						}

						if (!isset($filter_map) && (!isset($bypass_this) || (isset($bypass_this) && ($bypass_this === false))))
							$_mod_content .= '</div>';

						//  $_mod_content .= '</td></tr></table><hr />';
					}
				} else $_mod_content .= $error_request;
				$ii++;
			}
		} else {
			$_mod_content .= '';//0 '.$enregistrementString;
		}
		//  $_mod_content .= '</div></div>';
	}
	//if (isset($q))
	//$_mod_content = ereg_replace($q,'\<span class=\"qhighlight\"\>'.$q.'\<\/span\>',$_mod_content);
	if (isset($filter_map) || isset($filter_index)) {
		$admin_viewing_menu = '';
		$_head_mod_content = '';
	} else {
		$_mod_content .= '</div></div>';
		$_head_mod_content = '<div class="disp_inline">';
		if (isset($select_array) && is_array($select_array))
		$_head_mod_content = (isset($selectHead)?$selectHead:'').$_head_mod_content;
		/* no need to show this
	$_head_mod_content = ${$this_is."String"};
	$_head_mod_content = $_pre_css_bgimg.'<a href="'.$_SERVER['REQUEST_URI'].'" target="_self">'.$_head_mod_content.'</a>'.$_post_css_bgimg;
	*/
		if ((isset(${$this_is."Id"}) || isset(${$this_is."Type"})) && ($view_see_all_button === true))
			$_head_mod_content .= '<div class="see-all">'.$_pre_css_bgimg.'<a href="'.($admin_viewing===true?$local_url:lgx2readable($lg,$x,$this_is)).'" target="_self">'.$voirString.' '.$toutString.'</a>'.$_post_css_bgimg.'</div>';//.'<div style="float:left;">'.'</div>';
		//  if (!isset(${$this_is."Id"}) && (!isset(${"filter_search"}) || (isset(${"filter_search"}) && ((${"filter_search"}===true) || in_array($this_is.${"filter_search"},$array_fields) || (isset($that_is) && in_array($that_is.${"filter_search"},$that_array_fields))))))
		if (!isset(${$this_is."Id"}) && (!isset(${"filter_search"}) || (isset(${"filter_search"}) && (${"filter_search"}===true))))
			$_head_mod_content .= '<div class="filter-search">'.($admin_viewing===true?gen_form($lg,$x,$y):gen_form($lg,$x))
			.'<div>'.$_pre_css_bgimg.'<div class="bgimg-two-bot-search">
			<input type="text" name="q" value="'.(isset($q)?$q:'').'" /><input type="submit" value="'.$rechercherString.'" />'
			.(isset($q)&&($q_count>0)?'<br /> > '.$q_count.' '.$class_conjugaison->plural($confirmeString,'',$q_count):'')
			.' ('.(isset($filter_searchfield)?${$filter_searchfield."String"}:${(substr($array_fields[5],strlen($this_is))=='gendre'
			?'nom':substr($array_fields[5],strlen($this_is)))."String"}).')</div>'.$_post_css_bgimg.'</div></form></div>';
		$_head_mod_content .= '</div>';
		if (in_array($this_is."coords",$array_fields)&&!isset($that_is))
			$_ordered_mod_content = '<div class="see-all">'.$_pre_css_bgimg.'<a href="'.($admin_viewing===true?$local.'?lg='.$lg.'&amp;x='
			.(isset($filter_mapdisplay)&&preg_match("/^[0-9]+\$/",$filter_mapdisplay)
			?$filter_mapdisplay:$x):lgx2readable($lg,(isset($filter_mapdisplay)&&preg_match("/^[0-9]+\$/",$filter_mapdisplay)
			?$filter_mapdisplay:''))).'">&nbsp;'.$mapString.'&nbsp;</a>'.$_post_css_bgimg.(isset($_ordered_mod_content)
			?$_ordered_mod_content:'').'</div>';
		if (isset($_ordered_mod_content) && ($_ordered_mod_content != '')) {
			$_head_mod_content .= $_ordered_mod_content;
		}
	}
	$_mod_content = $_head_mod_content.$_mod_content;
}
${"_mod_".$this_is} = $_mod_content;

//reset
foreach(array($this_is."Id",$this_is."Type","protected_show","view_see_all_button","lightbox","show_oneimg_only","q",
		"this_is","filter_searchfield","list_array_fields","that_is","send","filter_map","filter_index","filter_future",
		"filter_past","filter_ordre","filter_leftlist","rqst","toggle","future","past","ordre","par","index_by_alpha",
		"ordered_by","this_index","past_index","show_array","array_tpl","show_array_by_id","array_tpl_by_id",
		"show_array_linked","array_tpl_linked","linked_content","bypass_this","select_array","filter_mapdisplay"
) as $reset_k) {
	${$reset_k} = null;
}
