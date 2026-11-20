#!/bin/sh
set -e
# Flag ditaruh satu folder di atas web root (/var/www), dibaca aplikasi saat role=admin.
echo "${FLAG_USER:-FLAG{placeholder_user_run_via_labctl}}" > /var/www/user.txt
chmod 644 /var/www/user.txt
unset FLAG_USER
exec apache2-foreground
