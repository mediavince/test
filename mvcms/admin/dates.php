<?PHP

/* SOURCE FILE: date.js */

// HISTORY
// ------------------------------------------------------------------
// May 17, 2003: Fixed bug in parseDate() for dates <1970
// March 11, 2003: Added parseDate() function
// March 11, 2003: Added "NNN" formatting option. Doesn't match up
//                 perfectly with SimpleDateFormat formats, but 
//                 backwards-compatability was required.

// ------------------------------------------------------------------
// These functions use the same 'format' strings as the 
// java.text.SimpleDateFormat class, with minor exceptions.
// The format string consists of the following abbreviations:
// 
// Field        | Full Form          | Short Form
// -------------+--------------------+-----------------------
// Year         | yyyy (4 digits)    | yy (2 digits), y (2 or 4 digits)
// Month        | MMM (name or abbr.)| MM (2 digits), M (1 or 2 digits)
//              | NNN (abbr.)        |
// Day of Month | dd (2 digits)      | d (1 or 2 digits)
// Day of Week  | EE (name)          | E (abbr)
// Hour (1-12)  | hh (2 digits)      | h (1 or 2 digits)
// Hour (0-23)  | HH (2 digits)      | H (1 or 2 digits)
// Hour (0-11)  | KK (2 digits)      | K (1 or 2 digits)
// Hour (1-24)  | kk (2 digits)      | k (1 or 2 digits)
// Minute       | mm (2 digits)      | m (1 or 2 digits)
// Second       | ss (2 digits)      | s (1 or 2 digits)
// AM/PM        | a                  |
//
// NOTE THE DIFFERENCE BETWEEN MM and mm! Month=MM, not mm!
// Examples:
//  "MMM d, y" matches: January 01, 2000
//                      Dec 1, 1900
//                      Nov 20, 00
//  "M/d/yy"   matches: 01/20/00
//                      9/2/00
//  "MMM dd, yyyy hh:mm:ssa" matches: "January 01, 2000 12:30:45AM"
// ------------------------------------------------------------------
if ($_GET['lg']) {
  $lg = stripslashes($_GET['lg']);
  if (!preg_match("/^[a-z]{2}\$/",$lg)){
    include '_security.php';
    Header("Location: $redirect");Die();
  }

  include '_incdb.php';
  include '_strings.php';

  $getdates = true;
  $write_js = "";
  
  foreach(array('ajourdhuiString'=>$aujourdhuiString,'array_months'=>$array_months,'array_days'=>$array_days) as $k=>$v) {
    if (isset($v)) {
      switch($k) {
        case 'ajourdhuiString':
          $write_js .= "var today_txt = '".ucfirst($v)."';$CRLF";
          break;
        case 'array_months':
          $am_names = "";
          $am_abbrvs = "";
          foreach($v as $s){
            $am_names .= ($am_names==''?'':",")."'".ucfirst($s)."'";
            $abbrv = substr(ucfirst(space2underscore($s)),0,3);
            if (stristr($am_abbrvs,$abbrv) && isset($s[4]))
            $abbrv = substr($abbrv,0,2).$s[4];
            $am_abbrvs .= ($am_abbrvs==''?'':",")."'".$abbrv."'";
          }
          $am_names = "var MONTH_NAMES=new Array(".$am_names.");$CRLF";
          $am_abbrvs = "var MONTH_ABBRVS=new Array(".$am_abbrvs.");$CRLF";
          $write_js .= $am_names.$am_abbrvs;
          break;
        case 'array_days':
          $ad_names = "";
          $ad_abbrvs = "";
          $array_ad_headers = array();
          foreach(array_reverse($v) as $s)
          $array_ad_headers[] = strtoupper($s[0]).(in_array(strtoupper($s[0]),$array_ad_headers)?space2underscore($s[1]):'');
          $array_ad_headers = array_reverse($array_ad_headers);
          foreach($v as $s){
            $ad_names .= ($ad_names==''?'':",")."'".ucfirst($s)."'";
            $abbrv = substr(ucfirst(space2underscore($s)),0,3);
            if (stristr($ad_abbrvs,$abbrv) && isset($s[4]))
            $abbrv = substr($abbrv,0,2).$s[4];
            $ad_abbrvs .= ($ad_abbrvs==''?'':",")."'".$abbrv."'";
          }
          $ad_names = "var DAY_NAMES=new Array(".$ad_names.");$CRLF";
          $ad_abbrvs = "var DAY_ABBRVS=new Array(".$ad_abbrvs.");$CRLF";
          $ad_headers = "var DAY_HEADRS=new Array('".implode("','",$array_ad_headers)."');$CRLF";
          $write_js .= $ad_names.$ad_abbrvs.$ad_headers;
          break;
      }
    } else {
      $getdates = false;
      break;
    }
  }
  if ($getdates===true) {
  
    echo $write_js;
  
  } else {
  
    if ($lg == 'fr') {
  echo "
  var today_txt = 'Aujourd`hui';
  var MONTH_NAMES=new Array(
    'Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'
    );//,
  var MONTH_ABBRVS=new Array(
    'Jan','Fev','Mar','Avr','Mai','Jui','Jul','Aou','Sep','Oct','Nov','Dec'
    );
  var DAY_NAMES=new Array(
    'Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'
    );//,
  var DAY_ABBRVS=new Array(
    'Dim','Lun','Mar','Mer','Jeu','Ven','Sam'
    );
  var DAY_HEADRS=new Array(
    'D','L','M','Me','J','V','S');
  ";
    } else if ($lg == 'it') {
  echo "
  var today_txt = 'Oggi';
  var MONTH_NAMES=new Array(
    'Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'
    );//,
  var MONTH_ABBRVS=new Array(
    'Gen','Feb','Mar','Apr','Mag','Giu','Lug','Ago','Set','Ott','Nov','Dic');
  var DAY_NAMES=new Array(
    'Domenica','Lunedì','Martedì','Mercoledì','Giovedì','Venerdì','Sabato'
    );//,
  var DAY_ABBRVS=new Array(
    'Dom','Lun','Mar','Mer','Gio','Ven','Sab'
    );
  var DAY_HEADRS=new Array(
    'D','L','M','Me','J','V','S');
  ";
    } else {
  echo "
  var today_txt = 'Today';
  var MONTH_NAMES=new Array(
    'January','February','March','April','May','June','July','August','September','October','November','December'
    );//,
  var MONTH_ABBRVS=new Array(
    'Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'
    );
  var DAY_NAMES=new Array(
    'Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'
    );//,
  var DAY_ABBRVS=new Array(
    'Sun','Mon','Tue','Wed','Thu','Fri','Sat'
    );
  var DAY_HEADRS=new Array(
    'Su','M','Tu','W','T','F','S');
  ";
    }
  }
}
?>