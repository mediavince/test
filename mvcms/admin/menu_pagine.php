<?php #۞ #

if (stristr($_SERVER["PHP_SELF"], "menu_pagine.php")) {
	include '_security.php';
}

function html_js($this_lg) {
global $trace, $mainurl, $tblcont, $tblblog, $font_family, $font_size, $default_lg, $x1subok, $menu_id_on_li, $menu_pad_left, $menu_pad_top;

$html_js = '
/*
 $Name  : menu_pagine.js, 20070227 © 1996-2007 pf;
 $Date  : Tue, 27 Feb 2007 10:00:00 GMT;
 $Exp   : Mon, 31 Dec 2007 23:59:59 GMT;
 $Lang  : html_js;
 $Target: none;
 $Type  : text/html_js;
 $Desc  : Load JS Menu.
 Programming and graphics by pf  -  All rights reserved.
 Please send any questions to: pjoef_at_tiscali.it

 adapted by www.mediavince.com to generate with PHP ordered and cleaned,,,
 this version to display in html

*/
//label, mw, mh, fnt, fs, fclr, fhclr, bg, bgh, halgn, valgn, pad, space, to,
// sx, sy, srel, opq, vert, idt, aw, ah, pth
// function mmLoadMenus
// pth - path
function mmLoadMenus(pth)
{
// IMPORTANT FOR ROUTINE START
  if (window.menu_1) return;
';

$windowmenuhtm = '';
$extrawindowmenuhtm = '';
$rootwindowmenuhtm = '';
$addmenuitemhtm = '';
$menuitemhtm = '';
$toprelatedhtm = '';
$relatedhtm = '';

$menuhorihtm = '';
$menulefthtm = '';
$menurighthtm = '';

$jsvar_1 = '';
$jsvar_2 = '';
$jsvar_3 = '';

$wherejs = "WHERE contlang='$this_lg' AND conturl LIKE '%.php%' AND conttype!='scroller' AND conttype!='leftlinks' ";// AND contorient!='center' contstatut='Y' AND 
$readjs = @mysql_query("SELECT * FROM $tblcont $wherejs ORDER BY contpg DESC ");
$nrowsjs = @mysql_num_rows($readjs);
$html_js .= "//$nrowsjs\n";
for ($ijs=0;$ijs<$nrowsjs;$ijs++) {
	$rowjs = mysql_fetch_array($readjs);
	$row_conturl = substr($rowjs["conturl"],0,-3).'htm';
	$row_conttitle = $rowjs["conttitle"];
	if	(strlen($rowjs["contpg"]) == '3')		$jsvar_3 .= 'var m'.$rowjs["contpg"].' = "'.$row_conttitle.'";'.'var l'.$rowjs["contpg"].' = "'.$row_conturl.'";'."\n"	;
	else if	(strlen($rowjs["contpg"]) == '2')		$jsvar_2 .= 'var m'.$rowjs["contpg"].' = "'.$row_conttitle.'";'.'var l'.$rowjs["contpg"].' = "'.$row_conturl.'";'."\n"	;
	else	$jsvar_1 .= 'var m'.$rowjs["contpg"].' = "'.$row_conttitle.'";'.'var l'.$rowjs["contpg"].' = "'.$row_conturl.'";'."\n"	;
	$row_contentry = str_replace($mainurl,"",$rowjs["contentry"]);
	if ($ijs > '0') $ijsminus = $ijs-1 ;
	
	if ((($x1subok === false) && preg_match("/^[2-9][0-9]{3}\$/", $rowjs["contpg"])) || (($x1subok === true) && preg_match("/^[1-9][0-9]{3}\$/", $rowjs["contpg"]))) {
	
		if ((sql_nrows($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".substr($rowjs["contpg"],0,-1)."' ") == '1') && (sql_nrows($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".substr($rowjs["contpg"],0,-2)."' ") == '1')) {
			$addmenuitemhtm = '  menu_'.substr($rowjs["contpg"],0,-1).'.addMenuItem("'.$row_conttitle.'","window.open(\'" + pth + "'.$row_conturl.'\', \'_self\');")'."\n".$addmenuitemhtm;
		}
		if ($rowjs["contmenu"] !== '') {
			$rowjs["contmenu"] = explode("|", $rowjs["contmenu"]);
			if	(!is_array($rowjs["contmenu"]))	$rowjs["contmenu"] =	array($rowjs["contmenu"])	;
				for ($kjs=0;$kjs<count($rowjs["contmenu"]);$kjs++) {
					if (sql_getone($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".$rowjs["contmenu"][$kjs]."' ","contstatut") == 'Y') {
						if (sql_nrows($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".(($rowjs["contmenu"][$kjs]*10)+1)."' ") > '0') {
							$addmenuitemhtm = '  menu_'.$rowjs["contpg"].'.addMenuItem(menu_'.$rowjs["contmenu"][$kjs].',"window.open(\'" + pth + "'.$row_conturl.'\', \'_self\');");'."\n".$addmenuitemhtm;
						if (sql_nrows($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".(($rowjs["contmenu"][$kjs]*10)+1)."' ") > '0') {
							$windowmenuhtm .= '  window.menu_'.$rowjs["contpg"].' = new Menu("'.sql_getone($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".$rowjs["contmenu"][$kjs]."' ","conttitle").'", 150, 18,"'.$font_family.'" , "'.$font_size.'", "#000000", "Navy", "#FFFFFF","#FFFFFF", "left", "middle", 2, 0, 1000, -5, 7, true, true, true, 0, false, false, pth);'."\n";
						} else {
							$windowmenuhtm .= '  window.menu_'.$rowjs["contpg"].' = new Menu(menu_'.$rowjs["contmenu"][$kjs].', 150, 18,"'.$font_family.'" , "'.$font_size.'", "#000000", "Navy", "#FFFFFF","#FFFFFF", "left", "middle", 2, 0, 1000, -5, 7, true, true, true, 0, false, false, pth);'."\n";
						}
					} else {
						if	(sql_nrows($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".(($rowjs["contpg"]*10)+1)."' ") == '0')	
						$windowmenuhtm .= '  window.menu_'.$rowjs["contmenu"][$kjs].' = new Menu("'.sql_getone($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".$rowjs["contmenu"][$kjs]."' ","conttitle").'", 150, 18,"'.$font_family.'" , "'.$font_size.'", "#000000", "Navy", "#FFFFFF","#FFFFFF", "left", "middle", 2, 0, 1000, -5, 7, true, true, true, 0, false, false, pth);'."\n";
						$addmenuitemhtm = '  menu_'.$rowjs["contmenu"][$kjs].'.addMenuItem("'.sql_getone($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".$rowjs["contpg"]."' ","conttitle").'","window.open(\'" + pth + "'.$row_conturl.'\', \'_self\');");'."\n".$addmenuitemhtm;
						if	(sql_nrows($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".(($rowjs["contmenu"][$kjs]*10)+1)."' ") == '0')	
						$windowmenuhtm .= '  window.menu_'.$rowjs["contpg"].' = new Menu(menu_'.$rowjs["contmenu"][$kjs].', 150, 18,"'.$font_family.'" , "'.$font_size.'", "#000000", "Navy", "#FFFFFF","#FFFFFF", "left", "middle", 2, 0, 1000, -5, 7, true, true, true, 0, false, false, pth);'."\n";
						if	(sql_nrows($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".(($rowjs["contmenu"][$kjs]*10)+1)."' ") == '0')	
						$windowmenuhtm .= '  window.menu_'.$rowjs["contpg"].' = new Menu(menu_'.$rowjs["contmenu"][$kjs].', 150, 18,"'.$font_family.'" , "'.$font_size.'", "#000000", "Navy", "#FFFFFF","#FFFFFF", "left", "middle", 2, 0, 1000, -5, 7, true, true, true, 0, false, false, pth);'."\n";

						$addmenuitemhtm = '  menu_'.$rowjs["contpg"].'.addMenuItem("'.sql_getone($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".$rowjs["contmenu"][$kjs]."' ","conttitle").'","window.open(\'" + pth + "'.$row_conturl.'\', \'_self\');");'."\n".$addmenuitemhtm;
						$addmenuitemhtm = '  menu_'.$rowjs["contpg"].'.addMenuItem(menu_'.$rowjs["contmenu"][$kjs].',"window.open(\'" + pth + "'.$row_conturl.'\', \'_self\');");'."\n".$addmenuitemhtm;
					}
				}
				if	(sql_nrows($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".(($rowjs["contmenu"][$kjs]*10)+1)."' ") == '0')	
				$windowmenuhtm .= '  window.menu_'.$rowjs["contmenu"][$kjs].' = new Menu("'.sql_getone($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".$rowjs["contmenu"][$kjs]."' ","conttitle").'", 150, 18,"'.$font_family.'" , "'.$font_size.'", "#000000", "Navy", "#FFFFFF","#FFFFFF", "left", "middle", 2, 0, 1000, -5, 7, true, true, true, 0, false, false, pth);'."\n";
			}
		}
	
	} else if ((($x1subok === false) && preg_match("/^[2-9][0-9]{2}\$/", $rowjs["contpg"])) || (($x1subok === true) && preg_match("/^[1-9][0-9]{2}\$/", $rowjs["contpg"]))) {
	
		if (sql_nrows($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".substr($rowjs["contpg"],0,-1)."' ") == '1') {
			if (sql_nrows($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".(($rowjs["contpg"]*10)+1)."' ") == '1') {
				$windowmenuhtm .= '  window.menu_'.$rowjs["contpg"].' = new Menu("'.$row_conttitle.'", 150, 18,"'.$font_family.'" , "'.$font_size.'", "#000000", "Navy", "#FFFFFF","#FFFFFF", "left", "middle", 2, 0, 1000, -5, 7, true, true, true, 0, false, false, pth);'."\n";
				$addmenuitemhtm = '  menu_'.substr($rowjs["contpg"],0,-1).'.addMenuItem(menu_'.$rowjs["contpg"].',"window.open(\'" + pth + "'.$row_conturl.'\', \'_self\');")'."\n".$addmenuitemhtm;
				$menuitemhtm .= '  menu_'.$rowjs["contpg"].'.hideOnMouseOut=true;'."\n";
				$menuitemhtm .= '  menu_'.$rowjs["contpg"].'.childMenuIcon=pth+"images/arrow.gif";'."\n";
				$menuitemhtm .= '  menu_'.$rowjs["contpg"].'.bgColor=\'#BFBFBF\';'."\n";
				$menuitemhtm .= '  menu_'.$rowjs["contpg"].'.menuBorder=1;'."\n";
			} else {
				$addmenuitemhtm = '  menu_'.substr($rowjs["contpg"],0,-1).'.addMenuItem("'.$row_conttitle.'","window.open(\'" + pth + "'.$row_conturl.'\', \'_self\');")'."\n".$addmenuitemhtm;
			}
		}
		if ($rowjs["contmenu"] !== '') {
			$rowjs["contmenu"] = explode("|", $rowjs["contmenu"]);
			if	(!is_array($rowjs["contmenu"]))	$rowjs["contmenu"] = array($rowjs["contmenu"])	;
			for ($kjs=0;$kjs<count($rowjs["contmenu"]);$kjs++) {
				if (sql_getone($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".$rowjs["contmenu"][$kjs]."' ","contstatut") == 'Y') {
					if (sql_nrows($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".(($rowjs["contmenu"][$kjs]*10)+1)."' ") > '0') {
						$addmenuitemhtm = '  menu_'.$rowjs["contpg"].'.addMenuItem(menu_'.$rowjs["contmenu"][$kjs].',"window.open(\'" + pth + "'.$row_conturl.'\', \'_self\');");'."\n".$addmenuitemhtm;
					} else {
						$addmenuitemhtm = '  menu_'.$rowjs["contpg"].'.addMenuItem("'.sql_getone($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".$rowjs["contmenu"][$kjs]."' ","conttitle").'","window.open(\'" + pth + "'.$row_conturl.'\', \'_self\');");'."\n".$addmenuitemhtm;
					}
				}
				if	(sql_nrows($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".(($rowjs["contmenu"][$kjs]*10)+1)."' ") == '0')	
				$windowmenuhtm .= '  window.menu_'.$rowjs["contmenu"][$kjs].' = new Menu("'.sql_getone($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".$rowjs["contmenu"][$kjs]."' ","conttitle").'", 150, 18,"'.$font_family.'" , "'.$font_size.'", "#000000", "Navy", "#FFFFFF","#FFFFFF", "left", "middle", 2, 0, 1000, -5, 7, true, true, true, 0, false, false, pth);'."\n";
			}
		}
		
	} else if ((($x1subok === false) && preg_match("/^[2-9][0-9]{1}\$/", $rowjs["contpg"])) || (($x1subok === true) && preg_match("/^[1-9][0-9]{1}\$/", $rowjs["contpg"]))) {
	
		if (sql_nrows($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".substr($rowjs["contpg"],0,-1)."' ") == '1') {
			if (sql_nrows($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".(($rowjs["contpg"]*10)+1)."' ") == '1') {
				$windowmenuhtm .= '  window.menu_'.$rowjs["contpg"].' = new Menu("'.$row_conttitle.'", 150, 18,"'.$font_family.'" , "'.$font_size.'", "#000000", "Navy", "#FFFFFF","#FFFFFF", "left", "middle", 2, 0, 1000, -5, 7, true, true, true, 0, false, false, pth);'."\n";
				$addmenuitemhtm = '  menu_'.substr($rowjs["contpg"],0,-1).'.addMenuItem(menu_'.$rowjs["contpg"].',"window.open(\'" + pth + "'.$row_conturl.'\', \'_self\');");'."\n".$addmenuitemhtm;
				$menuitemhtm .= '  menu_'.$rowjs["contpg"].'.hideOnMouseOut=true;'."\n";
				$menuitemhtm .= '  menu_'.$rowjs["contpg"].'.childMenuIcon=pth+"images/arrow.gif";'."\n";
				$menuitemhtm .= '  menu_'.$rowjs["contpg"].'.bgColor=\'#BFBFBF\';'."\n";
				$menuitemhtm .= '  menu_'.$rowjs["contpg"].'.menuBorder=1;'."\n";
				if ($rowjs["contmenu"] !== '') {
					$rowjs["contmenu"] = explode("|", $rowjs["contmenu"]);
					if	(!is_array($rowjs["contmenu"]))	$rowjs["contmenu"] =	array($rowjs["contmenu"])	;
					for ($kjs=0;$kjs<count($rowjs["contmenu"]);$kjs++) {
						if (sql_getone($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".$rowjs["contmenu"][$kjs]."' ","contstatut") == 'Y') {
							if (sql_nrows($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".(($rowjs["contmenu"][$kjs]*10)+1)."' ") > '0') {
								$addmenuitemhtm = '  menu_'.$rowjs["contpg"].'.addMenuItem(menu_'.$rowjs["contmenu"][$kjs].',"window.open(\'" + pth + "'.$row_conturl.'\', \'_self\');");'."\n".$addmenuitemhtm;
							} else {
								$addmenuitemhtm = '  menu_'.$rowjs["contpg"].'.addMenuItem("'.sql_getone($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".$rowjs["contmenu"][$kjs]."' ","conttitle").'","window.open(\'" + pth + "'.$row_conturl.'\', \'_self\');");'."\n".$addmenuitemhtm;
							}
						}
						if	(sql_nrows($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".(($rowjs["contmenu"][$kjs]*10)+1)."' ") == '0')	
						$windowmenuhtm .= '  window.menu_'.$rowjs["contmenu"][$kjs].' = new Menu("'.sql_getone($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".$rowjs["contmenu"][$kjs]."' ","conttitle").'", 150, 18,"'.$font_family.'" , "'.$font_size.'", "#000000", "Navy", "#FFFFFF","#FFFFFF", "left", "middle", 2, 0, 1000, -5, 7, true, true, true, 0, false, false, pth);'."\n";
					}
				}
			} else {
				if ($rowjs["contmenu"] !== '') {
					$extrawindowmenuhtm .= '  window.menu_'.$rowjs["contpg"].' = new Menu("'.$row_conttitle.'", 150, 18,"'.$font_family.'" , "'.$font_size.'", "#000000", "Navy", "#FFFFFF","#FFFFFF", "left", "middle", 2, 0, 1000, -5, 7, true, true, true, 0, false, false, pth);'."\n";
					$addmenuitemhtm = '  menu_'.substr($rowjs["contpg"],0,-1).'.addMenuItem(menu_'.$rowjs["contpg"].',"window.open(\'" + pth + "'.$row_conturl.'\', \'_self\');");'."\n".$addmenuitemhtm;
					$rowjs["contmenu"] = explode("|", $rowjs["contmenu"]);
					if	(!is_array($rowjs["contmenu"]))	$rowjs["contmenu"] =	array($rowjs["contmenu"])	;
					for ($kjs=0;$kjs<count($rowjs["contmenu"]);$kjs++) {
						if (sql_getone($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".$rowjs["contmenu"][$kjs]."' ","contstatut") == 'Y') {
							if (sql_nrows($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".(($rowjs["contmenu"][$kjs]*10)+1)."' ") > '0') {
								$addmenuitemhtm = '  menu_'.$rowjs["contpg"].'.addMenuItem(menu_'.$rowjs["contmenu"][$kjs].',"window.open(\'" + pth + "'.$row_conturl.'\', \'_self\');");'."\n".$addmenuitemhtm;
							} else {
								$addmenuitemhtm = '  menu_'.$rowjs["contpg"].'.addMenuItem("'.sql_getone($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".$rowjs["contmenu"][$kjs]."' ","conttitle").'","window.open(\'" + pth + "'.$row_conturl.'\', \'_self\');");'."\n".$addmenuitemhtm;
							}
						}
						if	(sql_nrows($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".(($rowjs["contmenu"][$kjs]*10)+1)."' ") == '0')	
						$windowmenuhtm .= '  window.menu_'.$rowjs["contmenu"][$kjs].' = new Menu("'.sql_getone($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".$rowjs["contmenu"][$kjs]."' ","conttitle").'", 150, 18,"'.$font_family.'" , "'.$font_size.'", "#000000", "Navy", "#FFFFFF","#FFFFFF", "left", "middle", 2, 0, 1000, -5, 7, true, true, true, 0, false, false, pth);'."\n";
					}
				} else {
					$windowmenuhtm .= '  window.menu_'.$rowjs["contpg"].' = new Menu("'.$row_conttitle.'", 150, 18,"'.$font_family.'" , "'.$font_size.'", "#000000", "Navy", "#FFFFFF","#FFFFFF", "left", "middle", 2, 0, 1000, -5, 7, true, true, true, 0, false, false, pth);'."\n";
					$addmenuitemhtm = '  menu_'.substr($rowjs["contpg"],0,-1).'.addMenuItem("'.$row_conttitle.'","window.open(\'" + pth + "'.$row_conturl.'\', \'_self\');");'."\n".$addmenuitemhtm;
				}
			}
		}
		
	} else if ((($x1subok === false) && preg_match("/^[2-9]{1}\$/", $rowjs["contpg"])) || (($x1subok === true) && preg_match("/^[1-9]{1}\$/", $rowjs["contpg"]))) {
	
		$rootwindowmenuhtm .= '  window.menu_'.$rowjs["contpg"].' = new Menu("root", 150, 18,"'.$font_family.'" , "'.$font_size.'", "#000000", "Navy", "#FFFFFF","#FFFFFF", "left", "middle", 2, 0, 1000, -5, 7, true, true, true, 0, false, false, pth);'."\n";
		if (sql_nrows($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".(($rowjs["contpg"]*10)+1)."' ") == '1') {
		} else {
		/*not showing if no top*/ //	$addmenuitemhtm = '  menu_'.$rowjs["contpg"].'.addMenuItem("'.$row_conttitle.'","window.open(\'" + pth + "'.$row_conturl.'&'.space2underscore($row_conttitle).'\', \'_self\');");'."\n".$addmenuitemhtm;
		}
		if ($rowjs["contmenu"] !== '') {
			$rowjs["contmenu"] = explode("|", $rowjs["contmenu"]);
			if	(!is_array($rowjs["contmenu"]))	$rowjs["contmenu"] =	array($rowjs["contmenu"])	;
			for ($kjs=0;$kjs<count($rowjs["contmenu"]);$kjs++) {
				if (sql_getone($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".$rowjs["contmenu"][$kjs]."' ","contstatut") == 'Y') {
					if (sql_nrows($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".(($rowjs["contmenu"][$kjs]*10)+1)."' ") > '0') {
						$addmenuitemhtm = '  menu_'.$rowjs["contpg"].'.addMenuItem(menu_'.$rowjs["contmenu"][$kjs].',"window.open(\'" + pth + "'.$row_conturl.'\', \'_self\');");'."\n".$addmenuitemhtm;
					} else {
						$addmenuitemhtm = '  menu_'.$rowjs["contpg"].'.addMenuItem("'.sql_getone($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".$rowjs["contmenu"][$kjs]."' ","conttitle").'","window.open(\'" + pth + "'.$row_conturl.'\', \'_self\');");'."\n".$addmenuitemhtm;
					}
				}
			}
		}
		if (sql_nrows($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND ( contpg='".(($rowjs["contpg"]*10)+1)."' OR contpg='".(($rowjs["contpg"]*10)+2)."' OR contpg='".(($rowjs["contpg"]*10)+3)."' OR contpg='".(($rowjs["contpg"]*10)+4)."' OR contpg='".(($rowjs["contpg"]*10)+5)."' OR contpg='".(($rowjs["contpg"]*10)+6)."' OR contpg='".(($rowjs["contpg"]*10)+7)."' OR contpg='".(($rowjs["contpg"]*10)+8)."' OR contpg='".(($rowjs["contpg"]*10)+9)."' ) ") > '0') {
			$menuitemhtm .= '  menu_'.$rowjs["contpg"].'.hideOnMouseOut=true;'."\n";
			$menuitemhtm .= '  menu_'.$rowjs["contpg"].'.bgColor=\'#BFBFBF\';'."\n";
			$menuitemhtm .= '  menu_'.$rowjs["contpg"].'.menuBorder=1;'."\n";
		} else {
			$menuitemhtm .= '  menu_'.$rowjs["contpg"].'.bgSrc=pth+"images/spacer.gif";'."\n";
			$menuitemhtm .= '  menu_'.$rowjs["contpg"].'.menuBorder=1;'."\n";
		}
	} else {
		if	($rootwindowmenuhtm !== '')	
		$rootwindowmenuhtm .= '  window.menu_'.$rowjs["contpg"].' = new Menu("root", 150, 18,"'.$font_family.'" , "'.$font_size.'", "#000000", "Navy", "#FFFFFF","#FFFFFF", "left", "middle", 2, 0, 1000, -5, 7, true, true, true, 0, false, false, pth);'."\n"	;
	}
	if ($rowjs["contmenu"] !== '') {
	//	$rowjs["contmenu"] = explode("|", $rowjs["contmenu"]);
		if	(!is_array($rowjs["contmenu"]))	$rowjs["contmenu"] =	array($rowjs["contmenu"])	;
		for ($kjs=0;$kjs<count($rowjs["contmenu"]);$kjs++) {
			if (sql_getone($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".$rowjs["contmenu"][$kjs]."' ","contstatut") == 'Y') { // (strlen($rowjs["contmenu"][$kjs]) > '1') && 
				if	(sql_nrows($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".(($rowjs["contmenu"][$kjs]*10)+1)."' ") == '0')	
				$windowmenuhtm .= '  window.menu_'.$rowjs["contmenu"][$kjs].' = new Menu("'.sql_getone($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".$rowjs["contmenu"][$kjs]."' ","conttitle").'", 150, 18,"'.$font_family.'" , "'.$font_size.'", "#000000", "Navy", "#FFFFFF","#FFFFFF", "left", "middle", 2, 0, 1000, -5, 7, true, true, true, 0, false, false, pth);'."\n";
			}
		}
	} else {
	}


  if ($x1subok === true) $hori_page_count = '10';
  else $hori_page_count = '20';
  
	if ($rowjs["contpg"] < $hori_page_count) {
		if (preg_match("/^[0-9]{0,1}\$/", $rowjs["contpg"])) {
			if ($rowjs["contorient"] == 'left') {
				$menulefthtm = '<li'.(isset($menu_id_on_li)?' id="im_'.$rowjs["contpg"].'" name="im_'.$rowjs["contpg"].'"':'').'><a href="'.$row_conturl.'" onMouseOver="MM_showMenu(window.menu_'.$rowjs["contpg"].',120,-10,null,\'im_'.$rowjs["contpg"].'\');return true;" onMouseOut="MM_startTimeout();return true;"  alt="'.$row_conttitle.'"><img'.(isset($menu_id_on_li)?'':' id="im_'.$rowjs["contpg"].'" name="im_'.$rowjs["contpg"].'"').' src="'.'images/spacer.gif" width="0" height="0" border="0" alt="'.$row_conttitle.'" />'.$row_conttitle.'</a></li>'.$menulefthtm;
			} else if ($rowjs["contorient"] == 'right') {
				$menurighthtm = '<li'.(isset($menu_id_on_li)?' id="im_'.$rowjs["contpg"].'" name="im_'.$rowjs["contpg"].'"':'').'><a href="'.$row_conturl.'" onMouseOver="MM_showMenu(window.menu_'.$rowjs["contpg"].',-155,-5,null,\'im_'.$rowjs["contpg"].'\');return true;" onMouseOut="MM_startTimeout();return true;"  alt="'.$row_conttitle.'"><img'.(isset($menu_id_on_li)?'':' id="im_'.$rowjs["contpg"].'" name="im_'.$rowjs["contpg"].'"').' src="'.'images/spacer.gif" width="0" height="0" border="0" alt="'.$row_conttitle.'" />'.$row_conttitle.'</a></li>'.$menurighthtm;
			} else {
			 	$inject_barre_verticale = '<font color="Navy"> | </font>'	;
			 	$inject_barre_verticale = ''	;
			//	if	(($rowjs["contpg"] == $x) || ($rowjs["contpg"] == substr($x,0,1)))	$inject_barre_verticale = '<font color="Red"> > </font>'	;
				if (($rowjs["contorient"] == 'center') && (sql_nrows($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".(($rowjs["contpg"]*10)+1)."' ") == '1')) {
					$menuhorihtm = '<li'.(isset($menu_id_on_li)?' id="im_'.$rowjs["contpg"].'" name="im_'.$rowjs["contpg"].'"':'').'><a href="'.$row_conturl.'" onMouseOver="MM_showMenu(window.menu_'.$rowjs["contpg"].',10,5,null,\'im_'.$rowjs["contpg"].'\');return true;" onMouseOut="MM_startTimeout();return true;"  alt="'.$row_conttitle.'"><img'.(isset($menu_id_on_li)?'':' id="im_'.$rowjs["contpg"].'" name="im_'.$rowjs["contpg"].'"').' src="'.'images/spacer.gif" width="0" height="0" border="0" alt="'.$row_conttitle.'" />'.$inject_barre_verticale.''.$row_conttitle.'</a></li>'.$menuhorihtm;
				} else {
					$menuhorihtm = '<li'.(isset($menu_id_on_li)?' id="im_'.$rowjs["contpg"].'" name="im_'.$rowjs["contpg"].'"':'').'><a href="'.$row_conturl.'" alt="'.$row_conttitle.'"><img'.(isset($menu_id_on_li)?'':' id="im_'.$rowjs["contpg"].'" name="im_'.$rowjs["contpg"].'"').' src="'.'images/spacer.gif" width="0" height="0" border="0" alt="'.$row_conttitle.'" />'.$inject_barre_verticale.''.$row_conttitle.'</a></li>'.$menuhorihtm;
				}
			}
		} else if (preg_match("/^1[0-9]{1}\$/", $rowjs["contpg"])) {
			if ($rowjs["contorient"] == 'left') {
				$menulefthtm = '<li'.(isset($menu_id_on_li)?' id="im_'.$rowjs["contpg"].'" name="im_'.$rowjs["contpg"].'"':'').'><a href="'.$row_conturl.'" alt="'.$row_conttitle.'"><img'.(isset($menu_id_on_li)?'':' id="im_'.$rowjs["contpg"].'" name="im_'.$rowjs["contpg"].'"').' src="'.'images/spacer.gif" width="0" height="0" border="0" alt="'.$row_conttitle.'" />'.$row_conttitle.'</a></li>'.$menulefthtm;
			} else if ($rowjs["contorient"] == 'right') {
				$menurighthtm = '<li'.(isset($menu_id_on_li)?' id="im_'.$rowjs["contpg"].'" name="im_'.$rowjs["contpg"].'"':'').'><a href="'.$row_conturl.'" alt="'.$row_conttitle.'"><img'.(isset($menu_id_on_li)?'':' id="im_'.$rowjs["contpg"].'" name="im_'.$rowjs["contpg"].'"').' src="'.'images/spacer.gif" width="0" height="0" border="0" alt="'.$row_conttitle.'" />'.$row_conttitle.'</a></li>'.$menurighthtm;
			} else {
			 	$inject_barre_verticale = '<font color="Navy"> | </font>'	;
			 	$inject_barre_verticale = ''	;
			//	if	(($rowjs["contpg"] == $x) || ($rowjs["contpg"] == substr($x,0,1)))	$inject_barre_verticale = '<font color="Red"> > </font>'	;
				if (($rowjs["contorient"] == 'center') && (sql_nrows($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND contpg='".(($rowjs["contpg"]*10)+1)."' ") == '1')) {
					$menuhorihtm = '<li'.(isset($menu_id_on_li)?' id="im_'.$rowjs["contpg"].'" name="im_'.$rowjs["contpg"].'"':'').'><a href="'.$row_conturl.'" onMouseOver="MM_showMenu(window.menu_'.$rowjs["contpg"].',10,5,null,\'im_'.$rowjs["contpg"].'\');return true;" onMouseOut="MM_startTimeout();return true;"  alt="'.$row_conttitle.'"><img'.(isset($menu_id_on_li)?'':' id="im_'.$rowjs["contpg"].'" name="im_'.$rowjs["contpg"].'"').' src="'.'images/spacer.gif" width="0" height="0" border="0" alt="'.$row_conttitle.'" />'.$inject_barre_verticale.''.$row_conttitle.'</a></li>'.$menuhorihtm;
				} else {
					$menuhorihtm = '<li'.(isset($menu_id_on_li)?' id="im_'.$rowjs["contpg"].'" name="im_'.$rowjs["contpg"].'"':'').'><a href="'.$row_conturl.'" alt="'.$row_conttitle.'"><img'.(isset($menu_id_on_li)?'':' id="im_'.$rowjs["contpg"].'" name="im_'.$rowjs["contpg"].'"').' src="'.'images/spacer.gif" width="0" height="0" border="0" alt="'.$row_conttitle.'" />'.$inject_barre_verticale.''.$row_conttitle.'</a></li>'.$menuhorihtm;
				}
			}
		}
	}
/*****************************************
	if ($rowjs["conttype"] == 'scroller') {
		$scrollerhtm = '<marquee behavior="scroll" direction="up" height="150" scrollamount="1" scrolldelay="30" onmouseover=\'this.stop()\' onmouseout=\'this.start()\'><br /> <br /><h1>'.$row_conttitle.'</h1>'.str_replace(  array("\r\n", "\r", "\n") , '' , $row_contentry).'</marquee>	';
	}
*****************************************/
}

if	($rootwindowmenuhtm == '')	
$rootwindowmenuhtm .= '  window.menu_1 = new Menu("root", 150, 18,"'.$font_family.'" , "'.$font_size.'", "#000000", "Navy", "#FFFFFF","#FFFFFF", "left", "middle", 2, 0, 1000, -5, 7, true, true, true, 0, false, false, pth);'."\n"	;
if	($menuitemhtm !== '')	$menuitemhtm .= '  menu_1.bgSrc=pth+"images/spacer.gif";'."\n".'  menu_1.menuBorder=1;'."\n";
// $trace .= '<br> <br>'.$windowmenuhtm.'<br> <hr>'.$extrawindowmenuhtm.'<br> <hr>'.$addmenuitemhtm.'<br> <hr>'.$menuitemhtm.'<br> <br>';

$html_js .= $windowmenuhtm."\n\r".$extrawindowmenuhtm."\n\r".$rootwindowmenuhtm."\n\r".$addmenuitemhtm."\n\r".$menuitemhtm.'

  menu_1.writeMenus();
}
';

$html_js .= $jsvar_3."\n\r".$jsvar_2."\n\r".$jsvar_1."\n\r";

if	($menuhorihtm !== '')	$menuhorihtm = '<ul>'.$menuhorihtm.'</ul>'	;
if	($menulefthtm !== '')	$menulefthtm = '<ul>'.$menulefthtm.'</ul>'	;
if	($menurighthtm !== '')	$menurighthtm = '<ul>'.$menurighthtm.'</ul>'	;

	$html_js .= "\n\r".' var menuhori = "'.addslashes($menuhorihtm)."\";";
	$html_js .= "\n\r".' var menuleft = "'.addslashes($menulefthtm)."\";";
	$html_js .= "\n\r".' var menuright = "'.addslashes($menurighthtm)."\";\n\r"; // .' var scroller = "'.addslashes($scrollerhtm)."\";\n\r"

	$html_js .= "\n\r".' var leftlinks = "'.preg_replace('#\r?\n#','',addslashes(sql_getone($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND conturl LIKE '%.php%' AND conttype='leftlinks' ","contentry")))."\";\n\r";

	$html_js .= "\n\r".' var toplinks = "'.preg_replace('#\r?\n#','',addslashes(sql_getone($tblcont,"WHERE contstatut='Y' AND contlang='$this_lg' AND conturl LIKE '%.php%' AND conttype='toplinks' ","contentry")))."\";\n\r";

	$html_js .= "\n\r".' var topicslist = "'.preg_replace('#\r?\n#','',addslashes(sql_getone($tblblog,"WHERE contstatut='Y' AND contlang='$this_lg' AND conturl LIKE '%.php%' AND conttype='toplinks' ","contentry")))."\";\n\r";


//if	($rootwindowmenuhtm == '')	$html_js = ""	;

return $html_js;
}

function scroller_js($this_lg) {
global $trace, $mainurl, $tblcont;

	$scrollerhtm = "";
	$wherescroller = "WHERE contstatut='Y' AND contlang='$this_lg' AND conturl LIKE '%.php%' AND conttype='scroller' ";
	$readscroller = sql_get($tblcont,$wherescroller,"conttitle,contentry");
	if (($readscroller[0] !== '.') && ($readscroller[1] !== '.')) {
		$scrollerhtm = '<marquee behavior="scroll" direction="up" height="50" scrollamount="1" scrolldelay="30" onmouseover=\'this.stop()\' onmouseout=\'this.start()\'><br /> <br /><h1>'.$readscroller[0].'</h1>'.str_replace(  array("\r\n", "\r", "\n", $mainurl) , '' , $readscroller[1]).'</marquee>	';
	}

	$scroller_js = "\n\r".' var scroller = "'.addslashes($scrollerhtm)."\";\n\r";

return $scroller_js;
}
