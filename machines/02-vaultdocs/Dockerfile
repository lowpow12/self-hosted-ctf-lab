FROM php:8.2-apache

# Buat user lokal; flag user diletakkan di home-nya (di luar web root).
RUN useradd -m -s /bin/bash analyst

COPY app/ /var/www/html/
RUN chown -R root:root /var/www/html && chmod -R 755 /var/www/html

# Catatan "internal" yang bocor lewat traversal (information disclosure).
COPY private/notes.txt /opt/vaultdocs/notes.txt
RUN chmod 644 /opt/vaultdocs/notes.txt

COPY entrypoint.sh /entrypoint.sh
RUN chmod 755 /entrypoint.sh

EXPOSE 80
ENTRYPOINT ["/entrypoint.sh"]
