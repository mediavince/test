<?php #۞ #
if (stristr($_SERVER['PHP_SELF'], basename(__FILE__))){include '_security.php';Header("Location: $redirect");Die();}

// @see _view.php index_generate()

function html_index($this_lg) {
  global $trace, $mainurl, $x, $tblcont, $tblstring, $slogan, $client, $charset_iso, $default_lg_pg_htm, $contTitle, $contEntry, $copyrightnoticeString, $menu_pagine, $rootwindowmenu, $meta_desc, $meta_keyw, $urladmin, $stylesheet, $javascript, $rootwindowmenu, $cologo, $topicslist;
  $lg = $this_lg;
  $sortirString = sql_stringit('general','sortir');
  $copyrightnoticeString = sql_stringit('general','copyrightnotice');
	$this_x = sql_getone($tblcont, " WHERE conttitle='$contTitle' AND contlang='$this_lg' ", "contpg");
	$base_x = $this_x[0];
//ok for 1 digit//	$contEntry = \ereg_replace('\?lg=([^]]*)&amp;x=([0-9]]*)\"',substr(sql_getone($tblcont," WHERE contpg='\\2' AND contlang='$this_lg' ","conturl"),0,-4).'.htm?hello"',$contEntry);//\?lg=([^]]*)\&
	$contEntry = \ereg_replace('\?lg=([^]]*)&amp;x=([0-9]]*)\"',substr(sql_getone($tblcont," WHERE contpg='\\2' AND contlang='$this_lg' ","conturl"),0,-4).'.htm"',$contEntry);
	$html_index = '
<!DOCTYPE html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="'.$this_lg.'"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="'.$this_lg.'"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="'.$this_lg.'"> <![endif]-->
<!--[if IE 9]>    <html class="no-js lt-ie10" lang="'.$this_lg.'"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js ie" lang="'.$this_lg.'"> <!--<![endif]-->
<head>
	<meta http-equiv="Content-Type" content="text/html; charset='.$charset_iso.'" />
	<meta name="author" content="Media Vince :: Your content &#064; the front row !" />
  <meta name="copyright" content="'.date('Y').', www.mediavince.com" />
  <title>'.$contTitle.'_'.$this_lg.' :: '.$slogan.'</title>
	<meta name="description" content="'.$meta_desc.'" />
	<meta name="keywords" content="'.$meta_keyw.'" />
	<meta name="ROBOTS" content="ALL" />
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <!--[if lt IE 9]><script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
  <script type="text/javascript" src="'.$mainurl.'-menu_pagine_'.$this_lg.'.js"></script>
  <script type="text/javascript" src="'.$mainurl.'-scroller_'.$this_lg.'.js"></script>
  '.$stylesheet.'
</head>
<body>
<script type="text/javascript"><!-- //
var thispg = \''.$this_x.'\';
var	wherearewe = \'\';
if (thispg > 10) {
	if (thispg.length == 4) {
		wherearewe = \'&#092; [<\'+\'a h\'+\'ref="\'+eval(\'l\'+thispg.substr(0,3))+\'" target="_self">\'+eval(\'m\'+thispg.substr(0,3))+\'</a>] &#092; [<\'+\'a h\'+\'ref="\'+eval(\'l\'+thispg.substr(0,2))+\'" target="_self">\'+eval(\'m\'+thispg.substr(0,2))+\'</a>] &#092; [<\'+\'a h\'+\'ref="\'+eval(\'l\'+thispg.substr(0,1))+\'" target="_self">\'+eval(\'m\'+thispg.substr(0,1))+\'</a>]\';
	} else if (thispg.length == 3) {
		wherearewe = \'&#092; [<\'+\'a h\'+\'ref="\'+eval(\'l\'+thispg.substr(0,2))+\'" target="_self">\'+eval(\'m\'+thispg.substr(0,2))+\'</a>] &#092; [<\'+\'a h\'+\'ref="\'+eval(\'l\'+thispg.substr(0,1))+\'" target="_self">\'+eval(\'m\'+thispg.substr(0,1))+\'</a>]\';
	} else if (thispg.length == 2) {
		wherearewe = \'&#092; [<\'+\'a h\'+\'ref="\'+eval(\'l\'+thispg.substr(0,1))+\'" target="_self">\'+eval(\'m\'+thispg.substr(0,1))+\'</a>]\';
	} else {
	}
}
';
if	(($menu_pagine === true) && ($rootwindowmenu !== ''))	
	$html_index .= '
mmLoadMenus(\''.$mainurl.'\');//whatdaheck
';
	$html_index .= '
// --></script>
';
	$html_index .= '
<div id="top">
<script type="text/javascript">
document.write(toplinks);
</script>
</div>
<div id="main">
  <header>
	<div id="header">
		<div id="toplogo">
      <div id="diaporama">
        <div class="hautfoto"></div>
        <div class="fotop2">
          <div class="gauchefoto"></div>
          <div class="diapo">
            <div id="flash">
        			<a href="https://www.adobe.com/go/getflashplayer" target="_blank">
        				<img src="'.$mainurl.'images/diaporama.gif" width="106" height="142" alt="Get Adobe Flash player" border="0" />
        			</a>
            </div>
          </div>
        </div>
        <div class="droitefoto"></div>
      </div>
      <div class="banner"><a href="'.$mainurl.'"><img src="'.$mainurl.'images/spacer.gif" width="600" height="80" border="0" alt="'.$slogan.'" /></a></div>
    </div>
		<h1>'.$contTitle.'</h1>
	</div>
  </header>
  <nav>
	<div id="navcontainer">
		<div class="lang">
			'.gen_lang().'
		</div>
<script type="text/javascript">
document.write(menuhori);
</script>
	</div>
  </nav>
  <section>
	<div id="baseone">
	<div id="bgleft">
  	<div id="secondnav">
      <div class="midfoto">
        <div class="gauchebasfoto"></div>
        <div class="centermidfoto"></div>
        <div class="droitebasfoto"></div>
      </div>
      <div class="basfoto"></div>
      <div id="lpane">
        <div id="upmclogo"></div>
    		<div id="sidenav">
<script type="text/javascript">
document.write(menuleft);
</script>
    		</div>';
if ($topicslist)
  $html_index .= '
		    <div id="topics">
<script type="text/javascript">
document.write(topicslist);
</script>
        </div>';
if ($base_x == '1')
  $html_index .= '
		    <br />
    		<div id="leftbox">
<script type="text/javascript">
document.write(leftlinks);
</script>
    		</div>';
  $html_index .= '
  		</div>
  	</div>
  	<div id="content">
  		<h2 class="title">'.$contTitle.'<div>
<script type="text/javascript">
document.write(wherearewe);
</script>&nbsp;
    </div></h2>';
  $html_index .= $contEntry.'
  	</div>
	</div>
	</div>
	<div class="clear"></div>
  </section>
';
  if	(isset($trace))	
  $html_index .= "<br />Trace debugger<br />$trace"	;
  $html_index .= '
  <footer>
	<div id="footer">
		<!-- webdeveloper credit -->
	  	'.$copyrightnoticeString.'
		<!-- Webdevelopment developed by <a href="https://www.mediavince.com" target="_blank">www.mediavince.com</a> - &copy; '.date('Y').' -->';
  if	(!stristr($_SERVER['HTTP_HOST'], "localhost"))	
  $html_index .= '<img src="https://www.mediavince.com/clients/'.$client.'.gif" width="1" height="1" hspace="0" vspace="0" border="0" '
      . 'title="www.mediavince.com :: Your content &#064; the front row !" alt="www.mediavince.com :: Your content &#064; the front row !" />';
  $html_index .= '
	</div>
  <footer>
</div>
</body>
</html>
';

return stripslashes($html_index);
}
