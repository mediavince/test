<?php if (stristr($_SERVER['PHP_SELF'],'_tpl_mail_newsletter.php')){include'_security.php';Header("Location: $redirect");Die();}$_tpl_mail_newsletter = "$mail_headers_text

$nl_copiercollerString :

$mainurl?nlid=$nlid

$nonhtml_content_generated

$slogan, $cosite
$clientemail

$mail_headers_html

<!-- 
$nl_copiercollerString :

$mainurl?nlid=$nlid

$nonhtml_content_generated

$slogan, $cosite
$clientemail
 -->
<html><body><table bgcolor='#FFFFFF' align='center' width='650' border='0' cellpadding='0' cellspacing='0'><tr><td align='center'><a href='$mainurl?nlid=$nlid' target='_blank'>
$cologo_img
</a></td></tr><tr><td>
$html_content_generated
</td></tr><tr><td align='center'>
<hr />
$footer
<br /> <br /></td></tr></table></body></html>
";
