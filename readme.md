# Installation

1. Download and install CakePHP framework, for example into /usr/lib/cake
1. Download and place EnigmaCommerce into your webroot (or a subfolder) /var/www/ec3
1. Edit /var/www/ec3/webroot/index.php
    1. Set ROOT to the place where the ec3 directory is (/var/www)
    1. Set APP_DIR to the name of the ec3 directory (ec3)
    1. Set CAKE_CORE_INCLUDE_PATH to the location of the cake libs (/usr/lib/cake)
1. Edit /var/www/ec3/.htaccess and /var/www/ec3/webroot/.htaccess
    1. If EC is in the webroot, remove any RewriteBase entries
    1. If EC is not in the webroot, set RewriteBase to the webroot (/ec3)
2. Copy /ec3/config/database.php.default to database.php and configure with db settings

## Worked Example

Some examples files to highlight the file structure:
    /var/www/vhosts/enigmagen/cakephp/README
    /var/www/vhosts/enigmagen/httpdocs/ec3/app_controller.php
    /var/www/vhosts/enigmagen/httpdocs/ec3/config/database.php
    /var/www/vhosts/enigmagen/httpdocs/ec3/webroot/css.php
