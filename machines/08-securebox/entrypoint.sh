#!/bin/sh
set -e
echo "${FLAG_USER:-FLAG{placeholder_user_run_via_labctl}}" > /opt/securebox/flag.txt
chmod 644 /opt/securebox/flag.txt
unset FLAG_USER
exec python3 /opt/securebox/serve.py 0.0.0.0 9000
