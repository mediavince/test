<?php if (stristr($_SERVER['PHP_SELF'],'_config.php')){include'_security.php';Header("Location: $redirect");Die();}

$codename = 'mvcms';
$client = 'mvcms';
//$active_template = "mvcms"; // in lib/templates/<mvcms> , replace all css in incdb and images etc...

$default_lg = 'en';
$array_lang = array($default_lg);//,'fr','it' etc... overwritten by actual statut in enum for each lang

$root_writable = true; // enables writing of files on root, from adminedit upon creation of new pages... if not able to write on root > false otherwise > true // overwritten by check in incdb if set to true depending on check...

$CRLF = true;
//$CRLF = false;// will become \\n :: if newsletter is not sent ok > uncomment, default is $CRLF = true or \\r\\n

$charset_iso = $mail_charset_iso = 'ISO-8859-1'; // UTF-8 buggy but might work better on some env

/*----JQUERY-------*/
// jquery is unabled by default with the latest and min version from branch 1
// 1 is the latest at the top but cache is set to revalidate more often,
// to avoid select the exact version you wish, check out jquery.org
$google_jquery_ver = '1.4.2';// at date of edit 1.4.4 // produces '!' error
// google_jquery enabled by default
// $google_jquery = true;

$bootstrap = false; // if true then make sure to set google_jquery = false !!

/*----MENUFUNC-----*/
$htaccess4sef = true; // this builds links as SEF, special htaccess needed
$x1subok = true; // this allows menus under 1 and prevent top menu to go over 9
$html_site = false; // create files in html in root folder and allow synchronize
$menu_pagine = true; // require _menu_pagine_root or not for dropdown dhtml menus
$js_prototype_scriptaculous = true;
$menu_prototype = false; // require _menu_pagine_root or not for dropdown dhtml menus
$show_menu_hierarchy = false; // shows listing of pages in every pages
$js_lightbox = true;
//$menu_id_on_li = true;// isset? defines whether the id for the menu items shows on li or not, defaults on img, changes might be needed on css
//$menu_pad_left = '0';// default -9 without menu_id_on_li
//$menu_pad_top = '26';// default 13 without menu_id_on_li

/*----OTHER OPTIONS E.G. GOOGLE-----*/
//$_google_analytics_async = true;// latest dev from google; code is placed after <body> tag instead of before </body>

/*-----MODULES-----*/
$array_safedir = array();

/*----ADMIN-----*/
$array_modules_admin = array("event", "gallery", "forum");
  if (stristr($_SERVER['PHP_SELF'],$urladmin)) {
    //  $_<module>_params_array = array('<field>'); // shows in admin table header
    //  $_<module>_array_mandatory_fields = array('<field>'); // if empty will error out
      $_admin_params_array = array('util','priv','email'); // shows in admin table header
      $_admin_array_mandatory_fields = array('','privilege','util','email'); // if empty will error out
      $_user_params_array = array('util','priv','email'); // shows in admin table header
      $_user_array_mandatory_fields = array('','privilege','util','email'); // if empty will error out
      $_membre_params_array = array('util','gendre','nom','prenom'); // shows in admin table header
      $_membre_array_mandatory_fields = array('','gendre','nom','prenom'); // if empty will error out
      $_event_params_array = array('type','from','until','title');
      $_event_array_mandatory_fields = array('','type','title','from','until');//,'type','title');
      $_gallery_params_array = array('title','date');
      $_gallery_array_mandatory_fields = array('','title');
      $_forum_params_array = array('title','date');
      $_forum_array_mandatory_fields = array('','type','title');
  }
  
/*----USERS-----*/
$user_can_add_types = false;// allows or not users to add types for enumtype_array
// enumtype_array > int(11) unsigned, produces selectable code for all possibilities taken from enum and assign string, then allows creation of new type, further options apply like - for deleting the selected item
$array_tables = array("membre", "event", "gallery", "forum", "comment");// unique from merge with basic ones, hardcoded
//$connected_byid_<tbl1>_<tbl2> = true;// belongs to relation
//$connected_byid_<tbl2>_<tbl1> = true;// belongs to relation
$array_interconnected_byid = array("user","membre");
$connected_byid_user_membre = true;
$connected_byid_membre_user = true;
$array_email_conf = array("user", "membre");// enables checkbox to send email confirmation

$array_modules = array("membre", "event", "gallery", "forum", "comment");// tables that can use the mod_generic

$array_fixed_modules = array("contact", "profil");// modules that use their own mod_<module>

$array_unmanaged_modules = array("contact", "profil");// modules that do not propose admin options and listing

