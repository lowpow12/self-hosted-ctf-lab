#!/bin/sh
# Writes flags from env at startup, then removes them from the environment
# so the web server process (www-data) cannot read them via /proc or getenv().
set -e

echo "${FLAG_USER:-FLAG{placeholder_user_run_via_labctl}}" > /home/pingu/user.txt
chown root:pingu /home/pingu/user.txt
chmod 644 /home/pingu/user.txt

echo "${FLAG_ROOT:-FLAG{placeholder_root_run_via_labctl}}" > /root/root.txt
chmod 600 /root/root.txt

unset FLAG_USER FLAG_ROOT
exec apache2-foreground
