<?PHP
if (stristr($_SERVER['PHP_SELF'],"_menu_pagine_root.php")) {
	include '_security.php';
	Header("Location: $redirect");Die();
}

$dbtable = $tblcont;
if	(stristr($_SERVER['REQUEST_URI'],$urlintro))	$dbtable = $tblintro	;
$menu_javascript = '<script type="text/javascript"><!-- //
/*
 $Name  : menu_pagine.js, 20070227 © 1996-2007 pf;
 $Date  : Tue, 27 Feb 2007 10:00:00 GMT;
 $Exp   : Mon, 31 Dec 2007 23:59:59 GMT;
 $Lang  : javascript;
 $Target: none;
 $Type  : text/javascript;
 $Desc  : Load JS Menu.
 Programming and graphics by pf  -  All rights reserved.
 Please send any questions to: pjoef_at_tiscali.it
 adapted by www.mediavince.com to generate with PHP ordered and cleaned...
*/
//label, mw, mh, fnt, fs, fclr, fhclr, bg, bgh, halgn, valgn, pad, space, to,
// sx, sy, srel, opq, vert, idt, aw, ah, pth
//=
//"var-conttitle", 160, 20, "var-font_family", "var-font_size", "#000000", "Navy", "#FFFFFF","#FFFFFF", "left", "middle", 2, 0, 1000, 
// -5, 7, true, true, true, 0, false, false, pth
//.
// function mmLoadMenus
// pth - path
function mmLoadMenus(pth)
{
// IMPORTANT FOR ROUTINE START
  if (window.menu_1) return;
';

$menu_javascript_vars = ', 160, 20, "'.$font_family.'", "'.$font_size.'", "#666666", "Navy", "#FFFFFF","#FFFFFF", "left", "middle", 2, 0, 1000, -5, 7, true, true, true, 0, false, false, pth';//"#000000", "Navy", "#FFFFFF","#FFFFFF", "left", "middle", 2, 0, 1000, -5, 7, true, true, true, 0, false, false, pth';

$windowmenu = '';
$extrawindowmenu = '';
$rootwindowmenu = '';
$addmenuitem = '';
$menuitem = '';
$toprelated = '';
$related = '';

$jsvar_1 = '';
$jsvar_2 = '';
$jsvar_3 = '';
$pages = '';

$regex_top = 2;
if ($x1subok === true) $regex_top -= 1;

$where = "WHERE $where_statut_lang conttype!='toplinks' AND conttype!='scroller' AND conttype!='leftlinks' ";// conturl LIKE '%.php%' ";// AND contorient!='center'
$read = @mysql_query("SELECT * FROM $dbtable $where ORDER BY contpg DESC ");
$nrows = @mysql_num_rows($read);
for ($i=0;$i<$nrows;$i++) {
	$row = mysql_fetch_array($read);
	$row_conturl = $row["conturl"];
	$row_conttitle = $row["conttitle"];
  $row_contpg = $row["contpg"];
  $contlogo_lock = ((!stristr($row["contlogo"],"1") && ($row["contlogo"] !== ""))?'lock':'');
  if	(!strstr($_SERVER['REQUEST_URI'],$urladmin) || !stristr($_SERVER['REQUEST_URI'],$urlintro)) {
  	$clean_title = space2underscore($row_conttitle);
  	if ($htaccess4sef === true) {
    	$row_conturl = (count($array_lang)>1?$lg."/":"");//$mainurl;
    	if (strlen($row_contpg)>1) {
        for($hj=1;$hj<strlen($row_contpg);$hj++){
          $substr_pg = substr($row_contpg,0,$hj);
          $row_conturl .= space2underscore(sql_getone($tblcont,"WHERE $where_statut_lang contpg='$substr_pg' ","conttitle"))."/";
        }
      }
      $row_conturl .= $clean_title."/";
    }
  }
	$row_conttitle = str_replace(" ","&nbsp;",$row["conttitle"]);
	if	(strstr($_SERVER['REQUEST_URI'],$urladmin) || stristr($_SERVER['REQUEST_URI'],$urlintro))	$row_conturl = '?lg='.$lg.'&x='.$row["contpg"]	; // strstr($_SERVER['PHP_SELF'],$urlclient) || 
	if ($row["contstatut"] == 'N') $row_conttitle = '<font color=\'Red\'><span class=\'sub'.$contlogo_lock.'\'>*'.$row["conttitle"].'*</span></font>' ;
	if	(strlen($row["contpg"]) == '3')		$jsvar_3 .= 'var m'.$row["contpg"].' = "<span class=\'sub'.$contlogo_lock.'\'>'.$row_conttitle.'</span>";'.'var l'.$row["contpg"].' = "'.$row_conturl.'";'."\n"	;
	else if	(strlen($row["contpg"]) == '2')		$jsvar_2 .= 'var m'.$row["contpg"].' = "<span class=\'sub'.$contlogo_lock.'\'>'.$row_conttitle.'</span>";'.'var l'.$row["contpg"].' = "'.$row_conturl.'";'."\n"	;
	else	$jsvar_1 .= 'var m'.$row["contpg"].' = "<span class=\'sub'.$contlogo_lock.'\'>'.$row_conttitle.'</span>";'.'var l'.$row["contpg"].' = "'.$row_conturl.'";'."\n"	;
	if ($i > '0') $iminus = $i-1 ;
	if (preg_match("/^[$regex_top-9][0-9]{3}\$/", $row["contpg"])) {
		if ((sql_nrows($dbtable,"WHERE $where_statut_lang contpg='".substr($row["contpg"],0,-1)."' ") == '1') && (sql_nrows($dbtable,"WHERE $where_statut_lang contpg='".substr($row["contpg"],0,-2)."' ") == '1')) {
			$addmenuitem = '  menu_'.substr($row["contpg"],0,-1).'.addMenuItem("<span class=\'sub'.$contlogo_lock.'\'>'.$row_conttitle.'</span>","window.open(\'" + pth + "'.$row_conturl.'\', \'_self\');")'."\n".$addmenuitem;
		}
		if ($row["contmenu"] !== '') {
			$row["contmenu"] = explode("|", $row["contmenu"]);
			if	(!is_array($row["contmenu"]))	$row["contmenu"] =	array($row["contmenu"])	;
				for ($k=0;$k<count($row["contmenu"]);$k++) {
					if (sql_getone($dbtable,"WHERE $where_statut_lang contpg='".$row["contmenu"][$k]."' ","contstatut") == 'Y') {
						if (sql_nrows($dbtable,"WHERE $where_statut_lang contpg='".(($row["contmenu"][$k]*10)+1)."' ") > '0') {
							$addmenuitem = '  menu_'.$row["contpg"].'.addMenuItem(menu_'.$row["contmenu"][$k].',"window.open(\'" + pth + "'.$row_conturl.'\', \'_self\');");'."\n".$addmenuitem;
						if (sql_nrows($dbtable,"WHERE $where_statut_lang contpg='".(($row["contmenu"][$k]*10)+1)."' ") > '0') {
							$windowmenu .= '  window.menu_'.$row["contpg"].' = new Menu("<span class=\'sub'.$contlogo_lock.'\'></span>'.sql_getone($dbtable,"WHERE $where_statut_lang contpg='".$row["contmenu"][$k]."' ","conttitle").'"'.$menu_javascript_vars.');'."\n";
						} else {
							$windowmenu .= '  window.menu_'.$row["contpg"].' = new Menu(menu_'.$row["contmenu"][$k].''.$menu_javascript_vars.');'."\n";
						}
					} else {
						if	(sql_nrows($dbtable,"WHERE $where_statut_lang contpg='".(($row["contpg"]*10)+1)."' ") == '0')	
						$windowmenu .= '  window.menu_'.$row["contmenu"][$k].' = new Menu("<span class=\'sub'.$contlogo_lock.'\'></span>'.sql_getone($dbtable,"WHERE $where_statut_lang contpg='".$row["contmenu"][$k]."' ","conttitle").'"'.$menu_javascript_vars.');'."\n";
						$addmenuitem = '  menu_'.$row["contmenu"][$k].'.addMenuItem("<span class=\'sub'.$contlogo_lock.'\'></span>'.sql_getone($dbtable,"WHERE $where_statut_lang contpg='".$row["contpg"]."' ","conttitle").'","window.open(\'" + pth + "'.$row_conturl.'\', \'_self\');");'."\n".$addmenuitem;
						if	(sql_nrows($dbtable,"WHERE $where_statut_lang contpg='".(($row["contmenu"][$k]*10)+1)."' ") == '0')	
						$windowmenu .= '  window.menu_'.$row["contpg"].' = new Menu(menu_'.$row["contmenu"][$k].''.$menu_javascript_vars.');'."\n";
						if	(sql_nrows($dbtable,"WHERE $where_statut_lang contpg='".(($row["contmenu"][$k]*10)+1)."' ") == '0')	
						$windowmenu .= '  window.menu_'.$row["contpg"].' = new Menu(menu_'.$row["contmenu"][$k].''.$menu_javascript_vars.');'."\n";

						$addmenuitem = '  menu_'.$row["contpg"].'.addMenuItem("<span class=\'sub'.$contlogo_lock.'\'></span>'.sql_getone($dbtable,"WHERE $where_statut_lang contpg='".$row["contmenu"][$k]."' ","conttitle").'","window.open(\'" + pth + "'.$row_conturl.'\', \'_self\');");'."\n".$addmenuitem;
						$addmenuitem = '  menu_'.$row["contpg"].'.addMenuItem(menu_'.$row["contmenu"][$k].',"window.open(\'" + pth + "'.$row_conturl.'\', \'_self\');");'."\n".$addmenuitem;
					}
				}
				if	(sql_nrows($dbtable,"WHERE $where_statut_lang contpg='".(($row["contmenu"][$k]*10)+1)."' ") == '0')	
				$windowmenu .= '  window.menu_'.$row["contmenu"][$k].' = new Menu("<span class=\'sub'.$contlogo_lock.'\'></span>'.sql_getone($dbtable,"WHERE $where_statut_lang contpg='".$row["contmenu"][$k]."' ","conttitle").'"'.$menu_javascript_vars.');'."\n";
			}
		}
	} else if (preg_match("/^[$regex_top-9][0-9]{2}\$/", $row["contpg"])) {
		if (sql_nrows($dbtable,"WHERE $where_statut_lang contpg='".substr($row["contpg"],0,-1)."' ") == '1') {
			if (sql_nrows($dbtable,"WHERE $where_statut_lang contpg='".(($row["contpg"]*10)+1)."' ") == '1') {
				$windowmenu .= '  window.menu_'.$row["contpg"].' = new Menu("'.$row_conttitle.'"'.$menu_javascript_vars.');'."\n";
				$addmenuitem = '  menu_'.substr($row["contpg"],0,-1).'.addMenuItem(menu_'.$row["contpg"].',"window.open(\'" + pth + "'.$row_conturl.'\', \'_self\');")'."\n".$addmenuitem;
				$menuitem .= '  menu_'.$row["contpg"].'.hideOnMouseOut=true;'."\n";
				$menuitem .= '  menu_'.$row["contpg"].'.childMenuIcon=pth+"images/arrow.gif";'."\n";
				$menuitem .= '  menu_'.$row["contpg"].'.bgColor=\'#BFBFBF\';'."\n";
				$menuitem .= '  menu_'.$row["contpg"].'.menuBorder=1;'."\n";
			} else {
				$addmenuitem = '  menu_'.substr($row["contpg"],0,-1).'.addMenuItem("<span class=\'sub'.$contlogo_lock.'\'>'.$row_conttitle.'</span>","window.open(\'" + pth + "'.$row_conturl.'\', \'_self\');")'."\n".$addmenuitem;
			}
		}
		if ($row["contmenu"] !== '') {
			$row["contmenu"] = explode("|", $row["contmenu"]);
			if	(!is_array($row["contmenu"]))	$row["contmenu"] =	array($row["contmenu"])	;
			for ($k=0;$k<count($row["contmenu"]);$k++) {
				if (sql_getone($dbtable,"WHERE $where_statut_lang contpg='".$row["contmenu"][$k]."' ","contstatut") == 'Y') {
					if (sql_nrows($dbtable,"WHERE $where_statut_lang contpg='".(($row["contmenu"][$k]*10)+1)."' ") > '0') {
						$addmenuitem = '  menu_'.$row["contpg"].'.addMenuItem(menu_'.$row["contmenu"][$k].',"window.open(\'" + pth + "'.$row_conturl.'\', \'_self\');");'."\n".$addmenuitem;
					} else {
						$addmenuitem = '  menu_'.$row["contpg"].'.addMenuItem("<span class=\'sub'.$contlogo_lock.'\'></span>'.sql_getone($dbtable,"WHERE $where_statut_lang contpg='".$row["contmenu"][$k]."' ","conttitle").'","window.open(\'" + pth + "'.$row_conturl.'\', \'_self\');");'."\n".$addmenuitem;
					}
				}
				if	(sql_nrows($dbtable,"WHERE $where_statut_lang contpg='".(($row["contmenu"][$k]*10)+1)."' ") == '0')	
				$windowmenu .= '  window.menu_'.$row["contmenu"][$k].' = new Menu("<span class=\'sub'.$contlogo_lock.'\'></span>'.sql_getone($dbtable,"WHERE $where_statut_lang contpg='".$row["contmenu"][$k]."' ","conttitle").'"'.$menu_javascript_vars.');'."\n";
			}
		}
	} else if (preg_match("/^[$regex_top-9][0-9]{1}\$/", $row["contpg"])) {
		if (sql_nrows($dbtable,"WHERE $where_statut_lang contpg='".substr($row["contpg"],0,-1)."' ") == '1') {
			if (sql_nrows($dbtable,"WHERE $where_statut_lang contpg='".(($row["contpg"]*10)+1)."' ") == '1') {
				$windowmenu .= '  window.menu_'.$row["contpg"].' = new Menu("<span class=\'sub'.$contlogo_lock.'\'>'.$row_conttitle.'</span>"'.$menu_javascript_vars.');'."\n";
				$addmenuitem = '  menu_'.substr($row["contpg"],0,-1).'.addMenuItem(menu_'.$row["contpg"].',"window.open(\'" + pth + "'.$row_conturl.'\', \'_self\');");'."\n".$addmenuitem;
				$menuitem .= '  menu_'.$row["contpg"].'.hideOnMouseOut=true;'."\n";
				$menuitem .= '  menu_'.$row["contpg"].'.childMenuIcon=pth+"images/arrow.gif";'."\n";
				$menuitem .= '  menu_'.$row["contpg"].'.bgColor=\'#BFBFBF\';'."\n";
				$menuitem .= '  menu_'.$row["contpg"].'.menuBorder=1;'."\n";
				if ($row["contmenu"] !== '') {
					$row["contmenu"] = explode("|", $row["contmenu"]);
					if	(!is_array($row["contmenu"]))	$row["contmenu"] =	array($row["contmenu"])	;
					for ($k=0;$k<count($row["contmenu"]);$k++) {
						if (sql_getone($dbtable,"WHERE $where_statut_lang contpg='".$row["contmenu"][$k]."' ","contstatut") == 'Y') {
							if (sql_nrows($dbtable,"WHERE $where_statut_lang contpg='".(($row["contmenu"][$k]*10)+1)."' ") > '0') {
								$addmenuitem = '  menu_'.$row["contpg"].'.addMenuItem(menu_'.$row["contmenu"][$k].',"window.open(\'" + pth + "'.$row_conturl.'\', \'_self\');");'."\n".$addmenuitem;
							} else {
								$addmenuitem = '  menu_'.$row["contpg"].'.addMenuItem("<span class=\'sub'.$contlogo_lock.'\'></span>'.sql_getone($dbtable,"WHERE $where_statut_lang contpg='".$row["contmenu"][$k]."' ","conttitle").'","window.open(\'" + pth + "'.$row_conturl.'\', \'_self\');");'."\n".$addmenuitem;
							}
						}
						if	(sql_nrows($dbtable,"WHERE $where_statut_lang contpg='".(($row["contmenu"][$k]*10)+1)."' ") == '0')	
						$windowmenu .= '  window.menu_'.$row["contmenu"][$k].' = new Menu("<span class=\'sub'.$contlogo_lock.'\'></span>'.sql_getone($dbtable,"WHERE $where_statut_lang contpg='".$row["contmenu"][$k]."' ","conttitle").'"'.$menu_javascript_vars.');'."\n";
					}
				}
			} else {
				if ($row["contmenu"] !== '') {
					$extrawindowmenu .= '  window.menu_'.$row["contpg"].' = new Menu("'.$row_conttitle.'"'.$menu_javascript_vars.');'."\n";
					$addmenuitem = '  menu_'.substr($row["contpg"],0,-1).'.addMenuItem(menu_'.$row["contpg"].',"window.open(\'" + pth + "'.$row_conturl.'\', \'_self\');");'."\n".$addmenuitem;
					$row["contmenu"] = explode("|", $row["contmenu"]);
					if	(!is_array($row["contmenu"]))	$row["contmenu"] =	array($row["contmenu"])	;
					for ($k=0;$k<count($row["contmenu"]);$k++) {
						if (sql_getone($dbtable,"WHERE $where_statut_lang contpg='".$row["contmenu"][$k]."' ","contstatut") == 'Y') {
							if (sql_nrows($dbtable,"WHERE $where_statut_lang contpg='".(($row["contmenu"][$k]*10)+1)."' ") > '0') {
								$addmenuitem = '  menu_'.$row["contpg"].'.addMenuItem(menu_'.$row["contmenu"][$k].',"window.open(\'" + pth + "'.$row_conturl.'\', \'_self\');");'."\n".$addmenuitem;
							} else {
								$addmenuitem = '  menu_'.$row["contpg"].'.addMenuItem("<span class=\'sub'.$contlogo_lock.'\'></span>'.sql_getone($dbtable,"WHERE $where_statut_lang contpg='".$row["contmenu"][$k]."' ","conttitle").'","window.open(\'" + pth + "'.$row_conturl.'\', \'_self\');");'."\n".$addmenuitem;
							}
						}
						if	(sql_nrows($dbtable,"WHERE $where_statut_lang contpg='".(($row["contmenu"][$k]*10)+1)."' ") == '0')	
						$windowmenu .= '  window.menu_'.$row["contmenu"][$k].' = new Menu("<span class=\'sub'.$contlogo_lock.'\'></span>'.sql_getone($dbtable,"WHERE $where_statut_lang contpg='".$row["contmenu"][$k]."' ","conttitle").'"'.$menu_javascript_vars.');'."\n";
					}
				} else {
					$windowmenu .= '  window.menu_'.$row["contpg"].' = new Menu("'.$row_conttitle.'"'.$menu_javascript_vars.');'."\n";
					$addmenuitem = '  menu_'.substr($row["contpg"],0,-1).'.addMenuItem("<span class=\'sub'.$contlogo_lock.'\'>'.$row_conttitle.'</span>","window.open(\'" + pth + "'.$row_conturl.'\', \'_self\');");'."\n".$addmenuitem;
				}
			}
		}
	} else if (preg_match("/^[$regex_top-9]{1}\$/", $row["contpg"])) {
		$rootwindowmenu .= '  window.menu_'.$row["contpg"].' = new Menu("root"'.$menu_javascript_vars.');'."\n";
		if (sql_nrows($dbtable,"WHERE $where_statut_lang contpg='".(($row["contpg"]*10)+1)."' ") == '1') {
		} else {
		/*not showing if no top*/ //	$addmenuitem = '  menu_'.$row["contpg"].'.addMenuItem("'.$row_conttitle.'","window.open(\'" + pth + "'.$row_conturl.'&'.space2underscore($row_conttitle).'\', \'_self\');");'."\n".$addmenuitem;
		}
		if ($row["contmenu"] !== '') {
			$row["contmenu"] = explode("|", $row["contmenu"]);
			if	(!is_array($row["contmenu"]))	$row["contmenu"] =	array($row["contmenu"])	;
			for ($k=0;$k<count($row["contmenu"]);$k++) {
				if (sql_getone($dbtable,"WHERE $where_statut_lang contpg='".$row["contmenu"][$k]."' ","contstatut") == 'Y') {
					if (sql_nrows($dbtable,"WHERE $where_statut_lang contpg='".(($row["contmenu"][$k]*10)+1)."' ") > '0') {
						$addmenuitem = '  menu_'.$row["contpg"].'.addMenuItem(menu_'.$row["contmenu"][$k].',"window.open(\'" + pth + "'.$row_conturl.'\', \'_self\');");'."\n".$addmenuitem;
					} else {
						$addmenuitem = '  menu_'.$row["contpg"].'.addMenuItem("<span class=\'sub'.$contlogo_lock.'\'></span>'.sql_getone($dbtable,"WHERE $where_statut_lang contpg='".$row["contmenu"][$k]."' ","conttitle").'","window.open(\'" + pth + "'.$row_conturl.'\', \'_self\');");'."\n".$addmenuitem;
					}
				}
			}
		}
		if (sql_nrows($dbtable,"WHERE $where_statut_lang ( contpg='".(($row["contpg"]*10)+1)."' OR contpg='".(($row["contpg"]*10)+2)."' OR contpg='".(($row["contpg"]*10)+3)."' OR contpg='".(($row["contpg"]*10)+4)."' OR contpg='".(($row["contpg"]*10)+5)."' OR contpg='".(($row["contpg"]*10)+6)."' OR contpg='".(($row["contpg"]*10)+7)."' OR contpg='".(($row["contpg"]*10)+8)."' OR contpg='".(($row["contpg"]*10)+9)."' ) ") > '0') {
			$menuitem .= '  menu_'.$row["contpg"].'.hideOnMouseOut=true;'."\n";
			$menuitem .= '  menu_'.$row["contpg"].'.bgColor=\'#BFBFBF\';'."\n";
			$menuitem .= '  menu_'.$row["contpg"].'.menuBorder=1;'."\n";
		} else {
			$menuitem .= '  menu_'.$row["contpg"].'.bgSrc=pth+"images/spacer.gif";'."\n";
			$menuitem .= '  menu_'.$row["contpg"].'.menuBorder=1;'."\n";
		}
	} else {
		if	($rootwindowmenu !== '')	
		$rootwindowmenu .= '  window.menu_'.$row["contpg"].' = new Menu("root"'.$menu_javascript_vars.');'."\n"	;
	}
	if ($row["contmenu"] !== '') {
	//	$row["contmenu"] = explode("|", $row["contmenu"]);
		if	(!is_array($row["contmenu"]))	$row["contmenu"] =	array($row["contmenu"])	;
		for ($k=0;$k<count($row["contmenu"]);$k++) {
			if (sql_getone($dbtable,"WHERE $where_statut_lang contpg='".$row["contmenu"][$k]."' ","contstatut") == 'Y') { // (strlen($row["contmenu"][$k]) > '1') && 
				if	(sql_nrows($dbtable,"WHERE $where_statut_lang contpg='".(($row["contmenu"][$k]*10)+1)."' ") == '0')	
				$windowmenu .= '  window.menu_'.$row["contmenu"][$k].' = new Menu("'.sql_getone($dbtable,"WHERE $where_statut_lang contpg='".$row["contmenu"][$k]."' ","conttitle").'"'.$menu_javascript_vars.');'."\n";
			}
		}
	} else {
	}
}
if	($rootwindowmenu == '')	
  $rootwindowmenu .= '  window.menu_1 = new Menu("root"'.$menu_javascript_vars.');'."\n"	;
if	($menuitem !== '')
	$menuitem .= '  menu_1.bgSrc=pth+"images/spacer.gif";'."\n".'  menu_1.menuBorder=1;'."\n";
// $trace .= '<br> <br>'.$windowmenu.'<br> <hr>'.$extrawindowmenu.'<br> <hr>'.$addmenuitem.'<br> <hr>'.$menuitem.'<br> <br>';

$menu_javascript .= $windowmenu."\n\r".$extrawindowmenu."\n\r".$rootwindowmenu."\n\r".$addmenuitem."\n\r".$menuitem.'
  menu_1.writeMenus();
}
';
$menu_javascript .= $jsvar_3."\n\r".$jsvar_2."\n\r".$jsvar_1."\n\r".'
// --></script>';

if	($rootwindowmenu == '')	$menu_javascript = ""	;

if ($menu_pagine === false) {
  $rootwindowmenu = '';
  $menu_javascript = '<script type="text/javascript"><!-- //'."\n\r".$jsvar_3."\n\r".$jsvar_2."\n\r".$jsvar_1."\n\r".'
  // --></script>';
}

$javascript = (isset($javascript)?$javascript:'').$menu_javascript;

?>