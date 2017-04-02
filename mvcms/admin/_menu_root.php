<?php #۞ #
if (stristr($_SERVER['PHP_SELF'], basename(__FILE__))){include '_security.php';Header("Location: $redirect");Die();}

if (!isset($protected_show))
$protected_show = false;
/*
if (stristr($_SERVER['PHP_SELF'],$urladmin) && ($logged_in === false)) {
//  $getcwd = getcwd()."/".$up;
}
*/
//!!!! contlogo in db is contpriv !!!!
$where_statut_lang = " contstatut='Y' AND contlang='$lg' AND ";
// allows multiple privileges because userPriv is an array
if (!isset($user_priv) || ($logged_in === false)) $user_priv = array("1");//
$where_priv = " (";
foreach($user_priv as $key)
$where_priv .= " contlogo LIKE '%$key%' OR ";
$where_priv .= " contlogo LIKE '%1%' OR contlogo='') AND ";

if ($protected_show === true)
$where_priv = "";

if (isset($x) && ($x !== '1')) {
	$redir_tourl = sql_getone($tblcont,"WHERE $where_statut_lang $where_priv contpg='$x' ",
							"conturl").($htaccess4sef===true?"_$lg.php":'');
	if (file_exists($redir_tourl)) {
		Header("Location: $mainurl$redir_tourl");Die();
	} else {Header("Location: $redirect");Die();}
}
if (!stristr($_SERVER['PHP_SELF'],"/index.php"))
$x = sql_getone($tblcont,
		"WHERE contstatut='Y' AND contlang='$default_lg' AND $where_priv conturl='"
		.substr(substr($_SERVER['PHP_SELF'],$extractfromphpself),0,-7)."_$default_lg.php' ",
		"contpg") ; // define x if no redirect
else {
	// will show right page that goes above 10 with x1sub false
	$last_piece = array_reverse($array_uri);
	if (isset($last_piece[0])) {
		if (($last_piece[0]=='') && isset($last_piece[1])) $last_piece[0] = $last_piece[1];
		$last_piece = $last_piece[0];
	} else $last_piece = '';
	$x = sql_getone($tblcont,"WHERE contstatut='Y'
								AND contlang='$lg'
								AND $where_priv conturl='$last_piece' ",
							"contpg");
}
if ($htaccess4sef === false) {
	foreach($array_modules as $key) {
		if (isset(${$key."Id"}) && preg_match("/^[0-9]+\$/",${$key."Id"})) {
			$id_type = sql_getone(${"tbl".$key},"WHERE ".$key."id='".${$key."Id"}."' ",
												$key."type");
			$id_archive = sql_getone(${"tbl".$key},"WHERE ".$key."id='".${$key."Id"}."' ",
													$key."archive");
			if ($id_type != '') {//&& in_array($id_archive,array('','N'))) {
				$id_type_url = sql_getone($tblcont,"WHERE contstatut='Y' AND conturl LIKE '%"
								.space2underscore(sql_stringit($key.'type',$id_type))."_$lg."
								."%' ","contpg");
				if (($id_type_url == $x) && in_array($id_archive,array('','N'))) {
					$potential_x = $x;
					$x = $id_type_url;
				} else {
					if ($id_archive == 'Y')
					$potential_x = $x;
				}
			} else {
				if ($id_archive == 'Y')
				$potential_x = $x;
			}
		}
	}
}

