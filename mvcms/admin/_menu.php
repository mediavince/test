<?PHP #۞ # ADMIN
if (stristr($_SERVER["PHP_SELF"], "_menu.php")) {
	include '_security.php';
	Header("Location: $redirect");Die();
}
//          $notice .= mvtrace(__FILE__,__LINE__)." $x<br />";

$where_statut_lang = " contlang='$lg' AND ";

if ($logged_in === false) {
	$local = $mainurl;
  if (isset($x) && ($x == 'z'))
    {Header("Location: $redirect");Die();}
  //!!!! contlogo in db is contpriv !!!!
  if (!isset($user_priv)) $user_priv = array("1");//
  $where_statut_lang = " contstatut='Y' AND contlang='$lg' AND (";
  // allows multiple privileges because userPriv is an array  
  foreach($user_priv as $key)
    $where_statut_lang .= " contlogo LIKE '%$key%' OR ";
  $where_statut_lang .= " contlogo LIKE '%1%' OR contlogo='') AND ";
}
if ($logged_in === true) {
  if (!isset($admin_priv)) $admin_priv = array("1");
  if (in_array('1',$admin_priv)) {
    if (($y == '2') && isset($adminId) && ($adminId != $admin_id))
    $adminId = $admin_id;
  //  if (!isset($send)&&($y=='2')) $send = 'edit';
  }
  if (isset($x) && ($x == 'z') && in_array('1',$admin_priv) && ((!isset($adminId) || (isset($adminId) && ($adminId != $admin_id))) && ($y != '2') && (!isset($send) || (isset($send) && (($send != 'edit') || ($send != $sauverString))))))// && ($_SERVER['QUERY_STRING'] != $admin_profil_url)
    {Header("Location: $mainurl$urladmin");Die();}
}

  if (isset($_POST['login']) && (!isset($_GET['x']) || !isset($_POST['x'])))// && ($login == $entrerString))
  $x = ($logged_in===true&&in_array('1',$admin_priv)?'1':'z');

$scroller = sql_get($tblcont," WHERE conttype='scroller' AND contlang='$lg' ","conttitle, contentry, contpg");
if ($scroller[0] == '.') {
	$scrollertitle = "-News Wire";
	$scrollerentry = '';
} else {
	$scrollertitle = $scroller[0];
	$scrollerentry = str_replace($filedir,$up.$filedir,$scroller[1]);
	$scrollerentry = str_replace($libdir,$up.$libdir,$scroller[1]);
}

$leftlinks = sql_get($tblcont," WHERE conttype='leftlinks' AND contlang='$lg' ","conttitle, contentry, contpg");
if ($leftlinks[0] == '.') {
	$leftlinkstitle = "-Left Links";
	$leftlinksentry = '';
} else {
	$leftlinkstitle = $leftlinks[0];
	$leftlinksentry = str_replace($filedir,$up.$filedir,$leftlinks[1]);
	$leftlinksentry = str_replace($libdir,$up.$libdir,$leftlinksentry);
}

$toplinks = sql_get($tblcont," WHERE conttype='toplinks' AND contlang='$lg' ","conttitle, contentry, contpg");
if ($toplinks[0] == '.') {
	$toplinkstitle = "-Top Links";
	$toplinksentry = '';
} else {
	$toplinkstitle = $toplinks[0];
	$toplinksentry = str_replace($filedir,$up.$filedir,$toplinks[1]);
	$toplinksentry = str_replace($libdir,$up.$libdir,$toplinksentry);
}

if ($x == '0') {
	Header("Location: $redirect");
  Die();
}

if ((sql_nrows($tblcont,"WHERE contlang='$lg' ") == '0') && ($x !== 'z'))
$lg = $default_lg;
$pgcontent = sql_get($tblcont,"WHERE contpg='$x' AND contlang='$lg' ","conttitle,contentry,contmetadesc,contmetakeyw,conttype,contlogo");
$title = $pgcontent[0]; // gets updated in adminedit upon changes as contTitle is set...
$page_title = $title;
$content = str_replace($filedir,$up.$filedir,$pgcontent[1]);
$content = str_replace($libdir,$up.$libdir,$content);
$desc = $pgcontent[2];
$keyw = $pgcontent[3];
$default_desc_keyw = sql_get($tblcont,"WHERE contpg='1' AND contlang='$lg' ","contmetadesc,contmetakeyw");
if ($default_desc_keyw[0] == '.')
  if ($lg != $default_lg)
  $default_desc_keyw = sql_get($tblcont,"WHERE contpg='1' AND contlang='$default_lg' ","contmetadesc,contmetakeyw");
