<?php #۞ #
//_ajaxsorter.php

include '_incdb.php';
//include '_incerror.php';// included in _incdb.php
include '_strings.php';

	if (isset($_COOKIE[$cookie_codename."admin"])) {
		$cookie = $_COOKIE[$cookie_codename."admin"];
		$read_cookie = explode("^", base64_decode($cookie));
		$admin_name = $read_cookie[0];
		$pass_word = $read_cookie[1];
		if (sql_nrows($tbladmin," WHERE adminutil='$admin_name' AND adminpass='$pass_word' AND adminstatut='Y' ") == 1) {
  		$logged_in = true;
		} else {
			$logged_in = false;
		}
	} else {
    Header("Location: $redirect");Die();
  }
	
if ($logged_in === true) {

  $action 			       	= $_POST['action'];
  $what 			       	  = $_POST['what'];
  $mod 			       	    = $_POST['mod'];
  $updateRecordsArray 	= $_POST['recordsArray'];

  if ($action == "updateRecordsListings") {

  	$listingCounter = 1;
  	foreach($updateRecordsArray as $recordIDValue) {
  		$query = "UPDATE ".${"tbl".$what}." SET {$what}sort = '$listingCounter' WHERE {$what}rid = '$recordIDValue' ".($mod=='gallery'?"AND {$what}img LIKE '%".$filedir.$mod."%' ":'');
  		mysql_query($query) or die('Error, insert query failed<br />'.$query);
  		$listingCounter++;
  	}

  //	echo '<pre>';
  //	print_r($updateRecordsArray);
  //	echo '</pre>';
  	echo '<div class="notice">If you refresh the page, you will see that records will stay just as you modified.<br />New order = '.implode(",",$updateRecordsArray).'</div>';
  }

  @mysql_free_results();
  @mysql_close($connection);

} else {
	Header("Location: $redirect");Die();
}