if (($htaccess4sef === true) && ($_SERVER['REQUEST_URI'] != $url_mirror)) {
	$lastpg = array_reverse($array_uri);
	if (count($lastpg)>1) {
		$count_lastpg = (count($array_lang)>1?count($lastpg)-1:count($lastpg));
		foreach($array_uri as $key=>$value) {
			if ($value == '') {
				$count_lastpg -= 1;
				continue;
			}
			if ($value == 'search') {
				$search_q_from_uri = $key+1;
			} else if (isset($search_q_from_uri)&&($key==$search_q_from_uri)) {
				$q = $value;
			} else {
				if ($value != '')
				$new_lastpg[] = $value;
			}
			if (in_array($value,$array_modules))
			$potential_type = $value;
			if (isset($tblhtaccess))
				if (in_array($value,sql_array($tblhtaccess,
											"WHERE htaccessstatut='Y' ","htaccessurl"))) {
					$get_x = sql_get($tblhtaccess,"WHERE htaccessstatut='Y'
										AND htaccesslang='$lg' AND htaccessurl='$value' "
										.(isset($potential_type)&&$value!=$potential_type?
											" AND htaccesstype='$potential_type'":'')
										." ORDER BY htaccessdate DESC ",
										"htaccessitem,htaccesstype");
          			if ($get_x[0] != '.') {
						${$get_x[1]."Id"} = $get_x[0];
						$this_sql_fields = sql_fields(${"tbl".$get_x[1]},'array');
						$get_info = sql_get(${"tbl".$get_x[1]},
											"WHERE ".$get_x[1]."rid='".$get_x[0]."'
												AND ".$get_x[1]."lang='$lg'
												AND ".$get_x[1]."statut='Y' ",
											implode(",",$this_sql_fields));
											//$get_x[1]."type,".$get_x[1]."archive");
						if ($get_info[0]!= '.') {
							${$get_x[1]."Type"} = $get_info[array_search(
													$get_x[1]."type",$this_sql_fields)];
							$is_archived = $get_info[array_search(
													$get_x[1]."archive",$this_sql_fields)];
							if ((${$get_x[1]."Type"}!='')
								&& in_array($is_archived,array('','N')))
							$new_potential_x = sql_getone($tblcont,
								"WHERE $where_statut_lang conturl='".space2underscore(
									sql_stringit($get_x[1]."type",${$get_x[1]."Type"}))."' ",
								"contpg");
							if (isset($new_potential_x) && ($new_potential_x != ''))
							$x = $new_potential_x;
						}
						$potential_x = sql_getone($tblcont,
										" WHERE $where_statut_lang conttype='".$get_x[1]."' ",
										"contpg");
						$potential_array_uri_k = $key;
					}
				}
			}
			if (isset($potential_x) && in_array($x,array('','.'))) {
				$try = sql_get($tblcont,"WHERE $where_statut_lang conturl='"
										.$array_uri[$potential_array_uri_k-1]."' ",
										"contpg,contentry");
				if ($get_x[0] == '.')
				$potential_x = false;
				else {
					if (strstr($try[1],"[".$get_x[1].":")
						|| strstr($try[1],"[".$get_x[1]."]"))
					$x = $try[0];
					else
					$x = $potential_x;
				}
			}
			if (isset($new_lastpg)) {
				$lastpg = array_reverse($new_lastpg);
				$count_lastpg = (count($array_lang)>1?count($lastpg)-1:count($lastpg));
			}
		} else
		$count_lastpg = 1;

if (in_array($x,array('','.')))
	if (!stristr($_SERVER['PHP_SELF'],$urladmin)
		&& !(($_SERVER['REQUEST_URI'] == $url_mirror)
		&& (substr($_SERVER['PHP_SELF'],$extractfromphpself) == "index.php"))) {
		if (isset($lastpg[0])&&($lastpg[0]!="")) {
			if (preg_match("/^[0-9]+\$/",$lastpg[0]) && isset($lastpg[1])) {
				$check_type_for_identification = true;
				$lastpg_arrayrev = $lastpg[1];
				$count_lastpg -= 1;
			} else
			$lastpg_arrayrev = $lastpg[0];
		} else
		$lastpg_arrayrev = (isset($lastpg[1])?$lastpg[1]:'');
		$get_x = sql_get($tblcont,"WHERE $where_statut_lang LENGTH(contpg)=$count_lastpg
									AND conturl='$lastpg_arrayrev' ",
								"contpg,conttype");
    	$x = (in_array($get_x[0],array("","."))
    		&&(!in_array($get_x[1],$array_modules)
    			||(in_array($get_x[1],$array_modules)
    			&&in_array(sql_getone($tblcont,
    								"WHERE $where_statut_lang conttype='$get_x[1]' ",
    								"contpg"),
    				array("",".")))
    		)?"1":$get_x[0]);
		if (isset($check_type_for_identification)) ${$get_x[1]."Id"} = $lastpg[0];
		if (($x == "") || ($x == '.')) {
			//	check htaccess table urls
			$lastpg_arrayrev = space2underscore($lastpg_arrayrev);
			if (isset($tblhtaccess))// :: check later if relevant here
				if (in_array($lastpg_arrayrev,sql_array($tblhtaccess,
						"WHERE htaccessstatut='Y' AND htaccesslang='$lg' ","htaccessurl"))) {
					$get_x = sql_get($tblhtaccess,"WHERE htaccessstatut='Y'
													AND htaccesslang='$lg'
													AND htaccessurl='$lastpg_arrayrev' ",
				"htaccessitem,htaccesslang,htaccesstype,htaccessmetadesc,htaccessmetakeyw");
					if ($get_x[0] != '.') {
						Header("Location: $mainurl"
							.lgx2readable($get_x[1],'',$get_x[2],$get_x[0])
							.($_SERVER['QUERY_STRING']!=""?'?'.$_SERVER['QUERY_STRING']:''));
						Die();
					}
				}
				$try = sql_get($tblcont,
						"WHERE contstatut='Y' AND $where_priv conturl='$lastpg_arrayrev' ",
						"contpg,contlang,conturl");
				if ($try[0]!='.') {
				//	$_SESSION['mv_notice'] = "redirected";
        			Header("Location: $mainurl".lgx2readable($lg,$try[0])
        				.($_SERVER['QUERY_STRING']!=""?'?'.$_SERVER['QUERY_STRING']:''));
        			Die();
				//	Header("Location: $mainurl".(count($array_lang)>1?$try[1]."/":'').$try[2]
				//	.($_SERVER['QUERY_STRING']!=""?'?'.$_SERVER['QUERY_STRING']:''));Die();
				}
			}
		} else {
			$x = '1';
		}
		if  (($_SERVER['REQUEST_URI'] == $url_mirror)
			|| ($_SERVER['REQUEST_URI'] == $url_mirror."/")
			|| ($_SERVER['REQUEST_URI'] == $url_mirror.$lg)
			|| ($_SERVER['REQUEST_URI'] == $url_mirror.$lg."/"))
		$x = '1';
	}
	if (($x == "") || ($x == '.')) {
		$x = '1';
		if (!stristr($_SERVER['PHP_SELF'],$urladmin))
		if (($_SERVER['REQUEST_URI'] != $url_mirror) && ($_SERVER['QUERY_STRING']=="")) {
			Header("Status: 404 Not Found");
			$notice = '<div
				style="text-align:center;color:red;font-weight:bold;margin-top:-5px;">'
					.strtoupper($erreurString).' 404! '
					.($lg=='it'?'Non Esistente':($lg=='fr'?'Introuvable':'Not Found')).'<br />
				<style type="text/css">
#goog-wm { }
#goog-wm h3.closest-match { }
#goog-wm h3.closest-match a { }
#goog-wm h3.other-things {background:none;height:10px;min-height:10px;margin-top:-5px;}
#goog-wm ul li { }
#goog-wm li.search-goog {display:block;}
				</style>
				<script type="text/javascript">
var GOOG_FIXURL_LANG = \''.$lg.'\';
var GOOG_FIXURL_SITE = \''.$mainurl.'\';
				</script>';
			if (!stristr($_SERVER['HTTP_HOST'],"localhost"))
			$notice .= '
				<script type="text/javascript"
    			src="http://linkhelp.clients.google.com/tbproxy/lh/wm/fixurl.js"></script>';
			$notice .= '</div>';
//  Header("HTTP/1.1 404 Not Found");
//  Header("Location: $redirect", "404 Not Found");
//    exit;
		}
	}

