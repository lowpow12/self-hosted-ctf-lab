#!/bin/sh
set -e
echo "${FLAG_USER:-FLAG{placeholder_user_run_via_labctl}}" > /home/snapper/user.txt
chmod 644 /home/snapper/user.txt
echo "${FLAG_ROOT:-FLAG{placeholder_root_run_via_labctl}}" > /root/root.txt
chmod 600 /root/root.txt
unset FLAG_USER FLAG_ROOT
exec apache2-foreground
