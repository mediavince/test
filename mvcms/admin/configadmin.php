<?PHP #۞ # ADMIN
if (stristr($_SERVER['PHP_SELF'],'configadmin.php')) {
  include '_security.php';
  Header("Location: $redirect");Die();
}

if (!stristr($_SERVER['PHP_SELF'],'_install.php'))
$content .= $admin_menu;

$first_line = '<'.'?PHP if (stristr($_SERVER[\'PHP_SELF\'],\'_config.php\')){include\'_security.php\';Header("Location: $redirect");Die();}';
$last_line = '?'.'>';

// lists all the texts strings appearing on site
if (!isset($send)) {
  if (!stristr($_SERVER['PHP_SELF'],'_install.php'))
  $content .= '<div class="selectHead">'.gen_form($lg,$x,$y).'<input name="send" type="submit" value="'.$envoyerString.'" /> | <input type="reset" value="Reset" /><br /> <br />';
//  $content .= '<label for="config">> config </label> <textarea name="config" rows="30" cols="30" style="width:98%;height:100%;">'.(file_exists($getcwd.$up.$safedir.'_config.php')?str_replace($last_line,"",file_get_contents($getcwd.$up.$safedir.'_config.php',NULL,NULL,strlen($first_line))):str_replace($last_line,"",file_get_contents($getcwd.$up.$urladmin.'defaults/_config.php',NULL,NULL,strlen($first_line)))).'</textarea><br /><input name="send" type="submit" value="'.$envoyerString.'" /> | <input type="reset" value="Reset" /></form></div>';
  $content .= '<label for="config">> config </label> <textarea name="config" rows="30" cols="30" style="width:98%;height:100%;">'.(@file_exists($getcwd.$up.$safedir.'_config.php')?substr(str_replace($last_line,"",@file_get_contents($getcwd.$up.$safedir.'_config.php')),strlen($first_line)):substr(str_replace($last_line,"",@file_get_contents($getcwd.$up.$urladmin.'defaults/_config.php')),strlen($first_line))).'</textarea><br />';
  if (!stristr($_SERVER['PHP_SELF'],'_install.php'))
  $content .= '<input name="send" type="submit" value="'.$envoyerString.'" /> | <input type="reset" value="Reset" /></form></div>';
// update the strings
} else if ($send == $envoyerString) {
  if (!$_POST){Header("Location: $redirect");Die();}
  $config = $_POST['config'];
  if (strstr($config,"<?") || strstr($config,"<?"))
    $update_rapport = 'no php tags allowed!';
  else
    $update_rapport = '';
	if ($update_rapport != '') {
  if (!stristr($_SERVER['PHP_SELF'],'_install.php'))
  $content .= '<br /><p style="text-align: center"><font color="Green"><b>'.$enregistrementString.' '.$nonString.' '.$modifieString.'</b></font><br /> <br />'.$update_rapport.'<br /> <br /><a href="?lg='.$lg.'&amp;x='.$x.'&amp;y='.$y.'">'.$retourString.' '.$verslisteString.' '.$detexteString.'</a></p>';
	} else {
	  $Fnm = $getcwd.$up.$safedir.'_config.php';
		$inF = fopen($Fnm,"w+");
		$config = $first_line.
                stripslashes($config).
                $last_line;
		fwrite($inF,$config);
		fclose($inF);
  $content .= '<br /><p style="text-align: center"><font color="Green"><b>config : '.$enregistrementString.' '.$effectueString.'</b></font></p>';
	}
} else {
	Header("Location: $redirect");Die();
}
?>