$scroller_div = "";
$scroller = sql_get($tblcont," WHERE conttype='scroller' AND contlang='$lg' ",
							"conttitle, contentry");
if ($scroller[0] == '.') {
	$scroller = sql_get($tblcont," WHERE conttype='scroller' AND contlang='$default_lg' ",
							"conttitle, contentry");
}
if ($scroller[0] == '.') {
	$scrollertitle = "-News Wire";
	$scrollerentry = '';
} else {
	$scrollertitle = $scroller[0];
	$scrollerentry = $scroller[1];
	$scroller_div = '
    <div id="scroller">
		<marquee behavior="scroll" direction="up" height="50"
				scrollamount="1" scrolldelay="30"
				onmouseover=\'this.stop()\' onmouseout=\'this.start()\'>
		<br /> <br />
		<h1>
'.$scrollertitle.'
		</h1>
'.$scrollerentry.'
		</marquee>
    </div><!-- END SCROLLER -->';
}

$leftlinks = sql_get($tblcont,"WHERE conttype='leftlinks' AND contlang='$lg' ",
							"conttitle,contentry");
if ($leftlinks[0] == '.') {
	$leftlinks = sql_get($tblcont,"WHERE conttype='leftlinks' AND contlang='$default_lg' ",
							"conttitle,contentry");
}
if ($leftlinks[0] == '.') {
	$leftlinkstitle = "-Left Links";
	$leftlinksentry = '';
} else {
	$leftlinkstitle = $leftlinks[0];
	$leftlinksentry = $leftlinks[1];
}

