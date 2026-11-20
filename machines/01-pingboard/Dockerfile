FROM php:8.2-apache

RUN apt-get update \
 && apt-get install -y --no-install-recommends iputils-ping sudo \
 && rm -rf /var/lib/apt/lists/*

# Local user that owns the user flag
RUN useradd -m -s /bin/bash pingu

# Privesc vector: misconfigured sudo rule (see GTFOBins)
RUN echo 'www-data ALL=(root) NOPASSWD: /usr/bin/find' > /etc/sudoers.d/netops-maint \
 && chmod 440 /etc/sudoers.d/netops-maint

COPY app/ /var/www/html/
RUN chown -R root:root /var/www/html && chmod -R 755 /var/www/html

COPY entrypoint.sh /entrypoint.sh
RUN chmod 755 /entrypoint.sh

EXPOSE 80
ENTRYPOINT ["/entrypoint.sh"]
