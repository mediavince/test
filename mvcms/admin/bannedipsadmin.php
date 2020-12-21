<?php #۞ #
if (stristr($_SERVER['PHP_SELF'], basename(__FILE__))){include '_security.php';Header("Location: $redirect");Die();}

$this_is = 'bannedips';
$dbtable = $tblbannedips;

$sql_query = "";
$sql_orderq = " ORDER BY `ip` ASC ";
$return = "ip";
$count = sql_nrows($dbtable,$sql_query);

$content .= $admin_menu;

if (!isset($send)) {
  $content .= '<div class="selectHead">'.gen_form($lg,$x,$y).'<input name="send" type="submit" value="'.$envoyerString.'" /> | <input type="reset" value="Reset" /><br /> <br />';
	$read = mysqli_query($connection, " SELECT * FROM $dbtable $sql_query $sql_orderq ");
	$nRows = mysqli_num_rows($read);
  $content .= 'New : <input name="new" type="text" style="width: 70%" value="" /><hr />';
	for ($i=1;$i<=$count;$i++) {
		$iminus = $i-1;
		if	($iminus == '0')	$iminus = '1'	;
		$row = mysqli_fetch_array($read);
		if ($row["ip"] == "") {
			sql_del($dbtable,"WHERE id='".$row["id"]."' ");
		} else {
  $content .= '<input name="'.$row["id"].'" type="text" style="width: 70%" value="'.$row["ip"].'" /><br />';
		}
	}
  $content .= '<br /> <br /><input name="send" type="submit" value="'.$envoyerString.'" /> | <input type="reset" value="Reset" /></form></div>';
} else if ($send == $envoyerString) {
	$update_rapport = '';
	if ($_POST["new"] !== "") {
		if (preg_match("/^[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}\$/",$_POST["new"])) {
			$new_ip = html_encode(strip_tags(stripslashes($_POST["new"])));
			if	(sql_nrows($dbtable,"WHERE ip='".$_POST["new"]."' ") == 0)
			$insertquery = mysqli_query($connection, " INSERT INTO $dbtable	(`id`, `ip`)	VALUES	('', '$new_ip')	");
			if (!$insertquery) {
  $content .= '<p style="color: red;"><b>'.$_POST["new"].'</b> : <b>'.$error_request.' '.$ouString.' '.$dejaexistantString.'</b></p>';
			} else {
  $content .= '<b>'.$_POST["new"].'</b> : '.$enregistrementString.' '.$effectueString.' [i]<br />';
			}
		} else {
  $content .= '<p style="color: red;"><b>'.$_POST["new"].'</b> : <b>'.$error_inv.'</b></p>';
		}
	}
	$read = mysqli_query($connection, " SELECT * FROM $dbtable $sql_query $sql_orderq ");
	$nRows = mysqli_num_rows($read);
	for ($i=1;$i<=$count;$i++) {
		$row = mysqli_fetch_array($read);
		$row_id = $row["id"];
		$row_ip = $row["ip"];
		if (isset($_POST[$row_id])) {
			$setq = " SET ip='".$_POST[$row_id]."' ";
			$whereq = " WHERE id='$row_id' ";
			if ($_POST[$row_id] == "") {
				$del = sql_del($dbtable,$whereq,$return);
				$update_rapport .= '<b>'.$row_ip.'</b> : '.$enregistrementString.' '.$effaceString.' <br />';
			} else if (preg_match("/^[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}\$/",$_POST[$row_id])) {
				if ($_POST[$row_id] == $row_ip) {
					$update_rapport .= '';
				} else if (sql_nrows($dbtable,"WHERE ip='".$_POST[$row_id]."' ") > 0) {
  $content .= '<p style="color: red;"><b>'.$_POST[$row_id].'</b> : <b>'.$dejaexistantString.'</b></p>';
				} else {
					$update = sql_update($dbtable,$setq,$whereq,$return);
					$update_rapport .= '<b>'.$update[0].'</b> : '.$enregistrementString.' '.$effectueString.' <br />';
				}
			} else {
  $content .= '<p style="color: red;"><b>'.$_POST[$row_id].'</b> : <b>'.$error_inv.'</b></p>';
			}
		}
	}
	if ($update_rapport == '') {
  $content .= '<br /><p style="text-align: center"><font color="Green"><b>'.$enregistrementString.' '.$nonString.' '.$modifieString.'</b></font><br /> <br /><a href="?lg='.$lg.'&x='.$x.'&y='.$y.'">'.$retourString.' '.$verslisteString.' >> IP</a></p>';
	} else {
  $content .= '<br />'.$update_rapport.'<br /><p style="text-align: center"><font color="Green"><b>'.$enregistrementString.' '.$effectueString.'</b></font><br /> <br /><a href="?lg='.$lg.'&x='.$x.'&y='.$y.'">'.$retourString.' '.$verslisteString.' >> IP</a></p>';
	}
} else {
	Header("Location: $redirect");
}