$toplinks = sql_get($tblcont,"WHERE conttype='toplinks' AND contlang='$lg' ",
							"conttitle,contentry,contpg");
if ($toplinks[0] == '.') {
	$toplinkstitle = "-Top Links";
	$toplinksentry = '';
} else {
	$toplinkstitle = $toplinks[0];
	$toplinksentry = str_replace("content/",$up."content/",$toplinks[1]);
}

if ($x == '0')
{Header("Location: $redirect");Die();}

$map_info = sql_get($tblcont,"WHERE conttype='map' ","contpg, conturl, conttitle");
$search_info = sql_get($tblcont,"WHERE conttype='search' ","contpg, conturl, conttitle");

if (sql_nrows($tblcont,"WHERE contlang='$lg' ") == 0)
$lg = $default_lg;

$where_statut_lang .= $where_priv;

$pgcontent = sql_get($tblcont,"WHERE $where_statut_lang contpg='$x' ",
						"conttitle,contentry,contmetadesc,contmetakeyw,conttype,contlogo");
$title = $pgcontent[0];
$content = $pgcontent[1];
$desc = $pgcontent[2];
$keyw = $pgcontent[3];
$default_desc_keyw = sql_get($tblcont,"WHERE contpg='1' AND contlang='$lg' ",
								"contmetadesc,contmetakeyw");
if ($default_desc_keyw[0] == '.')
	if ($lg != $default_lg)
	$default_desc_keyw = sql_get($tblcont,"WHERE contpg='1' AND contlang='$default_lg' ",
								"contmetadesc,contmetakeyw");
if ($default_desc_keyw[0] == '.') {
	$default_desc_keyw[0] = '';
	$default_desc_keyw[1] = '';
}
//in _strings.php
if ($desc == '') $desc = ($default_desc_keyw[0]!=''?$default_desc_keyw[0]:$meta_desc);
if ($keyw == '') $keyw = ($default_desc_keyw[1]!=''?$default_desc_keyw[1]:$meta_keyw);

