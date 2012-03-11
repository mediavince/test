<?PHP if (stristr($_SERVER['PHP_SELF'],'_mod_slider.php')) {include '_security.php';Header("Location: $redirect");Die();}/**/
//$this_is = 'slider';
$dbtable = ${"tblslider"};
$_mod_slider = '';

if (!isset($clickformoreinfoString)) $clickformoreinfoString = 'Click for more information!';

$stylesheet .= '<link rel="stylesheet" type="text/css" href="'.$mainurl.'lib/templates/ebri/slider_index.css" /><style type="text/css">.admin_menu{float:none;}#slider_index{height:100%;}</style>';
if (!stristr($javascript,'jquery.js')&&!stristr($javascript,'jquery.min.js'))
$javascript .= "<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js'></script>";
if (!stristr($javascript,'jquery.cycle.all.2.73.js'))
$javascript .= "<script type='text/javascript' src='http://cloud.github.com/downloads/malsup/cycle/jquery.cycle.all.2.73.js'>/*lib/jquery_cycle.js http://cloud.github.com/downloads/malsup/cycle/jquery.cycle.all.2.73.js*/</script>";

if (sql_fields($dbtable,'count')==0) {
  $create_slider = @mysql_query("DROP TABLE IF EXISTS `_slider`;");
  $create_slider = @mysql_query("CREATE TABLE `_slider` (
                  `sliderid` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  `sliderstatut` enum('N','Y') NOT NULL DEFAULT 'N',
                  `sliderdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                  `sliderlang` text NOT NULL,
                  `sliderrid` int(10) unsigned NOT NULL,
                  `slidertype` int(11) unsigned NOT NULL,
                  `slidertitle` text NOT NULL,
                  `sliderentry` longtext NOT NULL,
                  `sliderurl` text NOT NULL,
                  `sliderimg` text NOT NULL,
                  `slidersort` int(6) unsigned zerofill NOT NULL default '999999',
                  PRIMARY KEY (`sliderid`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
                ");
  if ($create_slider) $_mod_slider = "created<br />";
  else $_mod_slider = "not created<br />";
}

//if ($y!=='slider') {
  $jq_additional_instructions = '';

  $slider_sql = @mysql_query("SELECT * FROM $dbtable WHERE sliderstatut='Y' AND sliderlang='$lg' ORDER BY slidersort,sliderrid ");// LIMIT 1
  if ($slider_sql)
  while($row=@mysql_fetch_array($slider_sql)) {
    $slider_title = ($row['slidertitle']==$codename?'':preg_replace('/ /',"<br />",ucwords($row['slidertitle']),1));
    $_mod_slider .= ''.(!stristr($_SERVER['PHP_SELF'],$urladmin)?'<div id="feature':'<div id="recordsArray').'_'.$row['sliderrid'].'" class="campaign" style="background:url(\''.$up.img_size($row['sliderimg'],"ori").'\') no-repeat scroll center top transparent;" title="'.$clickformoreinfoString.'">
        <div id="feature_title'.'_'.$row['sliderrid'].'" class="campaign_content"><div class="campaign_title">'.$slider_title.'</div></div>
        <div id="feature_entry'.'_'.$row['sliderrid'].'" class="campaign_entry">'.$row['sliderentry'].'</div>
      </div>';//'.$row['slidertitle'].'<br />'.$row['sliderentry'].'
      $jq_additional_instructions .= "
$('#feature_title_{$row['sliderrid']}').click(function() {
  $('#slider_index').cycle('pause');
  $('#feature_entry_{$row['sliderrid']}').show('slow', function() {
    $('#feature_entry_{$row['sliderrid']}').click(function() {
      $('#feature_entry_{$row['sliderrid']}').hide('slow', function() {
        $('#slider_index').cycle('resume');
      });
    });
  });
});
      ";
  }
  if ($_mod_slider !== '')
  $_mod_slider = '
  <div id="ajax_response"></div>
  <div id="slider_index">'.$_mod_slider.'
  </div>';
  else
  $_mod_slider = 'void<br />';
  
  if (isset($slider_control))
  $_mod_slider .= '<div id="slider_controls">
    <ul>
      <li><a href="#" id="slider_prev">Previous</a></li>
      <li><a href="#" id="slider_next">Next</a></li>
    </ul>
  </div>';

	if (stristr($_SERVER['PHP_SELF'],$urladmin)&&($logged_in===true)) {
		$javascript .= '
			<script type="text/javascript" src="'.$up.'lib/vendors/jquery/jquery.ui.core.js"></script>
			<script type="text/javascript" src="'.$up.'lib/vendors/jquery/jquery.ui.widget.js"></script>
			<script type="text/javascript" src="'.$up.'lib/vendors/jquery/jquery.ui.mouse.js"></script>
			<script type="text/javascript" src="'.$up.'lib/vendors/jquery/jquery.ui.sortable.js"></script>
			<style type="text/css">
			#sortable ul{list-style-type:none;margin:0;padding:0;}
			#sortable li{float: left;}
			</style>
			<script type="text/javascript">
		  // $(function() { // to avoid lightbox issue
		  jQuery.noConflict();
		  // Put all your code in your document ready area
		  jQuery(document).ready(function($){
		    // Do jQuery stuff using $
				$("#slider_index").sortable({ opacity: 0.6, cursor: "move", update: function() {
		  			var order = $(this).sortable("serialize") + "&action=updateRecordsListings&what=slider";
		  			$.post("'.$up.$urladmin.'_ajaxsorter.php", order, function(theResponse){
		  				$("#ajax_response").html(theResponse);
		  			});
		  		}
				});
				$("#slider_index").disableSelection();
			});
			</script>
		';
	} else {
		$javascript .= "
		<script type='text/javascript'>
		  $(document).ready( function($) {
		    //blindX , blindY , blindZ , cover , curtainX , curtainY , fade , fadeZoom , growX , growY , none , scrollUp , scrollDown , scrollLeft , scrollRight , scrollHorz , scrollVert , shuffle , slideX , slideY , toss , turnUp , turnDown , turnLeft , turnRight , uncover , wipe , zoom
		    $('#slider_index').cycle({
		      fx: 'scrollRight',
		      speed: 1000,
		      pause: 0,
		      timeout: 7000,
		      delay: 1000,
		      prev: '#slider_next',
		      next: '#slider_prev'
		    });
        ".$jq_additional_instructions."
		  });
		</script>
		";
	}
//} else {
//  include $getcwd.$up.$urladmin.'itemadmin.php';
//}

//$_mod_content = $_mod_slider;
/**/
?>