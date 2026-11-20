#!/bin/sh
# Tulis flag user dari env (diinject labctl), 644 agar bisa dibaca via LFI
# oleh proses web (www-data), lalu hapus dari environment.
set -e

echo "${FLAG_USER:-FLAG{placeholder_user_run_via_labctl}}" > /home/analyst/user.txt
chmod 644 /home/analyst/user.txt

unset FLAG_USER
exec apache2-foreground