$this_type = $pgcontent[4];
$this_priv = $pgcontent[5];

//if (($x == "") || ($x == '.'))
foreach($array_modules as $key) {
	################################################################
	if (isset(${$key."Id"}) && preg_match("/^[0-9]+\$/",${$key."Id"})
		&& (!isset($potential_x) || (isset($potential_x) && ($potential_x === false)))
		&& (($this_type != $key)
			|| (isset($tblhtaccess) && isset($check_type_for_identification))
			|| (($htaccess4sef===true) && stristr($_SERVER['QUERY_STRING'],${$key."Id"}))
		))
		//	&& (isset($tblhtaccess) && (stristr($_SERVER['QUERY_STRING'],${$key."Id"})
		//	|| isset($check_type_for_identification))
		//	|| (!isset($tblhtaccess) && ($this_type != $key))) )//)
    {
		if (($htaccess4sef === false) && isset(${$key."Id"})
			&& preg_match("/^[0-9]+\$/",${$key."Id"})) {
			$id_type = sql_getone(${"tbl".$key},"WHERE ".$key
					.(in_array($key.'rid',sql_fields(${"tbl".$key},'array'))?'r':'')."id='"
					.${$key."Id"}."' ",
				$key."type");
			$id_archive = sql_getone(${"tbl".$key},"WHERE ".$key
					.(in_array($key.'rid',sql_fields(${"tbl".$key},'array'))?'r':'')."id='"
					.${$key."Id"}."' ",
				$key."archive");
			if ($id_type != '')
			$id_type_url = sql_getone($tblcont,
				"WHERE contstatut='Y' AND contpg='$x' AND conturl LIKE '%"
					.space2underscore(sql_stringit($key.'type',$id_type))."_$lg."."%' ",
				"conturl");
		}
		if (!isset($id_type_url) || (isset($id_type_url) && ($id_type_url != ''))) {
			$x_of_type = sql_getone($tblcont,
										"WHERE $where_statut_lang conttype='$key' ",
										"contpg");
			$header_loca = str_replace("?&","?",lgx2readable($lg,
										(in_array($x_of_type,array("","."))?"1":$x_of_type))
							.($htaccess4sef===true?
								(isset($tblhtaccess)&&preg_match("/^[0-9]+\$/",${$key."Id"})?
									sql_getone($tblhtaccess,
										"WHERE htaccesslang='$lg'
											AND htaccessitem='".${$key."Id"}."'
											AND htaccesstype='$key'
											ORDER BY htaccessdate DESC ",
										"htaccessurl")
									:((${$key."Id"}=='new')&&($logged_in===true)?'?send=':'')
										.${$key."Id"}):'?'.$key.'Id='.${$key."Id"})
							.(isset($send)?
								'?'.str_replace($key."Id=".${$key."Id"},"",
										$_SERVER['QUERY_STRING']):'')
						);
			//	echo 'header_loca > '.$header_loca;
			if (!in_array($x_of_type,array("",".")))
			{Header("Location: $mainurl".$header_loca);Die();}
			else
			{$view_see_all_button = false;$content = "[$key]";}
		}
	}//htaccessstatut='Y' AND htaccesslang='$lg' AND
}

$default_lg_pg = space2underscore(sql_getone($tblcont,
									"WHERE contpg='$x' AND contlang='$default_lg' ",
									"conttitle"));

if	($x == '1')	$default_lg_pg = 'index'	;

include $getcwd.$up.$urladmin.'_modulesparser.php';


if (isset($nlid))
include $getcwd.$up.$urladmin.'_shownewsletter.php';

include $getcwd.$up.$urladmin.'_menu_script.php';

if	($title == "")	$title = $slogan	;
if	($content == "")
	if ($nrows == 0) {
		$content = "In construction!";
		$title = 'Welcome';
	} else
	{Header("Location: $redirect");Die();}

$login = $loginform	;
