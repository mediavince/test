<?php #۞ #
if (stristr($_SERVER['PHP_SELF'], basename(__FILE__))){include '_security.php';Header("Location: $redirect");Die();}

if ($logged_in === true) {
  $new_name = $codename.(stristr($_SERVER["HTTP_HOST"],"mediavince.com")?'_mv':(stristr($_SERVER["HTTP_HOST"],"localhost")?'_lh':''))."_".date('YmdHis').".sql";
  $do = "mysqldump ".($dbhost==''?'':"-h$dbhost")." -u$dbuser -p$dbpass $dbname > $getcwd$up$safedir$new_name"; //_MySQL4.0.sql InnoDB
  $content .= exec(escapeshellcmd($do),$output,$result).($result=='0'?'<div style="background-color: #CF9;border: 1px solid green;padding: 5px;margin: 5px;">'.$class_conjugaison->plural($effectueString,'F','1').' <a href="'.$mainurl.$urladmin.'_download.php?sql='.base64_encode($new_name).'" target="_blank">'.$telechargerString.' '.$fichierString.'!</a></div>':'<div style="background-color: #FA5;width: 80%;border: 1px solid red;padding: 5px;margin: 5px;"> '.$error_inv.' copier et envoyer au webmaster ce qui suit :<br />=> '.$result.' == '.$do.'</div>');
} else {
	Header("Location: $redirect");Die();
}
