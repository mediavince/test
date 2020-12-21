<?php #۞ #
if (stristr($_SERVER['PHP_SELF'], basename(__FILE__))){include '_security.php';Header("Location: $redirect");Die();}
/**
 * Organize menu presentation according to current page and various preferences
 * set menuleft, menuhori, menuright
 */

$nonactive = "_";
$nonab = "b";
$active = "_active_";
$ab = "a";

$menuleft = '';
$t_menuleft = '';
$always_under = '';
$menuright = '';
$t_menuright = '';
$menuhori = '';
$t_menuhori = '';
$where = "WHERE $where_statut_lang conttype!='toplinks' AND conttype!='scroller' AND conttype!='leftlinks' ";// conturl LIKE '%_".$lg.".php%'
$read = mysqli_query($connection, "SELECT * FROM $tblcont $where ORDER BY CAST(contpg as CHAR) ");
$nrows = mysqli_num_rows($read);

$base_x = $x[0];
if (preg_match("/^1[0-9]{1,}\$/", $x) && ($x1subok === false))
  $base_x = $x[0].$x[1] ;

for ($i=0;$i<$nrows;$i++) {
	$row = mysqli_fetch_array($read);
	$row_conturl = $row["conturl"];
	$row_contpg = $row["contpg"];
	$row_conttitle = $row["conttitle"];
	$clean_title = $row["conturl"]; // space2underscore($row_conttitle);
  if ($htaccess4sef === true) {
  	if (count($array_lang)>1)
      $row_conturl = "$lg/";
  	else
      $row_conturl = "";
  	if (strlen($row_contpg)>1) {
      for($hj=1;$hj<strlen($row_contpg);$hj++){
        $substr_pg = substr($row_contpg,0,$hj);
        $row_conturl .= space2underscore(sql_getone($tblcont,"WHERE $where_statut_lang contpg='$substr_pg' ","conttitle"))."/";
      }
    }
    $row_conturl .= $clean_title."/";
  }
  $array_pages[] = $row;// use it to list the pages
	$base_pg = substr($row_contpg,0,(strlen($base_x)));
  
  if (stristr($_SERVER['PHP_SELF'],$urladmin) && ($logged_in === true)) {
    $row["href"] = $local.'?lg='.$lg.'&amp;x='.$row_contpg; // reset url with path
    $a_href = $row["href"].'" alt=" ';
    if ($row["contorient"] == 'left')
      $row_conttitle = '<span style="color:red;">&deg;</span>'.$row_conttitle;
    if ($row["contorient"] == 'right')
      $row_conttitle .= '<span style="color:red;">&deg;</span>';
  	if	($row["contstatut"] == 'N')
      $row_conttitle = '<span style="color:red;">*'.$row["conttitle"].'*</span>'	;
  } else {
    $row["href"] = $mainurl.$row_conturl; // reset url with path
    $a_href = $row["href"].'" alt=" ';
  }

  $curr_nav = "";
  if  ($row_contpg == $x) $curr_nav = ' id="currentnav" ' ;
 	
  $inject_barre_verticale = '';//'<font color="Navy"> | </font>'	;
	if	(($row_contpg == $base_x) || ($row_contpg == $x))
  	$inject_barre_verticale = '<span style="color:red;"> > </span>'	;
  if  ($row_contpg == $base_x)
    $menuleft = '<li class="title"><a href="'.$a_href.'">'.$row_conttitle.'</a></li>';
  
  $contlogo_lock = ((!stristr($row["contlogo"],"1") && ($row["contlogo"] !== ""))?'lock':'');
	
  if (preg_match("/^[0-9]{0,1}\$/", $row_contpg)) {
		if ($row["contorient"] == 'left') {
	  	$always_under .= '<li class="first"'.(isset($menu_id_on_li)?' id="im_'.$row_contpg.'" name="im_'.$row_contpg.'"':'').'><a href="'.$a_href.'"><img'.(isset($menu_id_on_li)?'':' id="im_'.$row_contpg.'" name="im_'.$row_contpg.'"').' src="'.$mainurl.'images/spacer.gif" width="0" height="0" border="0" alt=" " />'.$inject_barre_verticale.$row_conttitle.'</a></li>';
		} else if ($row["contorient"] == 'right') {
			$menuright .= '<li'.(isset($menu_id_on_li)?' id="im_'.$row_contpg.'" name="im_'.$row_contpg.'"':'').'><a href="'.$a_href.'"'.($menu_pagine===true?' onMouseOver="MM_showMenu(window.menu_'.$row_contpg.',-155,-5,null,\'im_'.$row_contpg.'\');return true;" onMouseOut="MM_startTimeout();return true;"':'').'><img'.(isset($menu_id_on_li)?'':' id="im_'.$row_contpg.'" name="im_'.$row_contpg.'"').' src="'.$mainurl.'images/spacer.gif" width="0" height="0" border="0" alt=" " />'.$inject_barre_verticale.$row_conttitle.'</a></li>';
		} else {
			$is_active = $nonactive;
			$is_aorb = $nonab;
			if ($row["contpg"] == $x[0]) {
				$is_active = $active;
				$is_aorb = $ab;
			}
      $menu[$row_contpg][0] = $row;// new menu
			if (($row["contorient"] == 'center') && (sql_nrows($tblcont,"WHERE $where_statut_lang contpg='".(($row["contpg"]*10)+1)."' ") == '1')) {
        // page has subpages
				$menuhori .= '<li'.(isset($menu_id_on_li)?' id="im_'.$row_contpg.'" name="im_'.$row_contpg.'"':'').'><a class="main_nav'.$is_active.'item" accesskey="'.$is_aorb.'" href="'.$a_href.'"'.($menu_pagine===true?' onMouseOver="MM_showMenu(window.menu_'.$row["contpg"].','.$menu_pad_left.','.$menu_pad_top.',null,\'im_'.$row["contpg"].'\');return true;" onMouseOut="MM_startTimeout();return true;"':'').' alt=" "><span class="main_nav'.$is_active.'item_text"><span class="'.$contlogo_lock.'"><img'.(isset($menu_id_on_li)?'':' id="im_'.$row_contpg.'" name="im_'.$row_contpg.'"').' src="'.$mainurl.'images/spacer.gif" width="0" height="0" border="0" alt=" " />'.$inject_barre_verticale.$row_conttitle.'</span></span></a></li>';
			} else {
        // page is ultimate
				$menuhori .= '<li'.(isset($menu_id_on_li)?' id="im_'.$row_contpg.'" name="im_'.$row_contpg.'"':'').'><a class="main_nav'.$is_active.'item" accesskey="'.$is_aorb.'" href="'.$a_href.'" alt=" "><span class="main_nav'.$is_active.'item_text"><span class="'.$contlogo_lock.'"><img'.(isset($menu_id_on_li)?'':' id="im_'.$row_contpg.'" name="im_'.$row_contpg.'"').' src="'.$mainurl.'images/spacer.gif" width="0" height="0" border="0" alt=" " />'.$inject_barre_verticale.$row_conttitle.'</span></span></a></li>';
			}
		}
	} else if (preg_match("/^1[0-9]{1}\$/", $row_contpg) && ($x1subok === false)) {
		if ($row["contorient"] == 'left') {
			if ($always_under == '') $always_under .= '<li class="title"'.(isset($menu_id_on_li)?' id="im_'.$row_contpg.'" name="im_'.$row_contpg.'"':'').'>';
      else $always_under .= '<li class="first'.$contlogo_lock.'"'.(isset($menu_id_on_li)?' id="im_'.$row_contpg.'" name="im_'.$row_contpg.'"':'').'>' ;
			$always_under .= '<a href="'.$a_href.'"><img'.(isset($menu_id_on_li)?'':' id="im_'.$row_contpg.'" name="im_'.$row_contpg.'"').' src="'.$mainurl.'images/spacer.gif" width="0" height="0" border="0" alt=" " />'.$row_conttitle.'</a></li>';
		} else if ($row["contorient"] == 'right') {
			$t_menuright .= '<li'.(isset($menu_id_on_li)?' id="im_'.$row_contpg.'" name="im_'.$row_contpg.'"':'').'><a href="'.$a_href.'"><img'.(isset($menu_id_on_li)?'':' id="im_'.$row_contpg.'" name="im_'.$row_contpg.'"').' src="'.$mainurl.'images/spacer.gif" width="0" height="0" border="0" alt=" " />'.$row_conttitle.'</a></li>';
		} else {
      $menu[substr($row_contpg, 0, 1)][$row_contpg][0] = $row; // new menu
			$t_menuhori .= '<li'.(isset($menu_id_on_li)?' id="im_'.$row_contpg.'" name="im_'.$row_contpg.'"':'').'><a href="'.$a_href.'"><img'.(isset($menu_id_on_li)?'':' id="im_'.$row_contpg.'" name="im_'.$row_contpg.'"').' src="'.$mainurl.'images/spacer.gif" width="0" height="0" border="0" alt=" " />'.$inject_barre_verticale.$row_conttitle.'</a></li>';
		}
	} else {
    if (($base_x == $base_pg) && ($row["contorient"] !== 'right') && ($show_menu_hierarchy === true)) {
      if (strlen($row_contpg) == strlen($base_pg)) {
        $menuleft .= '<li '.$curr_nav.' class="first'.$contlogo_lock.'"><a href="'.$a_href.'">'.$row_conttitle.'</a></li>';
      } else if (strlen($row_contpg) == strlen($base_pg)+1) {
        $menuleft .= '<li '.$curr_nav.' class="first'.$contlogo_lock.'"><a href="'.$a_href.'">'.$row_conttitle.'</a></li>';
      } else {
        if  (substr($row_contpg,0,strlen($base_x)+1) == substr($x,0,strlen($base_x)+1))
          if (strlen($row_contpg) == strlen($base_pg)+2) {
            $menuleft .= '<li '.$curr_nav.' class="second'.$contlogo_lock.'"><a href="'.$a_href.'">'.$row_conttitle.'</a></li>';
          } else {
            if (substr($row_contpg,0,strlen($base_x)+2) == substr($x,0,strlen($base_x)+2)) {
              $menuleft .= '<li '.$curr_nav.' class="third'.$contlogo_lock.'"><a href="'.$a_href.'">'.$row_conttitle.'</a></li>';
            }
          }
      }
    }
    // define deeper pages in menu
    if (strlen($row_contpg) == strlen($base_pg)+3) {
      $menu[substr($row_contpg, 0, 1)][substr($row_contpg, 0, -2)][substr($row_contpg, 0, -1)][$row_contpg][0] = $row;
    } elseif (strlen($row_contpg) == strlen($base_pg)+2) {
      $menu[substr($row_contpg, 0, 1)][substr($row_contpg, 0, -1)][$row_contpg][0] = $row;
    } else {
      $menu[substr($row_contpg, 0, 1)][$row_contpg][0] = $row;
    }
  }
  if ($i == $nrows-1) {
    $menuleft .= $t_menuleft.$always_under;
    $menuright .= $t_menuright;
    $menuhori .= $t_menuhori;
  }
}

