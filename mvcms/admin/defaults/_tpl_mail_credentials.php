<?php if (stristr($_SERVER['PHP_SELF'], basename(__FILE__))){include '_security.php';Header("Location: $redirect");Die();}$_tpl_mail_credentials = "
$mail_headers_text

$nl_copiercollerString :

  $mainurl".(isset($nlid)?"?nlid=$nlid":"")."

  ".(isset($this_isTitle)?$this_isTitle:$this_isUtil).",
   
  $tplemailaccountcreatedString $coname
  
  $tplemailvisitandloginString
   - $nomutilString : $this_isUtil
   - $motdepasseString : $this_isPass
   
  ".strip_tags(str_replace("<br />",$CRLF,$tplemailxtrainfoString))."
  
  ".(isset($nonhtml_content_generated)?$nonhtml_content_generated:"")."

  $slogan, $cosite
  $clientemail

$mail_headers_html

<!--
$nl_copiercollerString :

  $mainurl".(isset($nlid)?"?nlid=$nlid":"")."

  ".(isset($this_isTitle)?$this_isTitle:$this_isUtil).",
   
  $tplemailaccountcreatedString $coname
  
  $tplemailvisitandloginString
   - $nomutilString : $this_isUtil
   - $motdepasseString : $this_isPass
   
  ".strip_tags(str_replace("<br />",$CRLF,$tplemailxtrainfoString))."
  
  ".(isset($nonhtml_content_generated)?$nonhtml_content_generated:"")."

  $slogan, $cosite
  $clientemail
-->
<html><body><table bgcolor='#FFFFFF' align='center' width='650' border='0' cellpadding='0' cellspacing='0'><tr><td align='center'>
<a href='$mainurl".(isset($nlid)?"?nlid=$nlid":'')."' target='_blank'>$cologo_img</a>
</td></tr><tr><td>
  <br /> <br />
  
  ".(isset($this_isTitle)?$this_isTitle:$this_isUtil).",
  <br /> <br />
  $tplemailaccountcreatedString $coname
  <br /> <br />
  <a href='$mainurl'>$mainurl</a>
  <br /> <br />
  $tplemailvisitandloginString
  <br /> - $nomutilString : $this_isUtil
  <br /> - $motdepasseString : $this_isPass
  <br /> <br />
  $tplemailxtrainfoString
  <br /> <br />
  ".(isset($html_content_generated)?$html_content_generated:"")."

</td></tr><tr><td align='center'>
<hr />
$footer
<br /> <br /></td></tr></table></body></html>
";
