<?php #۞ #
if (stristr($_SERVER['PHP_SELF'], basename(__FILE__))){include '_security.php';Header("Location: $redirect");Die();}

$content .= $admin_menu;

if (!isset($array_templates_parsed))
$array_templates_parsed = array("basic_modules","advanced_modules");
else
$array_templates_parsed = array_unique(array_merge(array("basic_modules","advanced_modules"),$array_templates_parsed));

if (!isset($array_templates_notparsed))
$array_templates_notparsed = array("mail_newsletter","mail_communications","mail_credentials");
else
$array_templates_notparsed = array_unique(array_merge(array("mail_newsletter","mail_communications","mail_credentials"),$array_templates_notparsed));

$array_templates = array_unique(array_merge($array_templates_parsed,$array_templates_notparsed));

$content .= '<div class="selectHead">'.gen_form($lg,$x,$y).'<input type="submit" name="" value="site_template" />'.(isset($array_templates[1])?' | <input type="submit" name="tpl" value="'.implode($array_templates,'" /> | <input type="submit" name="tpl" value="').'" />':'').'</form></div>';

if (isset($_POST['tpl'])) $tpl = stripslashes($_POST['tpl']);

if (isset($_POST['tpl']) && in_array($tpl,$array_templates_parsed)) { // vars are represented as \$var
  $first_line = '<'.'?PHP if (stristr($_SERVER[\'PHP_SELF\'],\'_tpl_'.$tpl.'.php\') || !isset($this_is)) {include\'_security.php\';Header("Location: $redirect");Die();}';
  $file_check = "_tpl_$tpl.php";
  $last_line = '?'.'>';
} else { // vars are normal $var
  $first_line = '<'.'?PHP if (stristr($_SERVER[\'PHP_SELF\'],\''.(isset($tpl) && in_array($tpl,$array_templates_notparsed)?'_tpl_'.$tpl.'.php':'_template.php').'\')){include\'_security.php\';Header("Location: $redirect");Die();}'.(isset($tpl) && in_array($tpl,$array_templates_notparsed)?'$_tpl_'.$tpl:'$_template').' = "';
  $file_check = (isset($tpl) && in_array($tpl,$array_templates_notparsed)?"_tpl_$tpl.php":"_template.php");
  $last_line = '"; ?'.'>';
}

// lists all the texts strings appearing on site
if (!isset($send)) {
  $content .= '<div class="selectHead">'.gen_form($lg,$x,$y).'<input name="send" type="submit" value="'.$envoyerString.'" /> | <input type="reset" value="Reset" /><br /> <br />';
  if (file_exists($getcwd.$up.$safedir.$file_check)) {
  //  $template = str_replace($last_line,"",file_get_contents($getcwd.$up.$safedir.$file_check,NULL,NULL,strlen($first_line)));
    $template = substr(str_replace($last_line,"",file_get_contents($getcwd.$up.$safedir.$file_check)),strlen($first_line));
  } else {
  //  $template = str_replace($last_line,"",file_get_contents($getcwd.$up.$urladmin.'defaults/'.$file_check,NULL,NULL,strlen($first_line)));
    $template = substr(str_replace($last_line,"",file_get_contents($getcwd.$up.$urladmin.'defaults/'.$file_check)),strlen($first_line));
  }
	if (isset($tpl)) {
    if (in_array($tpl,$array_templates_parsed)) {
      $template = str_replace('"\$','"\\\\$',$template);
      $template = str_replace('\"','\\\\"',$template);
      $template = str_replace("\'","\\\\'",$template);
      $template = str_replace('&','&amp;',$template);
    }
    $content .= '<input type="hidden" name="tpl" value="'.$tpl.'" />';
  }
  $content .= '<label for="template">> template '.(isset($tpl)&&in_array($tpl,$array_templates_parsed)?$tpl.' (these regex are accepted and translated: \\\$,\\\\\',\\\",&amp; encodes as &amp;amp;)':' enclosed in double quotes, use standard html with \', escaping done automatically, use \" for conditions only !').' </label> <textarea name="template" rows="30" cols="30" style="width:98%;height:100%;">'.$template.'</textarea><br /><input name="send" type="submit" value="'.$envoyerString.'" /> | <input type="reset" value="Reset" /></form></div>';
// update the strings
} else if ($send == $envoyerString) {
  if (!$_POST){Header("Location: $redirect");Die();}
  $template = $_POST['template'];
  if (strstr($template,"<?") || strstr($template,"<?"))
    $update_rapport = 'no php tags allowed!';
  else
    $update_rapport = '';
	if ($update_rapport != '') {
  $content .= '<br /><p style="text-align: center"><font color="Green"><b>'.$enregistrementString.' '.$nonString.' '.$modifieString.'</b></font><br /> <br />'.$update_rapport.'<br /> <br /><a href="?lg='.$lg.'&amp;x='.$x.'&amp;y='.$y.'">'.$retourString.' '.$verslisteString.' '.$detexteString.'</a></p>';
	} else {
	  $Fnm = $getcwd.$up.$safedir.$file_check;
		$inF = fopen($Fnm,"w+");
		if (isset($tpl) && in_array($tpl,$array_templates_parsed)) {
      $template = str_replace('"$','"\\$',stripslashes($template));
    } else {
      $template = stripslashes($template);
    }
		$template = $first_line.
                $template.
                $last_line;
		fwrite($inF,$template);
		fclose($inF);
  $content .= '<br /><p style="text-align: center"><font color="Green"><b>'.$enregistrementString.' '.$effectueString.' '.(isset($tpl)?$pourString.' '.$tpl:'').'</b></font></p>';//<br /> <br /><a href="?lg='.$lg.'&amp;x='.$x.'&amp;y='.$y.'">'.$retourString.' '.$verslisteString.' '.$detexteString.'</a>';
	}
} else {
	Header("Location: $redirect");Die();
}
