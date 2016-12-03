<?php #۞ # VISITORS

if (stristr($_SERVER['PHP_SELF'],'_query_root.php')) {
  include '_security.php';
  Header("Location: $redirect");Die();
}
		$nRowsUser = sql_nrows($tbluser,"");
		$nRowsUsery = sql_nrows($tbluser," WHERE userstatut='Y' ");
		$nRowsUsern = sql_nrows($tbluser," WHERE userstatut='N' ");
/*
		$nRowsBiblio = sql_nrows($tblbiblio,"");
		$nRowsBiblioy = sql_nrows($tblbiblio," WHERE bibliostatut='Y' ");
		$nRowsBiblion = sql_nrows($tblbiblio," WHERE bibliostatut='N' ");
		$nRowsComment = sql_nrows($tblbibliocomment,"");
		$nRowsCommenty = sql_nrows($tblbibliocomment," WHERE bibliocommentstatut='Y' ");
		$nRowsCommentn = sql_nrows($tblbibliocomment," WHERE bibliocommentstatut='N' ");
		$nRowsInstitute = sql_nrows($tblinstitute,"");
		$nRowsInstitutey = sql_nrows($tblinstitute," WHERE institutestatut='Y' ");
		$nRowsInstituten = sql_nrows($tblinstitute," WHERE institutestatut='N' ");
*/
