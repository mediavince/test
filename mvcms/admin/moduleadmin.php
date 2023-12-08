<?php #۞ #
if (stristr($_SERVER['PHP_SELF'], basename(__FILE__))){include '_security.php';Header("Location: $redirect");Die();}

$content .= $admin_menu;

if (!isset($array_fixed_modules))
    $array_fixed_modules = array("contact","profil");
else
    $array_fixed_modules = array_unique(array_merge(array("contact","profil"),$array_fixed_modules));

if (isset($_POST['mod']))
    $mod = stripslashes($_POST['mod']);

$content .= '<div class="selectHead">'.gen_form($lg,$x,$y).gen_fullselectoption($array_fixed_modules,(isset($_POST['mod'])&&in_array($mod,$array_fixed_modules)?$mod:''),'','mod').' | <input type="submit" name="show" value="'.$choisirString.'" /></form></div>';

if ((isset($_POST['newmod']) && ($_POST['newmod'] != '')) || isset($_POST['mod'])) {
    if (!isset($mod))
        $mod = stripslashes($_POST['newmod']);
    $first_line = '<'.'?php if (stristr($_SERVER[\'PHP_SELF\'], basename(__FILE__))){include \'_security.php\';Header("Location: $redirect");Die();}'
	    .PHP_EOL.PHP_EOL;

    $file_check = "mods/_mod_$mod.php";
    $last_line = PHP_EOL; //.'?'.'>';
}

if (!isset($send)) {
    $content .= '<div class="selectHead">'.gen_form($lg,$x,$y).'<input name="send" type="submit" value="'.$envoyerString.'" /> | <input type="reset" value="Reset" /><br /> <br />';
    $module = '';
    if (isset($file_check))
        if (@file_exists($getcwd.$up.$safedir.$file_check)) {
            $module = substr(str_replace($last_line,"",file_get_contents($getcwd.$up.$safedir.$file_check)),strlen($first_line));
        } else {
            if (@file_exists($getcwd.$up.$urladmin.'defaults/mods/'.$file_check))
                $module = substr(str_replace($last_line,"",file_get_contents($getcwd.$up.$urladmin.'defaults/mods/'.$file_check)),strlen($first_line));
        }
    if (isset($mod))
        $content .= '<input type="hidden" name="mod" value="'.$mod.'" />';
    else
        $content .= 'Name of module ( _mod_&lt;name&gt;.php ) <input type="text" name="newmod" value="" /><br />';
    $content .= '<label for="module">> module </label> <textarea name="module" rows="30" cols="30" style="width:98%;height:100%;">'.str_replace("<","&lt;",str_replace("$","&#036;",str_replace("\\","&#092;",$module))).'</textarea><br /><input name="send" type="submit" value="'.$envoyerString.'" /> | <input type="reset" value="Reset" /></form></div>';
} else if ($send == $envoyerString) {
    if (!$_POST){
        Header("Location: $redirect");
        Die();
    }
    $module = $_POST['module'];
    $update_rapport = '';
    if (strstr($module,"<?") || strstr($module,"?>"))
        $update_rapport .= 'no php tags allowed!';
    if ($update_rapport != '') {
        $error .= '<b>'.$enregistrementString.' '.$nonString.' '.$modifieString.'</b><br /> <br />'.$update_rapport.'<br /> <br />';
    } else {
        if (!is_dir($getcwd.$up.$safedir.'mods'))
            mkdir($getcwd.$up.$safedir.'mods');
        $Fnm = $getcwd.$up.$safedir.$file_check;
        $inF = fopen($Fnm,"w+");
        $module = str_replace("+$/","+\\$/",stripslashes($module));
        $module = $first_line.$module.$last_line;
        fwrite($inF,$module);
        fclose($inF);
        $notice .= '<b>'.$enregistrementString.' '.$effectueString.' '.(isset($mod)?$pourString.' '.$mod:'').'</b>';
    }
} else {
    Header("Location: $redirect");Die();
}
