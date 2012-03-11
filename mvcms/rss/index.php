<?PHP #۞ #

include '../admin/_incdb.php';

$array_modules_oked = array();
foreach($array_modules as $k)
if (!in_array(sql_getone($tblcont,"WHERE conttype='$k' AND contstatut='Y' ","contpg")
							,array("",".")))
$array_modules_oked[] = $k;

/*
$array_rss = array('event','gallery');
$array_rss_dateindex = array('event'=>'from');
$rss_limit = 25;
$rss_days = 7;
$rss_cont = true;
*/

if (isset($array_rss))
$array_modules_oked = array_unique(array_intersect($array_rss,$array_modules_oked));

$sql = "SELECT DISTINCT htaccessdate,htaccessurl,htaccesstitle,htaccessitem,htaccesstype
				FROM _htaccess
				WHERE ".(isset($rss_days)&&($rss_days>0)?
									"htaccessdate >= DATE_SUB(NOW(), INTERVAL $rss_days DAY) AND ":'')
								." htaccessstatut='Y' AND htaccesslang='$lg' AND htaccesstype IN ('"
																					.implode("', '",$array_modules_oked)."') "
								.(isset($rss_cont)&&$rss_cont===true?
									"UNION SELECT contupdate,conturl,conttitle,contpg,conttype
									FROM _cont WHERE ".(isset($rss_days)&&($rss_days>0)?
														"contupdate >= DATE_SUB(NOW(), INTERVAL $rss_days DAY) AND ":'')
									." contstatut='Y' AND contlang='$lg' AND conttype='' ":'')."
				ORDER BY htaccessdate DESC LIMIT 0,"
										.(isset($rss_limit)&&($rss_limit>0)?$rss_limit:'100');

$get_rss = @mysql_query($sql);

header("Content-Type: application/xml");

echo '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
<title>'.$codename.'</title>
<link>'.$mainurl.'</link>
<description>'.$coinfo.'</description>
<language>'.($lg=='en'?"en-us":$lg."-".$lg).'</language>
<atom:link href="'.$mainurl.'rss/" rel="self" type="application/atom+xml" />
';

while($get_row = @mysql_fetch_array($get_rss)) {
  $link = $mainurl.lgx2readable($lg,
																($get_row[4]==''?$get_row[3]:''),
																($get_row[4]!=''?$get_row[4]:NULL),
																($get_row[4]!=''?$get_row[3]:NULL));
	if ($get_row[4]!='')
	$guid = $mainurl."?".$get_row[4]."Id=".$get_row[3];
	else
	$guid = $link;
  echo '
  <item>
    <guid>'.$guid.'</guid>
    <title><![CDATA['.utf8_encode(html_entity_decode(
	($get_row[4]!=''?strtoupper(space2underscore(sql_stringit('general',$get_row[4]))).": ":'')
	.$get_row[2])).']]></title>
    <pubDate>'.date(DATE_RSS,strtotime($get_row[0])).'</pubDate>
    <category>'.utf8_encode(($get_row[4]!=''?$get_row[4]:'cont')).'</category> 
    <link>'.$link.'</link>
  </item>
  ';
} 
 
echo '</channel>
</rss>';

die();

?>