if ($default_desc_keyw[0] == '.') {
  $default_desc_keyw[0] = '';
  $default_desc_keyw[1] = '';
}
  if ($desc == '') $desc = ($default_desc_keyw[0]!=''?$default_desc_keyw[0]:$meta_desc);// in _strings.php
  if ($keyw == '') $keyw = ($default_desc_keyw[1]!=''?$default_desc_keyw[1]:$meta_keyw);// in _strings.php

$this_type = $pgcontent[4];
$this_priv = $pgcontent[5];

if ($logged_in === true) {
	if	(($x !== 'z') && ($y == '1'))	require('adminedit.php')	;
	if ($x == 'z') {
		$title = 'Extra '.$adminString.' '.$optionsString;
		$style_admin_menu = "";
		
		$admin_menu = $style_admin_menu.'<div class="adminmenu">';
		
		//if	( (!isset($y)) || (!preg_match("/^[1-9]\$/", $y)) )	$y = "1"	;
		if	( !isset($y) || (!preg_match("/^[0-9]+\$/",$y) && !in_array($y,$array_keys_admin_menu)) || (preg_match("/^[0-9]+\$/",$y) && (($y<1) || (($y>8) && (sql_getone($tbladmin,"WHERE adminpriv LIKE '%0%' LIMIT 1 ","adminutil") != $admin_name)) || (($y>13) && (sql_getone($tbladmin,"WHERE adminpriv LIKE '%0%' LIMIT 1 ","adminutil") == $admin_name)))) )	$y = "1"	;
		
		$i = 0;
		foreach($array_admin_menu as $key=>$ico) {
		  if (in_array($ico,$array_super_admin_menu)) { // needs 2dim array
        if (($admin_priv[0] == '0') && (sql_getone($tbladmin,"WHERE adminpriv LIKE '%0%' LIMIT 1 ","adminutil") == $admin_name))
      	$loginform .= '<a style="padding:2px;" href="?lg='.$lg.'&amp;x=z&amp;y='.$key.'"><img src="'.$mainurl.'images/'.$ico.'" width="24" height="24" alt="'.$modificationString.' template" title="'.$modificationString.' '.${$key."String"}.'" border="'.($y==$key?'1" style="border:1px solid;padding:2px;':'0" style="padding:4px;').'" /></a>';//<div style="float:left;"></div>
  		} else {
        if (in_array($ico,$array_basic_admin_menu) || (in_array($ico,$array_modules) && (${"mod_".$ico} === true))) {
    		  if (in_array($ico,$array_modules)) {// if array is not 2dim defaults to categories
            if (($ico == $array_modules[0]) && ($y == '1')) {
              $admin_menu .= '<hr /><br />';
              $i = 0;
            }
    		    $key = $ico;
            $ico = "categories.png";
          }
          if ($key != 'membre') {
      		  if (($key == 'user') && in_array('membre',$array_modules)) { // if array is not 2dim defaults to categories
              $key = 'membre'; 
            }
            /*
      		  if ($y == '1')
      		  $admin_menu .= '<div style="float:left;min-height:50px;width:32%;padding:1px;margin:2px;border:1px solid gray;"><div style="float:left;min-width:50px;max-width:75px;"><a style="padding:2px;" href="?lg='.$lg.'&amp;x=z&amp;y='.$key.'"><img src="'.$mainurl.'images/'.$ico.'" '.show_img_attr($mainurl.'images/'.$ico).' alt="'.$gestionString.' '.$surString.' '.${$key."String"}.'" title="'.$gestionString.' '.$surString.' '.${$key."String"}.'" border="'.($y==$key?'1" style="border:1px solid;padding:2px;':'0').'" /></a></div><div style="float:left;text-align:left;padding:2px;"><b>'.${$key."String"}.'</b><br />informations</div></div>';//<hr /><br />
      		  */
      		  if (($y == '1') && !in_array('0',$admin_priv)) {
        		  if ($i==4) 
        		  $admin_menu .= '<div class="clear"></div>';
        		  $admin_menu .= '<div style="float:left;min-height:150px;width:23%;padding:1px;margin:2px;border:1px solid gray;text-align:left;"><div style="float:left;min-width:45px;max-width:75px;text-align:left;"><a style="padding:2px;" href="?lg='.$lg.'&amp;x=z&amp;y='.$key.'"><img src="'.$mainurl.'images/'.$ico.'" '.show_img_attr($mainurl.'images/'.$ico).' title="'.$gestionString.' '.$surString.' '.${$key."String"}.'" alt="'.$gestionString.' '.$surString.' '.${$key."String"}.'" align="left" border="0" /></a></div><b>'.ucfirst(${$key."String"}).'</b><br />'.(isset(${$key."HelpString"})?${$key."HelpString"}:'').'</div>';//<hr /><br /><div style="float:left;text-align:left;padding:2px;"></div>
      		  } else
      		  $admin_menu .= '<div style="float:left;max-width:100px;"><a style="padding:2px;" href="?lg='.$lg.'&amp;x=z&amp;y='.$key.'"><img src="'.$mainurl.'images/'.$ico.'" '.show_img_attr($mainurl.'images/'.$ico).' title="'.$gestionString.' '.$surString.' '.${$key."String"}.'" alt="'.$gestionString.' '.$surString.' '.${$key."String"}.'" border="'.($y==$key?'1" style="border:1px solid;padding:2px;':'0').'" '.(isset(${$key."HelpString"})?' onmouseover="javascript:getelbyid(\'help'.$key.'\').style.display=\'block\';" onmouseout="javascript:getelbyid(\'help'.$key.'\').style.display=\'none\';"':'').' /><br />'.ucfirst(${$key."String"}).'</a>'.(isset(${$key."HelpString"})?'<div id="help'.$key.'" class="helpbox" style="display:none;float:right;">'.${$key."HelpString"}.'</div>':'').'</div>';
      		}
      		$i++;
      	}
      }
		}
		
		$admin_menu .= '</div><hr /><br />';
		
		if (in_array('1',$admin_priv)) $admin_menu = '';
		
    if	($y == '1')	include 'extraoptions.php';
		if	(($y == '2') || ($y == 'admin')) {
    //	require('adminadmin.php')	;
      $tinyMCE = false;//no textarea
      $this_is = 'admin';
      if (!isset($params_array))
      $params_array = array('util','email','priv');
    	include 'itemadmin.php';
		}
    if	(($y == '3') || ($y == 'user') || ($y == 'membre')) {
    //	require('useradmin.php')	;
      $this_is = 'user';
      $params_array = array('util','email','priv');
      $array_mandatory_fields = array('util','email');
      if ((($y == '3') || ($y == 'user')) && in_array('membre',$array_modules))
      $y = 'membre';
      if ($y == 'membre') {
        if (!isset($params_array))
        $params_array = array('gendre','nom','prenom','profession');
        if (!isset($_membre_array_mandatory_fields))
        $_membre_array_mandatory_fields = array('','gendre','nom','prenom','profession');
        $that_is = 'membre';
      }
    	include 'itemadmin.php';
		}
    if  (($y == 'communications')) include 'communicationsadmin.php';
    if  (($y == '4') || ($y == 'newsletter')) include 'newsletteradmin.php';
		if	(($y == '5') || ($y == 'string'))	include 'textesadmin.php';
		if	(($y == '6') || ($y == 'bannedip'))	include 'bannedipsadmin.php';
		if	(($y == '7') || ($y == 'media'))	include 'mediaadmin.php';
		
		
		if (in_array($y,$array_modules_admin)) {
      $this_is = $y;
      if (@file_exists($getcwd.$up.$urladmin.$this_is.'admin.php'))
    	include $this_is.'admin.php';
    	else
    	include 'itemadmin.php';
		}
		
		
		if	(($y == '10') || ($y == 'template'))	include 'templateadmin.php';
		if	(($y == '11') || ($y == 'analytics'))	include 'google_analyticsadmin.php';
		if	(($y == '12') || ($y == 'params'))	include 'paramsadmin.php';
		if	(($y == '13') || ($y == 'config'))	include 'configadmin.php';
		if	(($y == '14') || ($y == 'module'))	include 'moduleadmin.php';
	} else {
    if (!isset($send) || ($send != 'edit'))
    include $getcwd.$up.$urladmin.'_modulesparser.php';
	
  }
	$scrollertitle = '<a href="?lg='.$lg.'&amp;x=10777&amp;send=edit">'.$scrollertitle.'<br />('.$modifierString.')</a>';
	$leftlinksentry .= '<p><a href="?lg='.$lg.'&x=10888&amp;send=edit">'.$leftlinkstitle.'<br />('.$modifierString.')</a></p>';
	$toplinksentry .= '<ul><li><a href="?lg='.$lg.'&amp;x=10999&amp;send=edit">'.$toplinkstitle.' ('.$modifierString.')</a></li></ul>';
}

include '_menu_script.php';

if	($title == "")	$title = $slogan	;
if	($content == "")	$content = "In construction!"	;

$login = $loginform	;

?>