$array_uniqueselection = array("eventmembre","forummembre");// only shows valid options like for a given institute only list corresponding members
//$array_int3_have_to_match = array("institute,membre"); // will generate matching selection menu options upon selection of the first

$array_modules_as_form = array();// disables listing of entry and propose a form

$array_galleries = array("gallery");

$array_csv_list = array("user", "membre");// offers to download excel list of given module

/*
$array_rss = array('event','gallery'); // defines the modules to show
$array_rss_dateindex = array('event'=>'from'); // listing by date (eventfrom) of item instead of last update
$rss_limit = 25; // default:100, defines the amount of entries
$rss_days = 7; // default:no limit, defines the date limit
$rss_cont = true; // default:none, shows or not the pages
*/

$tabbing = true;// shows tabs on profile page with tabs form array_tabs, below
$array_tabs = array("user", "membre", "event", "forum"); // default
$editable_by_membre = array("event", "forum");//$editable_by_membre = array("<module>");// for mod_profil but it is still not generic, has to be in array_tabs
$editable_by_membre_needed_params = array("membre");// fields that are in $editable_by_membre and are needed to submit content when logged in
$protected_show = true;// shows or not protected pages in the menus
//$map_image = 'content/europe-map_ori.gif'; // for map if coords field exists and a map has been uploaded
$mod_priv = true;// privileges are in place or not

$mod_newsletter = true;
$mod_contact = true;
$mod_inscrire = true;
$mod_event = true;
$mod_gallery = true;
// blog or forum commenting
//$mod_<module> = false;
//$<module>_commenting = false;
//$moderate_<module> = false;
$mod_forum = true;
$forum_commenting = true;
$moderate_forum = true;
$blog_multilang = false;// if translation is possible for each entry // still working on it leave false...

//$personalized_newsletter = false;

$tinyMCE = true; // lots of changes if set to Y or N
$very_basic_tinyMCE = true; // show much less options for users
$tiny_compression = false; // still buggy as true, to check
//$add_tmce_settings = 'content_css : "path/myfile.css?'.$now_time.'"';// or js ' + new Date().getTime()'; // to add more settings like in this case a css file in a given template

$nof = "1";// number of file uploadable, leave 1 is preferable
$listPerpg = "5";// number of items per page // add check for admin if needed

/*IMAGES*/
$max_sml = 200;
$max_big = 480;

$max_width = 200;
$max_height = 200;

$bigmax_width = 480;
$bigmax_height = 360;

$logoSize = "50";

/*STYLES*/
$font_family = "Trebuchet MS,Lucida Grande,Tahoma,Helvetica,Arial,sans-serif";
$font_size = "12";
$extra_meta = '';//<meta name="whatever" value="what you want"> // added after existing metas
$index_style = '';// additional styles for index, homepage to display different layout  

/* if lightbox_slideshow is installed in vendor (can be any other system)
*/
$lightbox_css = '
  <link rel="stylesheet" type="text/css" href="'.$up.'lib/vendors/lightbox_slideshow/lightbox.css" media="screen" />';
$lightbox_js = ($menu_prototype===true?'':'
  <script type="text/javascript" src="'.$up.'lib/vendors/lightbox_slideshow/prototype.js"></script>
  <script type="text/javascript" src="'.$up.'lib/vendors/lightbox_slideshow/scriptaculous.js?load=effects"></script>
  ').'
  <script type="text/javascript" src="'.$up.'lib/vendors/lightbox_slideshow/_mv_lightbox.js"></script>
  <script type="text/javascript" src="'.$up.'lib/vendors/lightbox_slideshow/langs.js"></script>';
$lightbox_initialize = 'showGroupName:true,slideTime:10';// lang added by default in view
//if image info is displayed on top, or imageDataCaption:north, if it works (_mv_lightbox.js defaults to north, update above to get lightbox.js instead)
$lightbox_css .= '
<style type="text/css">
#imageDetails{padding:5px 0 0 0;}
#detailsNav{padding:0;}
#closeLink{margin-top:5px;}
</style>';

$disable_lightbox_w_jquery_sort = true; // disables lightbox for sortable gallery

$array_sortable = array('gallery'); // default for all contphoto in a given gallery

/* for jquery sort: either use this or the other //
  <script type="text/javascript" src="'.$up.'lib/vendors/jquery/jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="'.$up.'lib/vendors/jquery/jquery-ui-1.7.1.custom.min.js"></script>
*/
$jquery_sort_js = '
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.js"></script>
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
		$("#sortable ul").sortable({ opacity: 0.6, cursor: "move", update: function() {
  			var order = $(this).sortable("serialize") + "&action=updateRecordsListings&what=contphoto";
  			$.post("'.$up.$urladmin.'_ajaxsorter.php", order, function(theResponse){
  				$("#ajax_response").html(theResponse);
  			});
  		}
		});
		$("#sortable").disableSelection();
	});
	</script>
