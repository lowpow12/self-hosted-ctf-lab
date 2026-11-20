#!/bin/sh
set -e
# Tulis flag dari env (diinject labctl), lalu hapus dari environment.
echo "${FLAG_USER:-FLAG{placeholder_user}}" > /home/user/user.txt 2>/dev/null || true
echo "${FLAG_ROOT:-FLAG{placeholder_root}}" > /root/root.txt
unset FLAG_USER FLAG_ROOT
exec apache2-foreground
