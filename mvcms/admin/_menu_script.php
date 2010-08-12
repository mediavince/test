<?PHP #۞ #

if (stristr($_SERVER["PHP_SELF"], "_menu_script.php")) {
	include '_security.php';
	Header("Location: $redirect");Die();
}

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
$read = @mysql_query("SELECT * FROM $tblcont $where ORDER BY CAST(contpg as CHAR) ");
$nrows = @mysql_num_rows($read);

$base_x = $x[0];
if  (preg_match("/^1[0-9]{1,}\$/", $x) && ($x1subok === false))
$base_x = $x[0].$x[1] ;

for ($i=0;$i<$nrows;$i++) {
	$row = mysql_fetch_array($read);
	$row_conturl = $row["conturl"];
	$row_contpg = $row["contpg"];
	//$array_pages[] = $row_contpg; use it to list the pages
	$row_conttitle = $row["conttitle"];
	$clean_title = space2underscore($row_conttitle);
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
	$base_pg = substr($row_contpg,0,(strlen($base_x)));
  
  if (stristr($_SERVER['REQUEST_URI'],$urladmin) && ($logged_in === true)) {
    $a_href = $local.'?lg='.$lg.'&amp;x='.$row_contpg.'" alt=" ';
    if ($row["contorient"] == 'left')
      $row_conttitle = '<span style=\'color:red;\'>&deg;</span>'.$row_conttitle;
    if ($row["contorient"] == 'right')
      $row_conttitle .= '<span style="color:red;">&deg;</span>';
  	if	($row["contstatut"] == 'N')
      $row_conttitle = '<font color=Red>*'.$row["conttitle"].'*</font>'	;
  } else {
    $a_href = $mainurl.$row_conturl.'" alt=" ';
  }
	
  $curr_nav = "";
  if  ($row_contpg == $x) $curr_nav = ' id="currentnav" ' ;
 	
  $inject_barre_verticale = '';//'<font color="Navy"> | </font>'	;
	if	(($row_contpg == $base_x) || ($row_contpg == $x))
  	$inject_barre_verticale = '<font color="Red"> > </font>'	;
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
			if (($row["contorient"] == 'center') && (sql_nrows($tblcont,"WHERE $where_statut_lang contpg='".(($row["contpg"]*10)+1)."' ") == '1')) {
				$menuhori .= '<li'.(isset($menu_id_on_li)?' id="im_'.$row_contpg.'" name="im_'.$row_contpg.'"':'').'><a class="main_nav'.$is_active.'item" accesskey="'.$is_aorb.'" href="'.$a_href.'"'.($menu_pagine===true?' onMouseOver="MM_showMenu(window.menu_'.$row["contpg"].','.$menu_pad_left.','.$menu_pad_top.',null,\'im_'.$row["contpg"].'\');return true;" onMouseOut="MM_startTimeout();return true;"':'').' alt=" "><span class="main_nav'.$is_active.'item_text"><span class="'.$contlogo_lock.'"><img'.(isset($menu_id_on_li)?'':' id="im_'.$row_contpg.'" name="im_'.$row_contpg.'"').' src="'.$mainurl.'images/spacer.gif" width="0" height="0" border="0" alt=" " />'.$inject_barre_verticale.$row_conttitle.'</span></span></a></li>';
			} else {
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
          if (strlen($row_contpg) == strlen($base_pg)+2)
            $menuleft .= '<li '.$curr_nav.' class="second'.$contlogo_lock.'"><a href="'.$a_href.'">'.$row_conttitle.'</a></li>';
          else
            if (substr($row_contpg,0,strlen($base_x)+2) == substr($x,0,strlen($base_x)+2))
              $menuleft .= '<li '.$curr_nav.' class="third'.$contlogo_lock.'"><a href="'.$a_href.'">'.$row_conttitle.'</a></li>';
      }
    }
  }
  if ($i == $nrows-1) {
    $menuleft .= $t_menuleft.$always_under;
    $menuright .= $t_menuright;
    $menuhori .= $t_menuhori;
  }
}

if	($menuhori !== '')	$menuhori = '<ul class="level1" id="root">'.$menuhori.'</ul>'	;
//if	($menuhori !== '')	$menuhori = '<ul id="main_nav_list">'.$menuhori.'</ul>'	;
if	($menuleft !== '')	$menuleft = '<ul>'.$menuleft.'</ul>'	;
if	($menuright !== '')	$menuright = '<ul>'.$menuright.'</ul>'	;

?>