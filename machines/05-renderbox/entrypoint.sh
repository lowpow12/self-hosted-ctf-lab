#!/bin/sh
set -e
echo "${FLAG_USER:-FLAG{placeholder_user_run_via_labctl}}" > /var/www/user.txt
chmod 644 /var/www/user.txt
unset FLAG_USER
exec apache2-foreground
