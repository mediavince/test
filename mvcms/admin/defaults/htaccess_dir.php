
# Options +FollowSymLinks

RewriteEngine On

############### localhost/folder
RewriteBase /folder

############### localhost
# RewriteBase /

## ENABLE ONE OR DISABLE TWO OR ALL ACCORDING TO SERVER SETTINGS, PHP5 HAS TO BE INSTALLED TO WORK
# AddHandler php5-script .php
# AddHandler application/x-httpd-php5 .php
AddType x-mapp-php5 .php

############### prevents recursive access to .svn folders,, 
############### replacing "index.php" with "- [F]" will act as forbidden
RewriteRule ^.svn index.php

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_URI} !^/index.php
RewriteCond %{REQUEST_URI} (/|\.php|\.html|\.htm|\.feed|\.pdf|\.raw|/[^.]*)$  [NC]
RewriteRule (.*) index.php

RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization},L]

RewriteCond %{QUERY_STRING} .
RewriteRule index.php(.*) /index.php? [L] 

########## Begin - Rewrite rules to block out some common exploits
## If you experience problems on your site block out the operations listed below
## This attempts to block the most common type of exploit `attempts` to Joomla!
#
# Block out any script trying to set a mosConfig value through the URL
RewriteCond %{QUERY_STRING} mosConfig_[a-zA-Z_]{1,21}(=|\%3D) [OR]
# Block out any script trying to base64_encode crap to send via URL
RewriteCond %{QUERY_STRING} base64_encode.*\(.*\) [OR]
# Block out any script that includes a <script> tag in URL
RewriteCond %{QUERY_STRING} (\<|%3C).*script.*(\>|%3E) [NC,OR]
# Block out any script trying to set a PHP GLOBALS variable via URL
RewriteCond %{QUERY_STRING} GLOBALS(=|\[|\%[0-9A-Z]{0,2}) [OR]
# Block out any script trying to modify a _REQUEST variable via URL
RewriteCond %{QUERY_STRING} _REQUEST(=|\[|\%[0-9A-Z]{0,2})
# Send all blocked request to homepage with 403 Forbidden error!
RewriteRule ^(.*)$ index.php [F,L]
#
########## End - Rewrite rules to block out some common exploits

