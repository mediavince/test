<?php #۞ #
if (stristr($_SERVER['PHP_SELF'], basename(__FILE__))){include '_security.php';Header("Location: $redirect");Die();}

function index_generate()
{
	global $trace,$getcwd,$up,$default_lg,$x,$lg,$q,$page_title,$title,$content,$desc,$keyw,
		$charset_iso,$meta_desc,$meta_keyw,$slogan,$confinfoString,$logged_in,$mainurl,$urladmin,
		$safedir,$urlintro,$filedir,$sortirString,$send,$tinyMCE,$stylesheet,$javascript,
		$rootwindowmenu,$local,$toplinksentry,$topicslist,$cologo,$menuhori,$menuleft,$login,
		$leftlinksentry,$notice,$error,$copyrightnoticeString,$client,$base_x,$menu_pagine,
		$tblcont,$_SERVER,$edit_text,$tblcont,$tiny_compression,$array_lang,$nRowsUsery,$this_priv,
		$font_family,$font_size,$extra_meta,$google_analytics,$this_annee,$_template,$i_elm,
		$default_desc_keyw,$nlid,$index_style,$local_uri,$admin_viewing,$lightbox,$lightbox_css,
		$lightbox_js,$filter_lightbox,$flash_array,$_google_analytics_async,$very_basic_tinyMCE,
		$rssdir,$codename,$x1subok,$lightbox_initialize,$add_tmce_settings,$jquery_sort_included,
		$disable_lightbox_w_jquery_sort,$jquery_sort_js,$jquery_captcha_included,$jquery_captcha_js;
  
################################## TEMPLATE VARS
	if (isset($nlid)) $index_style = '';
	$slogan = $confinfoString;
	if (!is_array($default_desc_keyw)) // in menu and menu_root after x?
	$default_desc_keyw = sql_get(
		$tblcont,
		"WHERE contpg='$x' AND contlang='$default_lg' ",
		"contmetadesc,contmetakeyw"
	);
	if ($default_desc_keyw[0] == '.') {
		$default_desc_keyw[0] = '';
		$default_desc_keyw[1] = '';
	}
	$desc = ($desc==""?$meta_desc:
						(($lg!=$default_lg)&&($desc==$default_desc_keyw[0])?$meta_desc:$desc));
	$keyw = ($keyw==""?$meta_keyw:
						(($lg!=$default_lg)&&($keyw==$default_desc_keyw[1])?$meta_keyw:$keyw));
  // see below head for _template inclusion
	if (($jquery_sort_included === true) && ($disable_lightbox_w_jquery_sort === true)) {
	} else {
		if (($lightbox === true) || ($filter_lightbox === true)) {
			$stylesheet .= $lightbox_css;
			$javascript .= $lightbox_js."
<script type=\"text/javascript\">
if (typeof(Event.observe) !== undefined)
Event.observe(window,'load',function(){Lightbox.initialize({"
			.($lightbox_initialize==''?'':$lightbox_initialize.",")
			.($lg&&$lg!='en'?"strings:lbStrings".strtoupper($lg):'')
			."});});</script>";
		}
	}
	if ($jquery_sort_included === true)
	$javascript .= $jquery_sort_js;
	if ($jquery_captcha_included === true)
	$javascript .= $jquery_captcha_js;
// <!DOCTYPE html
//	 PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
//	 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
//<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.$lg.'" lang="'.$lg.'">
	$html_view =  '<!DOCTYPE html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="'.$lg.'"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="'.$lg.'"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="'.$lg.'"> <![endif]-->
<!--[if IE 9]>    <html class="no-js lt-ie10" lang="'.$lg.'"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js ie" lang="'.$lg.'"> <!--<![endif]-->
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta http-equiv="Content-Type" content="text/html; charset='.$charset_iso.'" />
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
<meta name="author" content="Media Vince :: Your content &#064; the front row !" />
<meta name="copyright" content="www.mediavince.com, '.$this_annee.'" />
	'.$extra_meta.'
<meta name="description" content="'.$desc.'" />
<meta name="keywords" content="'.$keyw.'" />
<meta name="ROBOTS" content="ALL" />
<!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->';
	if (isset($rssdir))
	$html_view .= '
<link rel="alternate" type="application/rss+xml" title="RSS '
				.strtoupper($codename).'" href="'
				.$mainurl.$rssdir.($lg==$default_lg?'':"?lg=$lg").'" />';
	$html_view .= '
<title>'.(in_array($page_title,array('.',''))?$title:strip_tags($page_title))
	.(isset($array_lang[1])?'_'.$lg:'').' :: '.$slogan.'</title>
<base href="'.$mainurl.(stristr($_SERVER['PHP_SELF'],$urladmin)&&($logged_in===true)?$urladmin:'')
	.'" />'.((stristr($_SERVER['PHP_SELF'],$urladmin) || ($edit_text === true))
			&& ($tinyMCE === true) && ($logged_in === true)
			&& (!isset($send) || (isset($send) 
				&& (($send == 'new') || ($send == 'edit') || ($send == 'editemintro')))
	)?'
<link href="'.$mainurl.$urladmin.'css/screen.css" rel="stylesheet" type="text/css" />
<link href="'.$mainurl.$urladmin.'css/syntax.css" rel="stylesheet" type="text/css" />':'');
	if ($x=='z') $title = '<a href="?lg='.$lg.'&amp;x=z&amp;y=1">'.$title.'</a>';
	if (stristr($_SERVER['PHP_SELF'],$urladmin)
	&& !stristr($_SERVER['HTTP_HOST'],"localhost")
	&& ($logged_in === true)) {
		$html_view .=  '
<meta http-equiv="Refresh" content="901;URL='.$mainurl.$urladmin.'?login='.$sortirString.'" />';
	}

	$html_view .= $stylesheet;

// following needs to be in body otherwise breaks IE6
// 
//$html_view .= $stylesheet.'
//	<style>
//	body,body#tinymce.mceContentBody{font-family:'.$font_family.';font-size:'.$font_size.'px;}'
//	.($x==1?$index_style:'').'
//	</style>
//</head>
//<body>';// id="main_body"

	// flash array ['attributes.name','swf','swf.width','swf.height','swf.ver']
	if (isset($flash_array)) {
		$html_view .= '
<script type="text/javascript">
var flashvars = {};
//	flashvars.l = "k.jpg|z.jpg"; // var generated randomly or inside flash directly??
var params = {};
params.loop = "true";
params.menu = "false";
params.scale = "noborder";
params.salign = "tl";
params.allowscriptaccess = "sameDomain";
var attributes = {};
attributes.id = "d27cdb6e-ae6d-11cf-96b8-444553540000";
attributes.name = "'.$flash_array['attributes.name'].'";
swfobject.embedSWF("'.$mainurl.'images/'.$flash_array['swf'].'", "flash", "'
.$flash_array['swf.width'].'", "'.$flash_array['swf.height'].'", "'.$flash_array['swf.ver']
.'.0.0", "http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version='
.$flash_array['swf.ver'].',0,0,0", flashvars, params, attributes);
</script>';
	}

	if ($menu_pagine === true)
	$html_view .=  $javascript.'
<script type="text/javascript"><!-- //
var thispg = \''.$x.'\';
var	wherearewe = \'\';
if (thispg > '.((isset($x1subok)&&$x1subok===true)&&$x>10?'10':'20').') {
	if (thispg.length == 4) {
		wherearewe = \'&#092; [<\'+\'a h\'+\'ref="\'+eval(\'l\'+thispg.substr(0,3))
			+\'" target="_self">\'+eval(\'m\'+thispg.substr(0,3))+\'</a>] &#092; [<\'+\'a h\'
			+\'ref="\'+eval(\'l\'+thispg.substr(0,2))+\'" target="_self">\'
			+eval(\'m\'+thispg.substr(0,2))+\'</a>] &#092; [<\'+\'a h\'+\'ref="\'
			+eval(\'l\'+thispg.substr(0,1))+\'" target="_self">\'+eval(\'m\'
			+thispg.substr(0,1))+\'</a>]\';
	} else if (thispg.length == 3) {
		wherearewe = \'&#092; [<\'+\'a h\'+\'ref="\'+eval(\'l\'+thispg.substr(0,2))
			+\'" target="_self">\'+eval(\'m\'+thispg.substr(0,2))+\'</a>] &#092; [<\'+\'a h\'
			+\'ref="\'+eval(\'l\'+thispg.substr(0,1))+\'" target="_self">\'
			+eval(\'m\'+thispg.substr(0,1))+\'</a>]\';
	} else if (thispg.length == 2) {
		wherearewe = \'&#092; [<\'+\'a h\'+\'ref="\'+eval(\'l\'+thispg.substr(0,1))
		+\'" target="_self">\'+eval(\'m\'+thispg.substr(0,1))+\'</a>]\';
	} else {
	}
}
// --></script>
';

//	following needed to be before some javascript in body otherwise breaks IE6, checking
	$html_view .= '
<style>
body,body#tinymce.mceContentBody{font-family:'.$font_family.';font-size:'.$font_size.'px;}'
.($leftlinksentry==''?'#lpane #leftbox{display:none;}':'')
.($x==1?$index_style:'').'
</style>';

	if (isset($_google_analytics_async) && ($_google_analytics_async === true))
	if (!stristr($_SERVER['HTTP_HOST'],"localhost") && !stristr($_SERVER['PHP_SELF'],$urladmin)) {
		if (file_exists($getcwd.$up.$safedir.'_google_analytics.php'))
			include($getcwd.$up.$safedir.'_google_analytics.php');
		else
			include($getcwd.$up.$urladmin.'defaults/_google_analytics.php');
		$html_view .=  $_google_analytics;
	}

	$html_view .= '
</head>
<body>';// id="main_body"

	if (($menu_pagine === true) && ($rootwindowmenu !== ''))
	$html_view .=  '
<script type="text/javascript"><!-- //
mmLoadMenus(\''.$local.'\');
// --></script>
';

	/*
	if ($topicslist)
	$right_content .=  '
			    <div id="topics">
	          '.$topicslist.'
	        </div>';
	if ($base_x == '1')
	$right_content .=  '
			    <br />
	    		<div id="leftbox">
	    			'.$leftlinksentry.'
	    		</div>';
	$right_content .=  '<div class="clear"></div>';
	*/
	//	?!!//$gen_lang = (!stristr($_SERVER['PHP_SELF'],$urladmin)&&($logged_in===true)?'':gen_lang());
	$gen_lang = gen_lang();
	if (stristr($_SERVER['PHP_SELF'],$urladmin))
		$div_alogin =  '<div class="toploginwrapper"><div id="toplogin">'.$login.'</div></div>';
	else
		$div_alogin = '';
	if (!stristr($_SERVER['PHP_SELF'],$urladmin) && ($nRowsUsery>0))
		$div_ulogin =  '<div id="leftlogin">'.$login.'</div>';
	else
		$div_ulogin = '';
	$title = ((!stristr($this_priv,"1") && ($this_priv !== ""))?
  				'<span class="lock">'.$title.'</span>':$title);
	if (isset($q)) {
	//	$notice = $q.$notice;
	//	$content = str_ireplace("$q",'\<span class=\"qhighlight\"\>'.$q.'\<\/span\>',$content);
	}
	$content = (!stristr($_SERVER['PHP_SELF'],$urladmin)?'<div class="content_body">':'')
  			.($notice==''?'':'<div class="notice">'.$notice.'</div>')
  			.($error==''?'':'<div class="error">'.$error.'</div>')
  			.preg_replace('/(\.\.\/)+/',"$1",$content)
  			.(!stristr($_SERVER['PHP_SELF'],$urladmin)?'</div>':'');
	$notice = '';
	$error = '';
	$right_content = ''; // check below
	$content .= $right_content.'<div class="clear"></div> <br />';
	$footer = '<!-- webdeveloper credit -->
	  	'.$copyrightnoticeString.' '.$this_annee.' <!-- website developed by '
  		.'<a href="http://www.mediavince.com" target="_blank">www.mediavince.com</a> - &copy; -->';
  
	if (!stristr($_SERVER['PHP_SELF'],$urladmin))
	// || (stristr($_SERVER['PHP_SELF'],$urladmin) && ($logged_in === false)))
	$up = "";
  
	################################## IMPORT TEMPLATE
	if (file_exists($getcwd.$up.$safedir.'_template.php'))
		include($getcwd.$up.$safedir.'_template.php');
	else
		include($getcwd.$up.$urladmin.'defaults/_template.php');

	$html_view .= $_template;
	################################## IMPORT TEMPLATE

	if (!isset($_google_analytics_async) || ($_google_analytics_async === false))
	if (!stristr($_SERVER['HTTP_HOST'], "localhost") && !stristr($_SERVER['PHP_SELF'],$urladmin)) {
		if (file_exists($getcwd.$up.$safedir.'_google_analytics.php'))
			include($getcwd.$up.$safedir.'_google_analytics.php');
		else
			include($getcwd.$up.$urladmin.'defaults/_google_analytics.php');
		$html_view .=  $_google_analytics;
	}

	if (!empty($javascript))
		$html_view .= $javascript;
	
	if ((stristr($_SERVER['PHP_SELF'],$urladmin) || ($edit_text === true))
	&& ($tinyMCE === true) && ($logged_in === true)
	&& (!isset($send) || (isset($send)
		&& (($send == 'new') || ($send == 'edit') || ($send == 'editemintro'))))
	) {// && ($x != 'z') => tinyMce params is	mode : "exact",    elements : "elm1",';
		$inject_in_js = '
		theme : "advanced",
		plugins : "'.(
		stristr($_SERVER['PHP_SELF'],$urladmin)?
			'safari,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups'
			.',insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality'
			.',fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,pagebreak'
		:
			'paste,style,layer,table,link,inlinepopups,safari",
			editor_deselector : "mceNoEditor'
		).'",';
	$themes_plugin = '
		mode : "exact",
		elements : "elm1';
	if ($i_elm>1) {
		for($i=2;$i<=$i_elm;$i++)
		$themes_plugin .= ',elm'.$i;
	}
	$themes_plugin .= '",';
	$themes_plugin .=  $inject_in_js;
	$themes_plugin .=  '
    browsers : "msie,gecko,safari",
    cleanup_on_startup : true,
    convert_fonts_to_spans : true,
		force_br_newlines : true,
		convert_urls : true,
	  document_base_url : "'.$mainurl.'",
		'.(
      stristr($_SERVER['PHP_SELF'],$urladmin)?
        'extended_valid_elements : "script[type|src],marquee[onmouseout|onmouseover|scrolldelay'
          .'|scrollamount|height|width|direction|behavior],iframe[width|height|frameborder|scrolling'
          .'|marginheight|marginwidth|src]",
    theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,|,justifyleft,justifycenter'
          .',justifyright,justifyfull,|,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pasteword,|,bullist,numlist,|,outdent,indent'
          .',blockquote,|,link,unlink,anchor,image,cleanup,help,code,|,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "undo,redo,|,tablecontrols,|,hr,removeformat,visualaid'
          .',|,media,advhr,|,fullscreen",'
      :
      ($very_basic_tinyMCE===true?'theme_advanced_buttons1 : "pasteword,cleanup'
        .',|,bold,italic,underline,|,bullist,numlist,|,link,unlink,|,image",
   		theme_advanced_buttons2 : "",
   		theme_advanced_buttons3 : "",'
      :
      'theme_advanced_buttons1 : "cut,copy,paste,pasteword,|,bold,italic,underline,|,link,unlink'
        .',|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,outdent,indent'
        .',|,blockquote,|,code,|,image,|,undo,redo",
   		theme_advanced_buttons2 : "",')
    ).'
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "center",
		theme_advanced_statusbar_location : "bottom",
		external_link_list_url : "admin/tinymce_lists.php?tmcels=doc&amp;lg='.$lg.'",
		external_image_list_url : "admin/tinymce_lists.php?tmcels=img&amp;lg='.$lg.'",
		media_external_list_url : "admin/tinymce_lists.php?tmcels=swf&amp;lg='.$lg.'",
		theme_advanced_resize_horizontal : false,
		theme_advanced_resizing : true,
		apply_source_formatting : true
	// parametri perso
	//	flash_external_list_url : "admin/tinymce_lists.php?tmcels=swf&amp;lg='.$lg.'",
	//	template_external_list_url : "example_data/example_template_list.js?lg='.$lg.'",
	//	content_css : "/example_data/example_full.css",
	//	theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,|,justifyleft,justifycenter'
    .'//,justifyright,justifyfull,|,fontselect,fontsizeselect",
	//	theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops'
    .'//,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",
	//	file_browser_callback : "fileBrowserCallBack",
	//	theme_advanced_toolbar_location : "external", // bug on this CMS with IE6
	//	theme_advanced_disable : "hr,charmap,separator,sub,sup,separator"';

	if ($tiny_compression === true) {
	// REPLACE WITH tiny_mce.js SCRIPT TAG (BELOW) , ALSO CHECK IF SERVER USES ZLIB COMPRESSION 
		$html_view .= '
<script type="text/javascript" src="'.$mainurl.'lib/vendors/tiny_mce/tiny_mce_gzip.js"></script>
<script type="text/javascript">
tinyMCE_GZ.init({
	language: "'.$lg.'",
	disk_cache: true,
	debug: false,
	'.$themes_plugin.'
	'.(isset($add_tmce_settings)&&$add_tmce_settings!=''?','.$add_tmce_settings:'').'
});
</script>';
		
	//<!-- Needs to be seperate script tags! -->
	/*******************************************************/
	} else {
		$html_view .=  '
<script type="text/javascript" src="'.$mainurl.'lib/vendors/tiny_mce/tiny_mce.js"></script>';
	}
	$html_view .=  '
<script type="text/javascript">
tinyMCE.init({
	language: "'.$lg.'",
	'.$themes_plugin.'
	'.(isset($add_tmce_settings)&&$add_tmce_settings!=''?','.$add_tmce_settings:'').'
});
</script>
<script type="text/javascript">
function ajaxLoad() {
	var ed = tinyMCE.get(\'elm1\');
	// Do you ajax call here, window.setTimeout fakes ajax call
	ed.setProgressState(1); // Show progress
	window.setTimeout(function() {
		ed.setProgressState(0); // Hide progress
	//	ed.setContent(\'HTML content that got passed from server.\');
	}, 3000);
}
function ajaxSave() {
	var ed = tinyMCE.get(\'elm1\');
	// Do you ajax call here, window.setTimeout fakes ajax call
	ed.setProgressState(1); // Show progress
	window.setTimeout(function() {
		ed.setProgressState(0); // Hide progress
	//	alert(ed.getContent());
	}, 3000);
}
</script>';

	} // ends if urladmin tinyMCE...

	if (isset($trace))
	$html_view .=  '<div style="background-color:#FFFFFF;"><!-- debugger --><br />Trace debugger<br />'
				.$trace.'</div>';
	$html_view .=  '
</body>
</html>
';

	if (!stristr($_SERVER['PHP_SELF'],$urladmin)
	|| (stristr($_SERVER['PHP_SELF'],$urladmin) && ($logged_in === false)))
	$html_view = preg_replace('/\?lg=(\w+)\&amp;x=(\d+)/e','lgx2readable("$1","$2")',$html_view);
/*
	$html_view = preg_replace(
		'/\?lg=(\w+)\&amp;x=(\d+)/e',
		'$lg/sql_getone($tblcont,"WHERE contlang=\'$1\' AND contpg=\'$2\' ","conturl")',
		$html_view
	);
*/

	if (!stristr($_SERVER['PHP_SELF'],$urladmin) && !stristr($_SERVER['PHP_SELF'],$urlintro))
	$html_view = str_replace("mp3=../$filedir","mp3=$filedir",$html_view);

echo stripslashes($html_view);
}