';

$javascript = '
<script type="text/javascript" src="'.$up.'lib/vendors/jquery/jquery.hint.min.js"></script>
<script type="text/javascript">
jQuery.noConflict();
// Put all your code in your document ready area
jQuery(document).ready(function($){
	jQuery("input[title!=]").hint();
	$.fn.captchaRefresh = function (conf) {
		var config = $.extend({
			src:   "/captcha.png", 
			title: "Can`t see what it says? Click me to get a new string."
		}, conf);
		return this.each(function (x) {
			$("img[src^=" + config.src + "]", this)
				.attr("title", config.title)
				.attr("alt", config.title);
			// to use same image: uncomment this block and comment the following
		//	$(this).click(function (event) {// self click image for captcha
		//		var clicked = $(event.target);// self click image for captcha		
			$(".refreshcaptcha").click(function (event) {// class of img containing captcha
				var clicked = $(".imgspam");// class that gets clicked to update captcha
				if (clicked.attr("src") && clicked.attr("src").indexOf(config.src) === 0) {
					var now       = new Date();
					var separator = config.src.indexOf("?") == -1 ? "?" : "&";
					clicked.attr("src", config.src + separator + now.getTime());
				}
			});
		});
	};
});
</script>
';
/* put this in template where mainurl is set !!
	jQuery(\'#jquery-captcha-refresh-example\').captchaRefresh(
		{src: \''.$mainurl.'images/_captcha.php\'}
	);
 */

if ($bootstrap) {
	$stylesheet = '	
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
';
	$javascript = '
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
';
}

$this_annee = date('Y');

//$banner_src = 'images/banner_'.$client.'.gif';//png jpg

//$flash_array = array('attributes.name'=>'flash-container','swf'=>'file.swf','swf.width'=>'240','swf.height'=>'160','swf.ver'=>'6');

$array_multilingual = array(); // contains all fields that should be updated separately

$getvars_pg_array = array(); // contains all variables passed in application

// NEWSLETTER VARS
if	($mod_newsletter === true)	
$getvars_pg_array = array_merge($getvars_pg_array,array("nlid", "newsletterSujet", "newsletterMessage", "newsletterXmails"));

if	($mod_contact === true)	
$getvars_pg_array = array_merge($getvars_pg_array,array("email", "subject", "message"));

if	($tinyMCE === false)	
$getvars_pg_array = array_merge($getvars_pg_array,array("helpId", "helpPg", "helpTitle", "helpEntry"));

if	($mod_event === true)	
$getvars_pg_array = array_merge($getvars_pg_array,array("eventId", "eventDate", "eventStatut", "eventFrom", "eventUntil", "eventTitle", "eventEntry", "eventType", "new_eventType", "eventLocation", "new_eventLocation", "eventPublish", "eventArchive", "eventImg", "eventDoc", "eventMembre"));

if	($mod_gallery === true)	
$getvars_pg_array = array_merge($getvars_pg_array,array("galleryId", "galleryDate", "galleryStatut", "galleryTitle", "galleryEntry", "galleryGalleryphoto", "galleryImg", "galleryphotoId", "galleryphotoDate", "galleryphotoStatut", "galleryphotoDesc", "galleryphotoImg", "photoId"));//galleryphotoSort managed independently with ajaxsorter

##
if	($mod_inscrire === true)	
$getvars_pg_array = array_merge($getvars_pg_array,array("membreId", "membreDate", "membreStatut", "membreGendre", "membrePrenom", "membreNom", "membreTitre", "membreImg", "membreProfession", "membreAdresse", "membreVille", "membreCodpost", "membrePays", "membreNumtel", "membreSkype", "membreNumfax", "membreMarketing1", "membreEvent", "membreForum"));

$array_multilingual[] = "membreProfession";
$array_multilingual[] = "membreMarketing1";

if	($mod_forum === true)	
$getvars_pg_array = array_merge($getvars_pg_array,array("forumId", "forumStatut", "forumDate", "forumType", "new_forumType", "forumMembre", "forumTitle", "forumEntry", "forumTypeId", "forumPublish"));

if	($forum_commenting === true)	
$getvars_pg_array = array_merge($getvars_pg_array,array("commentId", "commentStatut", "commentDate", "commentName", "commentEmail", "commentWebsite", "commentOrganization", "commentEntry", "commentResponse", "commentIp","commentorder"));
