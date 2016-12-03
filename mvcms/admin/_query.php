<?php #۞ # ADMIN
if (stristr($_SERVER['PHP_SELF'],'_query.php')) {
  include '_security.php';
  Header("Location: $redirect");Die();
}

if ($logged_in === true) { // ###### else include ../query.php ######

########################################## A L W A Y S	A V A I L A B L E ###########################################
		$nRowsUser = sql_nrows($tbluser,"");
		$nRowsUsery = sql_nrows($tbluser," WHERE userstatut='Y' ");
		$nRowsUsern = sql_nrows($tbluser," WHERE userstatut='N' ");
		/*
		$nRowsMembre = sql_nrows($tblmembre,"");
		$nRowsMembrey = sql_nrows($tblmembre," WHERE membrestatut='Y' ");
		$nRowsMembren = sql_nrows($tblmembre," WHERE membrestatut='N' ");
    */
  if ($tinyMCE === false) {
  // WHEN ADDING A NEW HELP USE ACCENTS AND ` INSTEAD OF ' ALSO NO LINE BREAK BUT \n\r AND NO SPECIAL CHARACTERS [a-zA-Z]
  	$helpEdit = sql_get("$tblhelp"," WHERE helppg='0' AND helplang='$lg' ","helptitle, helpentry");
  	$helpEdit = $helpEdit[0].$helpEdit[1];
  	$helpUploadimg = sql_get("$tblhelp"," WHERE helppg='1' AND helplang='$lg' ","helptitle, helpentry");
  	$helpUploadimg = $helpUploadimg[0].$helpUploadimg[1];
  	$helpUploaddoc = sql_get("$tblhelp"," WHERE helppg='2' AND helplang='$lg' ","helptitle, helpentry");
  	$helpUploaddoc = $helpUploaddoc[0].$helpUploaddoc[1];

  	$nRowsHelp = sql_nrows($tblhelp,"");
  }
########################################## S T A R T	C O N D I T I O N ###########################################

/* // TEMPLATE
//	if	($x == '') {
//			$nRowsActivite = sql_nrows($tblactivite,"");
//			$nRowsActivitey = sql_nrows($tblactivite," WHERE activitestatut='Y' ");
//			$nRowsActiviten = sql_nrows($tblactivite," WHERE activitestatut='N' ");
//		}
*/
/*
  if	(($y == '4') || ($y == 'newsletter')) {
		$nRowsNewsletter = sql_nrows($tblnewsletter,"");
		$nRowsNewslettery = sql_nrows($tblnewsletter," WHERE newsletterstatut='Y' ");
		$nRowsNewslettern = sql_nrows($tblnewsletter," WHERE newsletterstatut='N' ");
	}
*/
	/*
  if	($y == '7') {
		$nRowsTopic = sql_nrows($tbltopic,"");
		$nRowsTopicy = sql_nrows($tbltopic," WHERE topicstatut='Y' ");
		$nRowsTopicn = sql_nrows($tbltopic," WHERE topicstatut='N' ");
		$nRowsComment = sql_nrows($tblcomment,"");
		$nRowsCommenty = sql_nrows($tblcomment," WHERE commentstatut='Y' ");
		$nRowsCommentn = sql_nrows($tblcomment," WHERE commentstatut='N' ");
	}
	*/
	/*
  if	($y == '7') {
		$nRowsBiblio = sql_nrows($tblbiblio,"");
		$nRowsBiblioy = sql_nrows($tblbiblio," WHERE bibliostatut='Y' ");
		$nRowsBiblion = sql_nrows($tblbiblio," WHERE bibliostatut='N' ");
		$nRowsComment = sql_nrows($tblbibliocomment,"");
		$nRowsCommenty = sql_nrows($tblbibliocomment," WHERE commentstatut='Y' ");
		$nRowsCommentn = sql_nrows($tblbibliocomment," WHERE commentstatut='N' ");
	}

  if	($y == '8') {
		$nRowsReunion = sql_nrows($tblreunion,"");
		$nRowsReuniony = sql_nrows($tblreunion," WHERE reunionstatut='Y' ");
		$nRowsReunionn = sql_nrows($tblreunion," WHERE reunionstatut='N' ");
		$nRowsComment = sql_nrows($tblreunioncomment,"");
		$nRowsCommenty = sql_nrows($tblreunioncomment," WHERE commentstatut='Y' ");
		$nRowsCommentn = sql_nrows($tblreunioncomment," WHERE commentstatut='N' ");
	}

  if	($y == '9') {
		$nRowsInstitute = sql_nrows($tblinstitute,"");
		$nRowsInstitutey = sql_nrows($tblinstitute," WHERE institutestatut='Y' ");
		$nRowsInstituten = sql_nrows($tblinstitute," WHERE institutestatut='N' ");
	}
	*/
	/*
  // EXTRA ADMIN OPTIONS
  if	($x == 'z') {
		$nRowsAdmin = sql_nrows($tbladmin,"");
		$nRowsAdminy = sql_nrows($tbladmin," WHERE adminstatut='Y' ");
		$nRowsAdminn = sql_nrows($tbladmin," WHERE adminstatut='N' ");
		*/
		/*
		$nRowsCat = sql_nrows($tblcat,"");
		$nRowsCaty = sql_nrows($tblcat," WHERE catstatut='Y'");
		$nRowsCatn = sql_nrows($tblcat," WHERE catstatut='N'");
	}
		*/

} else {
	include $getcwd.$up.$urladmin.'_query_root.php';
}