/** 
 * @todo fix issue in responsive mode
 */
function parseMultiDimentionalMenuHori($menu, $options = array(), $parent = null) {
  global $x;
  if (empty($options))
    $options = array('class' => 'dropdown-menu multi-level" role="menu" aria-labelledby="dropdownMenu',);
  $root = $x[0];
  $output = "";
  foreach($menu as $id => $pages) {
    if (count($pages) == 1) {
      $page = current($pages);
      $active = ($page['contpg']==$x?' class="active"':'');
      $output .= sprintf(
        '<li%s><a href="%s">%s</a></li>'
        , $active, $page['href'], $page['conttitle']
      );
    } else {
      $page = array_shift($pages);
      $dropdown = sprintf( //  role="button" aria-haspopup="true" aria-expanded="false"
        '<a class="withcaret" href="%s">%s</a>' // <a href="#" class="dropdown-toggle withcaret" data-toggle="dropdown-menu"><span class="caret"></span></a>'
        , $page['href'], $page['conttitle']
      );
      $submenu = parseMultiDimentionalMenuHori($pages, array('class' => 'dropdown-menu',), $page['contpg']);
      $active = (!$parent&&$page['contpg'][0]==$root?' active':'');
      $output .= sprintf(
        '<li class="dropdown-submenu%s">%s%s</li>'
        , $active, $dropdown, $submenu
      );
    }
  }
  if (!empty($output))
    $output = sprintf('<ul class="%s">%s</ul>', $options['class'], $output);

  return $output;
}

if ($bootstrap === true)
  $menuhori = parseMultiDimentionalMenuHori($menu, array('class' => 'nav navbar-nav',));
else {
  if  ($menuhori !== '')  $menuhori = '<ul class="level1" id="root">'.$menuhori.'</ul>' ;
}

//if	($menuhori !== '')	$menuhori = '<ul id="main_nav_list">'.$menuhori.'</ul>'	;
if	($menuleft !== '')	$menuleft = '<ul>'.$menuleft.'</ul>'	;
if	($menuright !== '')	$menuright = '<ul>'.$menuright.'</ul>'	;
