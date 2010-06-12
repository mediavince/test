<?PHP ## ADMIN
if (stristr($_SERVER['PHP_SELF'],'_modulesparser.php')) {
  include '_security.php';
  Header("Location: $redirect");Die();
}
$admin_viewing = false;
if (stristr($_SERVER['REQUEST_URI'],$urladmin) && ($logged_in === true) && isset($admin_name))
$admin_viewing = true;

if (!isset($array_fixed_modules)) $array_fixed_modules = array();
if (!isset($array_unmanaged_modules)) $array_unmanaged_modules = array("contact","profil");
$mod_array = array_unique(array_merge($array_fixed_modules,$array_modules));//,"catalog","inscrire");

foreach(array("content","leftlinksentry","toplinksentry","scrollerentry") as $this_content)
foreach($mod_array as $key_mod_array => $value_mod_array) {
	if (strstr(${$this_content},"[".$value_mod_array)) {
    if (($this_content == "content") && (isset($admin_priv) && !in_array('1',$admin_priv))) {
      $admin_menu = ($admin_viewing===true&&!in_array($value_mod_array,$array_unmanaged_modules)?'<div class="admin_menu">'.(in_array($value_mod_array,$array_csv_list)?'<a href="csvliste.php?action=list&amp;what='.$value_mod_array.'" target="_blank"><img src="'.$mainurl.'images/xlslogo.gif" width="16" height="16" title="CSV" alt="CSV" align="right" border="0" /></a>':'').'<a style="padding:2px;" href="?lg='.$lg.'&amp;x=z&amp;y='.$value_mod_array.'"><img src="'.$mainurl.'images/'.($value_mod_array=="membre"?$array_admin_menu["user"]:(isset($array_admin_menu[$value_mod_array])?$array_admin_menu[$value_mod_array]:'categories.png')).'" title="'.$gestionString.' '.$surString.' '.${$value_mod_array."String"}.'" alt="'.$gestionString.' '.$surString.' '.${$value_mod_array."String"}.'" border="0" />'.$gestionString.' '.$surString.' '.${$value_mod_array."String"}.'</a> | <a href="?lg='.$lg.'&amp;x=z&amp;y='.$value_mod_array.'&amp;send=new"><img src="'.$mainurl.'images/card_f2.png" title="'.$ajoutString.' '.${$value_mod_array."String"}.'" alt="'.$ajoutString.' '.${$value_mod_array."String"}.'" border="0" />'.$ajoutString.'</a>'.(isset(${$value_mod_array."Id"})?' | <a href="?lg='.$lg.'&amp;x=z&amp;y='.$value_mod_array.'&amp;send=edit&amp;'.$value_mod_array.'Id='.${$value_mod_array."Id"}.'">'.$modifierString.'</a> | <a href="?lg='.$lg.'&amp;x=z&amp;y='.$value_mod_array.'&amp;send=delete&amp;'.$value_mod_array.'Id='.${$value_mod_array."Id"}.'" onclick="return confirm(\''.$confirmationeffacementString.'\');"><img src="'.$mainurl.'images/delete.gif" width="10" height="10" title="'.$effacerString.'" alt="'.$effacerString.'" border="0" /> '.$effacerString.'</a>':'').'<br /> &nbsp; </div>':'').(isset($admin_menu)?$admin_menu:'');
      $admin_viewing_menu = $admin_menu;
    } else $admin_viewing_menu = "";
	  // the following is completely dynamic and is managed in template edit
	  
	  /*
    $pattern = "[".$value_mod_array."[a-zA-Z0-9,]{0,}[:]{0,1}[a-zA-Z0-9,\-_=|]{0,}"."]";
    preg_match($pattern,${$this_content},$matches);
    */
	  /* following fixes potential bug if instance of value mod array exists in content, in which it will not take [] but any other... -> bug // additionally, when multiple modules are inserted on one page, the order of the tables can prevent good display, make sure the modules appear on the page in the order they are in the array !!
	  */
    $pattern = "[[".$value_mod_array."[a-zA-Z0-9,|]{0,}[:]{0,1}[a-zA-Z0-9,\-_=|]{0,}"."]]";
	//  if (isset($matches[0])) unset($matches[0]);
	//  if (isset($matches[0])) $notice .= $matches[0]."|";
    preg_match($pattern,${$this_content},$matches);
    if (isset($matches[0])) $matches[0] = substr($matches[0],1,-1);
  //  $notice .= "<br />".$matches[0]."<br />";
        
    $this_is = $value_mod_array;
    if (in_array($matches[0],$array_modules)) { // params are passed, should be in array_modules
      if (@file_exists($getcwd.$up.$safedir.'_tpl_basic_modules.php'))
        include $getcwd.$up.$safedir.'_tpl_basic_modules.php';
      else
        include $getcwd.$up.$urladmin.'defaults/_tpl_basic_modules.php';
      $mod_url = $getcwd.$up.(in_array($this_is,$array_fixed_modules)?'lib/mods/_mod_'.$value_mod_array:$urladmin.'_mod_'.'generic').'.php';
      if (@file_exists($mod_url)) {
        if (($protected_show === true) && ($logged_in === false) && (!in_array($this_priv,array("","1")) || (($this_priv != '') && (!in_array("1",explode("|",$this_priv))))))
        ${$this_content} = str_replace("[".$value_mod_array."]",${$this_is."String"}.' > '.$error_accesspriv,${$this_content});
        else {
         	require($mod_url);
          ${$this_content} = str_replace("[".$value_mod_array."]",$admin_viewing_menu.${"_mod_".$value_mod_array},${$this_content});
        }
      } else {
        ${$this_content} = str_replace("[".$value_mod_array."]",$error_request.' > module: <i>\"'.$value_mod_array.'\"</i> '.$nonString.' '.$existantString,${$this_content});
      }
    } else { // [module <option>]
      $passed_parameters = explode(":",$matches[0]);
      if (isset($passed_parameters[1])) {
        if (strstr($passed_parameters[0],"|")) {
          $array_passed_tables = explode("|",$passed_parameters[0]);
          $dbtables = array($array_passed_tables[0]);
        } else
        $array_passed_tables = array();
        $dbtables = explode(",",$passed_parameters[0]);
        $this_is = $dbtables[0];
        if (isset($dbtables[1]))
        $that_is = $dbtables[1];
        $array_passed_parameters = explode("|",$passed_parameters[1]);
        foreach($array_passed_parameters as $new_param) {
          $new_param = explode("=",$new_param);
          if (isset($new_param[1])) {
            if (!isset(${$new_param[0]}))
            ${$new_param[0]} = $new_param[1];
            ${"filter_".$new_param[0]} = $new_param[1];
          } else {
            if ($new_param[0][0]=="-")
            ${"filter_".substr($new_param[0],1)} = false;
            else {
              ${"filter_".$new_param[0]} = true;
              if (!isset(${$new_param[0]}))
              ${$new_param[0]} = '';
            }
          }
        }
      }
      for ($looping_array_passed_tables=0;$looping_array_passed_tables<(isset($array_passed_tables[1])?count($array_passed_tables):1);$looping_array_passed_tables++) {
        if (isset($array_passed_tables[1]))
        $this_is = $array_passed_tables[$looping_array_passed_tables];
        if (@file_exists($getcwd.$up.$safedir.'_tpl_advanced_modules.php'))
          include $getcwd.$up.$safedir.'_tpl_advanced_modules.php';
        else
          include $getcwd.$up.$urladmin.'defaults/_tpl_advanced_modules.php';
        $mod_url = $getcwd.$up.(in_array($this_is,$array_fixed_modules)?'lib/mods/_mod_'.$value_mod_array:$urladmin.'_mod_'.'generic').'.php';
      //  $mod_url = $getcwd.$up.$urladmin.'_mod_'.(in_array($this_is,$array_fixed_modules)?$value_mod_array:'generic').'.php';
        if (isset($array_passed_tables[1])) {
          require($mod_url);
          $_mod_array_passed_tables = (isset($_mod_array_passed_tables)?$_mod_array_passed_tables:'').(${"_mod_".$this_is}!=''?"<br /><h2>".${$this_is."String"}."</h2>".${"_mod_".$this_is}:'');
        }
      }
      if (@file_exists($mod_url)) {
        if (($protected_show === true) && ($logged_in === false) && (!in_array($this_priv,array("","1")) || (($this_priv != '') && (!in_array("1",explode("|",$this_priv))))))
        ${$this_content} = str_replace("[".$matches[0]."]",${$this_is."String"}.' > '.$error_accesspriv,${$this_content});
        else {
          if (isset($array_passed_tables[1])) {
            ${$this_content} = str_replace("[".$matches[0]."]",$admin_viewing_menu.(isset($_mod_array_passed_tables)?$_mod_array_passed_tables:''),${$this_content});
          } else {
            require($mod_url);
            ${$this_content} = str_replace("[".$matches[0]."]",$admin_viewing_menu.(isset(${"_mod_".$value_mod_array})?${"_mod_".$value_mod_array}:''),${$this_content});
          }
        }
      } else {
        ${$this_content} = str_replace("[".$matches[0]."]",$error_request.' > module: <i>\"'.$value_mod_array.'\"</i> '.$nonString.' '.$existantString,${$this_content});
      }
    }
  }
}
	
?>