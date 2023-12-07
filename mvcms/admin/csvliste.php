<?php #۞ #

include '_incdb.php';
//include '_incerror.php';// included in _incdb.php
include '_strings.php';

	if (isset($_COOKIE[$cookie_codename."admin"])) {
		$cookie = $_COOKIE[$cookie_codename."admin"];
		$read_cookie = explode("^", base64_decode($cookie));
		$adminName = $read_cookie[0];
		$passWord = $read_cookie[1];
		if (sql_nrows($tbladmin," WHERE adminutil='$adminName' AND adminpass='$passWord' AND adminstatut='Y' ") == 1) {
  		    $logged_in = true;
		} else {
			$logged_in = false;
		}
	} else {
    Header("Location: $redirect");Die();
  }
	
if ($logged_in === true) {

	if (isset($_REQUEST['action'])) {
		if (($_REQUEST['action'] == "commentemail") || ($_REQUEST['action'] == "bibliocommentemail") || ($_REQUEST['action'] == "reunioncommentemail")) {
		  if ($_REQUEST['action'] == "bibliocommentemail")
		    $tblcomment = $tblbibliocomment;
		  if ($_REQUEST['action'] == "reunioncommentemail")
		    $tblcomment = $tblreunioncomment;
			$result=mysqli_query($connection, "SELECT * FROM $tblcomment WHERE commentstatut='Y' ORDER BY commentname ");
			if (mysqli_num_rows($result) == 0) {
				echo '0 '.$commentString.'<br />';
			} else {
				$commentemail = "";
				while($row=mysqli_fetch_array($result)) {
          if (($_REQUEST['action'] == "bibliocommentemail") || ($_REQUEST['action'] == "reunioncommentemail"))
					$commentemail .= $row['commentname'].';'.html_entity_decode((sql_getone($tbluser,"WHERE userutil='".$row['commentname']."' ","useremail")))."\n";
					else
					$commentemail .= $row['commentname'].';'.$row['commentemail'].';'.$row['commentwebsite'].';'.$row['commentorganization'].';'.$row['commentposition']."\n";
				}
				if ($commentemail !== '') {
					$Fnm = 'commentemail.csv';
				    header('Pragma: public');
				    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");                  // Date in the past   
				    header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT');
				    header('Cache-Control: no-store, no-cache, must-revalidate');     // HTTP/1.1
				    header('Cache-Control: pre-check=0, post-check=0, max-age=0');    // HTTP/1.1
				    header("Pragma: no-cache");
				    header("Expires: 0");
				    header('Content-Transfer-Encoding: none');
				    header('Content-Type: application/vnd.ms-excel;');                 // This should work for IE & Opera
				    header("Content-type: application/x-msexcel");                    // This should work for the rest
				    header('Content-Disposition: attachment; filename="'.$Fnm.'"');
					echo $commentemail;
				} else {
					Header("Location: $redirect");Die();
				}
			}
		} else if ($_REQUEST['action'] == "list") {
  		$action = $_REQUEST['action'];
  		if (in_array($_REQUEST['what'],$array_tables)) {
  		  $what = $_REQUEST['what'];
  		  $dbtable = ${"tbl".$what};
  		  $where = '';
  		  if (in_array($what,$array_interconnected_byid)) {
  		    foreach($array_interconnected_byid as $k)
  		      if (isset(${"connected_byid_".$what."_".$k}) && (${"connected_byid_".$what."_".$k} === true)) {
  		        $array_collected_tables[] = $k;
    		      $dbtable .= ",".${"tbl".$k};
    		      $where .= ($where==''?' WHERE ':' AND ').$what.'id='.$k.'id ';
    		    }
        }
        $sql = "SELECT * FROM $dbtable $where ";
        $read = mysqli_query($connection, $sql);
        $fields_array = sql_fields($dbtable,'array');
        $fields_list = '"'.implode('","',$fields_array)."\"\n\r";
  			if (mysqli_num_rows($read) == 0) {
  				echo $pasdeString.' '.${$what."String"}.'<br />';
  			} else {
          $list = '';// heading of cells added below
          while($row=mysqli_fetch_array($read)) {
            foreach($fields_array as $k) {
              if (substr($k,0,strlen($what)) == $what) {
                $field = substr($k,strlen($what));
              } else {
                $i = (isset($i)?$i++:0);
        		    if (substr($k,strlen($array_collected_tables[$i])) == 'id')
                $what = $array_collected_tables[$i];
                $field = substr($k,strlen($what));
              }
              if (in_array($field,$array_modules)) {
                $that_fields_array = sql_fields(${"tbl".$field},'array');
                $array_that_preferred_fields = array();
                foreach($array_preferred_fields as $apf)
                $array_that_preferred_fields[] = $field.$apf;
                $fetch_field = array_intersect($array_that_preferred_fields,$that_fields_array);
              }
              $list .= '"'.str_replace('"','""',(html_entity_decode((stristr($k,'gendre')?sql_stringit('gendre',$row[$k]):($row[$k]!=0&&in_array($field,$array_modules)&&isset($fetch_field[0])?sql_getone(${"tbl".$field},"WHERE ".$field."id='".$row[$k]."' ",$fetch_field[0]):$row[$k]))))).'",';//(strlen($row[$k])>1000?substr($row[$k],0,1000).'...':$row[$k])
            }
            $list .= '""'."\n\r";
          }
  				if ($list !== '') {
            $list = $fields_list.$list;
  					$Fnm = $what.'-'.$action.'_'.time('YMd').'.csv';
  				    header('Pragma: public');
  				    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");                  // Date in the past   
  				    header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT');
  				    header('Cache-Control: no-store, no-cache, must-revalidate');     // HTTP/1.1
  				    header('Cache-Control: pre-check=0, post-check=0, max-age=0');    // HTTP/1.1
  				    header("Pragma: no-cache");
  				    header("Expires: 0");
  				    header('Content-Transfer-Encoding: none');
  				    header('Content-Type: application/vnd.ms-excel;');                 // This should work for IE & Opera
  				    header("Content-type: application/x-msexcel");                    // This should work for the rest
  				    header('Content-Disposition: attachment; filename="'.$Fnm.'"');
  					echo $list;
  				} else {
  					Header("Location: $redirect");Die();
  				}
  			}
  		} else {
  			Header("Location: $redirect");Die();
  		}
		} else {
			Header("Location: $redirect");Die();
		}
	} else {
		Header("Location: $redirect");Die();
	}
} else {
	Header("Location: $redirect");Die();
}
