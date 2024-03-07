<?php #۞ #
if (stristr($_SERVER['PHP_SELF'], basename(__FILE__))){include '_security.php';Header("Location: $redirect");Die();}

$content .= $admin_menu;

$first_line = '<'.'?php if (stristr($_SERVER[\'PHP_SELF\'], basename(__FILE__))){include\'_security.php\';Header("Location: $redirect");Die();}'
  .'$_google_analytics = "';
$last_line = '";'; //.'?'.'>';

// lists all the texts strings appearing on site
if (!isset($send)) {
    $content .= '<div class="selectHead">'.gen_form($lg,$x,$y).'<input name="send" type="submit" value="'.$envoyerString.'" /> | <input type="reset" value="Reset" /><br /> <br />';
    //  $content .= '<label for="google_analytics">> google_analytics </label> <textarea name="google_analytics" rows="30" cols="30" style="width:98%;height:100%;">'.(file_exists($getcwd.$up.$safedir.'_google_analytics.php')?str_replace($last_line,"",file_get_contents($getcwd.$up.$safedir.'_google_analytics.php',NULL,NULL,strlen($first_line))):str_replace($last_line,"",file_get_contents($getcwd.$up.$urladmin.'defaults/_google_analytics.php',NULL,NULL,strlen($first_line)))).'</textarea><br /><input name="send" type="submit" value="'.$envoyerString.'" /> | <input type="reset" value="Reset" /></form></div>';
    $content .= '<label for="google_analytics">> google_analytics </label> <textarea name="google_analytics" rows="30" cols="30" style="width:98%;height:100%;">'.(file_exists($getcwd.$up.$safedir.'_google_analytics.php')?substr(str_replace($last_line,"",file_get_contents($getcwd.$up.$safedir.'_google_analytics.php')),strlen($first_line)):substr(str_replace($last_line,"",file_get_contents($getcwd.$up.$urladmin.'defaults/_google_analytics.php')),strlen($first_line))).'</textarea><br /><input name="send" type="submit" value="'.$envoyerString.'" /> | <input type="reset" value="Reset" /></form></div>';
} else if ($send == $envoyerString) {
    if (!$_POST) {
        Header("Location: $redirect");Die();
    }
    $google_analytics = $_POST['google_analytics'];
    if (strstr($google_analytics,"<"."?") || strstr($google_analytics,"?".">"))
        $update_rapport = 'no php tags allowed!';
    else
        $update_rapport = '';
    if ($update_rapport != '') {
        $content .= '<br /><p style="text-align: center"><font color="Green"><b>'.$enregistrementString.' '.$nonString.' '.$modifieString.'</b></font><br /> <br />'.$update_rapport.'<br /> <br /><a href="?lg='.$lg.'&amp;x='.$x.'&amp;y='.$y.'">'.$retourString.' '.$verslisteString.' '.$detexteString.'</a></p>';
    } else {
        $Fnm = $getcwd.$up.$safedir.'_google_analytics.php';
        $inF = fopen($Fnm,"w+");
        $google_analytics = $first_line.PHP_EOL.stripslashes($google_analytics).$last_line;
        fwrite($inF,$google_analytics);
        fclose($inF);
        $content .= '<br /><p style="text-align: center"><font color="Green"><b>'.$enregistrementString.' '.$effectueString.'</b></font></p>';//<br /> <br /><a href="?lg='.$lg.'&amp;x='.$x.'&amp;y='.$y.'">'.$retourString.' '.$verslisteString.' '.$detexteString.'</a>';
	}
} else {
	Header("Location: $redirect");Die();
}
