FROM php:8.2-apache

RUN useradd -m -s /bin/bash snapper

COPY app/ /var/www/html/
RUN chown -R root:root /var/www/html \
 && chmod -R 755 /var/www/html \
 && chmod 733 /var/www/html/uploads   # writable oleh www-data, tak perlu listing

# Privesc vector: /bin/bash di-set SUID root (kesalahan konfigurasi klasik).
RUN chmod u+s /bin/bash

COPY entrypoint.sh /entrypoint.sh
RUN chmod 755 /entrypoint.sh
EXPOSE 80
ENTRYPOINT ["/entrypoint.sh"]
